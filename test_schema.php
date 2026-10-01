<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$cols = Illuminate\Support\Facades\Schema::getColumnListing('sale_items');
foreach($cols as $c) {
    echo $c . ' : ' . Illuminate\Support\Facades\Schema::getColumnType('sale_items', $c) . "\n";
}
