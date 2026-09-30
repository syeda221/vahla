<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$units = \DB::table("units")->get();
echo json_encode($units, JSON_PRETTY_PRINT);

