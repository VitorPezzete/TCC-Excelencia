<?php

namespace App\Http\Controllers;

use App\Models\ItemCarrinho;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $itens = $user->itensCarrinho()->with('produto')->get();
        $addresses = $user->enderecos;
        
        $total = $itens->sum(function ($item) {
            return $item->produto->preco * $item->quantidade;
        });

        return view('carrinho', compact('itens', 'total', 'addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        
        $itemExistente = $user->itensCarrinho()
            ->where('produto_id', $request->produto_id)
            ->where('observacoes', $request->observacoes)
            ->first();

        if ($itemExistente) {
            $itemExistente->quantidade += $request->quantidade;
            $itemExistente->save();
        } else {
            $user->itensCarrinho()->create([
                'produto_id' => $request->produto_id,
                'quantidade' => $request->quantidade,
                'observacoes' => $request->observacoes,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'cart_count' => $user->itensCarrinho()->sum('quantidade'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        $item = Auth::user()->itensCarrinho()->findOrFail($id);
        $item->update(['quantidade' => $request->quantidade]);

        return response()->json([
            'success' => true,
            'message' => 'Quantidade atualizada!',
        ]);
    }

    public function destroy($id)
    {
        $item = Auth::user()->itensCarrinho()->findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removido do carrinho!',
        ]);
    }
    
    public function count()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }
        
        return response()->json([
            'count' => Auth::user()->itensCarrinho()->sum('quantidade')
        ]);
    }
}
