<!DOCTYPE html>
<html class="scroll-smooth" lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Excelência - Pagamento Seguro</title>
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <style>
        .mp-secure-field {
            height: 48px;
            background-color: #261715;
            border: 1px solid #374151; /* gray-700 */
            border-radius: 0.5rem;
            padding: 0 16px;
            color: #f3f4f6; /* gray-100 */
        }
        .mp-secure-field-focus { border-color: #d69c5e; box-shadow: 0 0 0 1px #d69c5e; }
        .mp-secure-field-error { border-color: #ef4444; }
    </style>
</head>
<body class="font-body bg-background-dark text-text-light min-h-screen flex flex-col relative overflow-x-hidden">

@include('header')

<div class="flex-grow max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-28 pb-12 relative z-10">
    <div class="mb-8 text-center">
        <h1 class="font-display text-4xl font-bold text-white mb-2">Checkout de Pagamento</h1>
        <p class="text-gray-400">Finalize o pagamento do seu Pedido #{{ $pedido->id }}</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Resumo do Pedido -->
        <div class="w-full md:w-1/3 order-2 md:order-1">
            <div class="bg-[#261715] rounded-xl border border-gray-800 p-6 shadow-soft sticky top-28">
                <h2 class="font-display text-2xl font-bold text-secondary border-b border-gray-800 pb-4 mb-4">Resumo</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($pedido->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Frete</span>
                        <span>R$ {{ number_format($pedido->taxa_entrega, 2, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-800 pt-4 flex justify-between font-bold text-xl text-white">
                        <span>Total</span>
                        <span class="text-secondary">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-800 text-sm text-gray-500 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#00b37e]">lock</span>
                    Seus dados de pagamento são processados de forma criptografada pelo Mercado Pago e não são salvos em nossos servidores.
                </div>
            </div>
        </div>

        <!-- Formulário Customizado Transparente -->
        <div class="w-full md:w-2/3 order-1 md:order-2">
            <div class="bg-background-card rounded-xl border border-gray-800 shadow-soft p-6 md:p-8">
                <h2 class="font-display text-xl font-bold text-white mb-6">Cartão de Crédito</h2>
                
                <form id="form-checkout">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Número do Cartão -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="block text-sm font-semibold text-gray-300">Número do Cartão</label>
                            <div id="form-checkout__cardNumber" class="mp-secure-field"></div>
                            <div class="flex items-center gap-2 mt-1">
                                <img id="payment-network-logo" class="h-5 hidden" src="" alt="Bandeira do Cartão"/>
                                <span id="error-cardNumber" class="text-xs text-red-500 hidden"></span>
                            </div>
                        </div>

                        <!-- Vencimento -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-300">Data de Vencimento</label>
                            <div id="form-checkout__expirationDate" class="mp-secure-field"></div>
                            <span id="error-expirationDate" class="text-xs text-red-500 hidden"></span>
                        </div>

                        <!-- CVV -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-300">Código de Segurança (CVV)</label>
                            <div id="form-checkout__securityCode" class="mp-secure-field"></div>
                            <span id="error-securityCode" class="text-xs text-red-500 hidden"></span>
                        </div>

                        <!-- Nome do Titular -->
                        <div class="md:col-span-2 space-y-1 mt-2">
                            <label class="block text-sm font-semibold text-gray-300">Nome do Titular (como no cartão)</label>
                            <input type="text" id="form-checkout__cardholderName" placeholder="NOME DO TITULAR"
                                class="w-full bg-[#261715] border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary transition-colors uppercase"/>
                        </div>

                        <!-- E-mail -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="block text-sm font-semibold text-gray-300">E-mail</label>
                            <input type="email" id="form-checkout__cardholderEmail" value="{{ $pedido->user->email }}"
                                class="w-full bg-[#261715] border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary transition-colors"/>
                        </div>

                        <!-- Tipo de Documento -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-300">Tipo de Documento</label>
                            <select id="form-checkout__identificationType"
                                class="w-full bg-[#261715] border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary transition-colors appearance-none">
                            </select>
                        </div>

                        <!-- Número do Documento -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-300">Número do Documento</label>
                            <input type="text" id="form-checkout__identificationNumber" placeholder="000.000.000-00"
                                class="w-full bg-[#261715] border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary transition-colors"/>
                        </div>
                        
                        <!-- Emissor (Oculto - Preenchido automaticamente) -->
                        <input type="hidden" id="form-checkout__issuer" />

                        <!-- Parcelas -->
                        <div class="md:col-span-2 space-y-1 mt-2">
                            <label class="block text-sm font-semibold text-gray-300">Parcelas</label>
                            <select id="form-checkout__installments"
                                class="w-full bg-[#261715] border border-gray-700 rounded-lg px-4 py-3 text-text-light focus:outline-none focus:border-secondary transition-colors appearance-none">
                                <option value="" disabled selected>Insira o número do cartão primeiro...</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-8">
                        <button type="submit" id="form-checkout__submit"
                            class="w-full bg-secondary hover:bg-[#c2884a] text-primary font-bold text-lg py-4 px-6 rounded-lg transition-all shadow-lg flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">lock</span>
                            Pagar R$ {{ number_format($pedido->total, 2, ',', '.') }}
                        </button>
                    </div>

                    <!-- Mensagens de Erro Geral -->
                    <div id="general-error" class="mt-4 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg hidden"></div>

                </form>
            </div>
        </div>
    </div>
</div>

@include('footer')

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const mp = new MercadoPago('{{ $publicKey }}', {
            locale: 'pt-BR'
        });

        // Configuração visual dos Secure Fields para o modo Escuro
        const customFonts = [];
        const fieldStyle = {
            fontFamily: 'Lato, sans-serif',
            fontSize: '16px',
            fontWeight: '400',
            color: '#f3f4f6',
            placeholderColor: '#6b7280'
        };

        const cardNumberElement = mp.fields.create('cardNumber', {
            placeholder: "0000 0000 0000 0000",
            style: fieldStyle
        });
        const expirationDateElement = mp.fields.create('expirationDate', {
            placeholder: "MM/YY",
            style: fieldStyle
        });
        const securityCodeElement = mp.fields.create('securityCode', {
            placeholder: "123",
            style: fieldStyle
        });

        cardNumberElement.mount('form-checkout__cardNumber');
        expirationDateElement.mount('form-checkout__expirationDate');
        securityCodeElement.mount('form-checkout__securityCode');

        let paymentMethodId = null;

        // Ao digitar o número do cartão
        cardNumberElement.on('binChange', async (data) => {
            const { bin } = data;
            const selectInstallments = document.getElementById('form-checkout__installments');
            const networkLogo = document.getElementById('payment-network-logo');
            
            if (!bin) {
                selectInstallments.innerHTML = '<option value="" disabled selected>Insira o número do cartão primeiro...</option>';
                networkLogo.classList.add('hidden');
                networkLogo.src = '';
                paymentMethodId = null;
                return;
            }

            try {
                const { results } = await mp.getPaymentMethods({ bin });
                const paymentMethod = results[0];
                paymentMethodId = paymentMethod.id;

                networkLogo.src = paymentMethod.secure_thumbnail;
                networkLogo.classList.remove('hidden');

                const amount = {{ $pedido->total }};
                
                // Get Installments
                const installmentsConfig = await mp.getInstallments({
                    amount: String(amount),
                    bin: bin,
                    paymentTypeId: 'credit_card'
                });
                
                if (installmentsConfig.length > 0) {
                    const payerCosts = installmentsConfig[0].payer_costs;
                    selectInstallments.innerHTML = '';
                    payerCosts.forEach(cost => {
                        const opt = document.createElement('option');
                        opt.value = cost.installments;
                        opt.text = cost.recommended_message;
                        selectInstallments.appendChild(opt);
                    });
                }
                
                // Get Issuers (Emissor do cartão)
                const issuersConfig = await mp.getIssuers({ paymentMethodId, bin });
                if(issuersConfig.length > 0) {
                    document.getElementById('form-checkout__issuer').value = issuersConfig[0].id;
                }

            } catch (e) {
                console.error('Erro ao buscar dados do bin', e);
            }
        });

        // Eventos de Focus/Blur e Validação para bordas dinâmicas
        ['cardNumber', 'expirationDate', 'securityCode'].forEach(field => {
            const el = eval(`${field}Element`);
            const div = document.getElementById(`form-checkout__${field}`);
            const errorSpan = document.getElementById(`error-${field}`);
            
            el.on('focus', () => { div.classList.add('mp-secure-field-focus'); });
            el.on('blur', () => { div.classList.remove('mp-secure-field-focus'); });
            el.on('validityChange', (state) => {
                if(state.error) {
                    div.classList.add('mp-secure-field-error');
                    errorSpan.textContent = state.error.message;
                    errorSpan.classList.remove('hidden');
                } else {
                    div.classList.remove('mp-secure-field-error');
                    errorSpan.classList.add('hidden');
                }
            });
        });

        // Carregar Tipos de Documento
        (async function getIdentificationTypes() {
            try {
                const identificationTypes = await mp.getIdentificationTypes();
                const select = document.getElementById('form-checkout__identificationType');
                identificationTypes.forEach(type => {
                    const opt = document.createElement('option');
                    opt.value = type.id;
                    opt.text = type.name;
                    select.appendChild(opt);
                });
            } catch (e) {
                console.error('Erro getIdentificationTypes', e);
            }
        })();

        // Submissão
        const form = document.getElementById('form-checkout');
        const btnSubmit = document.getElementById('form-checkout__submit');
        const generalError = document.getElementById('general-error');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Processando...';
            generalError.classList.add('hidden');

            try {
                const cardholderName = document.getElementById('form-checkout__cardholderName').value;
                const docType = document.getElementById('form-checkout__identificationType').value;
                const docNumber = document.getElementById('form-checkout__identificationNumber').value;
                const email = document.getElementById('form-checkout__cardholderEmail').value;
                const installments = document.getElementById('form-checkout__installments').value;
                const issuer = document.getElementById('form-checkout__issuer').value;

                if (!cardholderName || !docNumber || !installments) {
                    throw new Error("Por favor, preencha todos os dados solicitados.");
                }

                // Cria o token do cartão
                const token = await mp.fields.createCardToken({
                    cardholderName: cardholderName,
                    identificationType: docType,
                    identificationNumber: docNumber,
                });

                // Envia payload para o Backend
                const payload = {
                    token: token.id,
                    payment_method_id: paymentMethodId,
                    issuer_id: issuer || '',
                    installments: Number(installments),
                    payer: {
                        email: email,
                        identification: {
                            type: docType,
                            number: docNumber
                        }
                    }
                };

                const response = await fetch("{{ route('pagamento.cartao.processar', ['pedido' => $pedido->id]) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || "Pagamento recusado.");
                }

            } catch (error) {
                console.error(error);
                generalError.textContent = error.message || "Erro de comunicação com o Mercado Pago. Verifique os dados e tente novamente.";
                generalError.classList.remove('hidden');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<span class="material-symbols-outlined">lock</span> Pagar R$ {{ number_format($pedido->total, 2, ",", ".") }}';
            }
        });
    });
</script>
</body>
</html>
