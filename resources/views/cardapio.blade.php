<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Excelência Doces &amp; Salgados - Cardápio</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&amp;family=Lato:wght@300;400;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>tailwind.config = {};</script>
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #1A0F0E; }
    ::-webkit-scrollbar-thumb { background: #d69c5e; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #c2884a; }
</style>
</head>
<body class="font-body bg-background-dark text-text-light transition-colors duration-300 relative">

@include('header')

<div class="pt-24 pb-10 bg-background-dark border-b border-secondary/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-display text-5xl md:text-6xl font-bold text-white mb-4">Nosso Cardápio</h1>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto">Explore nossa seleção de delícias preparadas com os melhores ingredientes.</p>
    </div>
</div>

<section class="py-20 bg-[#140b0a]" id="destaques-semana">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-secondary font-bold tracking-widest uppercase text-sm block mb-3">Imperdíveis</span>
            <h2 class="font-display text-4xl md:text-5xl font-bold text-white">Destaques da Semana</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            @forelse($destaques as $destaque)
                <div class="bg-background-dark rounded-2xl overflow-hidden shadow-soft hover:shadow-xl transition-all duration-300 group border border-secondary/20 cursor-pointer open-modal"
                    data-name="{{ $destaque->nome }}"
                    data-description="{{ $destaque->descricao }}"
                    data-price="{{ number_format($destaque->preco, 2, ',', '.') }}"
                    data-category="{{ $destaque->categoria->nome ?? 'Destaque' }}"
                    data-image="{{ $destaque->imagem ? asset('images/products/' . $destaque->imagem) : '' }}">
                    <div class="relative h-72 overflow-hidden">
                        @if($destaque->imagem)
                            <img alt="{{ $destaque->nome }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" src="{{ asset('images/products/' . $destaque->imagem) }}"/>
                        @else
                            <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                <span class="material-icons text-6xl text-gray-600">image_not_supported</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors"></div>
                        <div class="absolute top-4 left-4 bg-secondary text-primary font-bold px-4 py-1 rounded-full text-sm uppercase tracking-wide flex items-center gap-1 shadow-lg">
                            <span class="material-icons text-sm">star</span> Destaque
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-display text-2xl font-bold text-white group-hover:text-secondary transition-colors">{{ $destaque->nome }}</h3>
                            <span class="text-xl font-bold text-secondary">R$ {{ number_format($destaque->preco, 2, ',', '.') }}</span>
                        </div>
                        <p class="text-gray-400 mb-6 text-sm">{{ $destaque->descricao }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 col-span-3 text-center">Nenhum destaque disponível no momento.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="py-20 bg-background-dark" id="cardapio-categorias">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @foreach($categorias as $categoria)
            <div class="mb-16">
                <div class="flex items-center gap-4 mb-8">
                    <h2 class="font-display text-3xl md:text-4xl font-bold text-white">{{ $categoria->nome }}</h2>
                    <div class="flex-grow h-px bg-secondary/20 ml-4"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($categoria->produtos->where('ativo', true) as $produto)
                        <div class="flex items-center gap-4 border-b border-gray-800 pb-4 group cursor-pointer open-modal"
                            data-name="{{ $produto->nome }}"
                            data-description="{{ $produto->descricao }}"
                            data-price="{{ number_format($produto->preco, 2, ',', '.') }}"
                            data-category="{{ $categoria->nome }}"
                            data-image="{{ $produto->imagem ? asset('images/products/' . $produto->imagem) : '' }}">
                            @if($produto->imagem)
                                <img alt="{{ $produto->nome }}" class="w-16 h-16 rounded-lg object-cover border border-secondary/20 shadow-soft flex-shrink-0" src="{{ asset('images/products/' . $produto->imagem) }}"/>
                            @else
                                <div class="w-16 h-16 rounded-lg bg-gray-800 flex items-center justify-center flex-shrink-0 border border-secondary/20">
                                    <span class="material-icons text-gray-600">fastfood</span>
                                </div>
                            @endif
                            <div class="flex-grow">
                                <h4 class="font-bold text-lg text-white group-hover:text-secondary transition-colors">{{ $produto->nome }}</h4>
                                <p class="text-sm text-gray-400">{{ $produto->descricao }}</p>
                            </div>
                            <span class="font-bold text-secondary text-lg whitespace-nowrap">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 col-span-2">Nenhum produto disponível nesta categoria.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</section>

<div id="product-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto">
    <div class="bg-background-dark w-full max-w-5xl rounded-2xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex flex-col md:flex-row border border-secondary/30 relative my-auto">
        <button id="modal-close" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-black/40 text-text-light hover:bg-secondary hover:text-primary transition-colors focus:outline-none backdrop-blur-md">
            <span class="material-icons">close</span>
        </button>
        <div class="md:w-1/2 relative h-64 sm:h-80 md:h-auto overflow-hidden">
            <img id="modal-image" alt="" class="w-full h-full object-cover"/>
            <div id="modal-image-placeholder" class="hidden w-full h-full bg-gray-800 flex items-center justify-center">
                <span class="material-icons text-8xl text-gray-600">fastfood</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-background-dark via-transparent to-transparent opacity-80"></div>
        </div>
        <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-background-dark relative max-h-[90vh] overflow-y-auto">
            <div class="mb-2">
                <span id="modal-category" class="text-secondary font-bold tracking-widest uppercase text-xs md:text-sm"></span>
            </div>
            <h2 id="modal-name" class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-text-light mb-4 leading-tight"></h2>
            <p id="modal-description" class="text-text-light/80 text-sm md:text-base leading-relaxed mb-6 font-light"></p>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-text-light mb-2" for="observacoes">Observações</label>
                <textarea class="w-full bg-[#2a1a18] border border-secondary/30 rounded-lg p-3 text-text-light text-sm focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary placeholder-text-light/40 transition-colors resize-none" id="observacoes" placeholder="Alguma preferência? Ex: remover açúcar, sem calda..." rows="2"></textarea>
            </div>
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-secondary/20">
                <div>
                    <span class="block text-sm text-text-light/60 mb-1">Preço unitário</span>
                    <span id="modal-price" class="font-display text-3xl font-bold text-secondary"></span>
                </div>
                <div class="flex items-center bg-[#2a1a18] rounded-full border border-secondary/30 p-1">
                    <button id="qty-minus" class="w-8 h-8 rounded-full flex items-center justify-center text-secondary hover:bg-secondary hover:text-primary transition-colors focus:outline-none">
                        <span class="material-icons text-xl">remove</span>
                    </button>
                    <span id="qty-display" class="w-10 text-center font-bold text-lg text-text-light">1</span>
                    <button id="qty-plus" class="w-8 h-8 rounded-full flex items-center justify-center text-secondary hover:bg-secondary hover:text-primary transition-colors focus:outline-none">
                        <span class="material-icons text-xl">add</span>
                    </button>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4 mt-auto">
                <button class="w-full sm:w-auto flex-1 bg-secondary hover:bg-[#c2884a] text-primary py-4 px-8 rounded-xl font-bold text-center transition-all shadow-[0_0_15px_rgba(214,156,94,0.3)] hover:shadow-[0_0_20px_rgba(214,156,94,0.5)] flex justify-center items-center gap-2 transform hover:-translate-y-1">
                    <span class="material-icons">shopping_bag</span>
                    Adicionar ao Pedido
                </button>
                <button id="modal-close-btn" class="w-full sm:w-auto text-text-light hover:text-secondary px-6 py-4 font-semibold transition-colors flex justify-center items-center gap-2 border border-transparent hover:border-secondary/20 rounded-xl">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@include('footer')

@vite('resources/js/cardapio.js')

</body></html>