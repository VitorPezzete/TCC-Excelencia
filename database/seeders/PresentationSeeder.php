<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Avaliacao;
use App\Models\ItemPedido;
use App\Models\Produto;

class PresentationSeeder extends Seeder
{
    public function run()
    {
        // Limpar dados anteriores caso rode duas vezes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pedido::truncate();
        Avaliacao::truncate();
        ItemPedido::truncate();
        \App\Models\Pagamento::truncate();
        \App\Models\Endereco::truncate();
        User::where('email', 'like', 'cliente%@teste.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Criar alguns Clientes (Users)
        $users = [];
        for ($i = 0; $i < 15; $i++) {
            $users[] = User::create([
                'name' => 'Cliente Apresentação ' . ($i + 1),
                'email' => 'cliente' . ($i + 1) . '@teste.com',
                'password' => bcrypt('12345678'),
                'phone' => '119999999' . sprintf('%02d', $i),
                'is_admin' => false,
                'created_at' => Carbon::now()->subDays(rand(1, 30))
            ]);
        }

        // 2. Pegar produtos reais (criados no ProductSeeder)
        $produtos = Produto::all();
        if ($produtos->isEmpty()) {
            $this->command->info('Nenhum produto encontrado. Cadastre produtos antes de rodar o PresentationSeeder.');
            return;
        }

        // 3. Criar Pedidos Realistas para o Faturamento
        $status_possiveis = ['pendente', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado'];
        
        foreach ($users as $index => $user) {
            // Criar um Endereco para o usuário
            $endereco = \App\Models\Endereco::create([
                'user_id' => $user->id,
                'nome' => 'Casa',
                'cep' => '01001000',
                'numero' => '123',
                'padrao' => true,
            ]);

            // Cada usuário fará 1 ou 2 pedidos
            $qtd_pedidos = rand(1, 2);
            for ($p = 0; $p < $qtd_pedidos; $p++) {
                
                $status_sorteado = $status_possiveis[array_rand($status_possiveis)];
                
                // Forçar alguns como 'entregue' para o ticket médio e alguns cancelados para a métrica de cancelamento
                if ($index < 8) $status_sorteado = 'entregue';
                if ($index == 10) $status_sorteado = 'cancelado';
                
                $pedido = Pedido::create([
                    'user_id' => $user->id,
                    'status' => $status_sorteado,
                    'subtotal' => 0, // Será calculado
                    'total' => 0, // Será calculado
                    'endereco_id' => $endereco->id,
                    'created_at' => Carbon::now()->subDays(rand(0, 15)),
                ]);

                \App\Models\Pagamento::create([
                    'pedido_id' => $pedido->id,
                    'metodo' => 'pix',
                    'troco_para' => null,
                ]);

                $total = 0;
                // Adicionar 1 a 3 itens no pedido
                $qtd_itens = rand(1, 3);
                for ($i = 0; $i < $qtd_itens; $i++) {
                    $produto = $produtos->random();
                    $quantidade = rand(1, 4);
                    
                    ItemPedido::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $produto->id,
                        'quantidade' => $quantidade,
                        'preco_unitario' => $produto->preco,
                        'preco_total' => $produto->preco * $quantidade
                    ]);
                    
                    $total += ($produto->preco * $quantidade);
                }

                // Atualizar o total do pedido
                $pedido->update([
                    'subtotal' => $total,
                    'total' => $total
                ]);
                
                // Atualizar faturamento apenas se estiver entregue
                if ($pedido->status === 'entregue' && rand(0,1)) {
                    // 4. Criar Avaliação para Pedidos Entregues
                    Avaliacao::create([
                        'user_id' => $user->id,
                        'pedido_id' => $pedido->id,
                        'nota' => rand(4, 5), // Notas boas para apresentação
                        'comentario' => 'Muito bom! Recomendo.',
                        'created_at' => Carbon::now()->subDays(rand(0, 5))
                    ]);
                }
            }
        }
    }
}
