<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$sale = App\Models\Sale::with('items')->find(8);
if ($sale) {
    echo "Reference: " . $sale->reference . "\n";
    echo "Sale Type: " . $sale->sale_type . "\n";
    echo "Items count: " . $sale->items->count() . "\n";
    foreach($sale->items as $i) {
        echo "Item: " . $i->product_id . " Qty: " . $i->qty . " TPieces: " . $i->total_pieces . " Color: " . $i->color . "\n";
    }
} else {
    echo "Sale not found\n";
}
