<?php

/*
Métodos HTTP:(
Get -> Usuário pediu para visualizar a página
Post -> Usuário está enviando dados)

Middleware: É um filtro que roda antes de chegar a rota/página.
Guest: Só deixa passar quem não está logado.
Auth: Só deixa passar quem está logado.
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardapioController;
use App\Http\Controllers\CarrinhoController;

Route::get('/', function () {
    $destaques = \App\Models\Produto::where('destaque', true)
        ->where('ativo', true)
        ->with('categoria')
        ->latest()
        ->take(8)
        ->get();
    return view('welcome', compact('destaques'));
})->name('home');

Route::get('/cardapio', [CardapioController::class, 'index'])->name('cardapio');
Route::get('/avaliacoes', function () {
    $nota = request('nota');
    $query = \App\Models\Avaliacao::with(['user', 'produto'])->latest();
    if ($nota) $query->where('nota', $nota);
    $avaliacoes     = $query->paginate(12);
    $mediaGeral     = \App\Models\Avaliacao::avg('nota') ?? 0;
    $totalAvaliacoes = \App\Models\Avaliacao::count();
    return view('avaliacoes', compact('avaliacoes', 'mediaGeral', 'totalAvaliacoes'));
})->name('avaliacoes.publicas');
Route::get('/carrinho/count', [CarrinhoController::class, 'count'])->name('carrinho.count');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::post('/cadastro', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'index'])->name('perfil');
    Route::post('/perfil/dados', [ProfileController::class, 'updateData'])->name('perfil.dados');
    Route::post('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('perfil.senha');
    Route::post('/perfil/enderecos', [ProfileController::class, 'storeAddress'])->name('perfil.enderecos.store');
    Route::delete('/perfil/enderecos/{id}', [ProfileController::class, 'destroyAddress'])->name('perfil.enderecos.destroy');
    Route::post('/perfil/enderecos/{id}/padrao', [ProfileController::class, 'setDefaultAddress'])->name('perfil.enderecos.padrao');
    Route::put('/perfil/enderecos/{id}', [ProfileController::class, 'updateAddress'])->name('perfil.enderecos.update');

    // Rotas do Carrinho
    Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho');
    Route::post('/carrinho', [CarrinhoController::class, 'store'])->name('carrinho.store');
    Route::put('/carrinho/{id}', [CarrinhoController::class, 'update'])->name('carrinho.update');
    Route::delete('/carrinho/{id}', [CarrinhoController::class, 'destroy'])->name('carrinho.destroy');
    
    // Rota de Checkout
    Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

    Route::post('/avaliacoes', function (\Illuminate\Http\Request $req) {
        $req->validate(['pedido_id' => 'required|integer', 'nota' => 'required|integer|min:1|max:5', 'comentario' => 'nullable|string|max:1000']);
        $pedido = \App\Models\Pedido::where('id', $req->pedido_id)->where('user_id', auth()->id())->where('status', 'entregue')->firstOrFail();
        \App\Models\Avaliacao::updateOrCreate(
            ['user_id' => auth()->id(), 'pedido_id' => $pedido->id],
            ['nota' => $req->nota, 'comentario' => $req->comentario]
        );
        return response()->json(['ok' => true]);
    })->name('avaliacoes.store');
});

// Painel Administrativo
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',  [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

    // Pedidos
    Route::get('/pedidos',                    [\App\Http\Controllers\AdminController::class, 'pedidos'])->name('pedidos');
    Route::patch('/pedidos/{id}/status',      [\App\Http\Controllers\AdminController::class, 'updateStatusPedido'])->name('pedidos.status');

    // Produtos
    Route::get('/produtos',                          [\App\Http\Controllers\AdminController::class, 'produtos'])->name('produtos');
    Route::post('/produtos',                         [\App\Http\Controllers\AdminController::class, 'storeProduto'])->name('produtos.store');
    Route::put('/produtos/{id}',                     [\App\Http\Controllers\AdminController::class, 'updateProduto'])->name('produtos.update');
    Route::patch('/produtos/{id}/toggle',            [\App\Http\Controllers\AdminController::class, 'toggleProduto'])->name('produtos.toggle');
    Route::patch('/produtos/{id}/toggle-destaque',   [\App\Http\Controllers\AdminController::class, 'toggleDestaque'])->name('produtos.toggleDestaque');
    Route::delete('/produtos/{id}',                  [\App\Http\Controllers\AdminController::class, 'destroyProduto'])->name('produtos.destroy');

    // Categorias
    Route::get('/categorias',     [\App\Http\Controllers\AdminController::class, 'listCategorias'])->name('categorias.list');
    Route::post('/categorias',    [\App\Http\Controllers\AdminController::class, 'storeCategoria'])->name('categorias.store');
    Route::delete('/categorias/{id}', [\App\Http\Controllers\AdminController::class, 'destroyCategoria'])->name('categorias.destroy');

    // Usuários
    Route::get('/usuarios',                       [\App\Http\Controllers\AdminController::class, 'usuarios'])->name('usuarios');
    Route::patch('/usuarios/{id}/toggle-admin',   [\App\Http\Controllers\AdminController::class, 'toggleAdmin'])->name('usuarios.toggleAdmin');

    // Avaliações
    Route::get('/avaliacoes',                        [\App\Http\Controllers\AdminController::class, 'avaliacoes'])->name('avaliacoes');
    Route::patch('/avaliacoes/{id}/responder',       [\App\Http\Controllers\AdminController::class, 'responderAvaliacao'])->name('avaliacoes.responder');

    // Detalhes de pedido
    Route::get('/pedidos/{id}/detalhes', [\App\Http\Controllers\AdminController::class, 'detalhesPedido'])->name('pedidos.detalhes');
});