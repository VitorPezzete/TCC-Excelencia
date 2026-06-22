document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Modal de Alerta Customizado ──
    const modalAlert      = document.getElementById('modal-alert');
    const modalAlertIcon  = document.getElementById('modal-alert-icon');
    const modalAlertWrap  = document.getElementById('modal-alert-icon-wrap');
    const modalAlertTitle = document.getElementById('modal-alert-title');
    const modalAlertMsg   = document.getElementById('modal-alert-msg');
    const modalAlertOk    = document.getElementById('modal-alert-ok');

    function showAlert(title, msg, type = 'warning') {
        const configs = {
            warning: { icon: 'warning',     wrap: 'bg-amber-500/10 border border-amber-500/20', iconCls: 'text-amber-400'  },
            error:   { icon: 'error',        wrap: 'bg-red-500/10 border border-red-500/20',     iconCls: 'text-red-400'    },
            info:    { icon: 'info',         wrap: 'bg-blue-500/10 border border-blue-500/20',   iconCls: 'text-blue-400'   },
            success: { icon: 'check_circle', wrap: 'bg-green-500/10 border border-green-500/20', iconCls: 'text-green-400'  },
        };
        const cfg = configs[type] || configs.warning;
        modalAlertIcon.textContent     = cfg.icon;
        modalAlertWrap.className       = `w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${cfg.wrap}`;
        modalAlertIcon.className       = `material-symbols-outlined text-3xl ${cfg.iconCls}`;
        modalAlertTitle.textContent    = title;
        modalAlertMsg.textContent      = msg;
        modalAlert?.classList.remove('hidden');
        return new Promise(resolve => {
            modalAlertOk.onclick = () => { modalAlert?.classList.add('hidden'); resolve(); };
        });
    }

    // ── Quantidade & Remoção ──
    async function updateQuantity(id, action) {
        const qtySpan = document.querySelector(`.item-qty[data-id="${id}"]`);
        const newQty  = parseInt(qtySpan.textContent) + (action === 'increase' ? 1 : -1);
        if (newQty < 1) return;
        const res = await fetch(`/carrinho/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ quantidade: newQty })
        });
        if (res.ok) window.location.reload();
        else showAlert('Erro', 'Não foi possível atualizar a quantidade.', 'error');
    }

    document.querySelectorAll('.btn-increase').forEach(btn => {
        btn.addEventListener('click', () => updateQuantity(btn.dataset.id, 'increase'));
    });
    document.querySelectorAll('.btn-decrease').forEach(btn => {
        btn.addEventListener('click', () => updateQuantity(btn.dataset.id, 'decrease'));
    });
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', async () => {
            const res = await fetch(`/carrinho/${btn.dataset.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            if (res.ok) window.location.reload();
            else showAlert('Erro', 'Não foi possível remover o item.', 'error');
        });
    });

    // ── Frete Dinâmico ──
    const selectAddress = document.getElementById('select-address');
    const freteVal      = document.getElementById('frete-val');
    const totalVal      = document.getElementById('total-val');
    const freteMsg      = document.getElementById('frete-msg');
    const subtotalEl    = document.getElementById('subtotal-val');

    function fmtBRL(v) {
        return parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    async function calculateFrete() {
        if (!selectAddress || !subtotalEl) return;
        const subtotal   = parseFloat(subtotalEl.dataset.val);
        const enderecoId = selectAddress.value;

        if (!enderecoId) {
            freteVal.textContent = 'A calcular';
            freteVal.className   = 'text-gray-400';
            freteMsg.classList.add('hidden');
            totalVal.textContent = `R$ ${fmtBRL(subtotal)}`;
            return;
        }

        freteVal.textContent = '...';
        freteVal.className   = 'text-gray-400 animate-pulse';

        try {
            const res  = await fetch('/admin/calcular-frete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ endereco_id: enderecoId, subtotal })
            });
            const data = await res.json();

            if (data.taxa === null) {
                freteVal.textContent = 'Indisponível';
                freteVal.className   = 'text-red-400 font-bold';
                freteMsg.textContent = '⚠ ' + (data.mensagem || 'Bairro fora da área de entrega.');
                freteMsg.className   = 'text-xs mt-2 text-red-400';
                freteMsg.classList.remove('hidden');
                totalVal.textContent = `R$ ${fmtBRL(subtotal)}`;
            } else if (data.taxa === 0) {
                freteVal.textContent = 'Grátis 🎉';
                freteVal.className   = 'text-green-400 font-bold';
                freteMsg.textContent = `Frete grátis para ${data.zona}!`;
                freteMsg.className   = 'text-xs mt-2 text-green-400';
                freteMsg.classList.remove('hidden');
                totalVal.textContent = `R$ ${fmtBRL(subtotal)}`;
            } else {
                freteVal.textContent = `R$ ${fmtBRL(data.taxa)}`;
                freteVal.className   = 'text-secondary font-semibold';
                freteMsg.textContent = `Zona: ${data.zona}`;
                freteMsg.className   = 'text-xs mt-2 text-gray-500';
                freteMsg.classList.remove('hidden');
                totalVal.textContent = `R$ ${fmtBRL(subtotal + data.taxa)}`;
            }
        } catch {
            freteVal.textContent = 'Erro';
            freteVal.className   = 'text-red-400';
        }
    }

    if (selectAddress) {
        selectAddress.addEventListener('change', async function () {
            calculateFrete();
            const opt     = this.options[this.selectedIndex];
            const preview = document.getElementById('address-preview');
            if (this.value && preview) {
                document.getElementById('addr-nome').textContent    = opt.dataset.nome ?? '';
                
                let detalheText = [
                    opt.dataset.numero ? `Nº ${opt.dataset.numero}` : '',
                    opt.dataset.complemento ?? '',
                    opt.dataset.bairro ? `Bairro: ${opt.dataset.bairro}` : ''
                ].filter(Boolean).join(' — ');

                document.getElementById('addr-detalhe').textContent = detalheText;
                document.getElementById('addr-cep').textContent = opt.dataset.cep ? `CEP: ${opt.dataset.cep}` : '';
                preview.classList.remove('hidden');

                if (opt.dataset.cep) {
                    const cepLimpo = opt.dataset.cep.replace(/\D/g, '');
                    if (cepLimpo.length === 8) {
                        try {
                            const res = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
                            const data = await res.json();
                            if (!data.erro) {
                                detalheText = [
                                    data.logradouro || '',
                                    opt.dataset.numero ? `Nº ${opt.dataset.numero}` : '',
                                    opt.dataset.complemento ?? '',
                                    (data.bairro || opt.dataset.bairro) ? `Bairro: ${data.bairro || opt.dataset.bairro}` : '',
                                    data.localidade || ''
                                ].filter(Boolean).join(' — ');
                                document.getElementById('addr-detalhe').textContent = detalheText;
                            }
                        } catch(e) {}
                    }
                }
            } else if (preview) {
                preview.classList.add('hidden');
            }
        });
        if (selectAddress.value) selectAddress.dispatchEvent(new Event('change'));
    }

    // ── Modal de Endereço ──
    const modalAddress      = document.getElementById('modal-address');
    const formAddress       = document.getElementById('form-address');
    const btnAddAddress     = document.getElementById('btn-add-address');
    const fieldCep          = document.getElementById('field-cep');
    const fieldStreet       = document.getElementById('field-street');
    const fieldNumber       = document.getElementById('field-number');
    const fieldNeighborhood = document.getElementById('field-neighborhood');
    const fieldCity         = document.getElementById('field-city');
    const fieldState        = document.getElementById('field-state');
    const cepStatus         = document.getElementById('cep-status');

    function openAddressModal()  { formAddress.reset(); cepStatus.classList.add('hidden'); modalAddress.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeAddressModal() { modalAddress.classList.add('hidden'); document.body.style.overflow = ''; }

    btnAddAddress?.addEventListener('click', openAddressModal);
    document.getElementById('modal-address-close')?.addEventListener('click', closeAddressModal);
    document.getElementById('modal-address-cancel')?.addEventListener('click', closeAddressModal);
    modalAddress?.addEventListener('click', e => { if (e.target === modalAddress) closeAddressModal(); });

    if (fieldCep) {
        fieldCep.addEventListener('blur', async function () {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;
            cepStatus.textContent = 'Buscando endereço...';
            cepStatus.className   = 'text-xs mt-1 text-gray-400';
            cepStatus.classList.remove('hidden');
            try {
                const res  = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await res.json();
                if (data.erro) {
                    cepStatus.textContent = 'CEP não encontrado.';
                    cepStatus.className   = 'text-xs mt-1 text-red-400';
                    return;
                }
                fieldStreet.value       = data.logradouro || '';
                fieldNeighborhood.value = data.bairro     || '';
                fieldCity.value         = data.localidade || '';
                fieldState.value        = data.uf         || '';
                cepStatus.textContent = 'Endereço preenchido automaticamente!';
                cepStatus.className   = 'text-xs mt-1 text-green-400';
                fieldNumber.focus();
            } catch {
                cepStatus.textContent = 'Erro ao buscar CEP. Preencha manualmente.';
                cepStatus.className   = 'text-xs mt-1 text-red-400';
            }
        });
        fieldCep.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5, 8);
            this.value = v;
        });
    }

    // ── Checkout ──
    const modalPayment       = document.getElementById('modal-payment');
    const paymentMethod      = document.getElementById('payment-method');
    const trocoContainer     = document.getElementById('troco-container');
    const paymentTroco       = document.getElementById('payment-troco');
    const btnConfirmCheckout = document.getElementById('btn-confirm-checkout');
    const formCheckout       = document.getElementById('form-checkout');
    const pixUrlTemplate     = formCheckout?.dataset.pixUrl ?? '';

    function closePaymentModal() { modalPayment?.classList.add('hidden'); document.body.style.overflow = ''; }

    document.getElementById('btn-checkout')?.addEventListener('click', () => {
        if (!selectAddress || !selectAddress.value) {
            showAlert('Endereço necessário', 'Selecione ou adicione um endereço de entrega antes de continuar.', 'warning');
            return;
        }
        if (freteVal?.className?.includes('text-red-400')) {
            showAlert('Entrega indisponível', freteMsg?.textContent || 'Este endereço está fora da área de entrega.', 'error');
            return;
        }
        formCheckout?.reset();
        trocoContainer?.classList.add('hidden');
        modalPayment?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    document.getElementById('modal-payment-close')?.addEventListener('click', closePaymentModal);
    document.getElementById('modal-payment-cancel')?.addEventListener('click', closePaymentModal);
    modalPayment?.addEventListener('click', e => { if (e.target === modalPayment) closePaymentModal(); });

    paymentMethod?.addEventListener('change', function () {
        trocoContainer?.classList.toggle('hidden', this.value !== 'dinheiro');
        if (this.value !== 'dinheiro' && paymentTroco) paymentTroco.value = '';
    });

    // ── Modal PIX ──
    const modalPix      = document.getElementById('modal-pix');
    const pixLoading    = document.getElementById('pix-loading');
    const pixContent    = document.getElementById('pix-content');
    const pixError      = document.getElementById('pix-error');
    const pixErrorMsg   = document.getElementById('pix-error-msg');
    const pixQrImg      = document.getElementById('pix-qr-img');
    const pixCodigo     = document.getElementById('pix-codigo');
    const pixCountdown  = document.getElementById('pix-countdown');
    let   countdownTimer = null;
    let   pixStatusInterval = null;
    let   currentPedidoId = null;

    function openPixModal() {
        pixLoading?.classList.remove('hidden');
        pixContent?.classList.add('hidden');
        pixError?.classList.add('hidden');
        modalPix?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePixModal() {
        modalPix?.classList.add('hidden');
        document.body.style.overflow = '';
        if (countdownTimer) clearInterval(countdownTimer);
        if (pixStatusInterval) clearInterval(pixStatusInterval);
    }

    function startCountdown(expiracaoIso) {
        if (countdownTimer) clearInterval(countdownTimer);
        function tick() {
            const diff = new Date(expiracaoIso) - new Date();
            if (diff <= 0) {
                pixCountdown.textContent = 'Expirado';
                pixCountdown.classList.replace('text-amber-400', 'text-red-400');
                clearInterval(countdownTimer);
                return;
            }
            const min = String(Math.floor(diff / 60000)).padStart(2, '0');
            const sec = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            pixCountdown.textContent = `${min}:${sec}`;
        }
        tick();
        countdownTimer = setInterval(tick, 1000);
    }

    async function buscarPix(pedidoId) {
        const url = pixUrlTemplate.replace(':id', pedidoId);
        pixLoading?.classList.remove('hidden');
        pixContent?.classList.add('hidden');
        pixError?.classList.add('hidden');

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (res.ok && data.success) {
                // Exibe QR Code
                pixQrImg.src    = `data:image/png;base64,${data.qr_code_base64}`;
                pixCodigo.value = data.qr_code ?? '';
                pixLoading?.classList.add('hidden');
                pixContent?.classList.remove('hidden');
                if (data.expiracao) startCountdown(data.expiracao);
                startPixPolling(pedidoId);
            } else {
                throw new Error(data.message || 'Erro ao gerar PIX.');
            }
        } catch (err) {
            pixLoading?.classList.add('hidden');
            pixError?.classList.remove('hidden');
            pixError?.classList.add('flex');
            pixErrorMsg.textContent = err.message || 'Tente novamente em instantes.';
        }
    }

    function startPixPolling(pedidoId) {
        if (pixStatusInterval) clearInterval(pixStatusInterval);
        pixStatusInterval = setInterval(async () => {
            try {
                const res = await fetch(`/checkout/${pedidoId}/status`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.status === 'pendente' || data.status === 'confirmado' || data.status === 'preparando' || data.status === 'saiu_para_entrega' || data.status === 'entregue') {
                    clearInterval(pixStatusInterval);
                    if (countdownTimer) clearInterval(countdownTimer);
                    
                    pixContent.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-6 space-y-4">
                            <span class="material-symbols-outlined text-6xl text-[#00b37e]">check_circle</span>
                            <h3 class="text-xl font-bold text-white">Pagamento Aprovado!</h3>
                            <p class="text-gray-400 text-sm text-center">Seu pedido foi confirmado e já está na cozinha.</p>
                            <button id="btn-pix-concluido-auto" class="mt-4 w-full border border-[#00b37e]/40 hover:border-[#00b37e] text-[#00b37e] hover:text-white hover:bg-[#00b37e]/10 font-bold py-3 rounded-xl transition-all text-sm">
                                Acompanhar Meu Pedido
                            </button>
                        </div>
                    `;
                    document.getElementById('btn-pix-concluido-auto')?.addEventListener('click', () => {
                        window.location.href = '/perfil?tab=pedidos';
                    });
                }
            } catch (err) {
                console.error('Erro ao buscar status do PIX', err);
            }
        }, 5000); // Polling a cada 5 segundos
    }

    // Botão copiar código PIX
    document.getElementById('btn-copiar-pix')?.addEventListener('click', async () => {
        const texto  = pixCodigo?.value;
        const btnTxt = document.getElementById('btn-copiar-texto');
        if (!texto) return;
        try {
            await navigator.clipboard.writeText(texto);
            btnTxt.textContent = 'Copiado!';
            setTimeout(() => { btnTxt.textContent = 'Copiar'; }, 2500);
        } catch {
            pixCodigo.select();
            document.execCommand('copy');
        }
    });

    // Já paguei → redireciona para perfil
    document.getElementById('btn-pix-concluido')?.addEventListener('click', () => {
        closePixModal();
        window.location.href = '/perfil?tab=pedidos';
    });

    // Tentar novamente
    document.getElementById('btn-pix-retry')?.addEventListener('click', () => {
        if (currentPedidoId) buscarPix(currentPedidoId);
    });

    // Fechar modal PIX
    document.getElementById('modal-pix-close')?.addEventListener('click', closePixModal);
    modalPix?.addEventListener('click', e => { if (e.target === modalPix) closePixModal(); });

    // ── Submit do Checkout ──
    formCheckout?.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!paymentMethod.value) {
            showAlert('Pagamento', 'Selecione uma forma de pagamento.', 'warning');
            return;
        }
        btnConfirmCheckout.disabled  = true;
        btnConfirmCheckout.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2 text-[18px]">refresh</span> Processando...';

        try {
            const res  = await fetch('/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({
                    endereco_id:      selectAddress.value,
                    metodo_pagamento: paymentMethod.value,
                    troco_para:       paymentTroco?.value || null,
                }),
            });
            const data = await res.json();

            if (res.ok && data.success) {
                if (data.requer_pix) {
                    // Fluxo PIX: fecha modal de pagamento e abre modal do QR Code
                    currentPedidoId = data.pedido_id;
                    closePaymentModal();
                    openPixModal();
                    buscarPix(data.pedido_id);
                } else {
                    // Outros métodos: redireciona direto
                    window.location.href = data.redirect;
                }
            } else {
                await showAlert('Ops!', data.message || 'Erro ao processar pedido.', 'error');
                btnConfirmCheckout.disabled  = false;
                btnConfirmCheckout.innerHTML = 'Confirmar Pedido';
            }
        } catch {
            await showAlert('Erro', 'Erro na comunicação com o servidor. Tente novamente.', 'error');
            btnConfirmCheckout.disabled  = false;
            btnConfirmCheckout.innerHTML = 'Confirmar Pedido';
        }
    });
});

