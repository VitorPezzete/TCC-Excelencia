<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $salgados = Categoria::where('nome', 'Salgados')->first();
    $doces    = Categoria::where('nome', 'Doces')->first();
    $cafes    = Categoria::where('nome', 'Cafés')->first();
    $combos   = Categoria::where('nome', 'Combos')->first();

    Produto::create(['categoria_id' => $salgados->id, 'nome' => 'Esfiha de Carne', 'descricao' => 'Massa de batata e recheio de carne temperada.', 'preco' => 4.00, 'destaque' => true, 'ativo' => true]);
    Produto::create(['categoria_id' => $salgados->id, 'nome' => 'Esfiha de Frango', 'descricao' => 'Massa de batata e recheio de frango cremoso.', 'preco' => 4.00, 'destaque' => false, 'ativo' => true]);

    Produto::create(['categoria_id' => $doces->id, 'nome' => 'Biscoitinhos Amanteigados', 'descricao' => 'Delicioso biscoito amanteigados derrete na boca.', 'preco' => 4.00, 'destaque' => false, 'ativo' => true]);
    Produto::create(['categoria_id' => $doces->id, 'nome' => 'Bota Fatia Fubá', 'descricao' => 'Fatia deliciosa de fubá', 'preco' => 2.00, 'destaque' => false, 'ativo' => true]);

    Produto::create(['categoria_id' => $cafes->id, 'nome' => 'Espresso', 'descricao' => 'Curto ou longo. Blend 100% arábica.', 'preco' => 7.00, 'destaque' => false, 'ativo' => true]);
    Produto::create(['categoria_id' => $cafes->id, 'nome' => 'Cappuccino Italiano', 'descricao' => 'Espresso, leite vaporizado e espuma cremosa.', 'preco' => 12.00, 'destaque' => false, 'ativo' => true]);

    Produto::create(['categoria_id' => $combos->id, 'nome' => 'Combo Clássico', 'descricao' => '1 Coxinha + 1 Espresso + 1 Brigadeiro.', 'preco' => 22.00, 'destaque' => false, 'ativo' => true]);
    Produto::create(['categoria_id' => $combos->id, 'nome' => 'Tarde Doce', 'descricao' => '1 Fatia de Bolo + 1 Cappuccino.', 'preco' => 28.00, 'destaque' => false, 'ativo' => true]);
}  
}
