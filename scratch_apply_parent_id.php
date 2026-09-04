<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('customers', 'parent_id')) {
    Schema::table('customers', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        $table->index('parent_id');
    });
    echo "parent_id column added successfully\n";
} else {
    echo "parent_id column already exists\n";
}
