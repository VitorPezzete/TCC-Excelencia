<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Excelência - Minha Conta</title>
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
<body class="font-body bg-background-dark text-text-light min-h-screen flex flex-col relative overflow-x-hidden"
    data-tab="{{ session('success_dados') || session('success_senha') ? 'dados' : (session('success_endereco') ? 'enderecos' : 'dados') }}">

@include('header')

<div class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-28 pb-12 flex flex-col md:flex-row gap-8 relative z-10">

    <aside class="w-full md:w-64 flex-shrink-0">
        <div class="bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft sticky top-28">
            <h2 class="font-display text-2xl font-bold text-secondary mb-6 border-b border-gray-800 pb-4">Minha Conta</h2>
            <nav class="space-y-2">
                <button data-tab="dados" class="tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-semibold text-left text-gray-400">
                    <span class="material-symbols-outlined">person</span> Meus Dados
                </button>
                <button data-tab="enderecos" class="tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-semibold text-left text-gray-400">
                    <span class="material-symbols-outlined">location_on</span> Meus Endereços
                </button>
                <button data-tab="pedidos" class="tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-semibold text-left text-gray-400">
                    <span class="material-symbols-outlined">receipt_long</span> Meus Pedidos
                </button>
                <div class="pt-4 mt-4 border-t border-gray-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-900/20 transition-colors font-semibold">
                            <span class="material-symbols-outlined">logout</span> Sair
                        </button>
                    </form>
                </div>
            </nav>
        </div>
    </aside>

    <main class="flex-1">

        <div id="tab-dados" class="tab-content hidden">
            <div class="mb-8">
                <h1 class="font-display text-4xl font-bold text-white mb-2">Meus Dados</h1>
                <p class="text-gray-400">Mantenha suas informações pessoais atualizadas.</p>
            </div>

            @if(session('success_dados'))
                <div class="mb-6 bg-green-900/30 border border-green-700 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <span class="material-icons text-sm">check_circle</span> {{ session('success_dados') }}
                </div>
            @endif

            <form method="POST" action="{{ route('perfil.dados') }}" class="space-y-8">
                @csrf
                <div class="bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft">
                    <h2 class="font-display text-2xl font-bold text-secondary mb-6 border-b border-gray-800 pb-4">Informações Pessoais</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-400">Nome Completo</label>
                            <input name="name" class="w-full bg-background-dark border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors" type="text" value="{{ old('name', $user->name) }}"/>
                            @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-400">E-mail</label>
                            <input class="w-full bg-background-dark/50 border border-gray-800 rounded-lg px-4 py-3 text-gray-500 cursor-not-allowed" disabled type="email" value="{{ $user->email }}"/>
                            <p class="text-xs text-gray-600">O e-mail não pode ser alterado.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-400">Telefone / WhatsApp</label>
                            <input name="phone" class="w-full bg-background-dark border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors" type="tel" value="{{ old('phone', $user->phone) }}"/>
                            @error('phone') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary hover:bg-[#c2884a] text-primary font-bold py-3 px-8 rounded-lg transition-colors" type="submit">Salvar Alterações</button>
                    </div>
                </div>
            </form>

            @if(session('success_senha'))
                <div class="mt-6 mb-2 bg-green-900/30 border border-green-700 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <span class="material-icons text-sm">check_circle</span> {{ session('success_senha') }}
                </div>
            @endif

            <form method="POST" action="{{ route('perfil.senha') }}" class="mt-8">
                @csrf
                <div class="bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft">
                    <h2 class="font-display text-2xl font-bold text-secondary mb-6 border-b border-gray-800 pb-4">Alterar Senha</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-400">Senha Atual</label>
                            <input name="current_password" class="w-full bg-background-dark border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors" placeholder="Digite sua senha atual" type="password"/>
                            @error('current_password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-400">Nova Senha</label>
                            <input name="new_password" class="w-full bg-background-dark border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors" placeholder="Mínimo 8 caracteres" type="password"/>
                            @error('new_password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-400">Confirmar Nova Senha</label>
                            <input name="new_password_confirmation" class="w-full bg-background-dark border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors" placeholder="Repita a nova senha" type="password"/>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-secondary hover:bg-[#c2884a] text-primary font-bold py-3 px-8 rounded-lg transition-colors" type="submit">Atualizar Senha</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="tab-enderecos" class="tab-content hidden">
            <div class="mb-8">
                <h1 class="font-display text-4xl font-bold text-white mb-2">Meus Endereços</h1>
                <p class="text-gray-400">Gerencie os endereços para entrega dos seus pedidos.</p>
            </div>

            @if(session('success_endereco'))
                <div class="mb-6 bg-green-900/30 border border-green-700 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <span class="material-icons text-sm">check_circle</span> {{ session('success_endereco') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <button id="btn-add-address" class="h-full min-h-[200px] border-2 border-dashed border-gray-700 hover:border-secondary rounded-xl flex flex-col items-center justify-center text-gray-500 hover:text-secondary bg-[#261715]/50 hover:bg-[#261715] transition-all group">
                    <div class="w-16 h-16 rounded-full bg-gray-800 group-hover:bg-secondary/20 flex items-center justify-center mb-4 transition-colors">
                        <span class="material-symbols-outlined text-3xl">add_location</span>
                    </div>
                    <span class="font-bold text-lg">Adicionar Novo Endereço</span>
                </button>

                @forelse($addresses as $address)
                    <div class="address-card bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft relative flex flex-col"
                        data-update-url="{{ route('perfil.enderecos.update', $address->id) }}"
                        data-name="{{ $address->nome }}"
                        data-cep="{{ $address->cep }}"
                        data-number="{{ $address->numero }}"
                        data-complement="{{ $address->complemento }}">

                        <div class="absolute top-6 right-6 flex gap-2">
                            <button type="button" class="btn-edit-address w-8 h-8 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white flex items-center justify-center transition-colors" title="Editar">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>

                            <form method="POST" action="{{ route('perfil.enderecos.destroy', $address->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-red-900/30 hover:bg-red-900/60 text-red-400 hover:text-red-300 flex items-center justify-center transition-colors" title="Remover"
                                    onclick="return confirm('Tem certeza que deseja remover este endereço?')">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center gap-3 mb-4">
                            <span class="material-symbols-outlined text-secondary text-2xl">home</span>
                            <h3 class="font-bold text-xl text-white">{{ $address->nome }}</h3>
                            @if($address->padrao)
                                <span class="bg-secondary/20 text-secondary text-xs font-bold px-2 py-1 rounded uppercase tracking-wider">Padrão</span>
                            @endif
                        </div>

                        <div class="text-gray-400 space-y-1 flex-grow mb-6">
                            <p class="realtime-address-street" data-cep="{{ $address->cep }}">Carregando endereço...</p>
                            <p>Número: {{ $address->numero }} @if($address->complemento) | Complemento: {{ $address->complemento }} @endif</p>
                            <p class="realtime-address-neighborhood"></p>
                            <p class="realtime-address-city"></p>
                        </div>

                        @if(!$address->padrao)
                            <form method="POST" action="{{ route('perfil.enderecos.padrao', $address->id) }}">
                                @csrf
                                <button type="submit" class="text-sm font-semibold text-gray-400 hover:text-secondary transition-colors mt-auto text-left w-fit">
                                    Tornar padrão
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 col-span-2 py-8">Nenhum endereço cadastrado ainda.</p>
                @endforelse
            </div>
        </div>

        <div id="tab-pedidos" class="tab-content hidden">
            <div class="mb-8">
                <h1 class="font-display text-4xl font-bold text-white mb-2">Meus Pedidos</h1>
                <p class="text-gray-400">Acompanhe o status e o histórico dos seus pedidos.</p>
            </div>
            <p class="text-gray-500 text-center py-12">Nenhum pedido encontrado.</p>
        </div>

    </main>
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
            <form id="form-address" method="POST"
                action="{{ route('perfil.enderecos.store') }}"
                data-store-url="{{ route('perfil.enderecos.store') }}"
                class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST"/>

                <div class="md:col-span-2">
                    <label class="block text-text-light font-semibold mb-2 text-sm">Nome do Endereço</label>
                    <input id="field-name" name="name" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="Ex: Casa, Trabalho" type="text"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">CEP</label>
                    <input id="field-cep" name="cep" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="00000-000" type="text"/>
                    <p id="cep-status" class="text-xs mt-1 hidden"></p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-text-light font-semibold mb-2 text-sm">Endereço</label>
                    <input id="field-street" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:outline-none placeholder-gray-600 opacity-70 cursor-not-allowed" placeholder="Preenchido automaticamente pelo CEP" type="text" readonly tabindex="-1"/>
                </div>

                <div>
                    <label class="block text-text-light font-semibold mb-2 text-sm">Número</label>
                    <input id="field-number" name="number" class="w-full bg-[#261715] border border-gray-700 text-text-light rounded-lg px-4 py-2 focus:ring-1 focus:ring-secondary focus:border-secondary outline-none transition-all placeholder-gray-600" placeholder="123" type="text"/>
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

                <div class="md:col-span-2 flex justify-end gap-4 pt-2">
                    <button id="modal-address-cancel" type="button" class="px-6 py-2 rounded-lg font-bold text-gray-400 hover:text-white transition-colors">Cancelar</button>
                    <button type="submit" class="px-6 py-2 rounded-lg font-bold bg-secondary text-primary hover:bg-[#c2884a] transition-colors">Salvar Endereço</button>
                </div>
            </form>
        </div>
    </div>
</div>

@vite('resources/js/perfil.js')
</body>
</html>