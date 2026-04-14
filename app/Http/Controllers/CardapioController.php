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
        $categorias = Categoria::with('produtos')->get();

        $destaques = Produto::where('destaque', true)->get();

        return view('cardapio', compact('categorias', 'destaques'));
    }
}
