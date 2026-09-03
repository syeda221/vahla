<?php
/**
 * Script: check_carton_products.php
 * Description: Preview & Fix Carton Product Prices with Crystal-Clear Comparison
 */

$baseDir = __DIR__;
if (!file_exists($baseDir . '/vendor/autoload.php')) {
    $baseDir = dirname(__DIR__);
}

require_once $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$isCli = (php_sapi_name() === 'cli');
$isUpdateMode = false;

if ($isCli) {
    global $argv;
    if (isset($argv) && in_array('--update', $argv)) {
        $isUpdateMode = true;
    }
} else {
    if (isset($_GET['update']) && $_GET['update'] === 'yes') {
        $isUpdateMode = true;
    }
}

// Check available columns
$hasPiecesPerBox = Schema::hasColumn('products', 'pieces_per_box');
$hasSizeMode = Schema::hasColumn('products', 'size_mode');
$hasPurchasePricePerPiece = Schema::hasColumn('products', 'purchase_price_per_piece');
$hasSalePricePerPiece = Schema::hasColumn('products', 'sale_price_per_piece');
$hasPurchasePricePerBox = Schema::hasColumn('products', 'purchase_price_per_box');
$hasSalePricePerBox = Schema::hasColumn('products', 'sale_price_per_box');

$query = DB::table('products');

if ($hasPiecesPerBox && $hasSizeMode) {
    $query->where(function($q) {
        $q->where('pieces_per_box', '>', 1)
          ->orWhere('size_mode', 'by_cartons');
    });
} elseif ($hasPiecesPerBox) {
    $query->where('pieces_per_box', '>', 1);
}

$products = $query->orderBy('id', 'asc')->get();
$totalCount = $products->count();

if ($isUpdateMode) {
    DB::beginTransaction();
}

$rows = [];
$updatedCount = 0;

foreach ($products as $p) {
    $id = $p->id;
    $code = $p->item_code ?? '-';
    $name = $p->item_name ?? '-';
    $ppb = isset($p->pieces_per_box) && (float)$p->pieces_per_box > 0 ? (float)$p->pieces_per_box : 1;
    $sizeMode = $p->size_mode ?? 'by_cartons';

    // Current DB values
    $currPurchaseRaw = 0;
    if ($hasPurchasePricePerPiece && isset($p->purchase_price_per_piece)) {
        $currPurchaseRaw = (float)$p->purchase_price_per_piece;
    } elseif (isset($p->price)) {
        $currPurchaseRaw = (float)$p->price;
    }

    $currSaleRaw = 0;
    if ($hasSalePricePerPiece && isset($p->sale_price_per_piece)) {
        $currSaleRaw = (float)$p->sale_price_per_piece;
    }

    // CURRENT STATE (Before Query):
    // If DB stored 100 as per_piece, then Current Carton Invoice Price = 100 * ppb (e.g. 1000)
    $currInvoiceCartonSale = $currSaleRaw * $ppb;
    $currInvoiceCartonPurch = $currPurchaseRaw * $ppb;
    $currPieceSale = $currSaleRaw;
    $currPiecePurch = $currPurchaseRaw;

    // AFTER QUERY (Target / Corrected State):
    // Entered price was Carton Price (e.g. 100) -> so Target Carton Price = 100, Target Piece Price = 100 / ppb
    $newPieceSale = $ppb > 1 ? round($currSaleRaw / $ppb, 4) : $currSaleRaw;
    $newPiecePurch = $ppb > 1 ? round($currPurchaseRaw / $ppb, 4) : $currPurchaseRaw;
    $newCartonSale = $currSaleRaw;
    $newCartonPurch = $currPurchaseRaw;

    if ($isUpdateMode && $ppb > 1) {
        $updateData = [];
        if ($hasPurchasePricePerPiece) {
            $updateData['purchase_price_per_piece'] = $newPiecePurch;
        }
        if ($hasSalePricePerPiece) {
            $updateData['sale_price_per_piece'] = $newPieceSale;
        }
        if ($hasPurchasePricePerBox) {
            $updateData['purchase_price_per_box'] = $newCartonPurch;
        }
        if ($hasSalePricePerBox) {
            $updateData['sale_price_per_box'] = $newCartonSale;
        }

        // Update variants JSON
        if (!empty($p->color)) {
            $variants = is_string($p->color) ? json_decode($p->color, true) : $p->color;
            if (is_array($variants)) {
                $changed = false;
                foreach ($variants as &$v) {
                    $vPpb = isset($v['conv_factor']) && (float)$v['conv_factor'] > 0 ? (float)$v['conv_factor'] : (isset($v['ppb']) && (float)$v['ppb'] > 0 ? (float)$v['ppb'] : $ppb);
                    
                    if (isset($v['purch_price']) && (float)$v['purch_price'] > 0 && $vPpb > 1) {
                        $v['purch_price'] = (string) round((float)$v['purch_price'] / $vPpb, 4);
                        $changed = true;
                    }
                    if (isset($v['cost']) && (float)$v['cost'] > 0 && $vPpb > 1) {
                        $v['cost'] = (string) round((float)$v['cost'] / $vPpb, 4);
                        $changed = true;
                    }
                    if (isset($v['sale_price']) && (float)$v['sale_price'] > 0 && $vPpb > 1) {
                        $v['sale_price'] = (string) round((float)$v['sale_price'] / $vPpb, 4);
                        $changed = true;
                    }
                    if (isset($v['price']) && (float)$v['price'] > 0 && $vPpb > 1) {
                        $v['price'] = (string) round((float)$v['price'] / $vPpb, 4);
                        $changed = true;
                    }
                    if (isset($v['wholesale_price']) && (float)$v['wholesale_price'] > 0 && $vPpb > 1) {
                        $v['wholesale_price'] = (string) round((float)$v['wholesale_price'] / $vPpb, 4);
                        $changed = true;
                    }
                }
                if ($changed) {
                    $updateData['color'] = json_encode($variants);
                }
            }
        }

        if (!empty($updateData)) {
            DB::table('products')->where('id', $id)->update($updateData);
            $updatedCount++;
        }
    }

    $rows[] = [
        'id' => $id,
        'code' => $code,
        'name' => $name,
        'ppb' => $ppb,
        // Before
        'curr_carton_sale' => $currInvoiceCartonSale,
        'curr_carton_purch' => $currInvoiceCartonPurch,
        'curr_piece_sale' => $currPieceSale,
        'curr_piece_purch' => $currPiecePurch,
        // After
        'new_carton_sale' => $newCartonSale,
        'new_carton_purch' => $newCartonPurch,
        'new_piece_sale' => $newPieceSale,
        'new_piece_purch' => $newPiecePurch,
    ];
}

if ($isUpdateMode) {
    DB::commit();
}

if ($isCli) {
    echo "========================================================================================================================\n";
    echo "📦 CARTON PRODUCTS PRICE COMPARISON PREVIEW\n";
    echo "========================================================================================================================\n";
    echo "Total Products: $totalCount | Mode: " . ($isUpdateMode ? "🚀 UPDATE APPLIED" : "🔍 PREVIEW ONLY") . "\n";
    echo "------------------------------------------------------------------------------------------------------------------------\n";
    printf("%-4s | %-10s | %-20s | %-4s | %-12s | %-12s | %-12s | %-12s\n", 
        "ID", "Code", "Item Name", "PPB", "OLD Ctn Sale", "NEW Ctn Sale", "OLD Pc Sale", "NEW Pc Sale");
    echo str_repeat("-", 120) . "\n";

    foreach ($rows as $r) {
        $sName = mb_substr($r['name'], 0, 19);
        printf("%-4d | %-10s | %-20s | %-4s | Rs.%-9.2f | Rs.%-9.2f | Rs.%-9.2f | Rs.%-9.4f\n",
            $r['id'], $r['code'], $sName, $r['ppb'], $r['curr_carton_sale'], $r['new_carton_sale'], $r['curr_piece_sale'], $r['new_piece_sale']);
    }
    echo "========================================================================================================================\n";
    if ($isUpdateMode) {
        echo "✅ SUCCESS: $updatedCount Products updated in database!\n";
    } else {
        echo "ℹ️ Run with --update to apply: php check_carton_products.php --update\n";
    }
} else {
    // HTML PREVIEW
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carton Price Checker & Preview</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 25px; color: #1e293b; }
            .card-box { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 24px; border: 1px solid #e2e8f0; }
            .badge-custom { font-size: 13px; font-weight: 600; padding: 6px 12px; border-radius: 6px; }
            .table-container { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; }
            table { margin-bottom: 0 !important; font-size: 13.5px; }
            th { background: #0f172a !important; color: #fff !important; font-weight: 600; text-align: center; vertical-align: middle; padding: 10px 8px !important; }
            th.sub-old { background: #dc2626 !important; }
            th.sub-new { background: #16a34a !important; }
            td { vertical-align: middle; padding: 8px 10px !important; }
            .val-old { color: #dc2626; font-weight: 600; }
            .val-new { color: #16a34a; font-weight: 700; }
            .example-banner { background: #eff6ff; border-left: 5px solid #2563eb; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
            .highlight-cell { background: #f0fdf4 !important; }
            .danger-cell { background: #fef2f2 !important; }
        </style>
    </head>
    <body>
        <div class="container-fluid" style="max-width: 1400px;">
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h3 class="fw-bold mb-1"><i class="fas fa-boxes text-primary me-2"></i>Carton Products Price Comparison & Live Fix</h3>
                        <p class="text-muted mb-0">Yeh screen aapko dikha rahi hai ke <strong>Abhi kya rates chal rahe hain (Ghalat)</strong> aur <strong>Query chalane ke baad kya rates honge (Sahi)</strong>.</p>
                    </div>
                    <div>
                        <span class="badge badge-custom bg-secondary">Total Products: ' . $totalCount . '</span>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Explanatory Banner -->
                <div class="example-banner">
                    <h6 class="fw-bold text-primary mb-2"><i class="fas fa-info-circle me-1"></i> Calculation Samajhne Ka Asaan Tareeqa:</h6>
                    <div class="row text-sm" style="font-size: 13.5px;">
                        <div class="col-md-6">
                            <strong class="text-danger"><i class="fas fa-times-circle"></i> ABHI (CURRENT SYSTEM - MULTIPLY ISSUE):</strong><br>
                            Agar aapne product banate waqt Price <strong>100</strong> aur Pack <strong>10</strong> likhi thi, to Sale invoice par 1 Carton ka bill <strong class="text-danger">Rs. 1,000 (10 Guna Zyada)</strong> calculate ho raha hai.
                        </div>
                        <div class="col-md-6">
                            <strong class="text-success"><i class="fas fa-check-circle"></i> QUERY KE BAAD (CORRECT FIX):</strong><br>
                            Query chalne ke baad 1 Carton ka bill <strong class="text-success">Rs. 100 (Exact Carton Price)</strong> hoga aur 1 Piece ka rate <strong class="text-success">Rs. 10</strong> hoga!
                        </div>
                    </div>
                </div>';

                if (!$isUpdateMode) {
                    echo '<div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <div>
                            <i class="fas fa-shield-alt fs-4 text-warning me-2"></i>
                            <strong>PREVIEW MODE (Database mein abhi koi tabdeeli nahi hui):</strong> Niche table mein har product ka rate tasalli se check kar lein.
                        </div>
                        <div>
                            <a href="?update=yes" class="btn btn-success fw-bold px-4 py-2" onclick="return confirm(\'Kya aapne table mein rates verify kar liye hain? Kya aap waqai database update karna chahte hain?\')">
                                <i class="fas fa-check-double me-1"></i> Apply & Fix All Carton Rates Now
                            </a>
                        </div>
                    </div>';
                } else {
                    echo '<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                        <i class="fas fa-check-circle fs-3 text-success"></i>
                        <div>
                            <h5 class="fw-bold mb-0">SUCCESSFULLY APPLIED! ✅</h5>
                            <span>Database mein ' . $updatedCount . ' carton products ke rates successfully theek ho gaye hain. Ab Sale aur Purchase invoice par carton rate bilkul exact calculate hoga.</span>
                        </div>
                    </div>';
                }

                echo '<div class="table-container">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 50px;">ID</th>
                                <th rowspan="2" style="width: 100px;">Code</th>
                                <th rowspan="2">Item Name</th>
                                <th rowspan="2" style="width: 70px;">Pack (PPB)</th>
                                <th colspan="2" class="sub-old"><i class="fas fa-exclamation-triangle me-1"></i> ABHI (CURRENT GHALAT CALCULATE HOTA HAI)</th>
                                <th colspan="2" class="sub-new"><i class="fas fa-check me-1"></i> QUERY KE BAAD (SAHI RATE HOGA)</th>
                                <th rowspan="2" style="width: 140px;" class="sub-new">Per Piece Rate</th>
                            </tr>
                            <tr>
                                <th class="sub-old" style="font-size:12px;">Carton Sale Bill</th>
                                <th class="sub-old" style="font-size:12px;">Carton Purch Bill</th>
                                <th class="sub-new" style="font-size:12px;">Carton Sale Bill</th>
                                <th class="sub-new" style="font-size:12px;">Carton Purch Bill</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($rows as $r) {
                    echo '<tr>
                        <td class="text-center fw-bold text-muted">' . $r['id'] . '</td>
                        <td class="text-center"><code>' . htmlspecialchars($r['code']) . '</code></td>
                        <td class="fw-bold">' . htmlspecialchars($r['name']) . '</td>
                        <td class="text-center"><span class="badge bg-dark">' . $r['ppb'] . ' Pcs</span></td>
                        
                        <!-- CURRENT (BEFORE) -->
                        <td class="text-end danger-cell val-old" title="Current 1 Carton Sale">Rs. ' . number_format($r['curr_carton_sale'], 2) . '</td>
                        <td class="text-end danger-cell val-old" title="Current 1 Carton Purchase">Rs. ' . number_format($r['curr_carton_purch'], 2) . '</td>
                        
                        <!-- AFTER (CORRECTED) -->
                        <td class="text-end highlight-cell val-new" title="New 1 Carton Sale">Rs. ' . number_format($r['new_carton_sale'], 2) . '</td>
                        <td class="text-end highlight-cell val-new" title="New 1 Carton Purchase">Rs. ' . number_format($r['new_carton_purch'], 2) . '</td>
                        
                        <!-- PER PIECE -->
                        <td class="text-end highlight-cell" style="font-size: 12px;">
                            <div>Sale: <strong class="text-success">Rs. ' . number_format($r['new_piece_sale'], 2) . '</strong></div>
                            <div>Purch: <strong class="text-primary">Rs. ' . number_format($r['new_piece_purch'], 2) . '</strong></div>
                        </td>
                    </tr>';
                }

                echo '</tbody>
                    </table>
                </div>

                <div class="mt-4 text-center text-muted" style="font-size: 13px;">
                    <i class="fas fa-lock me-1"></i> Yeh script safe hai aur DB transaction use karti hai. Database update hone ke baad aap is file ko delete kar sakte hain.
                </div>
            </div>
        </div>
    </body>
    </html>';
}
