<nav class="fixed w-full z-50 bg-background-dark/95 backdrop-blur-md shadow-sm border-b border-secondary/20 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex-shrink-0 flex items-center">
                <a class="flex items-center gap-3" href="#">
                    <img 
                        src="{{ Vite::asset('resources/images/logo.png') }}" 
                        alt="Excelência Logo" 
                        class="h-12 w-12 rounded-full border-2 border-secondary object-cover">
                    <span class="font-display font-bold text-2xl text-secondary">Excelência</span>
                </a>
            </div>

            <div class="hidden md:flex space-x-8 items-center">
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="{{ route('home') }}">Início</a>
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="/#historia">Nossa História</a>
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="/#destaques">Destaques</a>
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="{{ route('cardapio') }}">Ver Cardápio</a>
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="#contato">Contato</a>
                <!-- <a class="bg-secondary hover:bg-[#c2884a] text-primary px-6 py-2 rounded-full font-bold transition-all shadow-soft transform hover:scale-105" href="/cardapio">Ver Cardápio</a> -->

                @guest
                    {{-- Visitante: mostra botão Entrar --}}
                    <a class="bg-secondary hover:bg-[#c2884a] text-primary px-6 py-2 rounded-full font-bold transition-all shadow-soft transform hover:scale-105 flex items-center gap-2" href="/login">
                        <span class="material-icons text-sm">login</span>
                        Entrar
                    </a>
                @endguest

 @auth
    <a href="{{ route('perfil') }}" class="bg-secondary/20 text-secondary px-4 py-2 rounded-full font-semibold text-sm flex items-center gap-2 border border-secondary/30 hover:bg-secondary/30 transition-colors">
        <span class="material-icons text-sm">account_circle</span>
        Olá, {{ Auth::user()->name }}
    </a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-secondary hover:bg-[#c2884a] text-primary px-6 py-2 rounded-full font-bold transition-all shadow-soft transform hover:scale-105 flex items-center gap-2">
            <span class="material-icons text-sm">logout</span>
            Sair
        </button>
    </form>
@endauth
            </div>

            <div class="md:hidden flex items-center">
                <button class="text-text-light hover:text-secondary focus:outline-none">
                    <span class="material-icons text-3xl">menu</span>
                </button>
            </div>
        </div>
    </div>
</nav>