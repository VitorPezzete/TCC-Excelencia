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
        /* Customizando o container do Mercado Pago para combinar com nosso tema Dark */
        #paymentBrick_container {
            min-height: 400px;
        }
    </style>
</head>
<body class="font-body bg-background-dark text-text-light min-h-screen flex flex-col relative overflow-x-hidden">

@include('header')

<div class="flex-grow max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-28 pb-12 relative z-10">
    <div class="mb-8 text-center">
        <h1 class="font-display text-4xl font-bold text-white mb-2">Pagamento Seguro</h1>
        <p class="text-gray-400">Finalize o pagamento do seu Pedido #{{ $pedido->id }}</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Resumo do Pedido -->
        <div class="w-full md:w-1/3">
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
                    Pagamento processado de forma segura pelo Mercado Pago.
                </div>
            </div>
        </div>

        <!-- Área do Mercado Pago Bricks -->
        <div class="w-full md:w-2/3">
            <div class="bg-white rounded-xl overflow-hidden shadow-soft">
                <div id="paymentBrick_container"></div>
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

        const bricksBuilder = mp.bricks();

        const renderPaymentBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    amount: {{ $pedido->total }},
                    preferenceId: null, // Estamos usando o fluxo sem preferenceId prévio (PaymentAPI direta)
                },
                customization: {
                    visual: {
                        style: {
                            theme: "default",
                            customVariables: {
                                textPrimaryColor: "#333",
                                textSecondaryColor: "#666",
                                inputBackgroundColor: "#fff",
                                formBackgroundColor: "#fff",
                                baseColor: "#d69c5e", // Nossa cor secundária (dourado)
                                successColor: "#00b37e",
                                warningColor: "#eab308",
                                errorColor: "#ef4444",
                            }
                        }
                    },
                    paymentMethods: {
                        creditCard: "all",
                        debitCard: "all",
                        ticket: "all",
                        bankTransfer: "all",
                    },
                },
                callbacks: {
                    onReady: () => {
                        console.log('Payment Brick Ready');
                    },
                    onSubmit: ({ selectedPaymentMethod, formData }) => {
                        // Envia os dados para o nosso backend
                        return new Promise((resolve, reject) => {
                            fetch("{{ route('pagamento.cartao.processar', ['pedido' => $pedido->id]) }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(formData),
                            })
                            .then((response) => response.json())
                            .then((response) => {
                                if (response.success) {
                                    window.location.href = response.redirect;
                                    resolve();
                                } else {
                                    alert(response.message || "Ocorreu um erro ao processar o pagamento.");
                                    reject();
                                }
                            })
                            .catch((error) => {
                                console.error('Erro na requisição:', error);
                                alert("Falha na comunicação com o servidor.");
                                reject();
                            });
                        });
                    },
                    onError: (error) => {
                        console.error('Payment Brick Error:', error);
                    },
                },
            };
            window.paymentBrickController = await bricksBuilder.create(
                "payment",
                "paymentBrick_container",
                settings
            );
        };
        
        renderPaymentBrick(bricksBuilder);
    });
</script>
</body>
</html>
