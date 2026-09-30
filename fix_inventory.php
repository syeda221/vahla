<?php
$file = "app/Http/Controllers/ReportingController.php";
$lines = file($file);
$newLines = [];
$inLoop = false;
$replaced = false;

for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], "public function fetchInventoryDemand(Request \$request)") !== false) {
        $inLoop = false;
    }
    
    // Look for the foreach inside fetchInventoryDemand
    if (!$replaced && strpos($lines[$i], "foreach (\$products as \$product) {") !== false && $i > 200 && $i < 300) {
        $inLoop = true;
        $replaced = true;
        
        $replacement = <<<EOT
        foreach (\$products as \$product) {
            \$variants = \$this->getVariantStockBalances(\$product, \$warehouseId);
            
            // Purchase Price / Unit Cost
            \$purchPrice = 0;
            if (\$product->size_mode === "by_size" || \$product->size_mode === "by_m2") {
                \$m2PerPiece = (float) (\$product->pieces_per_m2 ?? 0);
                \$purchPerM2 = (float) (\$product->purchase_price_per_m2 ?? 0);
                \$purchPrice = \$m2PerPiece * \$purchPerM2;
            } else {
                \$purchPrice = (float) (\$product->purchase_price_per_piece ?? 0);
            }
            
            \$ppb = \$product->pieces_per_box > 0 ? \$product->pieces_per_box : 1;
            \$minQty = (float) (\$product->alert_quantity ?? ((\$product->alert_carton_quantity ?? 0) * \$ppb));
            
            foreach (\$variants as \$variant) {
                \$stock = \$variant["stock"];
                \$reqQty = max(0, \$minQty - \$stock);
                
                if (\$demandOnly && \$reqQty <= 0) {
                    continue;
                }
                
                \$costAmount = round(\$reqQty * \$purchPrice, 2);
                
                \$rows[] = [
                    "id"          => \$product->id,
                    "code"        => \$variant["code"] ?: "0",
                    "item_name"   => \$variant["name"],
                    "category"    => \$product->category_relation->name ?? "-",
                    "company"     => \$product->brand->name ?? "-",
                    "unit"        => \$product->unit->name ?? "Pcs",
                    "stock"       => round(\$stock, 2),
                    "p_price"     => round(\$purchPrice, 2),
                    "min_qty"     => round(\$minQty, 2),
                    "req_qty"     => round(\$reqQty, 2),
                    "cost_amount" => \$costAmount,
                    "status"      => \$stock <= 0 ? "out_of_stock" : (\$stock < \$minQty ? "low_stock" : "adequate"),
                ];
                
                \$totalItemsCount++;
                if (\$reqQty > 0) {
                    \$totalDemandItems++;
                }
                if (\$stock < 0) {
                    \$totalStockDeficit += abs(\$stock);
                }
                \$totalDemandQty  += \$reqQty;
                \$totalCostAmount += \$costAmount;
            }
        }
EOT;
        $newLines[] = $replacement . "\n";
        continue;
    }
    
    if ($inLoop) {
        if (strpos($lines[$i], "return response()->json([") !== false) {
            $inLoop = false;
            $newLines[] = $lines[$i];
        }
    } else {
        $newLines[] = $lines[$i];
    }
}
file_put_contents($file, implode("", $newLines));
echo "Fixed!";

