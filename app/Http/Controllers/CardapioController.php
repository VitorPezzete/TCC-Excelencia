<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Produto;


class CardapioController extends Controller
{
    //
    public function index() {
        $categorias = Categoria::where('ativo', true)
            ->with(['produtos' => function($q) {
                $q->where('ativo', true);
            }])
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        $destaques = Produto::where('destaque', true)->where('ativo', true)->get();

        return view('cardapio', compact('categorias', 'destaques'));
    }
}
