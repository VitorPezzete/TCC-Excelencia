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
            
            // Forçar o fetch de CEP
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

    // Carregar endereços em tempo real nos cards
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
});