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

        $cashAndBankHeadIds = \App\Models\AccountHead::whereRaw('LOWER(name) IN (?, ?)', ['cash', 'bank'])->pluck('id');
        $accounts = \App\Models\Account::whereIn('head_id', $cashAndBankHeadIds)
            ->where('status', 1)
            ->orderBy('title')
            ->get();

        $seriesList = InvoiceSeries::whereIn('prefix', ['PINV', 'TAX', 'CO', 'PO'])->orderBy('id')->get();
        if ($seriesList->isEmpty()) {
            $seriesList = InvoiceSeries::orderBy('prefix')->get();
        }

        return view('admin_panel.direct_grn.consolidate_preview', compact(
            'grns', 'consolidatedItems', 'vendor', 'totalNet', 'grandTotal', 'prevBalance', 'netBalance', 'nextInvoiceNo', 'accounts', 'seriesList', 'defaultPrefix'
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

        $validated = $request->validate([
            'invoice_no' => 'nullable|string',
            'invoice_prefix' => 'nullable|string',
            'invoice_date' => 'required|date',
            'vendor_bill_no' => 'nullable|string',
            'credit_days' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.color' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'extra_cost' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_account_id' => 'nullable|exists:accounts,id',
        ]);

        DB::beginTransaction();
        try {
            $vendorId = $grns->first()->vendor_id;
            $selectedPrefix = strtoupper(trim($request->input('invoice_prefix') ?: 'PINV'));
            $rawInvoiceNo = $request->input('invoice_no');
            $invoiceNo = InvoiceSeries::normalizeNumber($rawInvoiceNo, $selectedPrefix);

            if (Purchase::where('invoice_no', $invoiceNo)->exists()) {
                throw new \Exception("Invoice Number '{$invoiceNo}' already exists! Please enter a unique invoice number.");
            }

            InvoiceSeries::incrementCounterForInvoice($invoiceNo);

            $invoiceDate = $validated['invoice_date'] ?: now()->format('Y-m-d');
            $creditDays = (int) ($validated['credit_days'] ?? 0);
            $vendorBillNo = $validated['vendor_bill_no'] ?? null;

            $subtotal = 0;
            $totalItemsDiscount = 0;
            $itemsToCreate = [];

            foreach ($validated['items'] as $itemData) {
                $qty = (float) $itemData['qty'];
                $price = (float) $itemData['price'];
                $itemDiscount = (float) ($itemData['discount'] ?? 0);
                $lineTotal = max(0, ($qty * $price) - $itemDiscount);

                $subtotal += ($qty * $price);
                $totalItemsDiscount += $itemDiscount;

                $itemsToCreate[] = [
                    'product_id' => $itemData['product_id'],
                    'price' => $price,
                    'qty' => $qty,
                    'received_qty' => $qty,
                    'item_discount' => $itemDiscount,
                    'line_total' => $lineTotal,
                    'color' => $itemData['color'] ?? null,
                ];
            }

            $billDiscount = (float) ($request->input('discount') ?? 0);
            $totalDiscount = $totalItemsDiscount + $billDiscount;
            $extraCost = (float) ($request->input('extra_cost') ?? 0);
            $netAmount = max(0, $subtotal - $totalDiscount + $extraCost);
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
            $purchase->note = $request->input('remarks') ?: ("Consolidated Purchase Bill for GRNs: {$grnNumbersStr}");
            $purchase->subtotal = $subtotal;
            $purchase->discount = $totalDiscount;
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

            // Create Accounting Voucher & Record Payment
            try {
                $transactionService = app(\App\Services\TransactionService::class);
                if (method_exists($transactionService, 'createPurchaseVoucher')) {
                    $transactionService->createPurchaseVoucher($purchase);
                }
                if ($paidAmount > 0 && !empty($validated['payment_account_id'])) {
                    if (method_exists($transactionService, 'createPaymentForPurchase')) {
                        $transactionService->createPaymentForPurchase(
                            $purchase,
                            [$validated['payment_account_id']],
                            [$paidAmount]
                        );
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('Purchase Voucher Creation Notice: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('Purchase.home')->with('success', "Purchase Bill {$purchase->invoice_no} created successfully from GRN(s)!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Consolidate GRN Store Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error generating consolidated bill: ' . $e->getMessage());
        }
    }
}
