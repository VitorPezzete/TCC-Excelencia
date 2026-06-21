<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'is_store_open'],
            ['value' => '1', 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
