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
        $settings = [
            [
                'key' => 'company_ntn',
                'value' => '',
                'type' => 'string',
                'group' => 'company',
                'label' => 'NTN Number',
                'description' => 'Company National Tax Number (NTN) to display on invoices, quotations, purchase and prints.',
            ],
            [
                'key' => 'company_email',
                'value' => 'vahlamillstore@hotmail.com',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Email',
                'description' => 'Official company email displayed on prints and reports.',
            ],
            [
                'key' => 'deals_in',
                'value' => "Fasteners, Bearing, Lifting Equipments, V-belt, PPE's, Hand & Cutting Etc",
                'type' => 'string',
                'group' => 'company',
                'label' => 'Deals In',
                'description' => 'Business categories / deals-in summary displayed in invoice and quotation header.',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'company_ntn')->delete();
    }
};
