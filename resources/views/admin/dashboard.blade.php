<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Excelência — Painel Admin</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])

    {{-- Dados para gráficos (acessados pelo admin.js via window) --}}
    <script>
        window.adminData = {
            fat7: @json($faturamento7Dias),
            fat30: @json($faturamento30Dias),
            status: @json($pedidosPorStatus),
            catFat: @json($faturamentoPorCategoria),
        };
    </script>

    <style>
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d69c5e33;
            border-radius: 99px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #d69c5e88;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            border-radius: 9999px;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s;
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
        }

        .sidebar-link.active {
            background: rgba(214, 156, 94, 0.1);
            color: #d69c5e;
        }

        .kpi-card {
            background: #1d0e0b;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.04);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.15s;
        }

        @media(min-width:768px) {
            .kpi-card {
                padding: 1.5rem;
            }
        }

        .kpi-card:hover {
            border-color: rgba(214, 156, 94, 0.2);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            border-width: 1px;
            border-style: solid;
            cursor: pointer;
            transition: opacity 0.15s;
            white-space: nowrap;
        }

        .status-pill:hover {
            opacity: 0.75;
        }

        .th {
            padding: 0.75rem 1.25rem;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #4b5563;
            text-align: left;
        }

        .td {
            padding: 1.1rem 1.25rem;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeInUp .35s ease both;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .toggle-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .toggle-track {
            display: block;
            width: 2.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            background: #374151;
            transition: background 0.2s;
        }

        input:checked+.toggle-track {
            background: #d69c5e;
        }

        .toggle-thumb {
            position: absolute;
            left: 0.125rem;
            top: 0.125rem;
            width: 1rem;
            height: 1rem;
            background: #fff;
            border-radius: 9999px;
            transition: transform 0.2s;
        }

        input:checked~.toggle-thumb {
            transform: translateX(1rem);
        }

        .chart-toggle-btn {
            color: #6b7280;
        }

        .chart-toggle-btn.active-toggle {
            background: #d69c5e;
            color: #1a0f0e;
        }
    </style>
</head>

<body class="font-body bg-[#0f0806] text-text-light min-h-screen flex overflow-x-hidden">

    {{-- ───── OVERLAY mobile sidebar ───── --}}
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/70 backdrop-blur-sm md:hidden"></div>

    {{-- ═══════════════════════════════ SIDEBAR ═══════════════════════════════ --}}
    <aside id="sidebar-drawer" class="fixed top-0 left-0 z-50 h-full w-64 flex flex-col shrink-0
           bg-[#120601] border-r border-white/[0.04]
           transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.04]">
            <div
                class="w-10 h-10 rounded-full bg-secondary/10 border border-secondary/20 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-secondary text-[20px]">bakery_dining</span>
            </div>
            <div>
                <p class="font-display font-bold text-secondary text-base leading-tight">Excelência</p>
                <p class="text-[10px] text-gray-700 tracking-widest uppercase">Painel Admin</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex flex-col gap-1 px-3 py-4 flex-1 overflow-y-auto">
            <p class="px-4 pb-1.5 text-[9px] font-bold text-gray-700 uppercase tracking-widest">Gestão</p>
            <button class="sidebar-link active" data-section="overview">
                <span class="material-symbols-outlined text-[19px]">dashboard</span> Visão Geral
            </button>
            <button class="sidebar-link" data-section="pedidos">
                <span class="material-symbols-outlined text-[19px]">receipt_long</span> Pedidos
            </button>
            <button class="sidebar-link" data-section="produtos">
                <span class="material-symbols-outlined text-[19px]">cake</span> Produtos
            </button>
            <button class="sidebar-link" data-section="avaliacoes">
                <span class="material-symbols-outlined text-[19px]">star_rate</span> Avaliações
            </button>

            <p class="px-4 pt-5 pb-1.5 text-[9px] font-bold text-gray-700 uppercase tracking-widest">Configurações</p>
            <button class="sidebar-link" data-section="usuarios">
                <span class="material-symbols-outlined text-[19px]">group</span> Usuários
            </button>
        </nav>

        {{-- User box --}}
        <div class="p-4 border-t border-white/[0.04] space-y-3">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-full bg-secondary/10 border border-secondary/20 flex items-center justify-center text-secondary font-bold text-xs shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-600">Administrador</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('home') }}"
                    class="flex-1 text-center px-2 py-2 text-[11px] font-bold text-gray-600 hover:text-white border border-white/[0.06]  hover:border-white/20 rounded-full transition-colors">←
                    Site</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button
                        class="w-full text-[11px] font-bold text-red-600 hover:text-red-400 border border-red-900/20 hover:border-red-900/50 rounded-full py-2 transition-colors">Sair</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════════════════════════════ MAIN ═══════════════════════════════ --}}
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen min-w-0">

        {{-- TOPBAR --}}
        <header
            class="sticky top-0 z-30 bg-[#0f0806]/95 backdrop-blur border-b border-white/[0.04] px-4 md:px-6 py-3.5 flex items-center gap-3 justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <button id="btn-mobile-menu"
                    class="md:hidden text-gray-500 hover:text-secondary transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[26px]">menu</span>
                </button>
                <div class="min-w-0">
                    <h1 id="page-title"
                        class="font-display text-lg md:text-xl font-bold text-white leading-tight truncate">Visão Geral
                    </h1>
                    <p id="page-subtitle" class="text-[11px] text-gray-600 hidden sm:block">Resumo geral do seu negócio
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="hidden lg:block text-[11px] text-gray-700">{{ now()->translatedFormat('d \d\e F') }}</span>
                <span
                    class="bg-secondary/10 border border-secondary/20 text-secondary text-[11px] font-bold px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">verified</span>
                    <span class="hidden sm:inline">Admin</span>
                </span>
            </div>
        </header>

        <main class="p-3 md:p-5 lg:p-6 space-y-5 flex-1">

            {{-- ═══════════ OVERVIEW ═══════════ --}}
            <div id="section-overview" class="section active animate-fade-up space-y-5">

                {{-- KPI CARDS --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4">
                    <div class="kpi-card col-span-1">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-secondary/5 rounded-full -translate-y-8 translate-x-8">
                        </div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Faturamento / Mês
                        </p>
                        <p class="text-2xl md:text-3xl font-bold text-secondary font-display">R$
                            {{ number_format($totalFaturamentoMes, 2, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">Pedidos não cancelados</p>
                        <span
                            class="material-symbols-outlined text-[36px] text-secondary/8 absolute bottom-3 right-3">payments</span>
                    </div>
                    <div class="kpi-card col-span-1">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full -translate-y-8 translate-x-8">
                        </div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Pedidos Hoje</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-400 font-display">{{ $pedidosHoje }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">{{ now()->format('d/m/Y') }}</p>
                        <span
                            class="material-symbols-outlined text-[36px] text-blue-400/8 absolute bottom-3 right-3">shopping_bag</span>
                    </div>
                    <div class="kpi-card col-span-1">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-full -translate-y-8 translate-x-8">
                        </div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Clientes</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-400 font-display">{{ $totalClientes }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">Total de usuários</p>
                        <span
                            class="material-symbols-outlined text-[36px] text-green-400/8 absolute bottom-3 right-3">group</span>
                    </div>
                    <div class="kpi-card col-span-1">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-full -translate-y-8 translate-x-8">
                        </div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Nota Média</p>
                        <p class="text-2xl md:text-3xl font-bold text-purple-400 font-display">
                            {{ number_format($mediaAvaliacoes, 1) }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">{{ $totalAvaliacoes }} avaliações</p>
                        <span
                            class="material-symbols-outlined text-[36px] text-purple-400/8 absolute bottom-3 right-3">star</span>
                    </div>
                </div>

                {{-- GRÁFICOS --}}
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    {{-- Faturamento (7/30 dias) --}}
                    <div class="xl:col-span-2 bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-4 md:p-5">
                        <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
                            <div>
                                <h2 class="font-bold text-white text-sm">Faturamento</h2>
                                <p class="text-[11px] text-gray-600">Pedidos confirmados, excl. cancelados</p>
                            </div>
                            <div class="flex gap-1 bg-black/30 rounded-full p-1">
                                <button id="btn-fat-7"
                                    class="chart-toggle-btn active-toggle px-3 py-1 rounded-full text-[11px] font-bold transition-all">7
                                    dias</button>
                                <button id="btn-fat-30"
                                    class="chart-toggle-btn px-3 py-1 rounded-full text-[11px] font-bold text-gray-500 transition-all">30
                                    dias</button>
                            </div>
                        </div>
                        <canvas id="chartFaturamento" height="120"></canvas>
                    </div>

                    {{-- Pedidos por Status --}}
                    <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-4 md:p-5">
                        <h2 class="font-bold text-white text-sm mb-1">Pedidos por Status</h2>
                        <p class="text-[11px] text-gray-600 mb-4">Distribuição geral</p>
                        <canvas id="chartStatus" height="200"></canvas>
                    </div>
                </div>

                {{-- Faturamento por Categoria --}}
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-4 md:p-5">
                    <h2 class="font-bold text-white text-sm mb-1">Faturamento por Categoria</h2>
                    <p class="text-[11px] text-gray-600 mb-4">Vendas acumuladas por tipo de produto</p>
                    <canvas id="chartCategoria" height="70"></canvas>
                </div>

                {{-- Pedidos recentes --}}
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04]">
                    <div class="px-4 md:px-5 py-4 border-b border-white/[0.04] flex items-center justify-between">
                        <h2 class="font-bold text-white text-sm">Pedidos Recentes</h2>
                        <button class="text-xs text-secondary hover:text-secondary/70 font-semibold transition-colors"
                            data-jump="pedidos">Ver todos →</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[540px]">
                            <thead>
                                <tr class="border-b border-white/[0.03]">
                                    <th class="th">Pedido</th>
                                    <th class="th">Cliente</th>
                                    <th class="th">Pagamento</th>
                                    <th class="th">Status</th>
                                    <th class="th text-center">Detalhes</th>
                                    <th class="th text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.03]">
                                @forelse($pedidosRecentes as $pedido)
                                    @include('admin._pedido_row', ['pedido' => $pedido])
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-600 text-sm">Nenhum pedido ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════ PEDIDOS ═══════════ --}}
            <div id="section-pedidos" class="section animate-fade-up">
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04]">
                    {{-- Filtros --}}
                    <div class="px-4 md:px-5 py-4 border-b border-white/[0.04] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                            <h2 class="font-bold text-white text-sm">Todos os Pedidos</h2>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined text-[16px] text-gray-600 absolute left-3 top-1/2 -translate-y-1/2">search</span>
                                <input id="pedido-search" type="text" placeholder="Buscar pedido / cliente…"
                                    class="w-full sm:w-56 bg-black/30 border border-white/[0.06] text-white text-xs rounded-full pl-9 pr-4 py-2
                                       focus:outline-none focus:border-secondary/40 placeholder-gray-700 transition-all">
                            </div>
                        </div>
                        {{-- Filtro data --}}
                        <div class="flex flex-wrap gap-2 items-center">
                            <div
                                class="flex items-center gap-1.5 bg-black/30 border border-white/[0.06] rounded-full px-3 py-1.5">
                                <span class="material-symbols-outlined text-gray-600 text-[14px]">calendar_today</span>
                                <input id="filtro-data-ini" type="date"
                                    class="bg-transparent text-[11px] text-gray-400 focus:outline-none focus:text-white">
                            </div>
                            <span class="text-gray-700 text-xs">até</span>
                            <div
                                class="flex items-center gap-1.5 bg-black/30 border border-white/[0.06] rounded-full px-3 py-1.5">
                                <span class="material-symbols-outlined text-gray-600 text-[14px]">calendar_today</span>
                                <input id="filtro-data-fim" type="date"
                                    class="bg-transparent text-[11px] text-gray-400 focus:outline-none focus:text-white">
                            </div>
                            <button id="btn-limpar-data"
                                class="text-[11px] text-gray-600 hover:text-secondary transition-colors">Limpar</button>
                        </div>
                        {{-- Pills de status --}}
                        <div class="flex gap-2 flex-wrap">
                            @php
                                $statusOpts = ['' => 'Todos', 'pendente' => 'Pendente', 'confirmado' => 'Confirmado', 'preparando' => 'Preparando', 'saiu_para_entrega' => 'Saiu p/ Entrega', 'entregue' => 'Entregue', 'cancelado' => 'Cancelado'];
                                $statusCounts = $pedidosRecentes->groupBy('status')->map->count();
                            @endphp
                            @foreach($statusOpts as $val => $label)
                                <button
                                    class="status-filter-btn inline-flex items-center gap-1 px-3 py-1 text-[11px] font-bold rounded-full border transition-all
                                    {{ $val === '' ? 'border-secondary text-secondary' : 'border-white/10 text-gray-600 hover:border-white/25 hover:text-gray-300' }}"
                                    data-status="{{ $val }}">
                                    {{ $label }}
                                    @if($val !== '' && isset($statusCounts[$val]))
                                        <span
                                            class="bg-white/10 px-1.5 py-0.5 rounded-full text-[9px]">{{ $statusCounts[$val] }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                    {{-- Tabela --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px]">
                            <thead>
                                <tr class="border-b border-white/[0.03]">
                                    <th class="th">Pedido</th>
                                    <th class="th">Cliente</th>
                                    <th class="th">Data & Hora</th>
                                    <th class="th">Pagamento</th>
                                    <th class="th">Status</th>
                                    <th class="th text-center">Detalhes</th>
                                    <th class="th text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="pedidos-body" class="divide-y divide-white/[0.03]">
                                @forelse($pedidosRecentes as $pedido)
                                    @include('admin._pedido_row', ['pedido' => $pedido, 'showDate' => true])
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-600 text-sm">Nenhum pedido.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════ PRODUTOS ═══════════ --}}
            <div id="section-produtos" class="section animate-fade-up">
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04]">
                    <div class="px-4 md:px-5 py-4 border-b border-white/[0.04] space-y-3">
                        <div class="flex flex-wrap items-center gap-3 justify-between">
                            <h2 class="font-bold text-white text-sm">Gestão de Produtos</h2>
                            <div class="flex gap-2">
                                <button id="btn-nova-categoria"
                                    class="flex items-center gap-1.5 px-3 py-2 text-[11px] font-bold rounded-full border border-white/10 text-gray-400 hover:border-secondary/40 hover:text-secondary transition-all">
                                    <span class="material-symbols-outlined text-[15px]">category</span> Gerenciar Categorias
                                </button>
                                <button id="btn-novo-produto"
                                    class="flex items-center gap-1.5 px-4 py-2 bg-secondary text-primary text-[12px] font-bold rounded-full hover:bg-[#c2884a] transition-all shadow-md shadow-secondary/10">
                                    <span class="material-symbols-outlined text-[16px]">add</span> Novo Produto
                                </button>
                            </div>
                        </div>
                        {{-- Busca + filtro categoria --}}
                        <div class="flex flex-wrap gap-2">
                            <div class="relative flex-1 min-w-[180px]">
                                <span
                                    class="material-symbols-outlined text-[15px] text-gray-600 absolute left-3 top-1/2 -translate-y-1/2">search</span>
                                <input id="produto-search" type="text" placeholder="Buscar produto…"
                                    class="w-full bg-black/30 border border-white/[0.06] text-white text-xs rounded-full pl-9 pr-4 py-2
                                       focus:outline-none focus:border-secondary/40 placeholder-gray-700 transition-all">
                            </div>
                            <select id="produto-cat-filter"
                                class="bg-black/30 border border-white/[0.06] text-gray-400 text-xs rounded-full px-4 py-2 focus:outline-none focus:border-secondary/40 transition-all">
                                <option value="">Todas as categorias</option>
                                @foreach(\App\Models\Categoria::orderBy('nome')->get() as $cat)
                                    <option value="{{ $cat->nome }}">{{ $cat->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px]">
                            <thead>
                                <tr class="border-b border-white/[0.03]">
                                    <th class="th">Produto</th>
                                    <th class="th">Categoria</th>
                                    <th class="th text-right">Preço</th>
                                    <th class="th text-center">Destaque</th>
                                    <th class="th text-center">Status</th>
                                    <th class="th text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="produtos-tbody" class="divide-y divide-white/[0.03]">
                                @php $todosProdutos = \App\Models\Produto::with('categoria')->latest()->get(); @endphp
                                @forelse($todosProdutos as $produto)
                                    <tr class="hover:bg-white/[0.015] transition-colors" id="produto-row-{{ $produto->id }}"
                                        data-nome="{{ strtolower($produto->nome) }}"
                                        data-cat="{{ $produto->categoria?->nome }}">
                                        <td class="td">
                                            <div class="flex items-center gap-3">
                                                @if($produto->imagem)
                                                    <img src="{{ Str::startsWith($produto->imagem, 'http') ? $produto->imagem : asset('storage/' . $produto->imagem) }}"
                                                        class="w-10 h-10 rounded-full object-cover bg-gray-800 border border-white/10 shrink-0"
                                                        alt="{{ $produto->nome }}">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-gray-900 border border-white/10 flex items-center justify-center shrink-0">
                                                        <span
                                                            class="material-symbols-outlined text-gray-700 text-[18px]">image</span>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-white text-sm truncate">
                                                        {{ $produto->nome }}</p>
                                                    <p class="text-[11px] text-gray-600 truncate max-w-[160px]">
                                                        {{ Str::limit($produto->descricao, 38) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="td text-gray-500 text-xs">{{ $produto->categoria?->nome ?? '—' }}</td>
                                        <td class="td text-right font-bold text-secondary">R$
                                            {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                        <td class="td text-center">
                                            <button class="btn-toggle-destaque hover:scale-110 transition-transform"
                                                title="{{ $produto->destaque ? 'Remover Destaque' : 'Tornar Destaque' }}"
                                                data-id="{{ $produto->id }}"
                                                data-destaque="{{ $produto->destaque ? '1' : '0' }}">
                                                <span id="destaque-icon-{{ $produto->id }}"
                                                    class="material-symbols-outlined text-xl {{ $produto->destaque ? 'text-secondary' : 'text-gray-700' }}"
                                                    style="font-variation-settings:'FILL' {{ $produto->destaque ? '1' : '0' }}">star</span>
                                            </button>
                                        </td>
                                        <td class="td text-center">
                                            <span id="produto-status-{{ $produto->id }}"
                                                class="badge {{ $produto->ativo ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-500' }}">
                                                <span
                                                    class="material-symbols-outlined text-[12px]">{{ $produto->ativo ? 'check_circle' : 'cancel' }}</span>
                                                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td class="td text-center">
                                            <div class="flex items-center gap-2 justify-center">
                                                <button
                                                    class="btn-editar-produto p-1.5 text-gray-600 hover:text-secondary transition-colors rounded-full hover:bg-secondary/10"
                                                    title="Editar" data-id="{{ $produto->id }}"
                                                    data-nome="{{ $produto->nome }}"
                                                    data-descricao="{{ $produto->descricao }}"
                                                    data-preco="{{ $produto->preco }}"
                                                    data-categoria="{{ $produto->categoria_id }}"
                                                    data-ativo="{{ $produto->ativo ? '1' : '0' }}"
                                                    data-destaque="{{ $produto->destaque ? '1' : '0' }}"
                                                    data-imagem="{{ $produto->imagem ? (Str::startsWith($produto->imagem, 'http') ? $produto->imagem : asset('storage/' . $produto->imagem)) : '' }}">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                </button>
                                                <button
                                                    class="btn-toggle-produto px-3 py-1 text-[11px] font-bold border border-white/10 text-gray-500 hover:border-secondary/40 hover:text-secondary rounded-full transition-all"
                                                    data-id="{{ $produto->id }}"
                                                    data-ativo="{{ $produto->ativo ? '1' : '0' }}">
                                                    {{ $produto->ativo ? 'Desativar' : 'Ativar' }}
                                                </button>
                                                <button
                                                    class="btn-deletar-produto p-1.5 text-gray-700 hover:text-red-400 transition-colors rounded-full hover:bg-red-500/10"
                                                    title="Excluir produto" data-id="{{ $produto->id }}"
                                                    data-nome="{{ $produto->nome }}">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-600 text-sm">Nenhum produto
                                            cadastrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════ AVALIAÇÕES ═══════════ --}}
            <div id="section-avaliacoes" class="section animate-fade-up space-y-5">
                {{-- KPIs de avaliações --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                    <div class="kpi-card">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Nota Média</p>
                        <div class="flex items-end gap-2">
                            <p class="text-3xl font-bold text-secondary font-display">
                                {{ number_format($mediaAvaliacoes, 1) }}</p>
                            <div class="flex text-secondary text-lg mb-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span
                                        style="font-variation-settings:'FILL' {{ $i <= round($mediaAvaliacoes) ? '1' : '0' }}"
                                        class="material-symbols-outlined text-[16px]">star</span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-700 mt-1">de 5 estrelas</p>
                    </div>
                    <div class="kpi-card">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Total de
                            Avaliações</p>
                        <p class="text-3xl font-bold text-blue-400 font-display">{{ $totalAvaliacoes }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">De todos os clientes</p>
                    </div>
                    <div class="kpi-card">
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Aguardando
                            Resposta</p>
                        <p
                            class="text-3xl font-bold {{ $avaliacoesSemResposta > 0 ? 'text-amber-400' : 'text-green-400' }} font-display">
                            {{ $avaliacoesSemResposta }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">
                            {{ $avaliacoesSemResposta > 0 ? 'Precisam de atenção' : 'Tudo respondido!' }}</p>
                    </div>
                </div>

                {{-- Tabela de avaliações --}}
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] overflow-hidden">
                    <div
                        class="px-4 md:px-5 py-4 border-b border-white/[0.04] flex flex-wrap items-center gap-3 justify-between">
                        <h2 class="font-bold text-white text-sm">Todas as Avaliações</h2>
                        <div class="flex gap-2">
                            <button
                                class="avaliacao-filter-btn px-3 py-1 text-[11px] font-bold rounded-full border border-secondary text-secondary"
                                data-nota="">Todas</button>
                            @for($nota = 5; $nota >= 1; $nota--)
                                <button
                                    class="avaliacao-filter-btn px-3 py-1 text-[11px] font-bold rounded-full border border-white/10 text-gray-600 hover:border-secondary/40 hover:text-secondary transition-all"
                                    data-nota="{{ $nota }}">{{ $nota }}★</button>
                            @endfor
                        </div>
                    </div>
                    <div id="avaliacoes-container" class="divide-y divide-white/[0.03] min-h-[200px] flex flex-col">
                        <div id="avaliacoes-loading" class="flex items-center justify-center py-16">
                            <div class="flex items-center gap-3 text-gray-600">
                                <span
                                    class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                Carregando avaliações…
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ USUÁRIOS ═══════════ --}}
            <div id="section-usuarios" class="section animate-fade-up">
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] overflow-hidden">
                    <div class="px-4 md:px-5 py-4 border-b border-white/[0.04] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                            <h2 class="font-bold text-white text-sm">Gestão de Usuários</h2>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined text-[16px] text-gray-600 absolute left-3 top-1/2 -translate-y-1/2">search</span>
                                <input id="usuario-search" type="text" placeholder="Buscar usuário / e-mail…"
                                    class="w-full sm:w-56 bg-black/30 border border-white/[0.06] text-white text-xs rounded-full pl-9 pr-4 py-2 focus:outline-none focus:border-secondary/40 placeholder-gray-700 transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px]">
                            <thead>
                                <tr class="border-b border-white/[0.03]">
                                    <th class="th">Usuário</th>
                                    <th class="th">E-mail</th>
                                    <th class="th hidden md:table-cell">Telefone</th>
                                    <th class="th text-center">Pedidos</th>
                                    <th class="th text-center">Perfil</th>
                                    <th class="th text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="usuarios-tbody" class="divide-y divide-white/[0.03]">
                                @php $todosUsuarios = \App\Models\User::withCount('pedidos')->latest()->get(); @endphp
                                @forelse($todosUsuarios as $usuario)
                                    <tr class="hover:bg-white/[0.015] transition-colors"
                                        data-usuario="{{ strtolower($usuario->name . ' ' . $usuario->email) }}">
                                        <td class="td">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-secondary/10 border border-secondary/20 flex items-center justify-center text-secondary font-bold text-xs shrink-0">
                                                    {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                                </div>
                                                <span class="font-semibold text-white text-sm">{{ $usuario->name }}</span>
                                            </div>
                                        </td>
                                        <td class="td text-gray-500 text-xs">{{ $usuario->email }}</td>
                                        <td class="td text-gray-500 text-xs hidden md:table-cell">
                                            {{ $usuario->phone ?? '—' }}</td>
                                        <td class="td text-center font-bold text-gray-300">{{ $usuario->pedidos_count }}
                                        </td>
                                        <td class="td text-center">
                                            <span id="usuario-badge-{{ $usuario->id }}"
                                                class="badge {{ $usuario->is_admin ? 'bg-secondary/10 text-secondary' : 'bg-white/5 text-gray-500' }}">
                                                <span
                                                    class="material-symbols-outlined text-[12px]">{{ $usuario->is_admin ? 'admin_panel_settings' : 'person' }}</span>
                                                {{ $usuario->is_admin ? 'Admin' : 'Cliente' }}
                                            </span>
                                        </td>
                                        <td class="td text-center">
                                            @if($usuario->id !== Auth::id())
                                                <button
                                                    class="btn-toggle-admin px-3 py-1 text-[11px] font-bold border border-white/10 text-gray-500 hover:border-secondary/40 hover:text-secondary rounded-full transition-all"
                                                    data-id="{{ $usuario->id }}"
                                                    data-admin="{{ $usuario->is_admin ? '1' : '0' }}">
                                                    {{ $usuario->is_admin ? 'Remover Admin' : 'Tornar Admin' }}
                                                </button>
                                            @else
                                                <span class="text-[11px] text-gray-700 italic">Você</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-600 text-sm">Nenhum usuário.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

        <footer class="px-6 py-4 text-center text-[11px] text-gray-800 border-t border-white/[0.03]">
            © {{ now()->year }} Excelência Doces & Salgados — Painel Administrativo
        </footer>
    </div>

    {{-- ═══════════════════════════════ MODAIS ═══════════════════════════════ --}}

    {{-- MODAL: Alterar Status --}}
    <div id="modal-status"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-sm shadow-2xl">
            <div class="p-5 border-b border-white/[0.06] flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white text-sm">Alterar Status</h3>
                    <p class="text-[11px] text-gray-600 mt-0.5">Pedido <span id="modal-status-id"
                            class="text-secondary font-bold"></span></p>
                </div>
                <button id="modal-status-close"
                    class="text-gray-600 hover:text-white transition-colors p-1 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-4 grid grid-cols-2 gap-2">
                @php $statusList = ['pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado']; @endphp
                @foreach($statusList as $s)
                    <button
                        class="btn-status-choice px-3 py-2.5 rounded-xl border border-white/[0.06] text-xs font-semibold text-gray-500 hover:border-secondary/40 hover:text-secondary transition-all text-left"
                        data-status="{{ $s }}">
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL: Detalhes do Pedido --}}
    <div id="modal-order-detail"
        class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div
            class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-white/[0.06] flex items-center justify-between shrink-0">
                <h3 class="font-bold text-white text-sm">Detalhes do Pedido</h3>
                <button id="modal-order-detail-close"
                    class="text-gray-600 hover:text-white transition-colors p-1 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div id="order-detail-content" class="overflow-y-auto p-5 flex-1"></div>
        </div>
    </div>

    {{-- MODAL: Produto (Novo / Editar) --}}
    <div id="modal-produto"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div
            class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-xl shadow-2xl flex flex-col max-h-[92vh]">
            <div class="p-5 border-b border-white/[0.06] flex items-center justify-between shrink-0">
                <div>
                    <h3 id="modal-produto-titulo" class="font-bold text-white text-base">Novo Produto</h3>
                    <p class="text-[11px] text-gray-600 mt-0.5">Preencha os dados do produto</p>
                </div>
                <button id="modal-produto-close"
                    class="text-gray-600 hover:text-white transition-colors p-1 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="overflow-y-auto p-5">
                <form id="form-produto" class="space-y-4">
                    <input type="hidden" id="produto-edit-id" name="_edit_id" value="">
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label
                                class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Nome
                                *</label>
                            <input id="f-nome" name="nome" type="text" required placeholder="Ex: Coxinha de Frango"
                                class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                        </div>
                        <div class="sm:col-span-2">
                            <label
                                class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Descrição
                                *</label>
                            <textarea id="f-descricao" name="descricao" rows="3" required
                                placeholder="Descreva o produto…"
                                class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700 resize-none"></textarea>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Preço
                                (R$) *</label>
                            <input id="f-preco" name="preco" type="number" step="0.01" min="0" required
                                placeholder="0.00"
                                class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Categoria
                                *</label>
                            <select id="f-categoria" name="categoria_id" required
                                class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40">
                                <option value="">Selecione…</option>
                                @foreach(\App\Models\Categoria::orderBy('nome')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Preview + Upload --}}
                        <div class="sm:col-span-2">
                            <label
                                class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Imagem
                                do Produto</label>
                            <div class="flex items-center gap-4">
                                <div id="img-preview-wrapper"
                                    class="w-16 h-16 rounded-full bg-gray-900 border-2 border-dashed border-white/10 flex items-center justify-center shrink-0 overflow-hidden">
                                    <span id="img-preview-placeholder"
                                        class="material-symbols-outlined text-gray-700 text-[22px]">add_photo_alternate</span>
                                    <img id="img-preview" class="hidden w-full h-full object-cover" src="" alt="">
                                </div>
                                <label class="flex-1 cursor-pointer">
                                    <div
                                        class="w-full bg-black/30 border border-dashed border-white/10 hover:border-secondary/40 text-gray-600 hover:text-secondary text-xs rounded-xl px-4 py-3 flex items-center gap-2 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">upload</span>
                                        <span id="img-label-text">Clique para escolher foto…</span>
                                    </div>
                                    <input id="f-imagem" name="imagem" type="file" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                        {{-- Toggles --}}
                        <div
                            class="flex items-center gap-4 bg-black/20 border border-white/[0.06] rounded-xl px-4 py-3">
                            <span class="material-symbols-outlined text-gray-600 text-[18px]">check_circle</span>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-300">Produto Ativo</p>
                                <p class="text-[10px] text-gray-600">Visível no cardápio</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="f-ativo" name="ativo" value="1" checked class="sr-only">
                                <div class="toggle-track"></div>
                                <div class="toggle-thumb"></div>
                            </label>
                        </div>
                        <div
                            class="flex items-center gap-4 bg-black/20 border border-white/[0.06] rounded-xl px-4 py-3">
                            <span class="material-symbols-outlined text-gray-600 text-[18px]">star</span>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-300">Em Destaque</p>
                                <p class="text-[10px] text-gray-600">Exibido na home</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="f-destaque" name="destaque" value="1" class="sr-only">
                                <div class="toggle-track"></div>
                                <div class="toggle-thumb"></div>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" id="modal-produto-cancel"
                            class="px-5 py-2.5 text-xs font-bold text-gray-600 hover:text-white border border-white/10 hover:border-white/25 rounded-full transition-colors">Cancelar</button>
                        <button type="submit" id="btn-salvar-produto"
                            class="px-6 py-2.5 bg-secondary text-primary text-xs font-bold rounded-full hover:bg-[#c2884a] transition-all shadow-md">Salvar
                            Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Gerenciar Categorias --}}
    <div id="modal-categoria"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-md shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-white/[0.06] flex items-center justify-between shrink-0">
                <h3 class="font-bold text-white text-sm">Gerenciar Categorias</h3>
                <button id="modal-categoria-close"
                    class="text-gray-600 hover:text-white transition-colors p-1 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-5 flex-1 overflow-y-auto">
                <ul class="space-y-2 mb-6" id="lista-categorias-modal">
                    @foreach(\App\Models\Categoria::orderBy('nome')->get() as $cat)
                        <li class="flex items-center justify-between bg-black/20 border border-white/[0.06] rounded-xl px-4 py-2" id="categoria-row-{{ $cat->id }}">
                            <span class="text-sm text-gray-300 font-semibold">{{ $cat->nome }}</span>
                            <button class="btn-deletar-categoria text-gray-600 hover:text-red-400 p-1.5 rounded-full hover:bg-red-500/10 transition-colors" data-id="{{ $cat->id }}" data-nome="{{ $cat->nome }}" title="Excluir Categoria">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-white/[0.06] pt-5">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-2">Nova Categoria</label>
                    <div class="flex gap-2">
                        <input id="f-cat-nome" type="text" placeholder="Ex: Bolos Artesanais"
                            class="flex-1 bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                        <button id="btn-salvar-categoria"
                            class="px-5 py-2 bg-secondary text-primary text-xs font-bold rounded-xl hover:bg-[#c2884a] transition-all whitespace-nowrap shadow-md">Criar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Responder Avaliação --}}
    <div id="modal-avaliacao"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-md shadow-2xl">
            <div class="p-5 border-b border-white/[0.06] flex items-center justify-between">
                <h3 class="font-bold text-white text-sm">Responder Avaliação</h3>
                <button id="modal-avaliacao-close"
                    class="text-gray-600 hover:text-white transition-colors p-1 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div id="avaliacao-detalhes" class="bg-black/20 rounded-xl p-4 space-y-2"></div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Sua
                        Resposta *</label>
                    <textarea id="f-resposta-avaliacao" rows="3" placeholder="Escreva sua resposta pública…"
                        class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button id="modal-avaliacao-cancel"
                        class="px-4 py-2 text-xs font-bold text-gray-600 border border-white/10 rounded-full hover:text-white transition-colors">Cancelar</button>
                    <button id="btn-enviar-resposta"
                        class="px-5 py-2 bg-secondary text-primary text-xs font-bold rounded-full hover:bg-[#c2884a] transition-all">Enviar
                        Resposta</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>