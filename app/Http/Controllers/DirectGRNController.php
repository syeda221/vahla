<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\GoodsReceivingNote;
use App\Models\GoodsReceivingNoteItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\VendorLedger;
use App\Models\InvoiceSeries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectGRNController extends Controller
{
    public function index()
    {
        $grns = GoodsReceivingNote::with(['vendor', 'warehouse', 'items.product', 'purchase', 'invoice'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admin_panel.direct_grn.index', compact('grns'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $products = Product::orderBy('item_name')->get();

        $nextGrnNumber = InvoiceSeries::generateNextNo('DGRN');

        return view('admin_panel.direct_grn.create', compact('vendors', 'warehouses', 'products', 'nextGrnNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'grn_date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array',
            'price' => 'nullable|array',
            'grn_number' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'remarks' => 'nullable|string',
            'carrier_info' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $rawGrnNo = $request->input('grn_number');
            $grnNumber = InvoiceSeries::normalizeNumber($rawGrnNo, 'DGRN');

            if (GoodsReceivingNote::where('grn_number', $grnNumber)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'grn_number' => "GRN Number '{$grnNumber}' already exists.",
                ]);
            }

            InvoiceSeries::incrementCounterForInvoice($grnNumber);

            $warehouseId = (int) ($validated['warehouse_id'] ?? 1);

            $grn = GoodsReceivingNote::create([
                'purchase_id' => null,
                'vendor_id' => $validated['vendor_id'],
                'warehouse_id' => $warehouseId,
                'grn_number' => $grnNumber,
                'grn_date' => $validated['grn_date'],
                'status' => 'received',
                'is_invoiced' => 0,
                'remarks' => $validated['remarks'] ?? null,
                'carrier_info' => $validated['carrier_info'] ?? null,
                'created_by' => auth()->id() ?? 1,
            ]);

            foreach ($validated['product_id'] as $index => $productId) {
                $qty = (float) ($validated['qty'][$index] ?? 0);
                $price = (float) ($validated['price'][$index] ?? 0);
                if ($qty <= 0) continue;

                $product = Product::find($productId);
                $ppb = (float) ($product && $product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
                if ($ppb <= 0) $ppb = 1;

                $colorField = $request->input('color')[$index] ?? null;
                $pSizeMode = $product->size_mode ?? '';
                $unit = strtolower(trim(optional($product->unit)->name ?? ''));
                $convFactor = 1.0;

                if (!empty($colorField)) {
                    $b64Decoded = base64_decode($colorField, true);
                    $json = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                    if (!is_array($json)) {
                        $json = json_decode($colorField, true);
                    }
                    if (is_array($json)) {
                        if (isset($json['conv_factor']) && (float)$json['conv_factor'] > 0) {
                            $convFactor = (float) $json['conv_factor'];
                        } elseif (isset($json['weight_per_piece']) && (float)$json['weight_per_piece'] > 0) {
                            $convFactor = (float) $json['weight_per_piece'] / 1000.0;
                        }
                        if (isset($json['unit']) && $json['unit'] !== '') {
                            $unit = strtolower(trim($json['unit']));
                        }
                    }
                }

                if ($unit === 'gm' || $unit === 'g' || $unit === 'gram' || $unit === 'grams') {
                    $baseQty = $qty / 1000.0;
                } elseif ($unit === 'carton' || $unit === 'ctn' || $unit === 'box' || ($pSizeMode === 'by_cartons')) {
                    $baseQty = $qty * $ppb;
                } elseif ($pSizeMode === 'by_kg' || $pSizeMode === 'by_gm') {
                    if ($unit === 'pcs' || $unit === 'pc' || $unit === 'piece' || ($convFactor > 0 && $convFactor != 1.0)) {
                        $baseQty = $qty * $convFactor;
                    } else {
                        $baseQty = $qty;
                    }
                } elseif ($unit === 'pcs' || $unit === 'pc' || $unit === 'piece') {
                    $baseQty = $qty;
                } else {
                    $baseQty = $qty * $convFactor;
                }

                // Increase Stock
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->total_pieces += $baseQty;
                    $stock->quantity = $stock->total_pieces / ($ppb > 0 ? $ppb : 1);
                    $stock->save();
                } else {
                    WarehouseStock::create([
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                        'total_pieces' => $baseQty,
                        'quantity' => $baseQty / ($ppb > 0 ? $ppb : 1),
                        'price' => $price,
                    ]);
                }

                // Log Movement
                StockMovement::create([
                    'product_id' => $productId,
                    'type' => 'in',
                    'qty' => $baseQty,
                    'ref_type' => 'DIRECT_GRN',
                    'ref_id' => $grn->id,
                    'note' => 'Direct GRN In: ' . $grn->grn_number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                GoodsReceivingNoteItem::create([
                    'goods_receiving_note_id' => $grn->id,
                    'purchase_item_id' => null,
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'received_qty' => $qty,
                    'boxes' => $qty,
                    'loose_pieces' => 0,
                    'color' => $colorField,
                    'purchase_price' => $price,
                ]);
            }

            DB::commit();

            return redirect()->route('direct-grn.index')->with('success', "Direct Goods Receiving Note {$grn->grn_number} created successfully and stock added!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Direct GRN Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating Direct GRN: ' . $e->getMessage());
        }
    }

    public function consolidatePreview(Request $request)
    {
        $grnIds = $request->input('grn_ids', []);
        if (is_string($grnIds)) {
            $grnIds = explode(',', $grnIds);
        }
        if (empty($grnIds) && $request->has('grn_id')) {
            $grnIds = [$request->input('grn_id')];
        }
        if (empty($grnIds) || !is_array($grnIds)) {
            return redirect()->route('direct-grn.index')->with('info', 'Please select Goods Receiving Note(s) to consolidate into a Purchase Bill.');
        }

        $grns = GoodsReceivingNote::with(['items.product', 'vendor', 'warehouse', 'purchase'])
            ->whereIn('id', $grnIds)
            ->where('is_invoiced', 0)
            ->get();

        if ($grns->isEmpty()) {
            return redirect()->route('direct-grn.index')->with('error', 'None of the selected GRNs are eligible for invoicing.');
        }

        // Validate that all GRNs belong to the SAME vendor
        $vendorIds = $grns->pluck('vendor_id')->unique();
        if ($vendorIds->count() > 1) {
            return redirect()->route('direct-grn.index')->with('error', 'Validation Error: Sirf ek hi vendor ki GRNs aik sath consolidate ho sakti hain.');
        }

        // Validate PO separation
        $poIds = $grns->pluck('purchase_id')->unique();
        $hasPo = $grns->whereNotNull('purchase_id')->isNotEmpty();
        $hasDirect = $grns->whereNull('purchase_id')->isNotEmpty();

        if ($hasPo && $hasDirect) {
            return redirect()->route('direct-grn.index')->with('error', 'Validation Error: Direct GRN aur Purchase Order ki GRN ko aik sath consolidate nahi kar sakte! Alag alag consolidate karein.');
        }

        if ($poIds->filter()->count() > 1) {
            return redirect()->route('direct-grn.index')->with('error', 'Validation Error: Different Purchase Orders ki GRNs ko aik sath consolidate nahi kar sakte! Sirf ek hi Purchase Order ki GRNs select karein.');
        }

        // Group items by product_id & color
        $consolidatedItems = [];
        $totalNet = 0;

        foreach ($grns as $grn) {
            foreach ($grn->items as $item) {
                $key = $item->product_id . '_' . ($item->color ?? 'none');
                $price = (float) $item->purchase_price;

                if (!isset($consolidatedItems[$key])) {
                    $consolidatedItems[$key] = [
                        'product_id' => $item->product_id,
                        'product_name' => optional($item->product)->item_name ?: 'Product #' . $item->product_id,
                        'product_code' => optional($item->product)->item_code ?: '',
                        'color' => $item->color,
                        'received_qty' => 0,
                        'price' => $price,
                        'line_total' => 0,
                        'grn_references' => []
                    ];
                }

                $consolidatedItems[$key]['received_qty'] += (float) $item->received_qty;
                $lineTotal = (float) $item->received_qty * $price;
                $consolidatedItems[$key]['line_total'] += $lineTotal;
                $consolidatedItems[$key]['grn_references'][] = $grn->grn_number;
                $totalNet += $lineTotal;
            }
        }

        $vendor = $grns->first()->vendor;
        $vendorId = $vendor->id ?? 0;
        $lastLedger = VendorLedger::where('vendor_id', $vendorId)->orderBy('id', 'desc')->first();
        $prevBalance = $lastLedger ? (float)$lastLedger->closing_balance : (float)($vendor->opening_balance ?? 0);
        $grandTotal = $totalNet;
        $netBalance = $prevBalance + $grandTotal;

        $defaultPrefix = 'PINV';
        $nextInvoiceNo = InvoiceSeries::generateNextNo($defaultPrefix);

        return view('admin_panel.direct_grn.consolidate_preview', compact(
            'grns', 'consolidatedItems', 'vendor', 'totalNet', 'grandTotal', 'prevBalance', 'netBalance', 'nextInvoiceNo'
        ));
    }

    public function consolidateStore(Request $request)
    {
        $grnIds = $request->input('grn_ids', []);
        if (empty($grnIds)) {
            return redirect()->route('direct-grn.index')->with('error', 'No GRNs selected.');
        }

        $grns = GoodsReceivingNote::with(['items.product', 'vendor', 'purchase'])
            ->whereIn('id', $grnIds)
            ->where('is_invoiced', 0)
            ->get();

        if ($grns->isEmpty()) {
            return redirect()->route('direct-grn.index')->with('error', 'Selected GRNs are already invoiced.');
        }

        $vendorIds = $grns->pluck('vendor_id')->unique();
        if ($vendorIds->count() > 1) {
            return redirect()->route('direct-grn.index')->with('error', 'Validation Error: Sirf ek hi vendor ki GRNs aik sath consolidate ho sakti hain.');
        }

        $poIds = $grns->pluck('purchase_id')->unique();
        $hasPo = $grns->whereNotNull('purchase_id')->isNotEmpty();
        $hasDirect = $grns->whereNull('purchase_id')->isNotEmpty();

        if ($hasPo && $hasDirect) {
            return redirect()->route('direct-grn.index')->with('error', 'Validation Error: Direct GRN aur Purchase Order ki GRN ko aik sath consolidate nahi kar sakte! Alag alag consolidate karein.');
        }

        if ($poIds->filter()->count() > 1) {
            return redirect()->route('direct-grn.index')->with('error', 'Validation Error: Different Purchase Orders ki GRNs ko aik sath consolidate nahi kar sakte! Sirf ek hi Purchase Order ki GRNs select karein.');
        }

        DB::beginTransaction();
        try {
            $vendorId = $grns->first()->vendor_id;
            $selectedPrefix = $request->input('invoice_prefix') ?: 'PINV';
            $invoiceNo = InvoiceSeries::generateNextNo($selectedPrefix);
            InvoiceSeries::incrementCounterForInvoice($invoiceNo);

            $invoiceDate = $request->input('invoice_date') ?: now()->format('Y-m-d');
            $creditDays = (int) ($request->input('credit_days') ?? 0);
            $vendorBillNo = $request->input('vendor_bill_no') ?? null;

            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($grns as $grn) {
                foreach ($grn->items as $item) {
                    $price = (float) $item->purchase_price;
                    $lineTotal = (float) $item->received_qty * $price;
                    $subtotal += $lineTotal;

                    $itemsToCreate[] = [
                        'product_id' => $item->product_id,
                        'price' => $price,
                        'qty' => $item->received_qty,
                        'received_qty' => $item->received_qty,
                        'item_discount' => 0,
                        'line_total' => $lineTotal,
                        'color' => $item->color,
                    ];
                }
            }

            $extraCost = (float) ($request->input('extra_cost') ?? 0);
            $discount = (float) ($request->input('discount') ?? 0);
            $netAmount = max(0, $subtotal + $extraCost - $discount);
            $paidAmount = (float) ($request->input('paid_amount') ?? 0);
            $dueAmount = max(0, $netAmount - $paidAmount);

            $grnNumbersStr = $grns->pluck('grn_number')->implode(', ');

            $purchase = new Purchase();
            $purchase->purchase_type = 'purchase_invoice';
            $purchase->purchase_status = 'posted';
            $purchase->receiving_status = 'received';
            $purchase->parent_po_id = $grns->first()->purchase_id;
            $purchase->branch_id = 1;
            $purchase->warehouse_id = $grns->first()->warehouse_id ?? 1;
            $purchase->vendor_id = $vendorId;
            $purchase->invoice_no = $invoiceNo;
            $purchase->purchase_prefix = $selectedPrefix;
            $purchase->vendor_bill_no = $vendorBillNo;
            $purchase->purchase_date = $invoiceDate;
            $purchase->credit_days = $creditDays;
            $purchase->note = "Consolidated Purchase Bill for GRNs: {$grnNumbersStr}";
            $purchase->subtotal = $subtotal;
            $purchase->discount = $discount;
            $purchase->extra_cost = $extraCost;
            $purchase->net_amount = $netAmount;
            $purchase->paid_amount = $paidAmount;
            $purchase->due_amount = $dueAmount;
            $purchase->status_purchase = 'Approved';
            $purchase->save();

            // Save Items
            foreach ($itemsToCreate as $it) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $it['product_id'],
                    'price' => $it['price'],
                    'qty' => $it['qty'],
                    'received_qty' => $it['received_qty'],
                    'item_discount' => $it['item_discount'],
                    'line_total' => $it['line_total'],
                    'color' => $it['color'],
                ]);
            }

            // Mark all GRNs as Invoiced
            foreach ($grns as $grn) {
                $grn->is_invoiced = 1;
                $grn->invoice_id = $purchase->id;
                $grn->save();

                if ($grn->purchase_id) {
                    $po = Purchase::find($grn->purchase_id);
                    if ($po) {
                        $po->recalculateReceivingStatus();
                        $uninvoicedGrns = GoodsReceivingNote::where('purchase_id', $po->id)->where('is_invoiced', 0)->count();
                        if ($po->receiving_status === 'received' && $uninvoicedGrns === 0) {
                            $po->purchase_status = 'posted';
                            $po->save();
                        }
                    }
                }
            }

            // Update Vendor Ledger
            $vendor = Vendor::find($vendorId);
            if ($vendor) {
                $lastLedger = VendorLedger::where('vendor_id', $vendorId)->orderBy('id', 'desc')->first();
                $prevBal = $lastLedger ? (float)$lastLedger->closing_balance : (float)($vendor->opening_balance ?? 0);
                $newBal = $prevBal + $netAmount - $paidAmount;

                VendorLedger::create([
                    'vendor_id' => $vendorId,
                    'admin_or_user_id' => auth()->id() ?? 1,
                    'previous_balance' => $prevBal,
                    'closing_balance' => $newBal,
                    'opening_balance' => 0,
                    'created_at' => $invoiceDate . ' ' . now()->format('H:i:s'),
                ]);
            }

            DB::commit();

            return redirect()->route('Purchase.home')->with('success', "Consolidated Purchase Bill {$purchase->invoice_no} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Consolidate GRN Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating consolidated bill: ' . $e->getMessage());
        }
    }
}
