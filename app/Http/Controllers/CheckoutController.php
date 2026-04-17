<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Pagamento;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endereco_id' => 'required|exists:enderecos,id',
            'metodo_pagamento' => 'required|in:cartao_credito,cartao_debito,pix,dinheiro',
            'troco_para' => 'nullable|numeric|min:0'
        ]);

        $user = Auth::user();
        $itensCarrinho = $user->itensCarrinho()->with('produto')->get();

        if ($itensCarrinho->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Carrinho vazio'], 400);
        }

        try {
            DB::beginTransaction();

            $subtotal = $itensCarrinho->sum(function ($item) {
                return $item->produto->preco * $item->quantidade;
            });
            
            $taxa_entrega = 15.00;
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

            Pagamento::create([
                'pedido_id'  => $pedido->id,
                'metodo'     => $request->metodo_pagamento,
                'troco_para' => $request->metodo_pagamento === 'dinheiro' ? $request->troco_para : null,
            ]);

            $user->itensCarrinho()->delete();

            DB::commit();

            session()->flash('success_checkout', $pedido->id);
            session()->flash('tab', 'pedidos');

            return response()->json(['success' => true, 'redirect' => route('perfil')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erro ao finalizar pedido: ' . $e->getMessage()], 500);
        }
    }
}
