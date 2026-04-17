<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Excelência - Meu Carrinho</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/carrinho.js'])
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1A0F0E; }
        ::-webkit-scrollbar-thumb { background: #d69c5e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #c2884a; }
    </style>
</head>
<body class="font-body bg-background-dark text-text-light min-h-screen flex flex-col relative overflow-x-hidden">

@include('header')

<div class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-28 pb-12 relative z-10">
    <div class="mb-8">
        <h1 class="font-display text-4xl font-bold text-white mb-2">Meu Carrinho</h1>
        <p class="text-gray-400">Reveja seus itens e escolha o endereço para entrega.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

    <div class="w-full lg:w-2/3 space-y-6">
            @if(count($itens) > 0)
                @foreach($itens as $item)
                <div class="cart-item bg-[#261715] rounded-xl border border-gray-800 p-4 sm:p-6 shadow-soft flex flex-col sm:flex-row gap-6 relative" data-id="{{ $item->id }}">
                    <div class="w-full sm:w-24 h-24 rounded-lg bg-gray-800 overflow-hidden shrink-0">
                        <img src="{{ Str::startsWith($item->produto->imagem, 'http') ? $item->produto->imagem : asset('storage/' . $item->produto->imagem) }}" alt="{{ $item->produto->nome }}" class="w-full h-full object-cover"/>
                    </div>
                    
                    <div class="flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-display text-xl font-bold text-white">{{ $item->produto->nome }}</h3>
                                <p class="text-secondary font-bold text-lg">R$ {{ number_format($item->produto->preco, 2, ',', '.') }}</p>
                            </div>
                            @if($item->observacoes)
                                <p class="text-sm text-gray-400 mb-2"><strong>Obs:</strong> {{ $item->observacoes }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center gap-3 bg-background-dark border border-gray-800 rounded-lg px-2 py-1 relative">
                                <button class="btn-decrease w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white transition-colors" data-id="{{ $item->id }}">-</button>
                                <span class="item-qty w-6 text-center font-bold" data-id="{{ $item->id }}">{{ $item->quantidade }}</span>
                                <button class="btn-increase w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white transition-colors" data-id="{{ $item->id }}">+</button>
                            </div>
                            
                            <button class="btn-remove text-red-400 hover:text-red-300 transition-colors flex items-center gap-1 text-sm font-semibold" data-id="{{ $item->id }}">
                                <span class="material-symbols-outlined text-lg">delete</span> Remover
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="bg-[#261715] rounded-xl border border-gray-800 p-12 text-center shadow-soft">
                    <span class="material-symbols-outlined text-6xl text-gray-600 mb-4">shopping_cart</span>
                    <h3 class="font-display text-2xl font-bold text-white mb-2">Seu carrinho está vazio</h3>
                    <p class="text-gray-400 mb-6">Explore o nosso cardápio e adicione delícias aqui.</p>
                    <a href="{{ route('cardapio') }}" class="inline-block bg-secondary hover:bg-[#c2884a] text-primary font-bold py-3 px-8 rounded-lg transition-colors">Ver Cardápio</a>
                </div>
            @endif
        </div>

        <aside class="w-full lg:w-1/3">
            <div class="bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft sticky top-28 space-y-6">
                <h2 class="font-display text-2xl font-bold text-secondary border-b border-gray-800 pb-4">Resumo do Pedido</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span id="subtotal-val" data-val="{{ $total }}">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Frete</span>
                        <span id="frete-val" data-val="0">A calcular</span>
                    </div>
                    <div class="border-t border-gray-800 pt-4 flex justify-between font-bold text-xl text-white">
                        <span>Total</span>
                        <span id="total-val">R$ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-6">
                    <h3 class="font-bold text-white mb-4">Endereço de Entrega</h3>
                    
                    @if(count($addresses) > 0)
                        <select id="select-address" class="w-full bg-background-dark border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary mb-3">
                            <option value="">Selecione um endereço...</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->id }}"
                                    data-cep="{{ $address->cep }}"
                                    data-nome="{{ $address->nome }}"
                                    data-numero="{{ $address->numero }}"
                                    data-complemento="{{ $address->complemento }}"
                                    {{ $address->padrao ? 'selected' : '' }}>
                                    {{ $address->nome }}
                                    @if($address->numero) — Nº {{ $address->numero }}@endif
                                    @if($address->complemento) ({{ $address->complemento }})@endif
                                    @if($address->cep) · CEP {{ $address->cep }}@endif
                                    {{ $address->padrao ? '★' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div id="address-preview" class="hidden bg-black/20 border border-secondary/20 rounded-xl p-3 mb-3 space-y-1 text-xs">
                            <div class="flex items-center gap-1.5 text-secondary font-bold">
                                <span class="material-symbols-outlined text-[14px]">location_on</span>
                                <span id="addr-nome" class="text-white font-semibold"></span>
                            </div>
                            <p class="text-gray-400 pl-5" id="addr-detalhe"></p>
                            <p class="text-gray-600 pl-5" id="addr-cep"></p>
                        </div>
                    @endif

                    <button id="btn-add-address" class="w-full border border-dashed border-gray-600 hover:border-secondary text-gray-400 hover:text-secondary rounded-lg py-3 font-semibold transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Adicionar Novo Endereço
                    </button>
                    <p id="frete-msg" class="text-xs text-secondary mt-2 hidden">* O valor do frete foi estimado em R$ 15,00 para este local.</p>
                </div>

                <div class="pt-4">
                    <button id="btn-checkout" class="w-full bg-secondary hover:bg-[#c2884a] text-primary font-bold py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-2 {{ count($itens) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($itens) === 0 ? 'disabled' : '' }}>
                        Finalizar Pedido <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>

<div id="modal-address" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-background-dark rounded-xl border border-gray-800 w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h2 id="modal-address-title" class="font-display text-2xl font-bold text-white">Adicionar Novo Endereço</h2>
            <button id="modal-address-close" class="text-gray-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form id="form-address" method="POST" action="{{ route('perfil.enderecos.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST"/>
                <input type="hidden" name="redirect_to" value="carrinho"/>

                <div class="md:col-span-2">
                    <label class="block text-text-light font-semibold mb-2 text-sm">Nome do Endereço</label>
                    <input id="field-name" name="name" required class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="Ex: Casa, Trabalho" type="text"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">CEP</label>
                    <input id="field-cep" name="cep" required class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="00000-000" type="text"/>
                    <p id="cep-status" class="text-xs mt-1 hidden"></p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-text-light font-semibold mb-2 text-sm">Endereço</label>
                    <input id="field-street" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:outline-none placeholder-gray-600 opacity-70 cursor-not-allowed" placeholder="Preenchido automaticamente pelo CEP" type="text" readonly tabindex="-1"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">Número</label>
                    <input id="field-number" name="number" required class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="123" type="text"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">Complemento</label>
                    <input id="field-complement" name="complement" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="Apto 45, Bloco B" type="text"/>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-text-light font-semibold mb-2 text-sm">Bairro</label>
                    <input id="field-neighborhood" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:outline-none placeholder-gray-600 opacity-70 cursor-not-allowed" placeholder="Preenchido automaticamente pelo CEP" type="text" readonly tabindex="-1"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">Cidade</label>
                    <input id="field-city" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:outline-none placeholder-gray-600 opacity-70 cursor-not-allowed" placeholder="Preenchido automaticamente pelo CEP" type="text" readonly tabindex="-1"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">Estado</label>
                    <input id="field-state" maxlength="2" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:outline-none placeholder-gray-600 opacity-70 cursor-not-allowed" placeholder="SP" type="text" readonly tabindex="-1"/>
                </div>

                <div class="md:col-span-2 flex justify-end gap-4 pt-4">
                    <button id="modal-address-cancel" type="button" class="px-6 py-2 rounded-lg font-bold text-gray-400 hover:text-white transition-colors">Cancelar</button>
                    <button type="submit" class="px-6 py-2 rounded-lg font-bold bg-secondary text-primary hover:bg-[#c2884a] transition-colors">Salvar Endereço</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-payment" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-background-dark rounded-xl border border-gray-800 w-full max-w-md shadow-2xl overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-800 flex justify-between items-center">
            <h2 class="font-display text-2xl font-bold text-white">Finalizar Pedido</h2>
            <button id="modal-payment-close" class="text-gray-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form id="form-checkout">
                <div class="space-y-4">
                    <label class="block text-text-light font-semibold mb-2 text-sm">Forma de Pagamento</label>
                    <select id="payment-method" required class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-3 focus:outline-none focus:border-secondary transition-all">
                        <option value="">Selecione uma opção...</option>
                        <option value="pix">PIX</option>
                        <option value="cartao_credito">Cartão de Crédito</option>
                        <option value="cartao_debito">Cartão de Débito</option>
                        <option value="dinheiro">Dinheiro (Pagará na entrega/retirada)</option>
                    </select>

                    <div id="troco-container" class="hidden space-y-2 mt-4">
                        <label class="block text-text-light font-semibold text-sm">Troco para quanto?</label>
                        <input id="payment-troco" type="number" step="0.01" min="0" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="Ex: 50.00"/>
                        <p class="text-xs text-secondary">*Deixe em branco se não precisar de troco.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <button id="modal-payment-cancel" type="button" class="px-6 py-2 rounded-lg font-bold text-gray-400 hover:text-white transition-colors">Voltar</button>
                    <button type="submit" id="btn-confirm-checkout" class="px-6 py-2 rounded-lg font-bold bg-secondary text-primary hover:bg-[#c2884a] transition-colors flex items-center gap-2">Confirmar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('footer')
</body>
</html>
