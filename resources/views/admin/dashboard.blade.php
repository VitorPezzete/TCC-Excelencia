<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#c2884a">
    <meta name="vapid-pub-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>Excelência - Admin Dashboard</title>
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/admin.js', 'resources/js/accessibility.js'])

    {{-- Dados para gráficos (acessados pelo admin.js via window) --}}
    <script>
        window.adminData = {
            fat7: @json($faturamento7Dias),
            fat30: @json($faturamento30Dias),
            status: @json($pedidosPorStatus),
            catFat: @json($faturamentoPorCategoria),
            topProd: @json($topProdutos),
            vol7d: @json($volumePedidos7Dias),
            vol30d: @json($volumePedidos30Dias),
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

        span.status-pill {
            cursor: default;
        }

        .pedido-tab-btn {
            color: #6b7280;
            background: transparent;
            border: none;
            white-space: nowrap;
        }

        .pedido-tab-btn.active-tab {
            background: rgba(214, 156, 94, 0.12);
            color: #d69c5e;
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
            <div class="w-10 h-10 rounded-full border border-secondary/20 flex items-center justify-center shrink-0 overflow-hidden">
                <img src="/images/logo.png" alt="Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <p class="font-display font-bold text-secondary text-base leading-tight">Excelência</p>
                <p class="text-[10px] text-gray-700 tracking-widest uppercase">Dashboard</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex flex-col gap-1 px-3 py-4 flex-1 overflow-y-auto">
            <p class="px-4 pb-1.5 text-[9px] font-bold text-gray-700 uppercase tracking-widest">Relatórios</p>
            <button class="sidebar-link" data-section="overview">
                <span class="material-symbols-outlined text-[19px]">dashboard</span> Visão Geral
            </button>
            <button class="sidebar-link" data-section="avaliacoes">
                <span class="material-symbols-outlined text-[19px]">star</span> Avaliações
            </button>

            <p class="px-4 pt-4 pb-1.5 text-[9px] font-bold text-gray-700 uppercase tracking-widest border-t border-white/[0.04]">Gestão</p>
            <button class="sidebar-link active" data-section="pedidos">
                <span class="material-symbols-outlined text-[19px]">receipt_long</span> Pedidos
            </button>
            <button class="sidebar-link" data-section="produtos">
                <span class="material-symbols-outlined text-[19px]">cake</span> Produtos
            </button>
            <button class="sidebar-link" data-section="entrega">
                <span class="material-symbols-outlined text-[19px]">local_shipping</span> Zonas de Entrega
            </button>

            <p class="px-4 pt-4 pb-1.5 text-[9px] font-bold text-gray-700 uppercase tracking-widest border-t border-white/[0.04]">Configurações</p>
            <button class="sidebar-link" data-section="configuracoes">
                <span class="material-symbols-outlined text-[19px]">schedule</span> Horário
            </button>
        </nav>

        {{-- User box --}}
        <div class="p-4 border-t border-white/[0.04] space-y-3">
            <div class="flex items-center justify-between bg-white/[0.02] border border-white/[0.04] rounded-lg px-3 py-2">
                <div class="flex flex-col gap-0.5 min-w-0">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Status da Loja</span>
                    <span class="text-[11px] font-bold {{ $storeIsOpen ? 'text-green-400' : 'text-red-400' }}" id="sidebar-store-text">
                        <span id="sidebar-store-dot" class="inline-block w-1.5 h-1.5 rounded-full {{ $storeIsOpen ? 'bg-green-400' : 'bg-red-500' }} mr-1"></span>
                        {{ $storeIsOpen ? 'Aberta agora' : 'Fechada agora' }}
                    </span>
                    <span class="text-[9px] text-gray-700" id="sidebar-store-label">{{ \App\Helpers\StoreHelper::hoursLabel() }}</span>
                </div>
            </div>
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
                        class="font-display text-lg md:text-xl font-bold text-white leading-tight truncate">Pedidos
                    </h1>
                    <p id="page-subtitle" class="text-[11px] text-gray-600 hidden sm:block">Pedidos ativos de hoje
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

            {{-- ═══════════ OVERVIEW (oculto) ═══════════ --}}
            <div id="section-overview" class="section animate-fade-up space-y-5">

                {{-- KPI CARDS --}}
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4">
                    <div class="kpi-card col-span-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-secondary/5 rounded-full -translate-y-8 translate-x-8"></div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Faturamento / Mês</p>
                        <p class="text-lg xl:text-xl font-bold text-secondary font-display">R$ {{ number_format($totalFaturamentoMes, 2, ',', '.') }}</p>
                        <div class="flex items-center gap-1 mt-1 text-[10px]">
                            @if($variacaoFat >= 0)
                                <span class="text-green-400 font-bold">▲ {{ number_format($variacaoFat, 1, ',', '.') }}%</span>
                            @else
                                <span class="text-red-400 font-bold">▼ {{ number_format(abs($variacaoFat), 1, ',', '.') }}%</span>
                            @endif
                            <span class="text-gray-700">vs. mês ant.</span>
                        </div>
                        <span class="material-symbols-outlined text-[36px] text-secondary/8 absolute bottom-3 right-3">payments</span>
                    </div>

                    <div class="kpi-card col-span-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full -translate-y-8 translate-x-8"></div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Pedidos Hoje</p>
                        <p id="kpi-pedidos-hoje" class="text-lg xl:text-xl font-bold text-blue-400 font-display">{{ $pedidosHoje }}</p>
                        <div class="flex items-center gap-1 mt-1 text-[10px]">
                            @if($variacaoPedidos >= 0)
                                <span class="text-green-400 font-bold">▲ {{ number_format($variacaoPedidos, 1, ',', '.') }}%</span>
                            @else
                                <span class="text-red-400 font-bold">▼ {{ number_format(abs($variacaoPedidos), 1, ',', '.') }}%</span>
                            @endif
                            <span class="text-gray-700">vs. ontem</span>
                        </div>
                        <span class="material-symbols-outlined text-[36px] text-blue-400/8 absolute bottom-3 right-3">shopping_bag</span>
                    </div>

                    <div class="kpi-card col-span-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-full -translate-y-8 translate-x-8"></div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Ticket Médio</p>
                        <p class="text-lg xl:text-xl font-bold text-green-400 font-display">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">Este mês</p>
                        <span class="material-symbols-outlined text-[36px] text-green-400/8 absolute bottom-3 right-3">receipt</span>
                    </div>

                    <div class="kpi-card col-span-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-red-500/5 rounded-full -translate-y-8 translate-x-8"></div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Cancelamentos</p>
                        <p class="text-lg xl:text-xl font-bold text-red-400 font-display">{{ number_format($taxaCancelamento, 1, ',', '.') }}%</p>
                        <p class="text-[10px] text-gray-700 mt-1">Taxa este mês</p>
                        <span class="material-symbols-outlined text-[36px] text-red-400/8 absolute bottom-3 right-3">cancel</span>
                    </div>

                    <div class="kpi-card col-span-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-full -translate-y-8 translate-x-8"></div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Novos Clientes</p>
                        <p class="text-lg xl:text-xl font-bold text-orange-400 font-display">{{ $novosClientesMes }}</p>
                        <div class="flex items-center gap-1 mt-1 text-[10px]">
                            @if($variacaoClientes >= 0)
                                <span class="text-green-400 font-bold">▲ {{ number_format($variacaoClientes, 1, ',', '.') }}%</span>
                            @else
                                <span class="text-red-400 font-bold">▼ {{ number_format(abs($variacaoClientes), 1, ',', '.') }}%</span>
                            @endif
                            <span class="text-gray-700">vs. mês ant.</span>
                        </div>
                        <span class="material-symbols-outlined text-[36px] text-orange-400/8 absolute bottom-3 right-3">person_add</span>
                    </div>

                    <div class="kpi-card col-span-1">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-full -translate-y-8 translate-x-8"></div>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mb-2">Nota Média</p>
                        <p class="text-lg xl:text-xl font-bold text-purple-400 font-display">{{ number_format($mediaAvaliacoes, 1) }}</p>
                        <p class="text-[10px] text-gray-700 mt-1">{{ $totalAvaliacoes }} avaliações</p>
                        <span class="material-symbols-outlined text-[36px] text-purple-400/8 absolute bottom-3 right-3">star</span>
                    </div>
                </div>

                {{-- GRÁFICOS --}}
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    {{-- Faturamento e Volume (7/30 dias) --}}
                    <div class="xl:col-span-2 bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-4 md:p-5">
                        <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
                            <div>
                                <h2 class="font-bold text-white text-sm">Faturamento e Volume</h2>
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

                {{-- Top Produtos --}}
                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-4 md:p-5">
                    <h2 class="font-bold text-white text-sm mb-1">Top 5 Produtos Mais Vendidos</h2>
                    <p class="text-[11px] text-gray-600 mb-4">Pratos favoritos pelo público</p>
                    <canvas id="chartTopProdutos" height="100"></canvas>
                </div>
            </div>

            {{-- ═══════════ PEDIDOS ═══════════ --}}
            <div id="section-pedidos" class="section active animate-fade-up space-y-4">

                <div id="alert-pedidos-atrasados" class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 flex items-center justify-between gap-4 animate-pulse {{ $pedidosAtrasados > 0 ? '' : 'hidden' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-red-400 text-[24px]">warning</span>
                        <div>
                            <p class="text-red-400 font-bold text-sm">Atenção: <span id="count-atrasados">{{ $pedidosAtrasados }}</span> pedido(s) pendente(s) há mais de 5 minutos!</p>
                            <p class="text-red-400/80 text-[11px]">Verifique a aba de Ativos Hoje e aceite os pedidos para não atrasar a produção.</p>
                        </div>
                    </div>
                </div>

                {{-- Abas: Ativos / Histórico --}}
                <div class="flex gap-1 bg-black/30 border border-white/[0.04] rounded-2xl p-1 w-fit">
                    <button id="tab-ativos"
                        class="pedido-tab-btn active-tab px-5 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">bolt</span> Ativos Hoje
                        @php $ativos = $pedidosHoje_lista->count(); @endphp
                        @if($ativos > 0)
                            <span class="bg-secondary/20 text-secondary px-2 py-0.5 rounded-full text-[10px]">{{ $ativos }}</span>
                        @endif
                    </button>
                    <button id="tab-historico"
                        class="pedido-tab-btn px-5 py-2 text-xs font-bold text-gray-500 rounded-xl transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">history</span> Histórico
                    </button>
                </div>

                {{-- PAINEL: Pedidos Ativos --}}
                <div id="painel-ativos" class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04]">
                    <div class="px-4 md:px-5 py-4 border-b border-white/[0.04] flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                        <div>
                            <h2 class="font-bold text-white text-sm">Pedidos de Hoje</h2>
                            <p class="text-[11px] text-gray-600">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined text-[16px] text-gray-600 absolute left-3 top-1/2 -translate-y-1/2">search</span>
                            <input id="pedido-search" type="text" placeholder="Buscar cliente…"
                                class="w-full sm:w-52 bg-black/30 border border-white/[0.06] text-white text-xs rounded-full pl-9 pr-4 py-2 focus:outline-none focus:border-secondary/40 placeholder-gray-700 transition-all">
                        </div>
                    </div>
                    {{-- Filtro pílulas de status (somente ativos) --}}
                    <div class="px-4 md:px-5 py-3 border-b border-white/[0.03] flex gap-2 flex-wrap">
                        @php
                            $statusAtivos = [
                                ''=>'Todos',
                                'aguardando_pagamento'=>'Aguardando Pagto',
                                'pendente'=>'Pendente',
                                'confirmado'=>'Confirmado',
                                'preparando'=>'Preparando',
                                'saiu_para_entrega'=>'Saiu p/ Entrega',
                                'entregue'=>'Entregues',
                                'cancelado'=>'Cancelados'
                            ];
                            $countAtivos = $pedidosHoje_lista->groupBy('status')->map->count();
                        @endphp
                        @foreach($statusAtivos as $val => $label)
                            <button class="status-filter-btn inline-flex items-center gap-1 px-3 py-1 text-[11px] font-bold rounded-full border transition-all {{ $val==='' ? 'border-secondary text-secondary' : 'border-white/10 text-gray-600 hover:border-white/25 hover:text-gray-300' }}" data-status="{{ $val }}">
                                {{ $label }}
                                @if($val !== '' && isset($countAtivos[$val]))
                                    <span class="bg-white/10 px-1.5 py-0.5 rounded-full text-[9px]">{{ $countAtivos[$val] }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px]">
                            <thead>
                                <tr class="border-b border-white/[0.03]">
                                    <th class="th">Pedido</th>
                                    <th class="th">Cliente</th>
                                    <th class="th">Hora</th>
                                    <th class="th">Pagamento</th>
                                    <th class="th">Status</th>
                                    <th class="th text-center">Detalhes</th>
                                    <th class="th text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="pedidos-body" class="divide-y divide-white/[0.03]">
                                @forelse($pedidosHoje_lista as $pedido)
                                    @include('admin._pedido_row', ['pedido' => $pedido, 'showDate' => true])
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-14 text-gray-700 text-sm">
                                            <span class="material-symbols-outlined text-[38px] block mb-2 text-gray-800">coffee</span>
                                            Nenhum pedido hoje ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAINEL: Histórico --}}
                <div id="painel-historico" class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] hidden">
                    <div class="px-4 md:px-5 py-4 border-b border-white/[0.04] flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                        <div>
                            <h2 class="font-bold text-white text-sm">Histórico de Pedidos</h2>
                            <p class="text-[11px] text-gray-600">Pedidos finalizados — apenas visualização</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="flex items-center gap-1.5 bg-black/30 border border-white/[0.06] rounded-full px-3 py-1.5">
                                <span class="material-symbols-outlined text-gray-600 text-[14px]">calendar_today</span>
                                <input id="filtro-data-ini" type="date" class="bg-transparent text-[11px] text-gray-400 focus:outline-none focus:text-white">
                            </div>
                            <span class="text-gray-700 text-xs">até</span>
                            <div class="flex items-center gap-1.5 bg-black/30 border border-white/[0.06] rounded-full px-3 py-1.5">
                                <span class="material-symbols-outlined text-gray-600 text-[14px]">calendar_today</span>
                                <input id="filtro-data-fim" type="date" class="bg-transparent text-[11px] text-gray-400 focus:outline-none focus:text-white">
                            </div>
                            <button id="btn-limpar-data" class="text-[11px] text-gray-600 hover:text-secondary transition-colors">Limpar</button>
                        </div>
                    </div>
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
                            <tbody id="historico-body" class="divide-y divide-white/[0.03]">
                                @forelse($pedidosHistorico as $pedido)
                                    @include('admin._pedido_row', ['pedido' => $pedido, 'showDate' => true, 'readonly' => true])
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-14 text-gray-700 text-sm">
                                            <span class="material-symbols-outlined text-[38px] block mb-2 text-gray-800">inventory_2</span>
                                            Nenhum pedido no histórico.
                                        </td>
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
                                                {{--
                                                <button
                                                    class="btn-deletar-produto p-1.5 text-gray-700 hover:text-red-400 transition-colors rounded-full hover:bg-red-500/10"
                                                    title="Excluir produto" data-id="{{ $produto->id }}"
                                                    data-nome="{{ $produto->nome }}">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                                --}}
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

            {{-- ═══════════ AVALIAÇÕES (oculto) ═══════════ --}}
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

            {{-- ═══════════ ZONAS DE ENTREGA ═══════════ --}}
            <div id="section-entrega" class="section animate-fade-up space-y-5">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-white text-lg">Zonas de Entrega</h2>
                        <p class="text-[11px] text-gray-600 mt-0.5">Defina bairros e taxas de frete por zona</p>
                    </div>
                    <button id="btn-nova-zona"
                        class="flex items-center gap-1.5 px-4 py-2 bg-secondary text-primary text-[12px] font-bold rounded-full hover:bg-[#c2884a] transition-all shadow-md">
                        <span class="material-symbols-outlined text-[16px]">add</span> Nova Zona
                    </button>
                </div>

                <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[520px]">
                            <thead>
                                <tr class="border-b border-white/[0.03]">
                                    <th class="th">Zona</th>
                                    <th class="th">Bairros Cobertos</th>
                                    <th class="th text-center">Frete Grátis Acima</th>
                                    <th class="th text-right">Taxa</th>
                                    <th class="th text-center">Status</th>
                                    <th class="th text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="zonas-tbody" class="divide-y divide-white/[0.03]">
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-gray-600 text-sm">
                                        <span class="material-symbols-outlined text-[30px] block mb-2 text-gray-800">local_shipping</span>
                                        Nenhuma zona cadastrada ainda.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-[#1d0e0b] border border-secondary/20 rounded-2xl p-4 flex items-start gap-3">
                    <span class="material-symbols-outlined text-secondary text-[20px] mt-0.5 shrink-0">info</span>
                    <div class="text-[11px] text-gray-500 space-y-1">
                        <p><span class="text-gray-300 font-bold">Como funciona:</span> Ao fazer um pedido, o sistema identifica o bairro do endereço de entrega e busca a zona correspondente.</p>
                        <p>Se o bairro não estiver em nenhuma zona, o pedido será bloqueado com uma mensagem ao cliente.</p>
                        <p><span class="text-secondary font-bold">Frete Grátis:</span> Se o subtotal atingir o valor mínimo configurado na zona, a taxa será automaticamente zerada.</p>
                    </div>
                </div>
            </div>

            {{-- ═══════════ CONFIGURAÇÕES (Horário) ═══════════ --}}
            <div id="section-configuracoes" class="section animate-fade-up">
                <div class="max-w-xl">
                    <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-5 md:p-6 space-y-6">
                        <div>
                            <h2 class="font-bold text-white text-base">Horário de Funcionamento</h2>
                            <p class="text-[11px] text-gray-600 mt-0.5">Defina os dias e horários em que a loja aceita pedidos. As alterações têm efeito imediato em todas as páginas.</p>
                        </div>

                        {{-- Status atual --}}
                        <div id="cfg-status-badge" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-bold
                            {{ $storeIsOpen ? 'bg-green-500/10 border-green-500/30 text-green-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }}">
                            <span class="w-2 h-2 rounded-full {{ $storeIsOpen ? 'bg-green-400' : 'bg-red-500' }}"></span>
                            <span id="cfg-status-text">{{ $storeIsOpen ? 'Loja aberta agora' : 'Loja fechada agora' }}</span>
                            <span class="ml-auto text-[10px] font-normal text-gray-500" id="cfg-hours-label">{{ \App\Helpers\StoreHelper::hoursLabel() }}</span>
                        </div>

                        <form id="form-schedule" class="space-y-5">
                            @csrf
                            {{-- Dias da semana --}}
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Dias de Atendimento</p>
                                @php
                                    $schedule = \App\Helpers\StoreHelper::getSchedule();
                                    $dayNames = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
                                @endphp
                                <div class="flex flex-wrap gap-2">
                                    @foreach($dayNames as $idx => $day)
                                        <label class="day-toggle-label cursor-pointer">
                                            <input type="checkbox" name="days[]" value="{{ $idx }}"
                                                class="sr-only day-checkbox"
                                                {{ in_array($idx, $schedule['days']) ? 'checked' : '' }}>
                                            <span class="day-pill inline-flex items-center justify-center px-4 py-2 rounded-full text-xs font-bold border transition-all select-none
                                                {{ in_array($idx, $schedule['days']) ? 'bg-secondary/15 border-secondary text-secondary' : 'bg-white/[0.03] border-white/10 text-gray-600' }}">
                                                {{ $day }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Horários --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Abertura</label>
                                    <input type="time" name="open" id="cfg-open"
                                        value="{{ $schedule['open'] }}"
                                        class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Fechamento</label>
                                    <input type="time" name="close" id="cfg-close"
                                        value="{{ $schedule['close'] }}"
                                        class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40">
                                </div>
                            </div>

                            <button type="submit" id="btn-salvar-schedule"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-secondary text-primary text-sm font-bold rounded-xl hover:bg-[#c2884a] transition-all shadow-md shadow-secondary/10">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Salvar Horário
                            </button>
                        </form>

                        <div class="bg-secondary/5 border border-secondary/15 rounded-xl p-4 flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary text-[18px] mt-0.5 shrink-0">info</span>
                            <p class="text-[11px] text-gray-500">Ao salvar, o status da loja é atualizado imediatamente. Clientes em outras páginas verão o novo status em até 30 segundos (polling automático).</p>
                        </div>
                    </div>

                    {{-- Web Push Notifications --}}
                    <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-5 md:p-6 space-y-4 mt-6">
                        <div>
                            <h2 class="font-bold text-white text-base">Notificações em Segundo Plano</h2>
                            <p class="text-[11px] text-gray-600 mt-0.5">Receba alertas de novos pedidos mesmo quando a aba do navegador estiver fechada ou o celular bloqueado.</p>
                        </div>
                        
                        <div class="bg-black/30 border border-white/[0.06] rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-blue-400 text-3xl">notifications_active</span>
                                <div>
                                    <p class="text-sm font-bold text-gray-200" id="push-status-text">Status: Desconhecido</p>
                                    <p class="text-[10px] text-gray-500">Requer permissão do navegador</p>
                                </div>
                            </div>
                            <button id="btn-subscribe-push" class="px-5 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-full hover:bg-blue-500 transition-all shadow-md w-full sm:w-auto">
                                Ativar Neste Dispositivo
                            </button>
                        </div>

                        <div class="text-[10px] text-gray-600 space-y-1">
                            <p><strong>Dica:</strong> Se usar iPhone, adicione este site à sua "Tela de Início" antes de ativar.</p>
                            <p><strong>Privacidade:</strong> Apenas pedidos novos acionam notificações. Cada dispositivo/celular precisa ser ativado individualmente aqui.</p>
                        </div>
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
                    @foreach(\App\Models\Categoria::orderBy('ordem')->orderBy('nome')->get() as $cat)
                        <li class="flex items-center justify-between bg-black/20 border border-white/[0.06] rounded-xl px-4 py-2" id="categoria-row-{{ $cat->id }}" data-id="{{ $cat->id }}">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-gray-600 hover:text-white cursor-move drag-handle transition-colors">drag_indicator</span>
                                <span class="text-sm text-gray-300 font-semibold" id="cat-nome-{{ $cat->id }}">{{ $cat->nome }}</span>
                                <span class="badge {{ $cat->ativo ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400' }} ml-2" id="cat-badge-{{ $cat->id }}">
                                    <span class="material-symbols-outlined text-[10px] mr-1">{{ $cat->ativo ? 'check_circle' : 'cancel' }}</span>
                                    {{ $cat->ativo ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <button class="btn-editar-categoria text-gray-600 hover:text-secondary p-1.5 rounded-full hover:bg-secondary/10 transition-colors" data-id="{{ $cat->id }}" data-nome="{{ $cat->nome }}" title="Editar Categoria">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <button class="btn-toggle-categoria text-gray-600 hover:text-yellow-400 p-1.5 rounded-full hover:bg-yellow-400/10 transition-colors" data-id="{{ $cat->id }}" data-ativo="{{ $cat->ativo }}" title="{{ $cat->ativo ? 'Desativar Categoria' : 'Ativar Categoria' }}">
                                    <span class="material-symbols-outlined text-[18px]" id="cat-toggle-icon-{{ $cat->id }}">{{ $cat->ativo ? 'visibility_off' : 'visibility' }}</span>
                                </button>
                                <button class="btn-deletar-categoria text-gray-600 hover:text-red-400 p-1.5 rounded-full hover:bg-red-500/10 transition-colors" data-id="{{ $cat->id }}" data-nome="{{ $cat->nome }}" title="Excluir Categoria">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-white/[0.06] pt-5">
                    <label id="lbl-nova-categoria" class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-2">Nova Categoria</label>
                    <input type="hidden" id="f-cat-id" value="">
                    <div class="flex gap-2">
                        <input id="f-cat-nome" type="text" placeholder="Ex: Bolos Artesanais"
                            class="flex-1 bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                        <button id="btn-cancelar-categoria" class="hidden px-4 py-2 text-gray-500 hover:text-white transition-colors text-xs font-bold rounded-xl border border-white/10">Cancelar</button>
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

    {{-- MODAL: Confirmação Customizada --}}
    <div id="modal-confirm" class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-sm shadow-2xl animate-fade-up">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-500 text-3xl">warning</span>
                </div>
                <h3 class="font-bold text-white text-lg mb-2">Atenção!</h3>
                <p id="modal-confirm-msg" class="text-sm text-gray-400 mb-6">Você tem certeza disso?</p>
                <div class="flex items-center gap-3 w-full">
                    <button id="modal-confirm-cancel" class="flex-1 px-4 py-2.5 text-xs font-bold text-gray-400 bg-white/5 border border-white/10 rounded-xl hover:text-white hover:bg-white/10 transition-all">Cancelar</button>
                    <button id="modal-confirm-ok" class="flex-1 px-4 py-2.5 text-xs font-bold text-white bg-red-500/80 hover:bg-red-500 rounded-xl transition-all shadow-lg shadow-red-500/20">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Acessibilidade --}}
    @include('partials.accessibility')

    {{-- MODAL: Zona de Entrega (Criar / Editar) --}}
    <div id="modal-zona" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-[#120601] rounded-2xl border border-white/[0.06] w-full max-w-lg shadow-2xl flex flex-col max-h-[92vh]">
            <div class="p-5 border-b border-white/[0.06] flex items-center justify-between shrink-0">
                <div>
                    <h3 id="modal-zona-titulo" class="font-bold text-white text-base">Nova Zona de Entrega</h3>
                    <p class="text-[11px] text-gray-600 mt-0.5">Configure os bairros e a taxa de frete</p>
                </div>
                <button id="modal-zona-close" class="text-gray-600 hover:text-white transition-colors p-1 hover:bg-white/5 rounded-full">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="overflow-y-auto p-5">
                <form id="form-zona" class="space-y-4">
                    <input type="hidden" id="zona-edit-id" value="">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Nome da Zona *</label>
                        <input id="f-zona-nome" type="text" required placeholder="Ex: Zona Centro"
                            class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Taxa de Entrega (R$) *</label>
                            <input id="f-zona-taxa" type="number" step="0.01" min="0" required placeholder="10.00"
                                class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Frete Grátis acima de (R$)</label>
                            <input id="f-zona-frete-gratis" type="number" step="0.01" min="0" placeholder="Opcional"
                                class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1.5">Bairros Cobertos *</label>
                        <p class="text-[10px] text-gray-600 mb-2">Digite um bairro por linha. A comparação ignora maiúsculas/minúsculas.</p>
                        <textarea id="f-zona-bairros" rows="5" required placeholder="Centro&#10;Jardim América&#10;Vila Nova&#10;Ipiranga"
                            class="w-full bg-black/30 border border-white/[0.08] text-white text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-secondary/40 placeholder-gray-700 resize-none font-mono"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" id="modal-zona-cancel"
                            class="px-5 py-2.5 text-xs font-bold text-gray-600 hover:text-white border border-white/10 hover:border-white/25 rounded-full transition-colors">Cancelar</button>
                        <button type="submit" id="btn-salvar-zona"
                            class="px-6 py-2.5 bg-secondary text-primary text-xs font-bold rounded-full hover:bg-[#c2884a] transition-all shadow-md">Salvar Zona</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // ── Zonas de Entrega ──
    (function() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const tbody = document.getElementById('zonas-tbody');
        const modal = document.getElementById('modal-zona');
        const fNome = document.getElementById('f-zona-nome');
        const fTaxa = document.getElementById('f-zona-taxa');
        const fFreteGratis = document.getElementById('f-zona-frete-gratis');
        const fBairros = document.getElementById('f-zona-bairros');
        const editId = document.getElementById('zona-edit-id');
        const titulo = document.getElementById('modal-zona-titulo');

        function fmtBRL(v) {
            return parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function renderZonas(zonas) {
            if (!zonas.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-10 text-gray-600 text-sm">
                    <span class="material-symbols-outlined text-[30px] block mb-2 text-gray-800">local_shipping</span>
                    Nenhuma zona cadastrada ainda.
                </td></tr>`;
                return;
            }
            tbody.innerHTML = zonas.map(z => `
                <tr class="hover:bg-white/[0.015] transition-colors" id="zona-row-${z.id}">
                    <td class="td font-semibold text-white">${z.nome}</td>
                    <td class="td text-gray-500 text-xs max-w-xs">
                        <div class="flex flex-wrap gap-1">
                            ${z.bairros.map(b => `<span class="bg-white/5 border border-white/10 px-2 py-0.5 rounded-full text-[10px]">${b}</span>`).join('')}
                        </div>
                    </td>
                    <td class="td text-center text-xs ${z.frete_gratis_acima ? 'text-green-400 font-bold' : 'text-gray-700'}">
                        ${z.frete_gratis_acima ? 'R$ ' + fmtBRL(z.frete_gratis_acima) : '—'}
                    </td>
                    <td class="td text-right font-bold text-secondary">R$ ${fmtBRL(z.taxa)}</td>
                    <td class="td text-center">
                        <span class="badge ${z.ativo ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400'}">
                            <span class="material-symbols-outlined text-[12px]">${z.ativo ? 'check_circle' : 'cancel'}</span>
                            ${z.ativo ? 'Ativa' : 'Inativa'}
                        </span>
                    </td>
                    <td class="td text-center">
                        <div class="flex items-center gap-2 justify-center">
                            <button class="btn-editar-zona p-1.5 text-gray-600 hover:text-secondary transition-colors rounded-full hover:bg-secondary/10"
                                title="Editar" data-id="${z.id}"
                                data-nome="${z.nome}" data-taxa="${z.taxa}"
                                data-frete="${z.frete_gratis_acima ?? ''}"
                                data-bairros="${encodeURIComponent(z.bairros.join('\n'))}">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button class="btn-deletar-zona p-1.5 text-gray-700 hover:text-red-400 transition-colors rounded-full hover:bg-red-500/10"
                                title="Excluir" data-id="${z.id}" data-nome="${z.nome}">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
            bindZonaActions();
        }

        async function loadZonas() {
            const url = '/admin/zonas-entrega?t=' + Date.now();
            const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
            const zonas = await res.json();
            renderZonas(zonas);
        }

        function openModal(editing = false, data = {}) {
            editId.value = data.id ?? '';
            titulo.textContent = editing ? 'Editar Zona de Entrega' : 'Nova Zona de Entrega';
            fNome.value = data.nome ?? '';
            fTaxa.value = data.taxa ?? '';
            fFreteGratis.value = data.frete ?? '';
            fBairros.value = data.bairros ?? '';
            modal.classList.remove('hidden');
        }

        function closeModal() { modal.classList.add('hidden'); }

        document.getElementById('btn-nova-zona')?.addEventListener('click', () => openModal(false));
        document.getElementById('modal-zona-close')?.addEventListener('click', closeModal);
        document.getElementById('modal-zona-cancel')?.addEventListener('click', closeModal);
        modal?.addEventListener('click', e => { if (e.target === modal) closeModal(); });

        document.getElementById('form-zona')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-salvar-zona');
            btn.disabled = true; btn.textContent = 'Salvando…';

            const id = editId.value;
            const bairros = fBairros.value.split('\n').map(b => b.trim()).filter(Boolean);
            const body = JSON.stringify({
                nome: fNome.value.trim(),
                taxa: parseFloat(fTaxa.value),
                bairros,
                frete_gratis_acima: fFreteGratis.value ? parseFloat(fFreteGratis.value) : null,
                ativo: true,
            });

            const url  = id ? `/admin/zonas-entrega/${id}` : '/admin/zonas-entrega';
            const method = id ? 'PUT' : 'POST';
            const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body });

            if (res.ok) {
                closeModal();
                loadZonas();
            } else {
                alert('Erro ao salvar zona. Verifique os dados.');
            }
            btn.disabled = false; btn.textContent = 'Salvar Zona';
        });

        function bindZonaActions() {
            document.querySelectorAll('.btn-editar-zona').forEach(btn => {
                btn.onclick = () => openModal(true, {
                    id: btn.dataset.id,
                    nome: btn.dataset.nome,
                    taxa: btn.dataset.taxa,
                    frete: btn.dataset.frete,
                    bairros: decodeURIComponent(btn.dataset.bairros),
                });
            });
            document.querySelectorAll('.btn-deletar-zona').forEach(btn => {
                btn.onclick = async () => {
                    if (!confirm(`Excluir a zona "${btn.dataset.nome}"?`)) return;
                    const res = await fetch(`/admin/zonas-entrega/${btn.dataset.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                    });
                    if (res.ok) loadZonas();
                };
            });
        }

        // Carregar zonas ao clicar no menu
        document.querySelector('.sidebar-link[data-section="entrega"]')?.addEventListener('click', loadZonas);
    })();
    </script>

    @include('partials.global_alerts')
    
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</body>

</html>
