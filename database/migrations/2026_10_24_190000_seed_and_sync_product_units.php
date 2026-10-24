<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;

return new class extends Migration
{
    public function up(): void
    {
        $defaultUnits = [
            'Piece', 'Dozen', 'Meter', 'Kilogram',
            'Pcs', 'Kg', 'Mtr', 'Ft', 'Box', 'Ltr'
        ];

        foreach ($defaultUnits as $name) {
            Unit::firstOrCreate(['name' => $name]);
        }

        $units = DB::table('units')->pluck('id', 'name');
        $kgId  = $units['Kg'] ?? ($units['Kilogram'] ?? null);
        $mtrId = $units['Mtr'] ?? ($units['Meter'] ?? null);
        $ftId  = $units['Ft'] ?? null;
        $pcsId = $units['Pcs'] ?? ($units['Piece'] ?? null);
        $boxId = $units['Box'] ?? null;

        if ($kgId) {
            DB::table('products')->where('size_mode', 'by_kg')->whereNull('unit_id')->update(['unit_id' => $kgId]);
        }
        if ($mtrId) {
            DB::table('products')->where('size_mode', 'by_meter')->whereNull('unit_id')->update(['unit_id' => $mtrId]);
        }
        if ($ftId) {
            DB::table('products')->where('size_mode', 'by_feet')->whereNull('unit_id')->update(['unit_id' => $ftId]);
        }
        if ($pcsId) {
            DB::table('products')->where('size_mode', 'by_pieces')->whereNull('unit_id')->update(['unit_id' => $pcsId]);
        }
        if ($boxId) {
            DB::table('products')->where('size_mode', 'by_cartons')->whereNull('unit_id')->update(['unit_id' => $boxId]);
        }
        if ($pcsId) {
            DB::table('products')->whereNull('unit_id')->update(['unit_id' => $pcsId]);
        }
    }

    public function down(): void
    {
        // No down needed for data sync
    }
};
