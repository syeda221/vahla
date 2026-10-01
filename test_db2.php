<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$sale = App\Models\Sale::with('items')->find(8);
$c = app(App\Http\Controllers\SaleController::class);
$ref = new ReflectionMethod($c, '_getSaleItems');
$ref->setAccessible(true);
$items = $ref->invoke($c, $sale);
print_r($items->toArray());
