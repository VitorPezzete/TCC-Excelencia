<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $salgados = Category::where('name', 'Salgados')->first();
    $doces    = Category::where('name', 'Doces')->first();
    $cafes    = Category::where('name', 'Cafés')->first();
    $combos   = Category::where('name', 'Combos')->first();

    Product::create(['category_id' => $salgados->id, 'name' => 'Esfiha de Carne', 'description' => 'Massa de batata e recheio de carne temperada.', 'price' => 4.00, 'is_featured' => true, 'is_active' => true]);
    Product::create(['category_id' => $salgados->id, 'name' => 'Esfiha de Frango', 'description' => 'Massa de batata e recheio de frango cremoso.', 'price' => 4.00, 'is_featured' => false, 'is_active' => true]);

    Product::create(['category_id' => $doces->id, 'name' => 'Biscoitinhos Amanteigados', 'description' => 'Delicioso biscoito amanteigados derrete na boca.', 'price' => 4.00, 'is_featured' => false, 'is_active' => true]);
    Product::create(['category_id' => $doces->id, 'name' => 'Bota Fatia Fubá', 'description' => 'Fatia deliciosa de fubá', 'price' => 2.00, 'is_featured' => false, 'is_active' => true]);

    Product::create(['category_id' => $cafes->id, 'name' => 'Espresso', 'description' => 'Curto ou longo. Blend 100% arábica.', 'price' => 7.00, 'is_featured' => false, 'is_active' => true]);
    Product::create(['category_id' => $cafes->id, 'name' => 'Cappuccino Italiano', 'description' => 'Espresso, leite vaporizado e espuma cremosa.', 'price' => 12.00, 'is_featured' => false, 'is_active' => true]);

    Product::create(['category_id' => $combos->id, 'name' => 'Combo Clássico', 'description' => '1 Coxinha + 1 Espresso + 1 Brigadeiro.', 'price' => 22.00, 'is_featured' => false, 'is_active' => true]);
    Product::create(['category_id' => $combos->id, 'name' => 'Tarde Doce', 'description' => '1 Fatia de Bolo + 1 Cappuccino.', 'price' => 28.00, 'is_featured' => false, 'is_active' => true]);
}  
}
