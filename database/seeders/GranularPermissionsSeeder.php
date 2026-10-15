<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GranularPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modulesWithActions = [
            // Direct Sales
            'sales' => [
                'view',
                'create',
                'edit',
                'delete',
                'print',
            ],
            // Sales Orders
            'sales_orders' => [
                'view',
                'create',
                'edit',
                'delete',
                'generate_invoice',
                'print',
            ],
            // Quotations / Estimates
            'quotations' => [
                'view',
                'create',
                'edit',
                'delete',
                'convert_to_so',
                'convert_to_sale',
                'print',
            ],
            // Delivery Challans (Sales Orders & general)
            'delivery_challans' => [
                'view',
                'create',
                'edit',
                'delete',
                'generate_invoice',
                'print',
            ],
            // Direct Delivery Challans
            'direct_dc' => [
                'view',
                'create',
                'edit',
                'delete',
                'consolidate',
                'print',
            ],
            // Direct Purchases / Purchase Bills
            'purchases' => [
                'view',
                'create',
                'edit',
                'delete',
                'print',
            ],
            // Purchase Orders
            'purchase_orders' => [
                'view',
                'create',
                'edit',
                'delete',
                'print',
            ],
            // Goods Receiving Notes (GRN - from PO)
            'grn' => [
                'view',
                'create',
                'edit',
                'delete',
                'generate_invoice',
                'print',
            ],
            // Direct GRN
            'direct_grn' => [
                'view',
                'create',
                'edit',
                'delete',
                'consolidate',
                'print',
            ],
        ];

        $allCreated = [];
        foreach ($modulesWithActions as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";
                $perm = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
                $allCreated[] = $perm->name;
            }
        }

        // Grant newly created permissions to Super Admin and Admin roles
        $rolesToSync = Role::whereIn('name', ['Super Admin', 'superAdmin', 'admin'])->get();
        foreach ($rolesToSync as $role) {
            $role->givePermissionTo($allCreated);
        }

        // Reset cache again to apply immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
