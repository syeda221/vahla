<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $series = [
            ['prefix' => 'INV', 'padding' => 4, 'is_default' => 1],
            ['prefix' => 'TAX', 'padding' => 3, 'is_default' => 0],
            ['prefix' => 'CO', 'padding' => 3, 'is_default' => 0],
        ];

        foreach ($series as $s) {
            $exists = DB::table('invoice_series')->where('prefix', $s['prefix'])->exists();
            if (!$exists) {
                DB::table('invoice_series')->insert([
                    'prefix' => $s['prefix'],
                    'next_number' => 1,
                    'padding' => $s['padding'],
                    'is_default' => $s['is_default'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('invoice_series')->whereIn('prefix', ['TAX', 'CO'])->delete();
    }
};
