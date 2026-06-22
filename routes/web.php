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
use App\Http\Controllers\PagamentoController;

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
    if ($nota)
        $query->where('nota', $nota);
    $avaliacoes = $query->paginate(12);
    $mediaGeral = \App\Models\Avaliacao::avg('nota') ?? 0;
    $totalAvaliacoes = \App\Models\Avaliacao::count();
    return view('avaliacoes', compact('avaliacoes', 'mediaGeral', 'totalAvaliacoes'));
})->name('avaliacoes.publicas');
Route::get('/carrinho/count', [CarrinhoController::class, 'count'])->name('carrinho.count');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::post('/cadastro', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rotas de Recuperação de Senha
Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request')->middleware('guest');
Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('guest');
Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'index'])->name('perfil');

    // Notificações Web Push (Cliente e Admin)
    Route::post('/push/subscribe', [\App\Http\Controllers\AdminController::class, 'subscribePush']);

    Route::post('/perfil/dados', [ProfileController::class, 'updateData'])->name('perfil.dados');
    Route::post('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('perfil.senha');
    Route::post('/perfil/enderecos', [ProfileController::class, 'storeAddress'])->name('perfil.enderecos.store');
    Route::delete('/perfil/enderecos/{id}', [ProfileController::class, 'destroyAddress'])->name('perfil.enderecos.destroy');
    Route::post('/perfil/enderecos/{id}/padrao', [ProfileController::class, 'setDefaultAddress'])->name('perfil.enderecos.padrao');
    Route::put('/perfil/enderecos/{id}', [ProfileController::class, 'updateAddress'])->name('perfil.enderecos.update');

    // Rotas do Carrinho
    Route::get('/pedidos/api', [ProfileController::class, 'apiPedidos'])->name('perfil.pedidos.api');
    Route::patch('/perfil/pedidos/{id}/cancelar', [ProfileController::class, 'cancelarPedido'])->name('perfil.pedidos.cancelar');
    Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho');
    Route::post('/carrinho', [CarrinhoController::class, 'store'])->name('carrinho.store');
    Route::put('/carrinho/{id}', [CarrinhoController::class, 'update'])->name('carrinho.update');
    Route::delete('/carrinho/{id}', [CarrinhoController::class, 'destroy'])->name('carrinho.destroy');

    // Rota de Checkout
    Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{pedido}/status', [\App\Http\Controllers\CheckoutController::class, 'status'])->name('checkout.status');

    // Rota de Pagamento PIX (Mercado Pago)
    Route::post('/pagamento/pix/{pedido}', [PagamentoController::class, 'criarPix'])->name('pagamento.pix');

    // Rota de Pagamento Cartão (Mercado Pago Bricks)
    Route::get('/pagamento/cartao/{pedido}', [PagamentoController::class, 'showPaymentForm'])->name('pagamento.cartao');
    Route::post('/pagamento/cartao/{pedido}', [PagamentoController::class, 'processarCartao'])->name('pagamento.cartao.processar');

    Route::post('/avaliacoes', function (\Illuminate\Http\Request $req) {
        $req->validate(['pedido_id' => 'required|integer', 'nota' => 'required|integer|min:1|max:5', 'comentario' => 'nullable|string|max:1000']);
        $pedido = \App\Models\Pedido::where('id', $req->pedido_id)->where('user_id', auth()->id())->where('status', 'entregue')->firstOrFail();
        $avaliacao = \App\Models\Avaliacao::updateOrCreate(
            ['user_id' => auth()->id(), 'pedido_id' => $pedido->id],
            ['nota' => $req->nota, 'comentario' => $req->comentario]
        );

        $admins = \App\Models\User::where('is_admin', true)->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewReviewNotification($avaliacao));

        return response()->json(['ok' => true]);
    })->name('avaliacoes.store');
});

// Painel Administrativo
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

    // Pedidos
    Route::get('/pedidos', [\App\Http\Controllers\AdminController::class, 'pedidos'])->name('pedidos');
    Route::get('/pedidos/api/ativos', [\App\Http\Controllers\AdminController::class, 'apiAtivos'])->name('pedidos.api.ativos');
    Route::patch('/pedidos/{id}/status', [\App\Http\Controllers\AdminController::class, 'updateStatusPedido'])->name('pedidos.status');
    
    // Notificações Web Push de Teste (Somente Admin)
    Route::post('/push/test', [\App\Http\Controllers\AdminController::class, 'testPush']);

    Route::get('/store-status',               [\App\Http\Controllers\AdminController::class, 'getStoreStatus'])->name('store.status');
    Route::get('/schedule',                   [\App\Http\Controllers\AdminController::class, 'getSchedule'])->name('schedule.get');
    Route::post('/schedule',                  [\App\Http\Controllers\AdminController::class, 'updateSchedule'])->name('schedule.update');

    // Produtos
    Route::get('/produtos', [\App\Http\Controllers\AdminController::class, 'produtos'])->name('produtos');
    Route::post('/produtos', [\App\Http\Controllers\AdminController::class, 'storeProduto'])->name('produtos.store');
    Route::put('/produtos/{id}', [\App\Http\Controllers\AdminController::class, 'updateProduto'])->name('produtos.update');
    Route::patch('/produtos/{id}/toggle', [\App\Http\Controllers\AdminController::class, 'toggleProduto'])->name('produtos.toggle');
    Route::patch('/produtos/{id}/toggle-destaque', [\App\Http\Controllers\AdminController::class, 'toggleDestaque'])->name('produtos.toggleDestaque');
    Route::delete('/produtos/{id}', [\App\Http\Controllers\AdminController::class, 'destroyProduto'])->name('produtos.destroy');

    // Categorias
    Route::get('/categorias', [\App\Http\Controllers\AdminController::class, 'listCategorias'])->name('categorias.list');
    Route::post('/categorias', [\App\Http\Controllers\AdminController::class, 'storeCategoria'])->name('categorias.store');
    Route::post('/categorias/reorder', [\App\Http\Controllers\AdminController::class, 'reorderCategorias'])->name('categorias.reorder');
    Route::put('/categorias/{id}', [\App\Http\Controllers\AdminController::class, 'updateCategoria'])->name('categorias.update');
    Route::patch('/categorias/{id}/toggle', [\App\Http\Controllers\AdminController::class, 'toggleCategoria'])->name('categorias.toggle');
    Route::delete('/categorias/{id}', [\App\Http\Controllers\AdminController::class, 'destroyCategoria'])->name('categorias.destroy');

    // Avaliações
    Route::get('/avaliacoes', [\App\Http\Controllers\AdminController::class, 'avaliacoes'])->name('avaliacoes');
    Route::patch('/avaliacoes/{id}/responder', [\App\Http\Controllers\AdminController::class, 'responderAvaliacao'])->name('avaliacoes.responder');

    // Detalhes de pedido
    Route::get('/pedidos/{id}/detalhes', [\App\Http\Controllers\AdminController::class, 'detalhesPedido'])->name('pedidos.detalhes');

    // Zonas de Entrega
    Route::get('/zonas-entrega', [\App\Http\Controllers\AdminController::class, 'indexZonas'])->name('zonas.index');
    Route::post('/zonas-entrega', [\App\Http\Controllers\AdminController::class, 'storeZona'])->name('zonas.store');
    Route::put('/zonas-entrega/{id}', [\App\Http\Controllers\AdminController::class, 'updateZona'])->name('zonas.update');
    Route::delete('/zonas-entrega/{id}', [\App\Http\Controllers\AdminController::class, 'destroyZona'])->name('zonas.destroy');
    Route::post('/calcular-frete', [\App\Http\Controllers\AdminController::class, 'calcularFrete'])->name('frete.calcular');
});

// Webhook do Mercado Pago (público — chamado pelo servidor do MP, não pelo usuário)
Route::post('/webhook/mercadopago', [PagamentoController::class, 'webhook'])->name('webhook.mercadopago');

// API pública — status atual da loja (polling por todas as páginas)
Route::get('/api/store-status', [\App\Http\Controllers\AdminController::class, 'publicStoreStatus'])->name('api.store.status');