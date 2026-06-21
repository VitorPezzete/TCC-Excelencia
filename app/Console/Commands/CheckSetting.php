<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSetting extends Command
{
    protected $signature = 'setting:check {key} {value?}';
    protected $description = 'Lê ou define um valor na tabela settings';

    public function handle()
    {
        $key   = $this->argument('key');
        $value = $this->argument('value');

        if ($value !== null) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
            $this->info("Definido: {$key} = {$value}");
        }

        $current = DB::table('settings')->where('key', $key)->value('value');
        $this->info("Valor atual de [{$key}]: " . ($current ?? 'NULL'));
    }
}
