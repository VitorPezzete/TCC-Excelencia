<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Excelência Doces &amp; Salgados - Login e Cadastro</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>tailwind.config = {};</script>
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #1A0F0E; }
    ::-webkit-scrollbar-thumb { background: #d69c5e; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #c2884a; }
    .form-input {
        background-color: transparent;
        border: none;
        border-bottom: 1px solid rgba(214, 156, 94, 0.3);
        color: #fdf1e4;
        padding-left: 0;
        padding-right: 0;
        border-radius: 0;
        transition: border-color 0.3s ease;
    }
    .form-input:focus { outline: none; box-shadow: none; border-bottom-color: #d69c5e; }
    .form-input::placeholder { color: rgba(253, 241, 228, 0.5); }
</style>
</head>
<body class="font-body bg-background-dark text-text-light min-h-screen flex flex-col relative overflow-x-hidden">

<nav class="fixed w-full z-50 bg-background-dark/95 backdrop-blur-md shadow-sm border-b border-secondary/20 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex-shrink-0 flex items-center">
                <a class="flex items-center gap-3" href="#">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Excelência Logo" class="h-12 w-12 rounded-full border-2 border-secondary object-cover">
                    <span class="font-display font-bold text-2xl text-secondary">Excelência</span>
                </a>
            </div>
            <div class="hidden md:flex space-x-8 items-center">
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="{{ route('home') }}">Início</a>
                <a class="text-text-light hover:text-secondary font-semibold transition-colors" href="{{ route('cardapio') }}">Cardápio</a>
                <a class="bg-secondary hover:bg-[#c2884a] text-primary px-6 py-2 rounded-full font-bold transition-all shadow-soft transform hover:scale-105 flex items-center gap-2" href="#">
                    <span class="material-icons text-sm">login</span>
                    Entrar
                </a>
            </div>
            <div class="md:hidden flex items-center">
                <button class="text-text-light hover:text-secondary focus:outline-none">
                    <span class="material-icons text-3xl">menu</span>
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="flex-grow flex flex-col lg:flex-row pt-20 h-[calc(100vh-80px)] min-h-[700px]">

    <div class="hidden lg:block lg:w-1/2 relative bg-black overflow-hidden">
        <img alt="Interior da Cafeteria" class="absolute inset-0 w-full h-full object-cover opacity-60" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlmgiKC7kk2mTONgZUx_2nqynOo4WPR5tHfZN2EmtilrDIiQTCl_ANb6kc3UCeVCr7YdUIXj4GuGpC0iJkf3GCTDxdDttNzHigazymdb1Vg-tRRs7sA4APA6xGguHmXgmZL0W6TMSzm1R1SkqweXQp21tLF7a8svYRTOR-4AEVcmVrGB7780WVKLPrSPdansIMRmMdZ_RFPlwSC5oNWbcCe7IAH26i8ogoA45jQg2qXJI7VYBQGHiIuXs_NCGRTCdk7Kl03qIxsfWn"/>
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent flex flex-col justify-center p-16">
            <h1 class="font-display text-5xl xl:text-6xl font-bold text-white mb-6 leading-tight">
                Sua experiência<br/>premium começa<br/><span class="text-secondary italic">aqui.</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-md font-light">
                Acesse sua conta para fazer pedidos, acompanhar recompensas e descobrir novas delícias.
            </p>
            <div class="mt-12 flex gap-4">
                <div class="w-16 h-1 bg-secondary rounded-full"></div>
                <div class="w-4 h-1 bg-secondary/30 rounded-full"></div>
                <div class="w-4 h-1 bg-secondary/30 rounded-full"></div>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-16 bg-background-dark relative">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] pointer-events-none"></div>
        <div class="w-full max-w-md relative z-10">

            <div class="flex mb-12 border-b border-gray-800">
                <button id="tab-login" class="flex-1 pb-4 text-center font-bold text-lg border-b-2 border-secondary text-secondary font-display tracking-wide">
                    Entrar
                </button>
                <button id="tab-register" class="flex-1 pb-4 text-center font-bold text-lg border-b-2 border-transparent text-gray-500 hover:text-text-light transition-colors font-display tracking-wide">
                    Criar Conta
                </button>
            </div>

            <div class="space-y-8 animate-fade-in" id="login-form">
                <div class="text-center mb-10">
                    <h2 class="font-display text-3xl font-bold text-white mb-2">Bem-vindo de volta</h2>
                    <p class="text-gray-400 text-sm">Insira seus dados para pedir.</p>
                </div>
                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf

                    <div class="relative group">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="email">E-mail</label>
                        <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                            <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">mail</span>
                            <input class="form-input w-full py-3 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="email" id="email" placeholder="seu@email.com" type="email" value="{{ old('email') }}"/>
                        </div>
                        @error('email')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative group">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="password">Senha</label>
                        <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                            <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">lock</span>
                            <input class="form-input w-full py-3 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="password" id="password" placeholder="••••••••" type="password"/>
                            <button class="text-gray-500 hover:text-text-light focus:outline-none" id="toggle-password" type="button">
                                <span class="material-icons text-sm">visibility_off</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center">
                            <input class="h-4 w-4 rounded border-gray-700 bg-black/30 text-secondary focus:ring-secondary focus:ring-offset-background-dark" name="remember" id="remember-me" type="checkbox"/>
                            <label class="ml-2 block text-sm text-gray-400" for="remember-me">Lembrar de mim</label>
                        </div>
                        <!-- <a class="text-sm font-semibold text-secondary hover:text-[#c2884a] transition-colors" href="#">Esqueceu a senha?</a> -->
                    </div>

                    <button class="w-full bg-secondary hover:bg-[#c2884a] text-primary py-4 px-8 rounded-xl font-bold text-center transition-all shadow-[0_0_15px_rgba(214,156,94,0.2)] hover:shadow-[0_0_20px_rgba(214,156,94,0.4)] flex justify-center items-center gap-2 transform hover:-translate-y-1 mt-8" type="submit">
                        Acessar Conta
                        <span class="material-icons">arrow_forward</span>
                    </button>
                </form>
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Não tem uma conta?
                        <a id="link-to-register" class="font-bold text-secondary hover:underline cursor-pointer" href="#">Cadastre-se</a>
                    </p>
                </div>
            </div>

            <div class="hidden space-y-6 animate-fade-in" id="register-form">
                <div class="text-center mb-8">
                    <h2 class="font-display text-3xl font-bold text-white mb-2">Junte-se a nós</h2>
                    <p class="text-gray-400 text-sm">Preencha os dados abaixo para começar.</p>
                </div>
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div class="relative group">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="reg-name">Nome Completo</label>
                        <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                            <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">person</span>
                            <input class="form-input w-full py-2 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="name" id="reg-name" placeholder="João da Silva" type="text" value="{{ old('name') }}"/>
                        </div>
                        @error('name')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative group">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="reg-email">E-mail</label>
                        <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                            <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">mail</span>
                            <input class="form-input w-full py-2 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="email" id="reg-email" placeholder="seu@email.com" type="email" value="{{ old('email') }}"/>
                        </div>
                        @error('email')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative group">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="reg-phone">Telefone</label>
                        <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                            <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">phone</span>
                            <input class="form-input w-full py-2 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="phone" id="reg-phone" placeholder="(11) 99999-9999" type="tel" value="{{ old('phone') }}"/>
                        </div>
                        @error('phone')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative group">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="reg-password">Senha</label>
                        <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                            <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">lock</span>
                            <input class="form-input w-full py-2 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="password" id="reg-password" placeholder="••••••••" type="password"/>
                        </div>
                        @error('password')
                            <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="w-full bg-secondary hover:bg-[#c2884a] text-primary py-4 px-8 rounded-xl font-bold text-center transition-all shadow-[0_0_15px_rgba(214,156,94,0.2)] hover:shadow-[0_0_20px_rgba(214,156,94,0.4)] flex justify-center items-center gap-2 transform hover:-translate-y-1 mt-6" type="submit">
                        Criar Conta
                    </button>
                </form>
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Já tem uma conta?
                        <a id="link-to-login" class="font-bold text-secondary hover:underline cursor-pointer" href="#">Entre aqui</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

@vite('resources/js/login.js')
</body>
</html>