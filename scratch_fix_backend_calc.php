<?php
$file = 'c:/xampp/htdocs/atif_traderss/app/Http/Controllers/PurchaseController.php';
$content = file_get_contents($file);

$oldCalc = "                if (\$curSizeMode === 'by_size') {
                    // price is per m2. Gross = TotalPieces * m2_per_piece * price_per_m2
                    \$grossTotal = \$curPPM2 * \$qty * \$price;
                } elseif (\in_array($curSizeMode, ['by_cartons', 'by_bandal']) || \$curSizeMode === 'by_carton') {
                    // For cartons, price is per carton, so divide by pieces_per_box to get price per piece
                    \$ppb = isset(\$ppbs[\$i]) && \$ppbs[\$i] > 0 ? (float) \$ppbs[\$i] : 1;
                    \$grossTotal = \$qty * (\$price / \$ppb);
                } else {
                    // Standard
                    \$grossTotal = \$qty * \$price;
                }";

$newCalc = "                if (\$curSizeMode === 'by_size') {
                    // price is per m2. Gross = TotalPieces * m2_per_piece * price_per_m2
                    \$grossTotal = \$curPPM2 * \$qty * \$price;
                } else {
                    // price is always treated as per-piece for purchase entry
                    \$grossTotal = \$qty * \$price;
                }";

$content = str_replace($oldCalc, $newCalc, $content, $count);
file_put_contents($file, $content);

echo "Replaced calc: \$count\n";
