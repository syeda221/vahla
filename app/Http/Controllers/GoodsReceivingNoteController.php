<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\GoodsReceivingNote;
use App\Models\GoodsReceivingNoteItem;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\VendorLedger;
use App\Models\InvoiceSeries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceivingNoteController extends Controller
{
    public function index($purchaseId)
    {
        $purchase = Purchase::with(['vendor', 'warehouse', 'items.product'])->findOrFail($purchaseId);
        $grns = GoodsReceivingNote::with('items.product')->where('purchase_id', $purchaseId)->orderBy('id', 'desc')->get();

        return view('admin_panel.purchase.grn.list', compact('purchase', 'grns'));
    }

    public function create($purchaseId)
    {
        $purchase = Purchase::with(['vendor', 'warehouse', 'items.product'])->findOrFail($purchaseId);

        if ($purchase->purchase_type !== 'purchase_order') {
            return redirect()->back()->with('error', 'Goods Receiving Notes can only be created for Purchase Orders.');
        }

        if ($purchase->receiving_status === 'received') {
            return redirect()->back()->with('error', 'This Purchase Order is already fully received.');
        }

        return view('admin_panel.purchase.grn.create', compact('purchase'));
    }

    public function store(Request $request, $purchaseId)
    {
        $purchase = Purchase::with(['items.product', 'vendor', 'warehouse'])->findOrFail($purchaseId);

        if ($purchase->purchase_type !== 'purchase_order') {
            return redirect()->back()->with('error', 'Invalid purchase order type.');
        }

        $quantities = $request->input('grn_qty', []);
        
        // Filter out items with 0 or empty qty
        $receivings = array_filter($quantities, function($qty) {
            return is_numeric($qty) && (float)$qty > 0;
        });

        if (empty($receivings)) {
            return redirect()->back()->with('error', 'Please provide valid quantities to receive.');
        }

        DB::beginTransaction();
        try {
            // Validate all quantities against remaining order quantity
            foreach ($receivings as $itemId => $qty) {
                $item = $purchase->items->where('id', $itemId)->first();
                if (!$item) {
                    throw new \Exception('Invalid purchase item selected.');
                }

                $qtyFloat = (float) $qty;
                $ordered = (float) $item->qty;
                $alreadyReceived = (float) ($item->received_qty ?? 0);
                $remaining = max(0, $ordered - $alreadyReceived);

                if ($qtyFloat > $remaining + 0.0001) {
                    $prodName = optional($item->product)->item_name ?: 'Item';
                    throw new \Exception("Cannot receive {$qtyFloat} of {$prodName}. Only {$remaining} remaining on this PO.");
                }
            }

            // Generate GRN Number
            $grnCount = GoodsReceivingNote::where('purchase_id', $purchaseId)->count() + 1;
            $baseNo = $purchase->invoice_no ?: ('PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT));
            $grnNumber = $baseNo . '-GRN' . str_pad($grnCount, 2, '0', STR_PAD_LEFT);

            $grnWarehouseId = (int) ($purchase->warehouse_id ?: 1);

            $grn = GoodsReceivingNote::create([
                'purchase_id' => $purchase->id,
                'vendor_id' => $purchase->vendor_id,
                'warehouse_id' => $grnWarehouseId,
                'grn_number' => $grnNumber,
                'grn_date' => $request->input('grn_date') ?: now()->format('Y-m-d'),
                'status' => 'received',
                'is_invoiced' => 0,
                'invoice_id' => null,
                'remarks' => $request->input('remarks'),
                'carrier_info' => $request->input('carrier_info'),
                'created_by' => auth()->id() ?? 1,
            ]);

            foreach ($purchase->items as $item) {
                if (isset($receivings[$item->id])) {
                    $receivedQty = (float) $receivings[$item->id];
                    $warehouseId = $item->warehouse_id ?? $grnWarehouseId;
                    $productId = $item->product_id;

                    if ($productId && $receivedQty > 0) {
                        $product = $item->product;
                        $ppb = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : ($product->pieces_per_box ?? 1));
                        if ($ppb <= 0) $ppb = 1;

                        $pSizeMode = $item->size_mode ?? ($product->size_mode ?? '');
                        $unit = strtolower(trim($item->unit ?? optional($product->unit)->name ?? ''));
                        $convFactor = 1.0;

                        if (!empty($item->color)) {
                            $b64Decoded = base64_decode($item->color, true);
                            $json = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                            if (!is_array($json)) {
                                $json = json_decode($item->color, true);
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
                            $baseQty = $receivedQty / 1000.0;
                        } elseif ($unit === 'carton' || $unit === 'ctn' || $unit === 'box' || ($pSizeMode === 'by_cartons')) {
                            $baseQty = $receivedQty * $ppb;
                        } elseif ($pSizeMode === 'by_kg' || $pSizeMode === 'by_gm') {
                            if ($unit === 'pcs' || $unit === 'pc' || $unit === 'piece' || ($convFactor > 0 && $convFactor != 1.0)) {
                                $baseQty = $receivedQty * $convFactor;
                            } else {
                                $baseQty = $receivedQty;
                            }
                        } elseif ($unit === 'pcs' || $unit === 'pc' || $unit === 'piece') {
                            $baseQty = $receivedQty;
                        } else {
                            $baseQty = $receivedQty * $convFactor;
                        }

                        // Increase Warehouse Stock
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
                                'price' => $item->price ?? 0,
                            ]);
                        }

                        // Add Stock Movement IN
                        StockMovement::create([
                            'product_id' => $productId,
                            'type' => 'in',
                            'qty' => $baseQty,
                            'ref_type' => 'GRN',
                            'ref_id' => $grn->id,
                            'note' => "GRN #{$grn->grn_number} for PO #{$baseNo} (Warehouse #{$warehouseId})",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Create GRN Item
                    GoodsReceivingNoteItem::create([
                        'goods_receiving_note_id' => $grn->id,
                        'purchase_item_id' => $item->id,
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'received_qty' => $receivedQty,
                        'boxes' => $receivedQty,
                        'loose_pieces' => 0,
                        'color' => $item->color ?? null,
                        'purchase_price' => $item->price ?? 0,
                    ]);

                    // Update Purchase Item received_qty
                    $item->received_qty = (float)($item->received_qty ?? 0) + $receivedQty;
                    $item->save();
                }
            }

            $purchase->recalculateReceivingStatus();

            DB::commit();

            return redirect()->route('purchase_orders.index')->with('success', "Goods Receiving Note {$grn->grn_number} created successfully and warehouse stock updated!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('GRN Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error recording GRN: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $grn = GoodsReceivingNote::with(['purchase.vendor', 'warehouse', 'items.product', 'creator'])->findOrFail($id);
        return view('admin_panel.purchase.grn.print', compact('grn'));
    }

    public function generateInvoiceForGrn(Request $request, $id)
    {
        $grn = GoodsReceivingNote::with(['items.product', 'purchase.vendor', 'vendor', 'warehouse'])->findOrFail($id);

        if ($grn->is_invoiced) {
            return redirect()->back()->with('error', 'This Goods Receiving Note has already been invoiced.');
        }

        if ($grn->items->isEmpty()) {
            return redirect()->back()->with('error', 'This Goods Receiving Note has no items to invoice.');
        }

        DB::beginTransaction();
        try {
            $vendorId = $grn->vendor_id ?: optional($grn->purchase)->vendor_id;
            if (!$vendorId) {
                throw new \Exception('No vendor associated with this Goods Receiving Note.');
            }

            $selectedPrefix = $request->input('invoice_prefix') ?: 'PINV';
            $invoiceNo = InvoiceSeries::generateNextNo($selectedPrefix);
            InvoiceSeries::incrementCounterForInvoice($invoiceNo);

            $invoiceDate = $request->input('invoice_date') ?: now()->format('Y-m-d');
            $creditDays = (int) ($request->input('credit_days') ?? optional($grn->purchase)->credit_days ?? 0);
            $vendorBillNo = $request->input('vendor_bill_no') ?? null;

            // Calculate Totals from GRN items
            $subtotal = 0;
            $itemsData = [];

            foreach ($grn->items as $gItem) {
                $poItem = $gItem->purchaseItem;
                $unitPrice = $poItem ? (float)$poItem->price : (float)$gItem->purchase_price;
                $itemDiscount = $poItem && $poItem->qty > 0 ? ((float)$poItem->item_discount / (float)$poItem->qty) * (float)$gItem->received_qty : 0;
                $lineTotal = ($gItem->received_qty * $unitPrice) - $itemDiscount;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => $gItem->product_id,
                    'price' => $unitPrice,
                    'qty' => $gItem->received_qty,
                    'item_discount' => $itemDiscount,
                    'line_total' => $lineTotal,
                    'color' => $gItem->color,
                ];
            }

            $extraCost = (float) ($request->input('extra_cost') ?? 0);
            $discount = (float) ($request->input('discount') ?? 0);
            $netAmount = max(0, $subtotal + $extraCost - $discount);
            $paidAmount = (float) ($request->input('paid_amount') ?? 0);
            $dueAmount = max(0, $netAmount - $paidAmount);

            $purchase = new Purchase();
            $purchase->purchase_type = 'purchase_invoice';
            $purchase->purchase_status = 'posted';
            $purchase->receiving_status = 'received';
            $purchase->parent_po_id = $grn->purchase_id;
            $purchase->branch_id = optional($grn->purchase)->branch_id ?? 1;
            $purchase->warehouse_id = $grn->warehouse_id ?? 1;
            $purchase->vendor_id = $vendorId;
            $purchase->invoice_no = $invoiceNo;
            $purchase->purchase_prefix = $selectedPrefix;
            $purchase->vendor_bill_no = $vendorBillNo;
            $purchase->purchase_date = $invoiceDate;
            $purchase->credit_days = $creditDays;
            $purchase->note = "Generated from GRN #{$grn->grn_number}";
            $purchase->subtotal = $subtotal;
            $purchase->discount = $discount;
            $purchase->extra_cost = $extraCost;
            $purchase->net_amount = $netAmount;
            $purchase->paid_amount = $paidAmount;
            $purchase->due_amount = $dueAmount;
            $purchase->status_purchase = 'Approved';
            $purchase->save();

            // Save Purchase Items
            foreach ($itemsData as $iData) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $iData['product_id'],
                    'price' => $iData['price'],
                    'qty' => $iData['qty'],
                    'received_qty' => $iData['qty'],
                    'item_discount' => $iData['item_discount'],
                    'line_total' => $iData['line_total'],
                    'color' => $iData['color'],
                ]);
            }

            // Mark GRN as Invoiced
            $grn->is_invoiced = 1;
            $grn->invoice_id = $purchase->id;
            $grn->save();

            // Vendor Ledger Posting (Accounts Payable)
            $vendor = \App\Models\Vendor::find($vendorId);
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

            // Recalculate parent PO if all GRNs invoiced
            if ($grn->purchase_id) {
                $parentPo = Purchase::find($grn->purchase_id);
                if ($parentPo) {
                    $parentPo->recalculateReceivingStatus();
                    $uninvoicedGrns = GoodsReceivingNote::where('purchase_id', $parentPo->id)->where('is_invoiced', 0)->count();
                    if ($parentPo->receiving_status === 'received' && $uninvoicedGrns === 0) {
                        $parentPo->purchase_status = 'posted';
                        $parentPo->save();
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Purchase Bill {$purchase->invoice_no} generated successfully for GRN {$grn->grn_number}!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Generate Invoice For GRN Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating purchase invoice: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $grn = GoodsReceivingNote::with(['items.product', 'purchase.items'])->findOrFail($id);

        // 1. Invoiced Check
        $isInvoiced = $grn->is_invoiced == 1 
            || !empty($grn->invoice_id);

        if ($isInvoiced) {
            $msg = 'Cannot delete! A purchase bill/invoice has already been generated for this Goods Receiving Note.';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        DB::beginTransaction();
        try {
            // 2. Stock Rollback (Deduct stock that was added by this GRN)
            foreach ($grn->items as $item) {
                $warehouseId = $item->warehouse_id ?? ($grn->warehouse_id ?? 1);
                $productId = $item->product_id;
                $receivedQty = (float) $item->received_qty;

                if ($productId && $receivedQty > 0) {
                    $product = $item->product;
                    $ppb = (float) ($item->boxes > 0 ? $item->boxes : ($product->pieces_per_box ?? 1));
                    if ($ppb <= 0) $ppb = 1;

                    $pSizeMode = $product->size_mode ?? '';
                    $unit = strtolower(trim(optional(optional($product)->unit)->name ?? ''));
                    $convFactor = 1.0;

                    if (!empty($item->color)) {
                        $b64Decoded = base64_decode($item->color, true);
                        $json = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                        if (!is_array($json)) {
                            $json = json_decode($item->color, true);
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
                        $baseQty = $receivedQty / 1000.0;
                    } elseif ($unit === 'carton' || $unit === 'ctn' || $unit === 'box' || ($pSizeMode === 'by_cartons')) {
                        $baseQty = $receivedQty * $ppb;
                    } elseif ($pSizeMode === 'by_kg' || $pSizeMode === 'by_gm') {
                        if ($unit === 'pcs' || $unit === 'pc' || $unit === 'piece' || ($convFactor > 0 && $convFactor != 1.0)) {
                            $baseQty = $receivedQty * $convFactor;
                        } else {
                            $baseQty = $receivedQty;
                        }
                    } elseif ($unit === 'pcs' || $unit === 'pc' || $unit === 'piece') {
                        $baseQty = $receivedQty;
                    } else {
                        $baseQty = $receivedQty * $convFactor;
                    }

                    // Deduct from WarehouseStock
                    $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    if ($stock) {
                        $stock->total_pieces -= $baseQty;
                        $stock->quantity = $stock->total_pieces / ($ppb > 0 ? $ppb : 1);
                        $stock->save();
                    }
                }
            }

            // 3. Delete related stock movements
            StockMovement::where('ref_id', $grn->id)
                ->whereIn('ref_type', ['GRN', 'direct_grn'])
                ->delete();

            // 4. Cascade delete linked Purchase Order (if single GRN or all GRNs deleted and not invoiced)
            $parentPoId = $grn->purchase_id;

            // Delete GRN items & GRN record
            $grn->items()->delete();
            $grn->delete();

            if ($parentPoId) {
                $otherGrnsCount = GoodsReceivingNote::where('purchase_id', $parentPoId)->count();
                $parentPo = Purchase::with('items')->find($parentPoId);

                if ($parentPo) {
                    if ($otherGrnsCount === 0) {
                        // Delete parent Purchase Order
                        $parentPo->items()->delete();
                        $parentPo->delete();
                    } else {
                        // Recalculate receiving status for remaining GRNs
                        $parentPo->recalculateReceivingStatus();
                    }
                }
            }

            DB::commit();

            $msg = "Goods Receiving Note #{$grn->grn_number} and linked Purchase Order deleted, and warehouse stock reverted successfully.";
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = 'Error deleting Goods Receiving Note: ' . $e->getMessage();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return redirect()->back()->with('error', $msg);
        }
    }
}
