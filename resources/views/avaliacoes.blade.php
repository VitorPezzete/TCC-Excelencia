<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Excelência — Avaliações dos Clientes</title>
<meta name="description" content="Veja o que nossos clientes dizem sobre os doces e salgados da Excelência. Avaliações reais de quem já pediu.">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #1A0F0E; }
    ::-webkit-scrollbar-thumb { background: #d69c5e; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #c2884a; }
</style>
</head>
<body class="font-body bg-background-dark text-text-light min-h-screen flex flex-col">

@include('header')

<div class="pt-28 pb-10 bg-[#120a08] border-b border-secondary/10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-secondary font-bold tracking-widest uppercase text-sm block mb-3">O Que Dizem Nossos Clientes</span>
        <h1 class="font-display text-4xl md:text-5xl font-bold text-white mb-4">Avaliações</h1>
        <p class="text-gray-400 max-w-xl mx-auto">Opiniões reais de clientes que já experimentaram nossas delícias.</p>

        @if($mediaGeral > 0)
        <div class="mt-6 inline-flex items-center gap-3 bg-secondary/10 border border-secondary/20 px-6 py-3 rounded-full">
            <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                    <span class="material-symbols-outlined text-secondary text-xl"
                        style="font-variation-settings:'FILL' {{ $i <= round($mediaGeral) ? '1' : '0' }}">star</span>
                @endfor
            </div>
            <span class="text-secondary font-bold text-xl">{{ number_format($mediaGeral, 1) }}</span>
            <span class="text-gray-400 text-sm">/ 5 — {{ $totalAvaliacoes }} {{ $totalAvaliacoes === 1 ? 'avaliação' : 'avaliações' }}</span>
        </div>
        @endif
    </div>
</div>

<main class="flex-1 py-16 bg-background-dark">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-wrap gap-2 mb-10 justify-center">
            <a href="{{ route('avaliacoes.publicas') }}"
               class="px-4 py-1.5 text-sm font-bold rounded-full border transition-all {{ !request('nota') ? 'bg-secondary text-primary border-secondary' : 'border-white/10 text-gray-400 hover:border-secondary/40 hover:text-secondary' }}">
                Todas
            </a>
            @foreach([5,4,3,2,1] as $nota)
                <a href="{{ route('avaliacoes.publicas', ['nota' => $nota]) }}"
                   class="flex items-center gap-1 px-4 py-1.5 text-sm font-bold rounded-full border transition-all {{ request('nota') == $nota ? 'bg-secondary text-primary border-secondary' : 'border-white/10 text-gray-400 hover:border-secondary/40 hover:text-secondary' }}">
                    {{ $nota }} <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">star</span>
                </a>
            @endforeach
        </div>

        @if($avaliacoes->isEmpty())
            <div class="text-center py-20 space-y-3">
                <span class="material-symbols-outlined text-5xl text-gray-700 block">star_rate</span>
                <p class="text-gray-500">Nenhuma avaliação encontrada{{ request('nota') ? ' para este filtro.' : '.' }}</p>
            </div>
        @else
        <div class="space-y-5">
            @foreach($avaliacoes as $av)
            <div class="bg-[#1d0e0b] rounded-2xl border border-white/[0.04] p-6 hover:border-secondary/15 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="w-11 h-11 rounded-full bg-secondary/10 border border-secondary/20 flex items-center justify-center text-secondary font-bold text-sm">
                            {{ strtoupper(substr($av->user?->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-white text-sm">{{ $av->user?->name ?? 'Cliente' }}</p>
                            <p class="text-[11px] text-gray-600">{{ $av->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-[17px] {{ $i <= $av->nota ? 'text-secondary' : 'text-gray-700' }}"
                                        style="font-variation-settings:'FILL' {{ $i <= $av->nota ? '1' : '0' }}">star</span>
                                @endfor
                            </div>
                            @if($av->produto)
                                <span class="text-[11px] text-gray-500 bg-white/[0.04] px-2.5 py-0.5 rounded-full border border-white/[0.06] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">cake</span>
                                    {{ $av->produto->nome }}
                                </span>
                            @endif
                        </div>
                        @if($av->comentario)
                            <p class="text-gray-300 text-sm leading-relaxed">"{{ $av->comentario }}"</p>
                        @else
                            <p class="text-gray-600 text-xs italic">Sem comentário.</p>
                        @endif
                        @if($av->resposta_admin)
                            <div class="bg-secondary/5 border-l-2 border-secondary/40 pl-4 py-2.5 rounded-r-xl mt-3">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="material-symbols-outlined text-secondary text-[13px]">storefront</span>
                                    <p class="text-[10px] text-secondary font-bold uppercase tracking-widest">Resposta da Excelência</p>
                                </div>
                                <p class="text-sm text-gray-400">{{ $av->resposta_admin }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $avaliacoes->links() }}
        </div>
        @endif

    </div>
</main>

@include('footer')

</body>
</html>
