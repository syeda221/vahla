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
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $newPermissions = [
                'vouchers.receive_payment',
                'vouchers.make_payment',
                'cheques.view',
                'cheques.create',
                'cheques.edit',
                'cheques.delete',
            ];

            foreach ($newPermissions as $permName) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => 'web'
                ]);
            }

            // Sync to Super Admin, superAdmin, admin
            $roles = Role::whereIn('name', ['Super Admin', 'superAdmin', 'admin'])->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($newPermissions);
            }

            // Automatically grant to any custom roles that already have 'vouchers.create' or 'vouchers.view'
            $voucherRoles = Role::whereHas('permissions', function($q) {
                $q->whereIn('name', ['vouchers.create', 'vouchers.view']);
            })->get();

            foreach ($voucherRoles as $r) {
                $r->givePermissionTo(['vouchers.receive_payment', 'vouchers.make_payment', 'cheques.view']);
            }

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Throwable $e) {
            \Log::warning('Permission migration error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep permissions
    }
};
