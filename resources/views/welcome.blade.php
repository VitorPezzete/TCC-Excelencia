<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Excelência Doces &amp; Salgados</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&amp;family=Lato:wght@300;400;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1A0F0E; 
        }
        ::-webkit-scrollbar-thumb {
            background: #d69c5e; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #c2884a; 
        }
    </style>
</head>

@include('header')
<section class="relative pt-20 pb-24 flex items-center justify-center min-h-[80vh] bg-[#0a0504]">
<div class="absolute inset-0 z-0">
<img alt="Coffee and pastries background" class="w-full h-full object-cover opacity-30" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC33b8ZcNIo35nwxqQn43HQn4HmFMWH5cc96r12PBSI-56n1JvLo7gmK2VdgtRC2hMy9ZupAyxvNNYNw8aZ1nJ3o1ZCvKhSiy-Vx-OrpMq1xC13LjolNSKwhYImkOwsq0b0rqY1e-FhzjRjsVmK4CkWnSA8zCyOAn7nnJujk4nh3rOPbE6yWZN-J8M_Mqgnnw8GZeKOAOmRFqG3w1rGGq82F51S6JYrqN_WVHBngFiC4y47HgNU_pr9CgY-sscUqYUjI3GaxE924Y4X"/>
<div class="absolute inset-0 bg-gradient-to-b from-background-dark/80 to-background-dark"></div>
</div>
<div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-12">
<span class="block text-secondary font-bold tracking-widest uppercase mb-4 drop-shadow-md">Bem-vindo à Excelência</span>
<h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6 text-white drop-shadow-lg">
                Sabor e Tradição em Cada Pedaço
            </h1>
<p class="text-lg md:text-xl text-gray-300 font-light drop-shadow max-w-2xl mx-auto mb-10">
                Descubra a autêntica confeitaria artesanal e deixe-se envolver pelos nossos aromas e sabores únicos, feitos com o coração para você.
            </p>
<div class="flex flex-col sm:flex-row gap-4 justify-center">
<a class="inline-block bg-secondary hover:bg-[#c2884a] text-primary font-bold px-10 py-4 rounded-full transition-all shadow-lg transform hover:-translate-y-1 text-lg" href="{{ route('cardapio') }}">
                    Nosso Cardápio
                </a>
<a class="inline-block bg-transparent border-2 border-secondary text-secondary hover:bg-secondary/10 font-bold px-10 py-4 rounded-full transition-all shadow-lg text-lg" href="#historia">
                    Nossa História
                </a>
</div>
</div>
</section>
<section class="py-24 bg-background-dark" id="historia">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col lg:flex-row items-center gap-16">
<div class="lg:w-1/2 relative">
<div class="relative rounded-2xl overflow-hidden shadow-2xl z-10">
<img alt="Baker preparing dough" class="w-full h-[600px] object-cover hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUeRlg5yWimS8H8vdYTShSxTkrT49xfqtAqodAMnc2e6VMOWskfculjESOI511JIJEkATEboqVbSHG6OjYIh4QlxO6pT5QEi09x49QkQeET83tRcP3BxiS1sH5zmMcRWakNOnGY-ThIX3UsvDsyeSSk75Y_a-xRiRhmRIepe8UxhL4yBCtUnraYKsDVlTMEdaBiKMNyojFM3sy5-VU7E4ruqRzAiVUbn9aXhSSjpL_shnI0MQE542_cjsldMrsKFZROqWqbJGZLoSo"/>
<div class="absolute inset-0 bg-black/10"></div>
</div>
<div class="absolute -bottom-6 -right-6 w-full h-full border-4 border-secondary/30 rounded-2xl z-0 hidden sm:block"></div>
<div class="absolute -top-6 -left-6 w-32 h-32 bg-secondary/10 rounded-full blur-2xl z-0"></div>
</div>
<div class="lg:w-1/2">
<div class="flex items-center gap-3 mb-6">
<span class="h-px w-12 bg-secondary block"></span>
<span class="text-secondary font-bold tracking-widest uppercase text-sm">Nossa Origem</span>
</div>
<h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-8 leading-tight">
                        Do Forno de Casa<br/>Para a Sua Mesa
                    </h2>
<p class="text-lg text-gray-300 mb-6 leading-relaxed font-light">
                        A jornada da <span class="font-bold text-secondary">Excelência Doces &amp; Salgados</span> começou há mais de uma década, impulsionada pelo amor genuíno à arte da panificação e confeitaria. Tudo se iniciou em uma pequena cozinha familiar, onde receitas valiosas, guardadas a sete chaves e passadas de geração em geração, ganharam vida.
                    </p>
<p class="text-lg text-gray-300 mb-6 leading-relaxed font-light">
                        Naquela época, o aroma de pão fresco e bolos recém-saídos do forno já encantava a vizinhança. Com o tempo, o que era um dom natural e um hobby apaixonado transformou-se em uma missão: compartilhar momentos de doçura e sabor incomparável com mais pessoas.
                    </p>
<p class="text-lg text-gray-300 mb-10 leading-relaxed font-light">
                        Hoje, nosso espaço cresceu, mas a essência permanece intacta. Continuamos a sovar cada massa à mão, a selecionar minuciosamente os ingredientes mais frescos e a decorar cada doce com o mesmo carinho do primeiro dia. Acreditamos que o verdadeiro sabor está no afeto investido no preparo.
                    </p>
<div class="bg-[#140b0a] p-6 rounded-xl border border-secondary/20 shadow-soft">
<p class="font-display italic text-xl text-text-light mb-4">"Nossa maior recompensa é o sorriso de um cliente ao saborear nossas criações. Prazer em Servir é mais do que nosso lema; é a alma do nosso negócio."</p>
<p class="text-secondary font-bold text-sm tracking-wider uppercase">— Fundadores da Excelência</p>
</div>
</div>
</div>
</div>
</section>
<section class="py-24 bg-[#140b0a] border-y border-secondary/10">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-6">Nossos Pilares</h2>
<div class="h-1 w-24 bg-secondary mx-auto rounded-full"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-10">
<div class="bg-background-dark p-10 rounded-2xl border border-secondary/10 hover:border-secondary/30 transition-all duration-300 hover:-translate-y-2 shadow-soft text-center group">
<div class="w-20 h-20 mx-auto bg-secondary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-secondary/20 transition-colors">
<span class="material-symbols-outlined text-4xl text-secondary">favorite</span>
</div>
<h3 class="font-display text-2xl font-bold text-white mb-4">Missão</h3>
<p class="text-gray-300 font-light leading-relaxed">
                        Proporcionar experiências gastronômicas memoráveis, unindo sabor, afeto e qualidade impecável em cada doce, salgado e café servido.
                    </p>
</div>
<div class="bg-background-dark p-10 rounded-2xl border border-secondary/10 hover:border-secondary/30 transition-all duration-300 hover:-translate-y-2 shadow-soft text-center group">
<div class="w-20 h-20 mx-auto bg-secondary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-secondary/20 transition-colors">
<span class="material-symbols-outlined text-4xl text-secondary">visibility</span>
</div>
<h3 class="font-display text-2xl font-bold text-white mb-4">Visão</h3>
<p class="text-gray-300 font-light leading-relaxed">
                        Ser a confeitaria e padaria artesanal de maior referência na região, reconhecida por preservar a tradição enquanto inova em sabores únicos.
                    </p>
</div>
<div class="bg-background-dark p-10 rounded-2xl border border-secondary/10 hover:border-secondary/30 transition-all duration-300 hover:-translate-y-2 shadow-soft text-center group">
<div class="w-20 h-20 mx-auto bg-secondary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-secondary/20 transition-colors">
<span class="material-symbols-outlined text-4xl text-secondary">diamond</span>
</div>
<h3 class="font-display text-2xl font-bold text-white mb-4">Valores</h3>
<p class="text-gray-300 font-light leading-relaxed">
                        Compromisso com a qualidade, respeito à produção artesanal, dedicação ao cliente, paixão pelo que fazemos e inovação constante.
                    </p>
</div>
</div>
</div>
</section>
<section class="py-24 bg-background-dark" id="destaques">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<span class="text-secondary font-bold tracking-widest uppercase text-sm block mb-3">Sabores Inesquecíveis</span>
<h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-6">Nossos Destaques</h2>
<div class="h-1 w-24 bg-secondary mx-auto rounded-full mb-6"></div>
<p class="text-gray-300 font-light max-w-2xl mx-auto">Conheça as estrelas do nosso cardápio, preparadas diariamente com ingredientes selecionados e muito amor.</p>
</div>

@if($destaques->isEmpty())
    <div class="text-center py-16 text-gray-600">
        <span class="material-symbols-outlined text-5xl mb-4 block">restaurant_menu</span>
        <p class="text-sm">Nenhum produto em destaque. Configure no painel admin.</p>
    </div>
@else
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
    @foreach($destaques as $produto)
    <div class="bg-[#140b0a] rounded-2xl overflow-hidden shadow-soft border border-secondary/10 group hover:-translate-y-2 transition-transform duration-300 flex flex-col">
        <div class="h-48 overflow-hidden relative shrink-0">
            @if($produto->imagem)
                <img
                    alt="{{ $produto->nome }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    src="{{ Str::startsWith($produto->imagem, 'http') ? $produto->imagem : asset('storage/'.$produto->imagem) }}">
            @else
                <div class="w-full h-full bg-gradient-to-br from-secondary/10 to-secondary/5 flex items-center justify-center">
                    <span class="material-symbols-outlined text-secondary/30 text-6xl">restaurant_menu</span>
                </div>
            @endif
            @if($produto->categoria)
                <div class="absolute top-3 left-3 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full border border-white/10">
                    {{ $produto->categoria->nome }}
                </div>
            @endif
            <div class="absolute top-3 right-3 bg-secondary text-primary w-7 h-7 rounded-full flex items-center justify-center shadow-md">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">star</span>
            </div>
        </div>
        <div class="p-5 text-center flex-1 flex flex-col">
            <h3 class="font-display text-lg font-bold text-white mb-2">{{ $produto->nome }}</h3>
            <p class="text-gray-400 text-sm mb-4 flex-1 leading-relaxed">{{ Str::limit($produto->descricao, 70) }}</p>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-secondary font-bold text-lg">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                <a href="{{ route('cardapio') }}"
                   class="flex items-center gap-1 text-xs font-bold text-secondary hover:text-white bg-secondary/10 hover:bg-secondary/20 px-3 py-1.5 rounded-full transition-all border border-secondary/20">
                    <span class="material-icons text-[14px]">add_shopping_cart</span> Ver
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="mt-12 text-center">
<a class="inline-block bg-transparent border-2 border-secondary text-secondary hover:bg-secondary hover:text-primary font-bold px-8 py-3 rounded-full transition-all" href="{{ route('cardapio') }}">
    Ver Cardápio Completo
</a>
</div>
</div>
</section>


<section class="py-24 bg-[#140b0a] border-t border-secondary/10">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
<div class="max-w-2xl">
<span class="text-secondary font-bold tracking-widest uppercase text-sm block mb-3">Bastidores</span>
<h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-4">O Processo Artesanal</h2>
<p class="text-gray-300 font-light">Cada etapa da nossa produção é feita com cuidado minucioso, respeitando o tempo dos ingredientes para alcançar a perfeição.</p>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<div class="relative group rounded-2xl overflow-hidden shadow-soft h-80">
<img alt="Trabalhando a massa" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUeRlg5yWimS8H8vdYTShSxTkrT49xfqtAqodAMnc2e6VMOWskfculjESOI511JIJEkATEboqVbSHG6OjYIh4QlxO6pT5QEi09x49QkQeET83tRcP3BxiS1sH5zmMcRWakNOnGY-ThIX3UsvDsyeSSk75Y_a-xRiRhmRIepe8UxhL4yBCtUnraYKsDVlTMEdaBiKMNyojFM3sy5-VU7E4ruqRzAiVUbn9aXhSSjpL_shnI0MQE542_cjsldMrsKFZROqWqbJGZLoSo"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
<h4 class="font-display text-2xl font-bold text-white mb-2">Preparo Manual</h4>
<p class="text-gray-300 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">Sovando a massa com cuidado e técnica para a textura ideal.</p>
</div>
</div>
<div class="relative group rounded-2xl overflow-hidden shadow-soft h-80">
<img alt="Decoração de Bolos" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmGcPf1uSA9r6mNoKgmbKL_hFv34WKoAgtPCNXDnd3b1bX-Zq_QM77LB7YQn6c7Yx0Tyb-ZQ7qMBtRxg-pw-undawap8x966gZBx2Tb94kGR6s7KoBOkm1rqpxioKqNZlXh1c110ClXSrOPoGmfuWgEe8EuwFfEGvwT_A0zRN53h-_57KfE1lE-d7URhJXlDviws-eFAx9l1WzBumFKb-xQl2zHSJvPFMDze9THUoyMzQ0zx4C7ynelKrgabTaMUXDRTwlGGbT_f0B"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
<h4 class="font-display text-2xl font-bold text-white mb-2">Confeitaria Fina</h4>
<p class="text-gray-300 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">Decoração delicada de cada fatia com ingredientes selecionados.</p>
</div>
</div>
<div class="relative group rounded-2xl overflow-hidden shadow-soft h-80 md:col-span-2 lg:col-span-1">
<img alt="Salgados Fresquinhos" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDuPjaynjIZkd8Dt1EssOAuoswvfssKGZKMT43IqXQ8emCYagscn7bWMYtxcsuIi58ksec1hU3w1yASihiHGPbo_Q3dqFO3b5nxfVFagvc_w_1YCLMXKkKswfDFOSIfimAzhDjzBWMq5cLkZXXxR-kpR7tJ33nI3ALejzWlzRB-leYP-5z_OOzmWOKi91jGO4CXrQc-lae7p0XdFdHWf_-6erw_bjKZlRAoJs4A5iOs9nngHmXSyO9iMRtjGTy5wEZ0QtH6HW-EwZKk"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>
<div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
<h4 class="font-display text-2xl font-bold text-white mb-2">Salgados Dourados</h4>
<p class="text-gray-300 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">O ponto perfeito de crocância e recheios generosos.</p>
</div>
</div>
</div>
</div>
</section>
<section class="relative py-20 bg-[#0f0807] overflow-hidden border-t border-secondary/10">
<div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#d69c5e 2px, transparent 2px); background-size: 30px 30px;"></div>
<div class="absolute inset-0 bg-gradient-to-r from-background-dark/90 to-[#140b0a]/90 z-0"></div>
<div class="relative z-10 max-w-5xl mx-auto px-4 text-center">
<span class="material-symbols-outlined text-5xl text-secondary mb-6 block">restaurant_menu</span>
<h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-6">Pronto para Saborear Nossa História?</h2>
<p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto font-light">Venha experimentar as delícias que preparamos diariamente. Cada receita conta uma parte da nossa paixão por servir bem.</p>
<div class="flex flex-col sm:flex-row gap-4 justify-center">
<a class="inline-block bg-secondary hover:bg-[#c2884a] text-primary font-bold px-10 py-4 rounded-full transition-all shadow-lg transform hover:-translate-y-1 text-lg" href="{{ route('cardapio') }}">
                    Ver Nosso Cardápio
                </a>
<a class="inline-block bg-transparent border-2 border-secondary text-secondary hover:bg-secondary/10 font-bold px-10 py-4 rounded-full transition-all shadow-lg text-lg" href="#contato">
                    Fale Conosco
                </a>
</div>
</div>
</section>
<footer class="bg-[#0a0504] text-gray-300 py-16 border-t-8 border-secondary" id="contato">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
<div class="col-span-1 lg:col-span-1">
<a class="flex items-center gap-3 mb-6" href="#">
<span class="font-display font-bold text-3xl text-secondary">Excelência</span>
</a>
<p class="text-sm text-gray-400 mb-6 leading-relaxed">
                        Doces e salgados artesanais, preparados com os melhores ingredientes para adoçar o seu dia. Prazer em servir!
                    </p>
<div class="flex space-x-4">
<!-- <a class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary hover:bg-secondary hover:text-primary transition-colors" href="#"> -->
<!-- <span class="material-icons">facebook</span> -->
</a>
<!-- <a class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary hover:bg-secondary hover:text-primary transition-colors" href="#"> -->
<!-- <span class="material-icons">camera_alt</span> -->
</a>
</div>
</div>
<div>
<h4 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-2">
<span class="w-4 h-px bg-secondary"></span> Links Úteis
                    </h4>
<ul class="space-y-3">
<li><a class="hover:text-secondary transition-colors text-sm" href="{{ route('home') }}">Início</a></li>
<li><a class="hover:text-secondary transition-colors text-sm" href="#historia">Nossa História</a></li>
<li><a class="hover:text-secondary transition-colors text-sm" href="#destaques">Destaques</a></li>
<li><a class="hover:text-secondary transition-colors text-sm" href="{{ route('cardapio') }}">Cardápio</a></li>
</ul>
</div>
<div>
<h4 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-2">
<span class="w-4 h-px bg-secondary"></span> Contato
                    </h4>
<ul class="space-y-4">
<li class="flex items-start gap-3">
<span class="material-icons text-secondary text-xl">location_on</span>
<span class="text-sm">Rua José Bonifácio, 380 e 462 - Centro<br/>São Vicente, SP</span>
</li>
<li class="flex items-center gap-3">
<span class="material-icons text-secondary text-xl">phone</span>
<span class="text-sm">(13) 3467-4305</span>
</li>
</ul>
</div>
<div>
<h4 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-2">
<span class="w-4 h-px bg-secondary"></span> Horário
                    </h4>
<ul class="space-y-3">
<li class="flex justify-between border-b border-gray-800 pb-2">
<span class="text-sm">Seg - Sab</span>
<span class="text-sm text-secondary font-bold">07:00 - 19:00</span>
</li>
<li class="flex justify-between border-b border-gray-800 pb-2">
<span class="text-sm">Domingo</span>
<span class="text-sm text-red-400 font-bold">Fechado</span>
</li>
</ul>
</div>
</div>
<div class="mt-16 pt-8 border-t border-gray-800 text-center text-sm text-gray-500">
<p>© 2003 Excelência Doces &amp; Salgados. Todos os direitos reservados.</p>
</div>
</div>
</footer>
</body></html>