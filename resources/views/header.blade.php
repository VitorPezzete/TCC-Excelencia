<nav class="fixed w-full z-50 bg-background-dark/95 backdrop-blur-md border-b border-secondary/15">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- ── Logo ── --}}
            <a class="flex items-center gap-2.5 shrink-0" href="{{ route('home') }}">
                <img src="{{ Vite::asset('resources/images/logo.png') }}"
                     alt="Excelência Logo"
                     class="h-10 w-10 rounded-full border-2 border-secondary object-cover">
                <span class="font-display font-bold text-xl text-secondary whitespace-nowrap">Excelência</span>
            </a>

            {{-- ── Desktop nav links (centro) ── --}}
            <div class="hidden lg:flex items-center gap-6 flex-1 justify-center">
                <a href="{{ route('home') }}"
                   class="text-gray-400 hover:text-secondary font-semibold text-sm transition-colors whitespace-nowrap">Início</a>
                <a href="/#historia"
                   class="text-gray-400 hover:text-secondary font-semibold text-sm transition-colors whitespace-nowrap">Nossa História</a>
                <a href="/#destaques"
                   class="text-gray-400 hover:text-secondary font-semibold text-sm transition-colors whitespace-nowrap">Destaques</a>
                <a href="{{ route('cardapio') }}"
                   class="text-gray-400 hover:text-secondary font-semibold text-sm transition-colors whitespace-nowrap">Ver Cardápio</a>
                <a href="{{ route('avaliacoes.publicas') }}"
                   class="text-gray-400 hover:text-secondary font-semibold text-sm transition-colors whitespace-nowrap">Avaliações</a>
                <a href="/#contato"
                   class="text-gray-400 hover:text-secondary font-semibold text-sm transition-colors whitespace-nowrap">Contato</a>
            </div>

            {{-- ── Desktop actions (direita) ── --}}
            <div class="hidden lg:flex items-center gap-3 shrink-0">

                {{-- Carrinho --}}
                <a href="{{ route('carrinho') }}"
                   class="relative p-2 text-gray-400 hover:text-secondary transition-colors rounded-full hover:bg-secondary/10 group"
                   title="Carrinho">
                    <span class="material-icons text-[22px]">shopping_cart</span>
                    <span id="cart-count"
                          class="absolute -top-0.5 -right-0.5 bg-secondary text-primary text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full hidden">0</span>
                </a>

                @guest
                    <a href="/login"
                       class="flex items-center gap-1.5 bg-secondary hover:bg-[#c2884a] text-primary px-5 py-2 rounded-full font-bold text-sm transition-all shadow-md">
                        <span class="material-icons text-[16px]">login</span>
                        Entrar
                    </a>
                @endguest

                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-1.5 bg-secondary/10 text-secondary border border-secondary/25 px-3 py-1.5 rounded-full font-semibold text-xs hover:bg-secondary/20 transition-colors whitespace-nowrap">
                            <span class="material-icons text-[14px]">admin_panel_settings</span>
                            Admin
                        </a>
                    @endif

                    <a href="{{ route('perfil') }}"
                       class="flex items-center gap-2 bg-secondary/10 text-secondary border border-secondary/25 px-3 py-1.5 rounded-full font-semibold text-sm hover:bg-secondary/20 transition-colors">
                        <span class="w-6 h-6 rounded-full bg-secondary/25 flex items-center justify-center text-secondary font-bold text-[11px] shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                        <span class="hidden xl:inline truncate max-w-[100px]">{{ Str::words(Auth::user()->name, 1, '') }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-1.5 bg-secondary hover:bg-[#c2884a] text-primary px-4 py-2 rounded-full font-bold text-sm transition-all shadow-md whitespace-nowrap">
                            <span class="material-icons text-[16px]">logout</span>
                            Sair
                        </button>
                    </form>
                @endauth
            </div>

            {{-- ── Mobile: carrinho + hamburger ── --}}
            <div class="lg:hidden flex items-center gap-2 shrink-0">
                <a href="{{ route('carrinho') }}"
                   class="relative p-2 text-gray-400 hover:text-secondary transition-colors">
                    <span class="material-icons text-[22px]">shopping_cart</span>
                    <span id="cart-count-mobile"
                          class="absolute -top-0.5 -right-0.5 bg-secondary text-primary text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full hidden">0</span>
                </a>
                <button id="btn-hamburger"
                        class="p-2 text-gray-400 hover:text-secondary focus:outline-none transition-colors">
                    <span id="hamburger-icon" class="material-icons text-[26px]">menu</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Mobile menu drawer ── --}}
    <div id="mobile-menu" class="hidden lg:hidden bg-background-dark border-t border-secondary/10">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:text-secondary hover:bg-secondary/8 font-semibold text-sm transition-colors">
                <span class="material-icons text-[18px] text-secondary">home</span> Início
            </a>
            <a href="/#historia"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:text-secondary hover:bg-secondary/8 font-semibold text-sm transition-colors">
                <span class="material-icons text-[18px] text-secondary">history_edu</span> Nossa História
            </a>
            <a href="/#destaques"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:text-secondary hover:bg-secondary/8 font-semibold text-sm transition-colors">
                <span class="material-icons text-[18px] text-secondary">star</span> Destaques
            </a>
            <a href="{{ route('cardapio') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:text-secondary hover:bg-secondary/8 font-semibold text-sm transition-colors">
                <span class="material-icons text-[18px] text-secondary">restaurant_menu</span> Ver Cardápio
            </a>
            <a href="/#contato"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:text-secondary hover:bg-secondary/8 font-semibold text-sm transition-colors">
                <span class="material-icons text-[18px] text-secondary">location_on</span> Contato
            </a>
        </div>

        <div class="px-4 pb-4 pt-2 border-t border-secondary/10 space-y-2">
            @guest
                <a href="/login"
                   class="flex items-center justify-center gap-2 w-full bg-secondary text-primary font-bold px-4 py-3 rounded-xl hover:bg-[#c2884a] transition-all text-sm">
                    <span class="material-icons text-[16px]">login</span> Entrar / Cadastrar
                </a>
            @endguest
            @auth
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-secondary/10 text-secondary border border-secondary/20 font-semibold text-sm">
                        <span class="material-icons text-[16px]">admin_panel_settings</span> Painel Admin
                    </a>
                @endif
                <a href="{{ route('perfil') }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-secondary/20 text-secondary font-semibold text-sm hover:bg-secondary/10 transition-colors">
                    <span class="material-icons text-[16px]">account_circle</span> Minha Conta
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-secondary text-primary font-bold px-4 py-3 rounded-xl hover:bg-[#c2884a] transition-all text-sm">
                        <span class="material-icons text-[16px]">logout</span> Sair
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── Cart counter ──
        const badges = ['cart-count', 'cart-count-mobile'].map(id => document.getElementById(id));

        window.updateCartCount = function (count) {
            badges.forEach(badge => {
                if (!badge) return;
                badge.textContent = count;
                badge.classList.toggle('hidden', count < 1);
            });
        };

        fetch("{{ route('carrinho.count') }}")
            .then(r => r.json())
            .then(d => window.updateCartCount(d.count))
            .catch(() => {});

        // ── Hamburger ──
        const btn  = document.getElementById('btn-hamburger');
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('hamburger-icon');

        btn?.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (icon) icon.textContent = isOpen ? 'menu' : 'close';
        });

        // Fechar ao clicar em link de âncora
        menu?.querySelectorAll('a[href*="#"]').forEach(link =>
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
                if (icon) icon.textContent = 'menu';
            })
        );
    });
</script>

@include('partials.accessibility')