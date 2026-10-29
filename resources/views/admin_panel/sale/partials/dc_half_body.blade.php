<div class="invoice-content-top">
    <!-- Header -->
    <div class="pad-header">
        <div class="logo-section">
            @if(!empty($companyLogo) && file_exists(public_path($companyLogo)))
                <img src="{{ asset($companyLogo) }}" alt="Company Logo" class="logo-img">
            @else
                <div class="logo-text-box">
                    <span class="logo-title">{{ \App\Models\Setting::get('company_name', 'VAHLA') }}</span>
                    <span class="logo-subtitle">MILL STORE</span>
                </div>
            @endif
            <div class="badge-wrapper">
                <span class="invoice-badge">{{ $badgeTitle ?? 'DELIVERY CHALLAN' }}</span>
                <span class="copy-type-tag">{{ $copyType }}</span>
            </div>
        </div>
        
        <div class="company-details-section">
            <div class="deals-in">
                <span>DEALER IN:</span> {{ \App\Models\Setting::get('deals_in', 'Fasteners, Bearings, Equipments, V-belt, PPE\'s, Hand & Cutting Tools etc.') }}
            </div>
            <div class="contact-line">
                <span dir="ltr">{{ \App\Models\Setting::get('company_address', 'SHOP: 91-74-OP, FC GARDEN, SECTOR NO: 15-A/L-1, SADDAR BAZAR HYD.') }}</span>
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="contact-line">
                <span dir="ltr">{{ \App\Models\Setting::get('company_phone', '+92 022 2780007 / 03003077299') }}</span>
                <i class="fa-solid fa-phone"></i>
            </div>
            @if(\App\Models\Setting::get('website_link') || \App\Models\Setting::get('company_email'))
            <div class="contact-line">
                <span dir="ltr">{{ \App\Models\Setting::get('website_link') ?: \App\Models\Setting::get('company_email', 'vahlamillstore@hotmail.com') }}</span>
                <i class="fa-solid fa-envelope"></i>
            </div>
            @endif
        </div>
    </div>

    <!-- Meta -->
    <div class="meta-section">
        <div class="meta-left">
            <div class="meta-row">
                <div class="meta-label">S.No.</div>
                <div class="meta-value">{{ $sale ? $sale->invoice_no : ($dcNumbers ?? 'DC') }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">LPO</div>
                <div class="meta-value">{{ $lpoReference }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">T/P</div>
                <div class="meta-value">{{ optional($sale)->transport_name ?? (optional($sale)->vehicle_number ?? '') }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">M/s</div>
                <div class="meta-value">{{ $customerName }}</div>
            </div>
        </div>
        <div class="meta-right">
            <div class="meta-row">
                <div class="meta-label" style="width: 55px;">Date:</div>
                <div class="meta-value">{{ \Carbon\Carbon::parse(optional($sale)->sale_date ?? (optional($sale)->created_at ?? now()))->format('d-m-Y') }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label" style="width: 55px;">D.C. No:</div>
                <div class="meta-value">{{ $dcNumbers }}</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="pad-table">
        <thead>
            <tr>
                <th style="width: 5%;">S.NO</th>
                <th class="desc-col" style="width: 44%; text-align: left;">DESCRIPTION</th>
                <th style="width: 10%;">QTY</th>
                <th class="rate-col" style="width: 13%;">RATE</th>
                <th style="width: 13%;">UNIT</th>
                <th class="rate-col" style="width: 15%;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chunkItems as $item)
                @php
                    $sNo = $startIndex + $loop->index;
                    
                    // Support both array items ($saleItems) and model items ($dc->items)
                    $isModel = is_object($item) && !($item instanceof \Illuminate\Support\Collection);
                    
                    if ($isModel) {
                        $productTitle = optional($item->product)->item_name ?? (optional($item->saleItem)->product_name ?? 'Item');
                        $saleItem = $item->saleItem;
                        $rate = $saleItem ? (float)$saleItem->price : (float)($item->price ?? 0);
                        $dispQty = (float)($item->delivered_qty ?? 0);
                        $lineTotal = $dispQty * $rate;
                        $dispUnit = optional(optional($item->product)->unit)->name ?? (optional(optional($saleItem)->unit)->name ?? 'Pcs');

                        $variant = [];
                        $colorData = $saleItem ? $saleItem->color : $item->color;
                        if (!empty($colorData)) {
                            $b64 = base64_decode($colorData, true);
                            if ($b64 !== false) $variant = json_decode($b64, true) ?: [];
                            if (empty($variant)) $variant = json_decode($colorData, true) ?: [];
                        }
                        $vName = $variant['variant_name'] ?? '';
                        $vSize = (!empty($variant['size']) && $variant['size'] !== '-') ? $variant['size'] : '';
                        $vColor = (!empty($variant['color']) && $variant['color'] !== '-') ? $variant['color'] : '';
                        $vExtra = [];
                        if ($vSize) $vExtra[] = $vSize;
                        if ($vColor) $vExtra[] = $vColor;
                        $vExtraStr = count($vExtra) > 0 ? ' (' . implode(', ', $vExtra) . ')' : '';

                        if ($vName && strtolower(trim($vName)) !== strtolower(trim($productTitle))) {
                            $productTitle .= ' — ' . $vName;
                        }
                        $productTitle .= $vExtraStr;
                        if (!empty($variant['unit'])) $dispUnit = ucfirst($variant['unit']);
                        $itemDesc = $item->description ?? '';
                    } else {
                        // Array format from _getSaleItems
                        $piecesPerBox = (int)($item['pieces_per_box'] ?? 1);
                        if ($piecesPerBox <= 0) $piecesPerBox = 1;
                        
                        $totalPieces = (float)($item['total_pieces'] ?? 0);
                        $rawSoldQty = (float)($item['qty_box'] ?? 0);
                        $convFactor = (float)($item['pieces_per_box'] ?? 1);
                        if ($convFactor <= 0) $convFactor = 1;
                        $vUnitRaw = trim($item['variant_unit'] ?? '');
                        $vUnitLower = strtolower($vUnitRaw);
                        $sizeMode = $item['size_mode'] ?? 'by_size';

                        $dispQty = $totalPieces;
                        $dispUnit = 'Pcs';

                        if (!empty($vUnitRaw) && !in_array($vUnitLower, ['standard', 'default', '-'])) {
                            if ($rawSoldQty > 0) {
                                $dispQty = $rawSoldQty;
                            } elseif ($convFactor > 0 && abs($convFactor - 1.0) > 0.00001) {
                                $dispQty = round($totalPieces / $convFactor, 4);
                            } else {
                                $dispQty = $totalPieces;
                            }
                            $dispUnit = ucfirst($vUnitRaw);
                        } elseif ($sizeMode === 'by_kg' || $sizeMode === 'by_gm') {
                            $kgWt = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                            if ($vUnitLower === 'gm' || $vUnitLower === 'g' || $sizeMode === 'by_gm') {
                                $dispQty = $kgWt * 1000;
                                $dispUnit = 'Gm';
                            } else {
                                $dispQty = $kgWt;
                                $dispUnit = 'Kg';
                            }
                        } elseif ($sizeMode === 'by_meter') {
                            $dispQty = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                            $dispUnit = 'Mtr';
                        } elseif ($sizeMode === 'by_feet') {
                            $dispQty = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                            $dispUnit = 'Ft';
                        } elseif ($sizeMode === 'by_pieces') {
                            $dispQty = $totalPieces;
                            $dispUnit = 'Pcs';
                        } elseif ($sizeMode === 'by_cartons' || $sizeMode === 'by_size') {
                            $boxes = floor($totalPieces / $piecesPerBox);
                            $loosePieces = fmod($totalPieces, $piecesPerBox);
                            if ($boxes > 0 && $loosePieces == 0) {
                                $dispQty = $boxes;
                                $dispUnit = $sizeMode == 'by_cartons' ? 'Ctn' : 'Box';
                            } elseif ($boxes == 0 && $loosePieces > 0) {
                                $dispQty = $loosePieces;
                                $dispUnit = 'Pcs';
                            } elseif ($boxes > 0 && $loosePieces > 0) {
                                $dispQty = "{$boxes}.{$loosePieces}";
                                $dispUnit = $sizeMode == 'by_cartons' ? 'Ctn' : 'Box';
                            } else {
                                $dispQty = $totalPieces;
                                $dispUnit = 'Pcs';
                            }
                        }

                        $vName = $item['variant_name'] ?? '';
                        $vSize = (!empty($item['size_val']) && $item['size_val'] !== '-') ? $item['size_val'] : '';
                        $vColor = (!empty($item['color_val']) && $item['color_val'] !== '-') ? $item['color_val'] : '';
                        $vExtra = [];
                        if ($vSize) $vExtra[] = $vSize;
                        if ($vColor) $vExtra[] = $vColor;
                        $vExtraStr = count($vExtra) > 0 ? ' (' . implode(', ', $vExtra) . ')' : '';

                        $productTitle = $item['item_name'];
                        if ($vName && strtolower(trim($vName)) !== strtolower(trim($productTitle))) {
                            $productTitle .= ' — ' . $vName;
                        }
                        $productTitle .= $vExtraStr;
                        $rate = (float)($item['price'] ?? 0);
                        $lineTotal = (float)($item['total'] ?? 0);
                        $itemDesc = $item['description'] ?? '';
                    }
                @endphp

                <tr>
                    <td style="text-align: center;">{{ $sNo }}</td>
                    <td class="desc-col" style="font-weight: 600;">
                        {{ $productTitle }}
                        @if(!empty($itemDesc))
                            <div style="font-size: 8.5px; color: #475569; font-weight: normal;">
                                {{ $itemDesc }}
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ ($dispQty == (int)$dispQty) ? (int)$dispQty : number_format($dispQty, 2) }}
                    </td>
                    <td class="rate-col" style="text-align: right;">{{ number_format($rate, 2) }}</td>
                    <td style="text-align: center;">{{ $dispUnit }}</td>
                    <td class="rate-col" style="text-align: right; font-weight: bold;">{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach

            {{-- Totals block only on the final part --}}
            @if ($isLastPart)
                @if(optional($sale)->total_extradiscount > 0)
                <tr>
                    <td colspan="5" class="totals-label text-danger">DISCOUNT:</td>
                    <td class="rate-col totals-val text-danger">
                        -{{ number_format($sale->total_extradiscount, 2) }}
                    </td>
                </tr>
                @endif
                @if(optional($sale)->shipping_charges > 0)
                <tr>
                    <td colspan="5" class="totals-label" style="color: #0284c7;">SHIPPING CHARGES:</td>
                    <td class="rate-col totals-val" style="color: #0284c7;">
                        +{{ number_format($sale->shipping_charges, 2) }}
                    </td>
                </tr>
                @endif
                @if(optional($sale)->cutting_charges > 0)
                <tr>
                    <td colspan="5" class="totals-label" style="color: #d97706;">CUTTING CHARGES:</td>
                    <td class="rate-col totals-val" style="color: #d97706;">
                        +{{ number_format($sale->cutting_charges, 2) }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td colspan="5" class="totals-label">TOTAL:</td>
                    <td class="rate-col totals-val" style="background-color: rgba(224, 242, 254, 0.6);">
                        {{ number_format($finalTotal, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="totals-label">PAID:</td>
                    <td class="rate-col totals-val" style="color: #16a34a;">
                        {{ number_format($paidAmount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="totals-label">DUE:</td>
                    <td class="rate-col totals-val" style="color: #dc2626;">
                        {{ number_format($dueAmount, 2) }}
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="6" style="text-align: center; font-weight: bold; font-size: 9px; color: #1e40af; background: #eff6ff; padding: 2.5px;">
                        --- CONTINUED ON NEXT HALF PAGE ---
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Footer -->
<div class="pad-footer">
    <div class="footer-note">
        Goods once sold can not be exchange<br>
        or taken back without receipt.
    </div>
    <div class="footer-sign">
        <img src="{{ asset('assets/images/stamp.png') }}" class="footer-stamp" alt="Stamp">
        For {{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}
    </div>
</div>
