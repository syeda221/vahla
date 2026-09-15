<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = [
            'key' => 'company_logo',
            'type' => 'image',
            'group' => 'company',
            'label' => 'Company Logo',
            'description' => 'Upload company logo to display on reports.',
        ];

        \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
            ['key' => $setting['key']],
            array_merge($setting, [
                'updated_at' => now(),
            ])
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not dropping them because this is a fix migration to ensure they exist.
    }
};
