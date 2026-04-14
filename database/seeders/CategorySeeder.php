<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create(['nome' => 'Salgados']);
        Categoria::create(['nome' => 'Doces']);
        Categoria::create(['nome' => 'Cafés']);
        Categoria::create(['nome' => 'Combos']);
        //
    }
}
