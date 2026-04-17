@php
    $statusClasses = [
        'pendente'          => 'bg-gray-500/15 text-gray-400 border-gray-700/50',
        'confirmado'        => 'bg-blue-500/15 text-blue-400 border-blue-800/50',
        'preparando'        => 'bg-amber-500/15 text-amber-400 border-amber-800/50',
        'saiu_para_entrega' => 'bg-purple-500/15 text-purple-400 border-purple-800/50',
        'entregue'          => 'bg-green-500/15 text-green-400 border-green-800/50',
        'cancelado'         => 'bg-red-500/15 text-red-400 border-red-800/50',
    ];
    $statusIcons = [
        'pendente'          => 'schedule',
        'confirmado'        => 'thumb_up',
        'preparando'        => 'soup_kitchen',
        'saiu_para_entrega' => 'two_wheeler',
        'entregue'          => 'check_circle',
        'cancelado'         => 'cancel',
    ];
    $statusLabels = [
        'pendente'          => 'Pendente',
        'confirmado'        => 'Confirmado',
        'preparando'        => 'Preparando',
        'saiu_para_entrega' => 'Saiu p/ Entrega',
        'entregue'          => 'Entregue',
        'cancelado'         => 'Cancelado',
    ];
    $metodoLabels = ['pix'=>'PIX','cartao_credito'=>'Crédito','cartao_debito'=>'Débito','dinheiro'=>'Dinheiro'];
    $metodoIcons  = ['pix'=>'qr_code_2','cartao_credito'=>'credit_card','cartao_debito'=>'credit_card','dinheiro'=>'payments'];
    $metodo = $pedido->pagamento?->metodo ?? '';
@endphp
<tr class="hover:bg-white/[0.012] transition-colors"
    data-status="{{ $pedido->status }}"
    data-id="{{ $pedido->id }}"
    data-date="{{ $pedido->created_at->format('Y-m-d') }}">

    <td class="td font-bold text-secondary text-sm">#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</td>

    <td class="td">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-secondary/10 border border-secondary/20 flex items-center justify-center text-secondary font-bold text-[11px] shrink-0">
                {{ strtoupper(substr($pedido->user?->name ?? '?', 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-white text-xs truncate max-w-[120px]">{{ $pedido->user?->name ?? '—' }}</p>
            </div>
        </div>
    </td>

    @if(isset($showDate) && $showDate)
        <td class="td text-gray-600 text-xs">{{ $pedido->created_at->format('d/m H:i') }}</td>
    @endif

    <td class="td">
        <span class="inline-flex items-center gap-1 text-[11px] text-gray-500 bg-white/[0.04] px-2.5 py-1 rounded-full font-semibold">
            <span class="material-symbols-outlined text-[13px]">{{ $metodoIcons[$metodo] ?? 'payments' }}</span>
            {{ $metodoLabels[$metodo] ?? '—' }}
        </span>
    </td>

    <td class="td">
        <button id="badge-status-{{ $pedido->id }}"
            class="btn-change-status status-pill {{ $statusClasses[$pedido->status] ?? '' }}"
            data-id="{{ $pedido->id }}">
            <span class="material-symbols-outlined text-[13px]">{{ $statusIcons[$pedido->status] ?? 'schedule' }}</span>
            {{ $statusLabels[$pedido->status] ?? $pedido->status }}
            <span class="material-symbols-outlined text-[11px]">expand_more</span>
        </button>
    </td>

    <td class="td text-center">
        <button class="btn-ver-detalhes-pedido inline-flex items-center gap-1 text-[11px] text-gray-500 hover:text-secondary transition-colors border border-white/[0.06] hover:border-secondary/30 px-3 py-1.5 rounded-full"
            data-id="{{ $pedido->id }}" title="Ver detalhes do pedido">
            <span class="material-symbols-outlined text-[14px]">receipt_long</span>
        </button>
    </td>

    <td class="td text-right font-bold text-secondary text-sm">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>

</tr>
