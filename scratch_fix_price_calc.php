<?php
$file = 'c:/xampp/htdocs/atif_traderss/resources/views/admin_panel/purchase/edit.blade.php';
$content = file_get_contents($file);

$oldCalc = "                // --- TOTAL CALCULATION ---
                let grossTotal = 0;

                if (sizeMode == 'by_size') {
                    // Price is per M2. Total M2 = totalPieces * pieces_per_m2 (m2/piece)
                    grossTotal = (totalPieces * pieces_per_m2) * price;
                } else if (['by_cartons', 'by_bandal'].includes(sizeMode)) {
                    // Price is per Carton.
                    // If ppb > 0
                    if (ppb > 0) {
                        grossTotal = (totalPieces / ppb) * price;
                    } else {
                        grossTotal = totalPieces * price; // fallback
                    }
                } else {
                    // Price is per Piece
                    grossTotal = totalPieces * price;
                }";

$newCalc = "                // --- TOTAL CALCULATION ---
                let grossTotal = 0;

                if (sizeMode == 'by_size') {
                    // Price is per M2. Total M2 = totalPieces * pieces_per_m2 (m2/piece)
                    grossTotal = (totalPieces * pieces_per_m2) * price;
                } else {
                    // price is always treated as per-piece for purchase entry
                    grossTotal = totalPieces * price;
                }";

$oldSelect2 = "                    let price = 0;
                    let unitLabel = '';

                    if (sizeMode === 'by_size') {
                        price = pM2;
                        unitLabel = '(m²)';
                    } else if (['by_cartons', 'by_bandal'].includes(sizeMode)) {
                        price = pPiece * ppb; // Carton Price
                        unitLabel = '(carton)';
                    } else {
                        price = pPiece;
                        unitLabel = '(piece)';
                    }

                    \$row.find('.price').val(price);
                    // Add/Update label (remove old if any)
                    \$row.find('.price-unit-label').remove();
                    \$row.find('.price').after(
                        '<small class=\"text-muted price-unit-label\" style=\"font-size:0.7rem;\">' +
                        unitLabel + '</small>');";

$newSelect2 = "                    let price = 0;
                    let unitLabel = '';

                    if (sizeMode === 'by_size') {
                        price = pM2;
                        unitLabel = '(m²)';
                    } else {
                        price = pPiece;
                        unitLabel = '(pieces)';
                    }

                    \$row.find('.price').val(price);
                    // Add/Update label (remove old if any)
                    \$row.find('.price-unit-label').remove();
                    \$row.find('.price').after(
                        '<small class=\"text-muted price-unit-label\" style=\"font-size:0.7rem;\">' +
                        unitLabel + '</small>');";

$content = str_replace($oldCalc, $newCalc, $content, $count1);
$content = str_replace($oldSelect2, $newSelect2, $content, $count2);

file_put_contents($file, $content);
echo "Replaced Calc: $count1\nReplaced Select2: $count2\n";
