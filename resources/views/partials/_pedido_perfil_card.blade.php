<div class="bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft flex flex-col md:flex-row md:items-center justify-between gap-4" id="pedido-card-{{ $pedido->id }}">
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-xl text-white">Pedido #{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</h3>
            <span class="text-gray-400 text-sm">{{ $pedido->created_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="flex items-center gap-2">
            @php
                $statusConfig = [
                    'pendente' => ['color' => 'bg-gray-500/20 text-gray-400', 'icon' => 'pending'],
                    'confirmado' => ['color' => 'bg-blue-500/20 text-blue-400', 'icon' => 'thumb_up'],
                    'preparando' => ['color' => 'bg-yellow-500/20 text-yellow-500', 'icon' => 'soup_kitchen'],
                    'saiu_para_entrega' => ['color' => 'bg-blue-500/20 text-blue-400', 'icon' => 'two_wheeler'],
                    'entregue' => ['color' => 'bg-green-500/20 text-green-400', 'icon' => 'check_circle'],
                    'cancelado' => ['color' => 'bg-red-500/20 text-red-500', 'icon' => 'cancel'],
                ];
                $config = $statusConfig[$pedido->status];
            @endphp
            <span class="{{ $config['color'] }} text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">{{ $config['icon'] }}</span>
                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
            </span>
        </div>
    </div>
    <div class="flex flex-col md:items-end gap-3">
        <div class="text-gray-400">Total: <span class="text-white font-bold text-lg">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span></div>
        <div class="flex gap-2 flex-wrap">
            <button class="btn-detalhes-pedido px-5 py-2 bg-transparent border border-secondary text-secondary hover:bg-secondary hover:text-background-dark font-bold rounded-full transition-colors text-sm" data-pedido='@json($pedido->load("itens.produto", "endereco"))'>Ver Detalhes</button>
            @if($pedido->status === 'entregue')
                @if($pedido->avaliacao)
                    <a href="{{ route('avaliacoes.publicas') }}" class="px-5 py-2 bg-secondary/10 border border-secondary/30 text-secondary hover:bg-secondary/20 font-bold rounded-full transition-colors text-sm flex items-center gap-1.5 inline-flex">
                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings:'FILL' 1">star</span>
                        Ver Avaliação
                    </a>
                @else
                    <button class="btn-avaliar-pedido px-5 py-2 bg-secondary/10 border border-secondary/30 text-secondary hover:bg-secondary/20 font-bold rounded-full transition-colors text-sm flex items-center gap-1.5"
                        data-pedido-id="{{ $pedido->id }}"
                        data-ja-avaliou="0">
                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings:'FILL' 1">star</span>
                        Avaliar
                    </button>
                @endif
            @endif
            @if($pedido->status === 'pendente')
                <button class="btn-cancelar-pedido px-5 py-2 bg-transparent border border-red-500/50 text-red-500 hover:bg-red-500/10 font-bold rounded-full transition-colors text-sm" data-pedido-id="{{ $pedido->id }}">Cancelar</button>
            @elseif(in_array($pedido->status, ['confirmado', 'preparando', 'saiu_para_entrega']))
                <button class="btn-solicitar-cancelamento px-5 py-2 bg-transparent border border-gray-600 text-gray-400 hover:bg-gray-800 font-bold rounded-full transition-colors text-sm">Cancelar</button>
            @endif
        </div>
    </div>
</div>
