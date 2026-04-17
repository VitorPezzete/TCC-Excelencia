document.addEventListener('DOMContentLoaded', function () {
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const btnIncrease = document.querySelectorAll('.btn-increase');
    const btnDecrease = document.querySelectorAll('.btn-decrease');
    const btnRemove   = document.querySelectorAll('.btn-remove');

    async function updateQuantity(id, action) {
        const qtySpan = document.querySelector(`.item-qty[data-id="${id}"]`);
        let currentQty = parseInt(qtySpan.textContent);
        let newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;

        if (newQty < 1) return;

        try {
            const response = await fetch(`/carrinho/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantidade: newQty })
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Ocorreu um erro ao atualizar a quantidade.');
            }
        } catch (error) {
            console.error('Erro:', error);
        }
    }

    btnIncrease.forEach(btn => {
        btn.addEventListener('click', function() {
            updateQuantity(this.dataset.id, 'increase');
        });
    });

    btnDecrease.forEach(btn => {
        btn.addEventListener('click', function() {
            updateQuantity(this.dataset.id, 'decrease');
        });
    });

    btnRemove.forEach(btn => {
        btn.addEventListener('click', async function() {
            if(!confirm('Tem certeza que deseja remover este item?')) return;
            
            const id = this.dataset.id;
            try {
                const response = await fetch(`/carrinho/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Erro ao remover o item.');
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        });
    });

    const selectAddress = document.getElementById('select-address');
    const freteVal = document.getElementById('frete-val');
    const totalVal = document.getElementById('total-val');
    const freteMsg = document.getElementById('frete-msg');
    const subtotalEl = document.getElementById('subtotal-val');

    function calculateFrete() {
        if (!selectAddress || !subtotalEl) return;
        
        const subtotal = parseFloat(subtotalEl.dataset.val);

        if (selectAddress.value !== "") {
            const freteMock = 15.00;
            freteVal.textContent = `R$ ${freteMock.toFixed(2).replace('.', ',')}`;
            freteMsg.classList.remove('hidden');
            
            const newTotal = subtotal + freteMock;
            totalVal.textContent = `R$ ${newTotal.toFixed(2).replace('.', ',')}`;
        } else {
            freteVal.textContent = "A calcular";
            freteMsg.classList.add('hidden');
            totalVal.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        }
    }

    if (selectAddress) {
        selectAddress.addEventListener('change', function() {
            calculateFrete();
            const opt = this.options[this.selectedIndex];
            const preview = document.getElementById('address-preview');
            const addrNome = document.getElementById('addr-nome');
            const addrDetalhe = document.getElementById('addr-detalhe');
            const addrCep = document.getElementById('addr-cep');
            if (this.value && preview) {
                const nome = opt.dataset.nome ?? '';
                const numero = opt.dataset.numero ? `Nº ${opt.dataset.numero}` : '';
                const complemento = opt.dataset.complemento ?? '';
                const cep = opt.dataset.cep ?? '';
                if (addrNome) addrNome.textContent = nome;
                if (addrDetalhe) addrDetalhe.textContent = [numero, complemento].filter(Boolean).join(' — ');
                if (addrCep) addrCep.textContent = cep ? `CEP: ${cep}` : '';
                preview.classList.remove('hidden');
            } else if (preview) {
                preview.classList.add('hidden');
            }
        });
        calculateFrete();
        selectAddress.dispatchEvent(new Event('change'));
    }


    // Lógica do Modal de Endereço (reaproveitada do Perfil)
    const modalAddress    = document.getElementById('modal-address');
    const formAddress     = document.getElementById('form-address');
    const btnAddAddress   = document.getElementById('btn-add-address');
    const modalClose      = document.getElementById('modal-address-close');
    const modalCancel     = document.getElementById('modal-address-cancel');

    const fieldCep          = document.getElementById('field-cep');
    const fieldStreet       = document.getElementById('field-street');
    const fieldNumber       = document.getElementById('field-number');
    const fieldNeighborhood = document.getElementById('field-neighborhood');
    const fieldCity         = document.getElementById('field-city');
    const fieldState        = document.getElementById('field-state');
    const cepStatus         = document.getElementById('cep-status');

    function openModal() {
        formAddress.reset();
        cepStatus.classList.add('hidden');
        modalAddress.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalAddress.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (btnAddAddress) btnAddAddress.addEventListener('click', openModal);
    if (modalClose)    modalClose.addEventListener('click', closeModal);
    if (modalCancel)   modalCancel.addEventListener('click', closeModal);
    if (modalAddress) {
        modalAddress.addEventListener('click', e => { 
            if (e.target === modalAddress) closeModal(); 
        });
    }

    if(fieldCep) {
        fieldCep.addEventListener('blur', async function () {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            cepStatus.textContent = 'Buscando endereço...';
            cepStatus.className = 'text-xs mt-1 text-gray-400';
            cepStatus.classList.remove('hidden');

            try {
                const res  = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await res.json();

                if (data.erro) {
                    cepStatus.textContent = 'CEP não encontrado.';
                    cepStatus.className = 'text-xs mt-1 text-red-400';
                    return;
                }

                fieldStreet.value       = data.logradouro || '';
                fieldNeighborhood.value = data.bairro     || '';
                fieldCity.value         = data.localidade || '';
                fieldState.value        = data.uf         || '';

                cepStatus.textContent = 'Endereço preenchido automaticamente!';
                cepStatus.className = 'text-xs mt-1 text-green-400';

                fieldNumber.focus();

            } catch {
                cepStatus.textContent = 'Erro ao buscar CEP. Preencha manualmente.';
                cepStatus.className = 'text-xs mt-1 text-red-400';
            }
        });

        fieldCep.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5, 8);
            this.value = v;
        });
    }

    // Lógica do Modal de Pagamento (Checkout)
    const btnCheckout = document.getElementById('btn-checkout');
    const modalPayment = document.getElementById('modal-payment');
    const modalPaymentClose = document.getElementById('modal-payment-close');
    const modalPaymentCancel = document.getElementById('modal-payment-cancel');
    const formCheckout = document.getElementById('form-checkout');
    const paymentMethod = document.getElementById('payment-method');
    const trocoContainer = document.getElementById('troco-container');
    const paymentTroco = document.getElementById('payment-troco');
    const btnConfirmCheckout = document.getElementById('btn-confirm-checkout');

    function openPaymentModal() {
        if (!selectAddress || !selectAddress.value) {
            alert('Por favor, selecione ou adicione um endereço de entrega primeiro.');
            return;
        }
        formCheckout.reset();
        trocoContainer.classList.add('hidden');
        modalPayment.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        modalPayment.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (btnCheckout) btnCheckout.addEventListener('click', openPaymentModal);
    if (modalPaymentClose) modalPaymentClose.addEventListener('click', closePaymentModal);
    if (modalPaymentCancel) modalPaymentCancel.addEventListener('click', closePaymentModal);
    if (modalPayment) {
        modalPayment.addEventListener('click', e => { 
            if (e.target === modalPayment) closePaymentModal(); 
        });
    }
    
    if (paymentMethod) {
        paymentMethod.addEventListener('change', function() {
            if (this.value === 'dinheiro') {
                trocoContainer.classList.remove('hidden');
            } else {
                trocoContainer.classList.add('hidden');
                paymentTroco.value = '';
            }
        });
    }

    if (formCheckout) {
        formCheckout.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnConfirmCheckout.disabled = true;
            btnConfirmCheckout.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2">refresh</span> Processando...';

            try {
                const response = await fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        endereco_id: selectAddress.value,
                        metodo_pagamento: paymentMethod.value,
                        troco_para: paymentTroco.value || null
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Erro ao processar pedido.');
                    btnConfirmCheckout.disabled = false;
                    btnConfirmCheckout.innerHTML = 'Confirmar Pedido';
                }
            } catch (error) {
                console.error('Erro no checkout:', error);
                alert('Erro na comunicação com o servidor.');
                btnConfirmCheckout.disabled = false;
                btnConfirmCheckout.innerHTML = 'Confirmar Pedido';
            }
        });
    }
});
