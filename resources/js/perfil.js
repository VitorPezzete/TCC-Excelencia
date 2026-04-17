document.addEventListener('DOMContentLoaded', function () {

    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    function activateTab(tabName) {
        tabContents.forEach(c => c.classList.add('hidden'));
        tabBtns.forEach(btn => {
            btn.classList.remove('bg-secondary/10', 'text-secondary', 'border', 'border-secondary/30');
            btn.classList.add('text-gray-400');
        });

        const content = document.getElementById('tab-' + tabName);
        if (content) content.classList.remove('hidden');

        const activeBtn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-secondary/10', 'text-secondary', 'border', 'border-secondary/30');
            activeBtn.classList.remove('text-gray-400');
        }
    }

    tabBtns.forEach(btn => btn.addEventListener('click', function () {
        activateTab(this.dataset.tab);
    }));

    const initialTab = (document.body.dataset.tab || 'dados').trim();
    activateTab(initialTab);

    const modalAddress    = document.getElementById('modal-address');
    const modalTitle      = document.getElementById('modal-address-title');
    const formAddress     = document.getElementById('form-address');
    const methodField     = document.getElementById('form-method');
    const btnAddAddress   = document.getElementById('btn-add-address');
    const modalClose      = document.getElementById('modal-address-close');
    const modalCancel     = document.getElementById('modal-address-cancel');

    const fieldName         = document.getElementById('field-name');
    const fieldCep          = document.getElementById('field-cep');
    const fieldStreet       = document.getElementById('field-street');
    const fieldNumber       = document.getElementById('field-number');
    const fieldComplement   = document.getElementById('field-complement');
    const fieldNeighborhood = document.getElementById('field-neighborhood');
    const fieldCity         = document.getElementById('field-city');
    const fieldState        = document.getElementById('field-state');
    const cepStatus         = document.getElementById('cep-status');

    function openModal(mode = 'create', address = null) {
        formAddress.reset();
        cepStatus.classList.add('hidden');

        if (mode === 'create') {
            modalTitle.textContent = 'Adicionar Novo Endereço';
            formAddress.action = formAddress.dataset.storeUrl;
            methodField.value = 'POST';
        } else {
            modalTitle.textContent = 'Editar Endereço';
            formAddress.action = address.dataset.updateUrl;
            methodField.value = 'PUT';

            fieldName.value         = address.dataset.name         || '';
            fieldCep.value          = address.dataset.cep          || '';
            fieldNumber.value       = address.dataset.number       || '';
            fieldComplement.value   = address.dataset.complement   || '';
            
            fieldCep.dispatchEvent(new Event('blur'));
        }

        modalAddress.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalAddress.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (btnAddAddress) btnAddAddress.addEventListener('click', () => openModal('create'));
    if (modalClose)    modalClose.addEventListener('click', closeModal);
    if (modalCancel)   modalCancel.addEventListener('click', closeModal);
    modalAddress.addEventListener('click', e => { if (e.target === modalAddress) closeModal(); });

    document.querySelectorAll('.btn-edit-address').forEach(btn => {
        btn.addEventListener('click', function () {
            openModal('edit', this.closest('.address-card'));
        });
    });

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

    document.querySelectorAll('.address-card').forEach(async card => {
        const cep = card.dataset.cep;
        if (!cep) return;
        try {
            const rawCep = cep.replace(/\D/g, '');
            const res = await fetch(`https://viacep.com.br/ws/${rawCep}/json/`);
            const data = await res.json();
            if(!data.erro) {
                const streetEl = card.querySelector('.realtime-address-street');
                const neighEl  = card.querySelector('.realtime-address-neighborhood');
                const cityEl   = card.querySelector('.realtime-address-city');
                
                if(streetEl) streetEl.textContent = data.logradouro || '';
                if(neighEl)  neighEl.textContent  = data.bairro || '';
                if(cityEl)   cityEl.textContent   = `${data.localidade} - ${data.uf}, ${cep}`;
            } else {
                card.querySelector('.realtime-address-street').textContent = 'CEP Inválido ou não encontrado';
            }
        } catch {
            card.querySelector('.realtime-address-street').textContent = 'Erro ao buscar endereço em tempo real';
        }
    });

    const modalPedido = document.getElementById('modal-pedido');
    const modalPedidoClose = document.getElementById('modal-pedido-close');
    const modalPedidoCancel = document.getElementById('modal-pedido-cancel');
    const btnDetalhes = document.querySelectorAll('.btn-detalhes-pedido');

    function closePedidoModal() {
        modalPedido.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (modalPedidoClose) modalPedidoClose.addEventListener('click', closePedidoModal);
    if (modalPedidoCancel) modalPedidoCancel.addEventListener('click', closePedidoModal);
    if (modalPedido) {
        modalPedido.addEventListener('click', e => {
            if (e.target === modalPedido) closePedidoModal();
        });
    }

    const formatCurrency = val => parseFloat(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    const statusMap = {
        'pendente':          { label: 'Pendente', icon: 'pending', color: 'gray' },
        'confirmado':        { label: 'Confirmado', icon: 'thumb_up', color: 'blue' },
        'preparando':        { label: 'Em Preparação', icon: 'soup_kitchen', color: 'yellow' },
        'saiu_para_entrega': { label: 'Saiu para Entrega', icon: 'two_wheeler', color: 'blue' },
        'entregue':          { label: 'Entregue', icon: 'check_circle', color: 'green' },
        'cancelado':         { label: 'Cancelado', icon: 'cancel', color: 'red' },
    };

    btnDetalhes.forEach(btn => {
        btn.addEventListener('click', function() {
            const pedido = JSON.parse(this.dataset.pedido);
            
            document.getElementById('mp-id').textContent = '#' + String(pedido.id).padStart(4, '0');
            
            const dateObj = new Date(pedido.created_at);
            document.getElementById('mp-date').textContent = 'Realizado em ' + dateObj.toLocaleDateString('pt-BR', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit'});
            
            const statusInfo = statusMap[pedido.status] || statusMap['pendente'];
            document.getElementById('mp-status-label').textContent = statusInfo.label;
            document.getElementById('mp-status-label').className = `font-bold text-${statusInfo.color}-400 text-xl tracking-wide uppercase`;
            document.getElementById('mp-status-icon').textContent = statusInfo.icon;
            document.getElementById('mp-status-icon-container').className = `bg-${statusInfo.color}-500/20 text-${statusInfo.color}-400 p-3 rounded-full flex items-center justify-center shadow-inner shadow-${statusInfo.color}-500/10`;

            const itemsList = document.getElementById('mp-items-list');
            itemsList.innerHTML = '';
            
            pedido.itens.forEach(item => {
                const obsHTML = item.observacoes ? `<p class="text-sm text-gray-400">${item.observacoes}</p>` : '';
                itemsList.innerHTML += `
                    <div class="flex justify-between items-center bg-background-dark/50 p-3 rounded-lg border border-gray-800/50">
                        <div class="flex gap-4 items-center">
                            <div class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center text-secondary font-bold text-sm">${item.quantidade}x</div>
                            <div>
                                <p class="font-bold text-white">${item.produto.nome}</p>
                                ${obsHTML}
                            </div>
                        </div>
                        <p class="font-bold text-white">R$ ${formatCurrency(item.preco_total)}</p>
                    </div>
                `;
            });

            document.getElementById('mp-address-name').textContent = pedido.endereco ? pedido.endereco.nome : 'Retirada/Sem Endereço';
            
            if(pedido.endereco) {
                document.getElementById('mp-address-desc').innerHTML = `
                    ${pedido.endereco.logradouro || 'Buscando...'}, ${pedido.endereco.numero}<br/>
                    ${pedido.endereco.complemento ? pedido.endereco.complemento + '<br/>' : ''}
                    CEP: ${pedido.endereco.cep}<br/>
                `;
                
                const rawCep = pedido.endereco.cep.replace(/\D/g, '');
                fetch(`https://viacep.com.br/ws/${rawCep}/json/`)
                    .then(res => res.json())
                    .then(data => {
                        if(!data.erro) {
                            document.getElementById('mp-address-desc').innerHTML = `
                                ${data.logradouro}, ${pedido.endereco.numero}<br/>
                                ${pedido.endereco.complemento ? pedido.endereco.complemento + '<br/>' : ''}
                                ${data.bairro}<br/>
                                ${data.localidade} - ${data.uf}
                            `;
                        }
                    });
            } else {
                 document.getElementById('mp-address-desc').textContent = '--';
            }

            document.getElementById('mp-subtotal').textContent = 'R$ ' + formatCurrency(pedido.subtotal);
            document.getElementById('mp-taxa').textContent = pedido.taxa_entrega > 0 ? 'R$ ' + formatCurrency(pedido.taxa_entrega) : 'Grátis';
            document.getElementById('mp-taxa').className = pedido.taxa_entrega > 0 ? 'text-gray-400' : 'text-green-400';
            document.getElementById('mp-total').textContent = 'R$ ' + formatCurrency(pedido.total);

            modalPedido.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });
});