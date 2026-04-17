<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Avaliacao;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $totalFaturamentoMes = Pedido::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelado'])
            ->sum('total');

        $pedidosHoje = Pedido::whereDate('created_at', today())->count();
        $totalClientes = User::where('is_admin', false)->count();

        $produtoMaisVendido = DB::table('itens_pedido')
            ->join('produtos', 'itens_pedido.produto_id', '=', 'produtos.id')
            ->select('produtos.nome', DB::raw('SUM(itens_pedido.quantidade) as total_vendido'))
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderByDesc('total_vendido')
            ->first();

        $faturamento7Dias = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'total' => Pedido::whereDate('created_at', $date)
                    ->whereNotIn('status', ['cancelado'])
                    ->sum('total'),
            ];
        });

        $faturamento30Dias = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'total' => Pedido::whereDate('created_at', $date)
                    ->whereNotIn('status', ['cancelado'])
                    ->sum('total'),
            ];
        });

        $pedidosPorStatus = Pedido::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        $faturamentoPorCategoria = DB::table('itens_pedido')
            ->join('produtos', 'itens_pedido.produto_id', '=', 'produtos.id')
            ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
            ->join('pedidos', 'itens_pedido.pedido_id', '=', 'pedidos.id')
            ->whereNotIn('pedidos.status', ['cancelado'])
            ->select('categorias.nome', DB::raw('SUM(itens_pedido.preco_total) as total'))
            ->groupBy('categorias.id', 'categorias.nome')
            ->orderByDesc('total')
            ->get();

        $pedidosRecentes = Pedido::with(['user', 'pagamento'])
            ->latest()
            ->take(10)
            ->get();

        $mediaAvaliacoes = Avaliacao::avg('nota') ?? 0;
        $totalAvaliacoes = Avaliacao::count();
        $avaliacoesSemResposta = Avaliacao::whereNull('resposta_admin')->count();

        return view('admin.dashboard', compact(
            'totalFaturamentoMes', 'pedidosHoje', 'totalClientes', 'produtoMaisVendido',
            'faturamento7Dias', 'faturamento30Dias', 'pedidosPorStatus',
            'faturamentoPorCategoria', 'pedidosRecentes',
            'mediaAvaliacoes', 'totalAvaliacoes', 'avaliacoesSemResposta'
        ));
    }

    public function pedidos(Request $request)
    {
        $query = Pedido::with(['user', 'pagamento'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('busca')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->busca.'%'));
        }

        $pedidos = $query->paginate(15);
        return view('admin.pedidos', compact('pedidos'));
    }

    public function updateStatusPedido(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,confirmado,preparando,saiu_para_entrega,entregue,cancelado',
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $request->status]);
    }

    public function produtos()
    {
        $produtos = Produto::with('categoria')->latest()->paginate(20);
        return view('admin.produtos', compact('produtos'));
    }

    public function toggleProduto($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->update(['ativo' => !$produto->ativo]);
        return response()->json(['success' => true, 'ativo' => $produto->ativo]);
    }

    public function toggleDestaque($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->update(['destaque' => !$produto->destaque]);
        return response()->json(['success' => true, 'destaque' => (bool) $produto->destaque]);
    }

    public function storeProduto(Request $request)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'descricao'    => 'required|string',
            'preco'        => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem'       => 'nullable|image|max:2048',
            'ativo'        => 'nullable',
            'destaque'     => 'nullable',
        ]);

        $imagemPath = null;
        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('produtos', 'public');
        }

        $produto = Produto::create([
            'nome'         => $request->nome,
            'descricao'    => $request->descricao,
            'preco'        => $request->preco,
            'categoria_id' => $request->categoria_id,
            'imagem'       => $imagemPath,
            'ativo'        => $request->filled('ativo'),
            'destaque'     => $request->filled('destaque'),
        ]);

        return response()->json(['success' => true, 'produto' => $produto->load('categoria')]);
    }

    public function updateProduto(Request $request, $id)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'descricao'    => 'required|string',
            'preco'        => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem'       => 'nullable|image|max:2048',
            'ativo'        => 'nullable',
            'destaque'     => 'nullable',
        ]);

        $produto = Produto::findOrFail($id);

        $dados = [
            'nome'         => $request->nome,
            'descricao'    => $request->descricao,
            'preco'        => $request->preco,
            'categoria_id' => $request->categoria_id,
            'ativo'        => $request->filled('ativo'),
            'destaque'     => $request->filled('destaque'),
        ];

        if ($request->hasFile('imagem')) {
            if ($produto->imagem && !str_starts_with($produto->imagem, 'http')) {
                Storage::disk('public')->delete($produto->imagem);
            }
            $dados['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        $produto->update($dados);

        return response()->json(['success' => true, 'produto' => $produto->load('categoria')]);
    }

    public function storeCategoria(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:100|unique:categorias,nome']);
        $categoria = Categoria::create(['nome' => $request->nome]);
        return response()->json(['success' => true, 'categoria' => $categoria]);
    }

    public function listCategorias()
    {
        return response()->json(Categoria::orderBy('nome')->get());
    }

    public function usuarios()
    {
        $usuarios = User::withCount('pedidos')->latest()->paginate(20);
        return view('admin.usuarios', compact('usuarios'));
    }

    public function toggleAdmin($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['is_admin' => !$usuario->is_admin]);
        return response()->json(['success' => true, 'is_admin' => $usuario->is_admin]);
    }

    public function avaliacoes(Request $request)
    {
        $query = Avaliacao::with(['user', 'produto'])->latest();

        if ($request->filled('nota')) $query->where('nota', $request->nota);
        if ($request->filled('sem_resposta')) $query->whereNull('resposta_admin');

        return response()->json($query->get());
    }

    public function responderAvaliacao(Request $request, $id)
    {
        $request->validate(['resposta' => 'required|string|max:1000']);

        $avaliacao = Avaliacao::findOrFail($id);
        $avaliacao->update([
            'resposta_admin' => $request->resposta,
            'respondido_em'  => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyProduto($id)
    {
        $produto = Produto::findOrFail($id);
        if ($produto->imagem && !str_starts_with($produto->imagem, 'http')) {
            Storage::disk('public')->delete($produto->imagem);
        }
        $produto->delete();
        return response()->json(['success' => true]);
    }

    public function destroyCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        if ($categoria->produtos()->count() > 0) {
            return response()->json(['error' => 'Categoria possui produtos vinculados. Remova ou mova os produtos primeiro.'], 422);
        }
        $categoria->delete();
        return response()->json(['success' => true]);
    }

    public function detalhesPedido($id)
    {
        $pedido = Pedido::with(['user', 'itens.produto', 'pagamento', 'endereco'])->findOrFail($id);
        return response()->json([
            'id'         => $pedido->id,
            'status'     => $pedido->status,
            'created_at' => $pedido->created_at->format('d/m/Y H:i'),
            'updated_at' => $pedido->updated_at->format('d/m/Y H:i'),
            'total'      => number_format($pedido->total, 2, ',', '.'),
            'cliente'    => $pedido->user?->name ?? '—',
            'email'      => $pedido->user?->email ?? '—',
            'pagamento'  => $pedido->pagamento?->metodo ?? '—',
            'observacoes'=> $pedido->observacoes ?? '',
            'endereco'   => $pedido->endereco
                ? implode(', ', array_filter([
                    $pedido->endereco->nome ?? null,
                    $pedido->endereco->numero ? 'Nº ' . $pedido->endereco->numero : null,
                    $pedido->endereco->complemento ?? null,
                    $pedido->endereco->cep ? 'CEP: ' . $pedido->endereco->cep : null,
                  ]))
                : '—',
            'itens'      => $pedido->itens->map(fn($i) => [
                'nome'       => $i->produto?->nome ?? '—',
                'quantidade' => $i->quantidade,
                'preco'      => number_format($i->preco_unitario, 2, ',', '.'),
                'imagem'     => $i->produto?->imagem
                    ? (str_starts_with($i->produto->imagem, 'http') ? $i->produto->imagem : asset('storage/'.$i->produto->imagem))
                    : null,
            ]),
        ]);
    }
}
