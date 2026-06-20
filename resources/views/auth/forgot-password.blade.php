<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Recuperar Senha - Excelência</title>
<link rel="icon" type="image/png" href="/images/logo.png">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
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
<body class="font-body bg-background-dark text-text-light min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] pointer-events-none"></div>

    <div class="w-full max-w-md p-8 sm:p-12 relative z-10 animate-fade-in">
        <div class="text-center mb-8">
            <a href="{{ route('login') }}" class="inline-block mb-6">
                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo" class="h-16 w-16 mx-auto rounded-full border-2 border-secondary object-cover">
            </a>
            <h2 class="font-display text-3xl font-bold text-white mb-2">Esqueceu a senha?</h2>
            <p class="text-gray-400 text-sm">Sem problemas. Informe seu endereço de e-mail e enviaremos um link de recuperação.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 font-medium text-sm text-green-400 text-center bg-green-900/20 border border-green-500/30 rounded-lg p-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div class="relative group">
                <label class="text-xs font-bold text-secondary uppercase tracking-wider mb-1 block" for="email">E-mail Cadastrado</label>
                <div class="flex items-center gap-3 border-b border-secondary/30 focus-within:border-secondary transition-colors">
                    <span class="material-icons text-gray-500 group-focus-within:text-secondary transition-colors text-lg">mail</span>
                    <input class="form-input w-full py-3 bg-transparent text-white placeholder-gray-600 focus:ring-0 border-none" name="email" id="email" placeholder="seu@email.com" type="email" value="{{ old('email') }}" required autofocus/>
                </div>
                @error('email')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button class="w-full bg-secondary hover:bg-[#c2884a] text-primary py-4 px-8 rounded-xl font-bold text-center transition-all shadow-[0_0_15px_rgba(214,156,94,0.2)] hover:shadow-[0_0_20px_rgba(214,156,94,0.4)] flex justify-center items-center gap-2 transform hover:-translate-y-1 mt-8" type="submit">
                Enviar Link de Recuperação
                <span class="material-icons">send</span>
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-secondary hover:underline cursor-pointer flex items-center justify-center gap-1">
                <span class="material-icons text-[16px]">arrow_back</span>
                Voltar para o Login
            </a>
        </div>
    </div>
</body>
</html>
