<?php

use Illuminate\Database\Migrations\Migration;
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

        $permissions = [
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',
            'warehouse.stock.view',
            'warehouse.stock.create',
            'warehouse.stock.edit',
            'warehouse.stock.delete',
            'opening.stock.view',
            'opening.stock.create',
            'opening.stock.edit',
            'opening.stock.delete',
            'stock.transfer.view',
            'stock.transfer.create',
            'stock.transfer.edit',
            'stock.transfer.delete',
            'stock.adjust.view',
            'stock.adjust.create',
            'stock.adjust.edit',
            'stock.adjust.delete',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Grant permissions to Super Admin and Admin roles
        $roles = Role::whereIn('name', ['Super Admin', 'superAdmin', 'Admin', 'admin'])->get();
        foreach ($roles as $role) {
            $role->syncPermissions(Permission::all());
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'opening.stock.view',
            'opening.stock.create',
            'opening.stock.edit',
            'opening.stock.delete',
        ])->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
