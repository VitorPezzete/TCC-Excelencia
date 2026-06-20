<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Pagamento;
use App\Models\Endereco;
use App\Models\ZonaEntrega;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endereco_id'      => 'required|exists:enderecos,id',
            'metodo_pagamento' => 'required|in:cartao_online,cartao_credito,cartao_debito,pix,dinheiro',
            'troco_para'       => 'nullable|numeric|min:0',
        ]);

        $is_open = true;
        try {
            $is_open = (\App\Models\Setting::where('key', 'is_store_open')->value('value') ?? '1') === '1';
        } catch (\Exception $e) {}

        if (!$is_open) {
            return response()->json(['success' => false, 'message' => 'A loja está fechada no momento. Não é possível fazer novos pedidos.'], 400);
        }

        $user          = Auth::user();
        $itensCarrinho = $user->itensCarrinho()->with('produto')->get();

        if ($itensCarrinho->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Carrinho vazio'], 400);
        }

        try {
            DB::beginTransaction();

            $subtotal = $itensCarrinho->sum(function ($item) {
                return $item->produto->preco * $item->quantidade;
            });

            // Taxa dinâmica por zona de entrega
            $endereco = Endereco::findOrFail($request->endereco_id);
            $bairro   = trim($endereco->bairro ?? '');

            // Auto-enriquecimento via ViaCEP para endereços antigos sem bairro
            if (empty($bairro) && !empty($endereco->cep)) {
                $cepLimpo = preg_replace('/\D/', '', $endereco->cep);
                try {
                    $resposta = \Illuminate\Support\Facades\Http::timeout(5)
                        ->get("https://viacep.com.br/ws/{$cepLimpo}/json/");
                    if ($resposta->ok()) {
                        $dados = $resposta->json();
                        if (!empty($dados['bairro'])) {
                            $bairro = $dados['bairro'];
                            $endereco->update(['bairro' => $bairro]);
                        }
                    }
                } catch (\Exception $e) { /* ignora erro de rede */ }
            }

            $zona = !empty($bairro) ? ZonaEntrega::encontrarPorBairro($bairro) : null;

            if (!$zona) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Bairro "' . ($bairro ?: 'não informado') . '" fora da área de entrega. Verifique seu endereço ou entre em contato conosco.',
                ], 422);
            }

            // Frete grátis se o subtotal atingir o mínimo da zona
            $taxa_entrega = ($zona->frete_gratis_acima && $subtotal >= $zona->frete_gratis_acima)
                ? 0
                : (float) $zona->taxa;

            $total = $subtotal + $taxa_entrega;

            $pedido = Pedido::create([
                'user_id'      => $user->id,
                'endereco_id'  => $request->endereco_id,
                'status'       => 'pendente',
                'subtotal'     => $subtotal,
                'taxa_entrega' => $taxa_entrega,
                'total'        => $total,
            ]);

            foreach ($itensCarrinho as $item) {
                ItemPedido::create([
                    'pedido_id'      => $pedido->id,
                    'produto_id'     => $item->produto_id,
                    'quantidade'     => $item->quantidade,
                    'preco_unitario' => $item->produto->preco,
                    'preco_total'    => $item->produto->preco * $item->quantidade,
                    'observacoes'    => $item->observacoes,
                ]);
            }

            // Status inicial do pagamento: pendente para PIX e Cartão online, null para presenciais
            $statusPagamento = in_array($request->metodo_pagamento, ['pix', 'cartao_online']) ? 'pendente' : null;

            Pagamento::create([
                'pedido_id'  => $pedido->id,
                'metodo'     => $request->metodo_pagamento,
                'troco_para' => $request->metodo_pagamento === 'dinheiro' ? $request->troco_para : null,
                'status'     => $statusPagamento,
            ]);

            $user->itensCarrinho()->delete();

            DB::commit();

            // PIX → não redireciona ainda; frontend vai buscar o QR Code
            if ($request->metodo_pagamento === 'pix') {
                return response()->json([
                    'success'     => true,
                    'requer_pix'  => true,
                    'pedido_id'   => $pedido->id,
                ]);
            }

            if ($request->metodo_pagamento === 'cartao_online') {
                return response()->json([
                    'success'  => true,
                    'redirect' => route('pagamento.cartao', ['pedido' => $pedido->id]),
                ]);
            }

            // Outros métodos → redireciona direto para o perfil
            session()->flash('success_checkout', $pedido->id);
            session()->flash('tab', 'pedidos');

            return response()->json([
                'success'  => true,
                'redirect' => route('perfil') . '?tab=pedidos',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar pedido: ' . $e->getMessage(),
            ], 500);
        }
    }
}

