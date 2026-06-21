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
use App\Models\ZonaEntrega;
use App\Models\Endereco;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        // Faturamento
        $totalFaturamentoMes = Pedido::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereNotIn('status', ['cancelado'])
            ->sum('total');

        $fatMesAnterior = Pedido::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastMonthYear)
            ->whereNotIn('status', ['cancelado'])
            ->sum('total');

        $variacaoFat = $fatMesAnterior > 0 
            ? (($totalFaturamentoMes - $fatMesAnterior) / $fatMesAnterior) * 100 
            : ($totalFaturamentoMes > 0 ? 100 : 0);

        // Ticket Médio
        $ticketMedio = Pedido::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereNotIn('status', ['cancelado'])
            ->avg('total') ?? 0;

        // Taxa de Cancelamento
        $totalPedidosMes = Pedido::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        $canceladosMes = Pedido::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'cancelado')
            ->count();
        $taxaCancelamento = $totalPedidosMes > 0 ? ($canceladosMes / $totalPedidosMes) * 100 : 0;

        // Pedidos Hoje
        $pedidosHoje = Pedido::whereDate('created_at', today())->count();
        $pedidosOntem = Pedido::whereDate('created_at', today()->subDay())->count();
        $variacaoPedidos = $pedidosOntem > 0 
            ? (($pedidosHoje - $pedidosOntem) / $pedidosOntem) * 100 
            : ($pedidosHoje > 0 ? 100 : 0);

        // Clientes
        $totalClientes = User::where('is_admin', false)->count();
        $novosClientesMes = User::where('is_admin', false)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        $novosClientesMesAnterior = User::where('is_admin', false)
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastMonthYear)
            ->count();
        $variacaoClientes = $novosClientesMesAnterior > 0
            ? (($novosClientesMes - $novosClientesMesAnterior) / $novosClientesMesAnterior) * 100
            : ($novosClientesMes > 0 ? 100 : 0);

        // Pedidos Pendentes Antigos (mais de 5 min)
        $pedidosAtrasados = Pedido::where('status', 'pendente')
            ->whereDate('created_at', today())
            ->where('created_at', '<', now()->subMinutes(5))
            ->count();

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

        $topProdutos = DB::table('itens_pedido')
            ->join('produtos', 'itens_pedido.produto_id', '=', 'produtos.id')
            ->join('pedidos', 'itens_pedido.pedido_id', '=', 'pedidos.id')
            ->whereNotIn('pedidos.status', ['cancelado'])
            ->select('produtos.nome', DB::raw('SUM(itens_pedido.quantidade) as total'))
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $volumePedidos7Dias = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'total' => Pedido::whereDate('created_at', $date)
                    ->whereNotIn('status', ['cancelado'])
                    ->count(),
            ];
        });

        $volumePedidos30Dias = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->format('d/m'),
                'total' => Pedido::whereDate('created_at', $date)
                    ->whereNotIn('status', ['cancelado'])
                    ->count(),
            ];
        });

        $mediaAvaliacoes = Avaliacao::avg('nota') ?? 0;
        $totalAvaliacoes = Avaliacao::count();
        $avaliacoesSemResposta = Avaliacao::whereNull('resposta_admin')->count();

        // Pedidos de hoje (ativos — excluindo entregue e cancelado)
        $pedidosHoje_lista = Pedido::with(['user', 'pagamento'])
            ->whereDate('created_at', today())
            ->whereNotIn('status', ['entregue', 'cancelado'])
            ->latest()
            ->get();

        // Histórico: entregues e cancelados de qualquer data
        $pedidosHistorico = Pedido::with(['user', 'pagamento'])
            ->whereIn('status', ['entregue', 'cancelado'])
            ->latest()
            ->take(50)
            ->get();

        // Mantido para compatibilidade com contadores no filtro de status
        $pedidosRecentes = $pedidosHoje_lista;

        $storeIsOpen = \App\Helpers\StoreHelper::isOpen();

        return view('admin.dashboard', compact(
            'totalFaturamentoMes', 'variacaoFat', 'ticketMedio', 'taxaCancelamento',
            'pedidosHoje', 'variacaoPedidos', 'pedidosAtrasados',
            'totalClientes', 'novosClientesMes', 'variacaoClientes', 'produtoMaisVendido',
            'faturamento7Dias', 'faturamento30Dias', 'pedidosPorStatus',
            'faturamentoPorCategoria', 'topProdutos', 'volumePedidos7Dias', 'volumePedidos30Dias',
            'mediaAvaliacoes', 'totalAvaliacoes', 'avaliacoesSemResposta',
            'pedidosHoje_lista', 'pedidosHistorico', 'pedidosRecentes', 'storeIsOpen'
        ));
    }

    public function getStoreStatus()
    {
        return response()->json([
            'is_open' => \App\Helpers\StoreHelper::isOpen(),
            'hours'   => \App\Helpers\StoreHelper::hoursLabel(),
        ]);
    }

    public function getSchedule()
    {
        return response()->json(\App\Helpers\StoreHelper::getSchedule());
    }

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'days'  => 'required|array|min:1',
            'days.*'=> 'integer|between:0,6',
            'open'  => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'close' => ['required', 'regex:/^\d{2}:\d{2}$/'],
        ]);

        $schedule = [
            'days'  => array_map('intval', $request->days),
            'open'  => $request->open,
            'close' => $request->close,
        ];

        \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
            ['key' => 'store_schedule'],
            ['value' => json_encode($schedule), 'updated_at' => now(), 'created_at' => now()]
        );

        return response()->json([
            'success'  => true,
            'schedule' => $schedule,
            'is_open'  => \App\Helpers\StoreHelper::isOpen(),
            'label'    => \App\Helpers\StoreHelper::hoursLabel(),
        ]);
    }

    public function publicStoreStatus()
    {
        return response()->json([
            'is_open'  => \App\Helpers\StoreHelper::isOpen(),
            'schedule' => \App\Helpers\StoreHelper::getSchedule(),
            'label'    => \App\Helpers\StoreHelper::hoursLabel(),
        ])->header('Cache-Control', 'no-store');
    }

    public function subscribePush(Request $request)
    {
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $user = auth()->user();

        $user->updatePushSubscription($endpoint, $key, $token);
        
        return response()->json(['success' => true], 200);
    }

    public function testPush()
    {
        $user = auth()->user();
        // Crie um pedido dummy para testar
        $pedido = new \App\Models\Pedido(['id' => 9999, 'total' => 123.45]);
        $user->notify(new \App\Notifications\NewOrderPushNotification($pedido));
        
        return response()->json(['success' => true], 200);
    }


    public function apiAtivos()
    {
        $pedidosHoje_lista = Pedido::with(['user', 'pagamento'])
            ->whereDate('created_at', today())
            ->whereNotIn('status', ['entregue', 'cancelado'])
            ->latest()
            ->get();

        $html = '';
        foreach ($pedidosHoje_lista as $pedido) {
            $html .= view('admin._pedido_row', ['pedido' => $pedido, 'showDate' => true])->render();
        }

        $statusCounts = $pedidosHoje_lista->groupBy('status')->map->count();
        
        $pedidosAtrasadosCount = $pedidosHoje_lista->filter(function ($pedido) {
            return $pedido->status === 'pendente' && $pedido->created_at < now()->subMinutes(5);
        })->count();

        // Real-time Dashboard KPIs
        $totalFaturamentoHoje = Pedido::whereDate('created_at', today())
            ->whereNotIn('status', ['cancelado'])
            ->sum('total');
        $pedidosHojeCount = Pedido::whereDate('created_at', today())->count();

        return response()->json([
            'html' => $html,
            'count' => $pedidosHoje_lista->count(),
            'latest_id' => $pedidosHoje_lista->max('id') ?? 0,
            'status_counts' => $statusCounts,
            'pedidos_atrasados_count' => $pedidosAtrasadosCount,
            'faturamento_hoje' => $totalFaturamentoHoje,
            'pedidos_hoje_count' => $pedidosHojeCount
        ]);
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

        $pedido = Pedido::with('user')->findOrFail($id);
        $pedido->update(['status' => $request->status]);

        // Enviar notificação push para o cliente
        if ($pedido->user) {
            $pedido->user->notify(new \App\Notifications\OrderStatusNotification($pedido));
        }

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
            'imagem'       => 'nullable|image|max:5120',
            'ativo'        => 'nullable',
            'destaque'     => 'nullable',
        ], [
            'imagem.max'   => 'A imagem do produto não pode ser maior que 5MB.',
            'imagem.image' => 'O arquivo selecionado deve ser uma imagem válida.',
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
            'imagem'       => 'nullable|image|max:5120',
            'ativo'        => 'nullable',
            'destaque'     => 'nullable',
        ], [
            'imagem.max'   => 'A imagem do produto não pode ser maior que 5MB.',
            'imagem.image' => 'O arquivo selecionado deve ser uma imagem válida.',
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
        /*
        $produto = Produto::findOrFail($id);
        if ($produto->imagem && !str_starts_with($produto->imagem, 'http')) {
            Storage::disk('public')->delete($produto->imagem);
        }
        $produto->delete();
        return response()->json(['success' => true]);
        */
        return response()->json(['error' => 'A exclusão de produtos está temporariamente desativada.'], 403);
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
            'subtotal'   => number_format($pedido->subtotal, 2, ',', '.'),
            'taxa_entrega'=> number_format($pedido->taxa_entrega, 2, ',', '.'),
            'troco_para' => $pedido->troco_para ? number_format($pedido->troco_para, 2, ',', '.') : null,
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
                'observacao' => $i->observacoes,
                'imagem'     => $i->produto?->imagem
                    ? (str_starts_with($i->produto->imagem, 'http') ? $i->produto->imagem : asset('storage/'.$i->produto->imagem))
                    : null,
            ]),
        ]);
    }

    // ═══════════════════════════════════════════
    //  ZONAS DE ENTREGA
    // ═══════════════════════════════════════════

    public function indexZonas()
    {
        return response()->json(ZonaEntrega::orderBy('nome')->get());
    }

    public function storeZona(Request $request)
    {
        $request->validate([
            'nome'               => 'required|string|max:100',
            'taxa'               => 'required|numeric|min:0',
            'bairros'            => 'required|array|min:1',
            'bairros.*'          => 'required|string|max:100',
            'frete_gratis_acima' => 'nullable|numeric|min:0',
        ]);

        $zona = ZonaEntrega::create([
            'nome'               => $request->nome,
            'taxa'               => $request->taxa,
            'bairros'            => array_map('trim', $request->bairros),
            'frete_gratis_acima' => $request->frete_gratis_acima ?: null,
            'ativo'              => true,
        ]);

        return response()->json(['success' => true, 'zona' => $zona]);
    }

    public function updateZona(Request $request, $id)
    {
        $request->validate([
            'nome'               => 'required|string|max:100',
            'taxa'               => 'required|numeric|min:0',
            'bairros'            => 'required|array|min:1',
            'bairros.*'          => 'required|string|max:100',
            'frete_gratis_acima' => 'nullable|numeric|min:0',
            'ativo'              => 'nullable|boolean',
        ]);

        $zona = ZonaEntrega::findOrFail($id);
        $zona->update([
            'nome'               => $request->nome,
            'taxa'               => $request->taxa,
            'bairros'            => array_map('trim', $request->bairros),
            'frete_gratis_acima' => $request->frete_gratis_acima ?: null,
            'ativo'              => $request->boolean('ativo', true),
        ]);

        return response()->json(['success' => true, 'zona' => $zona]);
    }

    public function destroyZona($id)
    {
        ZonaEntrega::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // Endpoint público: calcula a taxa dado um endereço
    public function calcularFrete(Request $request)
    {
        $request->validate(['endereco_id' => 'required|integer']);

        $endereco = Endereco::where('id', $request->endereco_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $bairro = trim($endereco->bairro ?? '');

        // ── Se o bairro estiver vazio, tenta enriquecer via ViaCEP ──
        if (empty($bairro) && !empty($endereco->cep)) {
            $cepLimpo = preg_replace('/\D/', '', $endereco->cep);
            try {
                $resposta = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get("https://viacep.com.br/ws/{$cepLimpo}/json/");

                if ($resposta->ok()) {
                    $dados = $resposta->json();
                    if (!empty($dados['bairro'])) {
                        $bairro = $dados['bairro'];
                        // Persiste o bairro para não precisar buscar novamente
                        $endereco->update(['bairro' => $bairro]);
                    }
                }
            } catch (\Exception $e) {
                // Ignora erro de rede — continua sem bairro
            }
        }

        if (empty($bairro)) {
            return response()->json([
                'taxa'     => null,
                'mensagem' => 'Não foi possível identificar o bairro deste endereço. Edite o endereço e confirme o bairro.',
            ]);
        }

        $zona = ZonaEntrega::encontrarPorBairro($bairro);

        if (!$zona) {
            return response()->json([
                'taxa'     => null,
                'zona'     => null,
                'bairro'   => $bairro,
                'mensagem' => "Bairro \"{$bairro}\" ainda não possui taxa de entrega cadastrada.",
            ]);
        }

        $subtotal  = $request->subtotal ?? 0;
        $taxaFinal = ($zona->frete_gratis_acima && $subtotal >= $zona->frete_gratis_acima)
            ? 0
            : (float) $zona->taxa;

        return response()->json([
            'taxa'               => $taxaFinal,
            'zona'               => $zona->nome,
            'bairro'             => $bairro,
            'frete_gratis_acima' => $zona->frete_gratis_acima,
        ]);
    }
}
