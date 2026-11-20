<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perm1 = Permission::firstOrCreate(['name' => 'sales.allow_low_stock', 'guard_name' => 'web']);
        $perm2 = Permission::firstOrCreate(['name' => 'sales.low_stock', 'guard_name' => 'web']);

        $superAdmin = Role::whereIn('name', ['Super Admin', 'superAdmin'])->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([$perm1, $perm2]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', ['sales.allow_low_stock', 'sales.low_stock'])->delete();
    }
};
