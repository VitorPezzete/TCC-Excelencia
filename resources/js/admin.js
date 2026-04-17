document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    async function patchJson(url, body) {
        return fetch(url, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
    }

    async function postFormData(url, formData, method = 'POST') {
        const headers = { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' };
        if (method !== 'POST') formData.append('_method', method);
        return fetch(url, { method: 'POST', headers, body: formData });
    }

    function showToast(msg, type = 'success') {
        const bg   = type === 'success' ? 'bg-green-500/90' : type === 'error' ? 'bg-red-500/90' : 'bg-blue-500/90';
        const icon = type === 'success' ? 'check_circle'   : type === 'error' ? 'error'         : 'info';
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-[999] ${bg} text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold flex items-center gap-2 animate-fade-up`;
        toast.innerHTML = `<span class="material-symbols-outlined text-[18px]">${icon}</span>${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }

    const STATUS_LABELS  = { pendente:'Pendente', confirmado:'Confirmado', preparando:'Preparando', saiu_para_entrega:'Saiu p/ Entrega', entregue:'Entregue', cancelado:'Cancelado' };
    const STATUS_CLASSES = { pendente:'bg-gray-500/15 text-gray-400 border-gray-700/50', confirmado:'bg-blue-500/15 text-blue-400 border-blue-800/50', preparando:'bg-amber-500/15 text-amber-400 border-amber-800/50', saiu_para_entrega:'bg-purple-500/15 text-purple-400 border-purple-800/50', entregue:'bg-green-500/15 text-green-400 border-green-800/50', cancelado:'bg-red-500/15 text-red-400 border-red-800/50' };
    const STATUS_ICONS   = { pendente:'schedule', confirmado:'thumb_up', preparando:'soup_kitchen', saiu_para_entrega:'two_wheeler', entregue:'check_circle', cancelado:'cancel' };

    const sections  = document.querySelectorAll('.section');
    const sideLinks = document.querySelectorAll('.sidebar-link[data-section]');
    const PAGE_META = {
        overview:   { title: 'Visão Geral',  sub: 'Resumo geral do seu negócio' },
        pedidos:    { title: 'Pedidos',       sub: 'Gerencie e atualize os pedidos' },
        produtos:   { title: 'Produtos',      sub: 'Cadastre e edite seus produtos' },
        avaliacoes: { title: 'Avaliações',    sub: 'Feedback dos seus clientes' },
        usuarios:   { title: 'Usuários',      sub: 'Controle de acesso' },
    };

    function activateSection(name) {
        sections.forEach(s => s.classList.remove('active'));
        sideLinks.forEach(l => l.classList.remove('active'));
        const section = document.getElementById('section-' + name);
        if (section) { section.classList.remove('animate-fade-up'); void section.offsetWidth; section.classList.add('active', 'animate-fade-up'); }
        document.querySelector(`.sidebar-link[data-section="${name}"]`)?.classList.add('active');
        const meta = PAGE_META[name] || { title: name, sub: '' };
        const pageTitle    = document.getElementById('page-title');
        const pageSubtitle = document.getElementById('page-subtitle');
        if (pageTitle) pageTitle.textContent    = meta.title;
        if (pageSubtitle) pageSubtitle.textContent = meta.sub;
        closeMobileSidebar();
        if (name === 'avaliacoes') loadAvaliacoes();
    }

    sideLinks.forEach(btn => btn.addEventListener('click', () => activateSection(btn.dataset.section)));
    document.querySelectorAll('[data-jump]').forEach(btn => {
        btn.addEventListener('click', () => activateSection(btn.dataset.jump));
    });

    const btnMobileMenu  = document.getElementById('btn-mobile-menu');
    const sidebarDrawer  = document.getElementById('sidebar-drawer');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function openMobileSidebar()  { sidebarDrawer?.classList.remove('-translate-x-full'); sidebarOverlay?.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeMobileSidebar() { sidebarDrawer?.classList.add('-translate-x-full');    sidebarOverlay?.classList.add('hidden');    document.body.style.overflow = ''; }

    btnMobileMenu?.addEventListener('click', openMobileSidebar);
    sidebarOverlay?.addEventListener('click', closeMobileSidebar);

    const data = window.adminData || {};

    const CHART_BASE_OPTIONS = {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1d0e0b',
                borderColor: '#d69c5e22',
                borderWidth: 1,
                titleColor: '#d69c5e',
                bodyColor: '#e5e7eb',
                padding: 10,
            }
        }
    };

    let chartFat;
    function buildFatChart(period) {
        const raw    = period === 7 ? data.fat7 : data.fat30;
        const labels = raw.map(d => d.label);
        const values = raw.map(d => d.total);
        if (chartFat) chartFat.destroy();
        const ctx = document.getElementById('chartFaturamento')?.getContext('2d');
        if (!ctx) return;
        chartFat = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets: [{
                label: 'Faturamento (R$)',
                data: values,
                borderColor: '#d69c5e',
                backgroundColor: 'rgba(214,156,94,0.06)',
                borderWidth: 2.5,
                pointBackgroundColor: '#d69c5e',
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true,
            }]},
            options: {
                ...CHART_BASE_OPTIONS,
                plugins: { ...CHART_BASE_OPTIONS.plugins,
                    tooltip: { ...CHART_BASE_OPTIONS.plugins.tooltip,
                        callbacks: { label: ctx => ' R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits:2 }) }
                    }
                },
                scales: {
                    x: { grid:{ color:'rgba(255,255,255,0.03)' }, ticks:{ color:'#4b5563', font:{ size:10 } } },
                    y: { grid:{ color:'rgba(255,255,255,0.03)' }, ticks:{ color:'#4b5563', font:{ size:10 },
                        callback: v => 'R$' + v.toLocaleString('pt-BR') } }
                }
            }
        });
    }

    buildFatChart(7);

    const btn7  = document.getElementById('btn-fat-7');
    const btn30 = document.getElementById('btn-fat-30');
    function setActiveToggle(active, inactive) {
        active?.classList.add('active-toggle');
        active?.classList.remove('text-gray-500');
        inactive?.classList.remove('active-toggle');
        inactive?.classList.add('text-gray-500');
    }
    setActiveToggle(btn7, btn30);
    btn7?.addEventListener('click',  () => { buildFatChart(7);  setActiveToggle(btn7, btn30); });
    btn30?.addEventListener('click', () => { buildFatChart(30); setActiveToggle(btn30, btn7); });

    const STATUS_COLORS_MAP = { pendente:'#6b7280', confirmado:'#60a5fa', preparando:'#f59e0b', saiu_para_entrega:'#a855f7', entregue:'#22c55e', cancelado:'#ef4444' };
    (() => {
        const statusData = data.status || {};
        const sLabels = Object.keys(statusData).map(k => STATUS_LABELS[k] || k);
        const sValues = Object.values(statusData);
        const sColors = Object.keys(statusData).map(k => STATUS_COLORS_MAP[k] || '#888');
        if (!sValues.length) return;
        const ctx = document.getElementById('chartStatus')?.getContext('2d');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: { labels: sLabels, datasets: [{ data: sValues, backgroundColor: sColors, borderColor: '#0f0806', borderWidth: 3, hoverOffset: 8 }] },
            options: {
                ...CHART_BASE_OPTIONS,
                plugins: { ...CHART_BASE_OPTIONS.plugins,
                    legend: { position: 'bottom', labels: { color: '#6b7280', font: { size: 10 }, padding: 12, boxWidth: 10, usePointStyle: true, pointStyle: 'circle' } },
                    tooltip: { ...CHART_BASE_OPTIONS.plugins.tooltip,
                        callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} pedidos` }
                    }
                }
            }
        });
    })();

    (() => {
        const catData = data.catFat || [];
        if (!catData.length) return;
        const ctx = document.getElementById('chartCategoria')?.getContext('2d');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: catData.map(c => c.nome),
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: catData.map(c => c.total),
                    backgroundColor: 'rgba(214,156,94,0.7)',
                    borderColor: '#d69c5e',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                ...CHART_BASE_OPTIONS,
                plugins: { ...CHART_BASE_OPTIONS.plugins,
                    tooltip: { ...CHART_BASE_OPTIONS.plugins.tooltip,
                        callbacks: { label: ctx => ' R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits:2 }) }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#4b5563', font:{ size:10 } } },
                    y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color:'#4b5563', font:{ size:10 }, callback: v => 'R$' + v.toLocaleString('pt-BR') } }
                }
            }
        });
    })();

    const pedidoSearch   = document.getElementById('pedido-search');
    const filtroDataIni  = document.getElementById('filtro-data-ini');
    const filtroDataFim  = document.getElementById('filtro-data-fim');
    const btnLimparData  = document.getElementById('btn-limpar-data');

    function applyPedidoFilters() {
        const q       = pedidoSearch?.value.toLowerCase() || '';
        const ini     = filtroDataIni?.value || '';
        const fim     = filtroDataFim?.value || '';
        const statusBtn = document.querySelector('.status-filter-btn.border-secondary');
        const statusSel = statusBtn?.dataset.status || '';
        document.querySelectorAll('#pedidos-body tr').forEach(row => {
            const text    = row.textContent.toLowerCase();
            const rowSt   = row.dataset.status || '';
            const rowDate = row.dataset.date   || '';
            const matchQ   = !q         || text.includes(q);
            const matchSt  = !statusSel || rowSt   === statusSel;
            const matchIni = !ini       || rowDate >= ini;
            const matchFim = !fim       || rowDate <= fim;
            row.style.display = (matchQ && matchSt && matchIni && matchFim) ? '' : 'none';
        });
    }

    pedidoSearch?.addEventListener('input', applyPedidoFilters);
    filtroDataIni?.addEventListener('change', applyPedidoFilters);
    filtroDataFim?.addEventListener('change', applyPedidoFilters);
    btnLimparData?.addEventListener('click', () => {
        if (filtroDataIni) filtroDataIni.value = '';
        if (filtroDataFim) filtroDataFim.value = '';
        applyPedidoFilters();
    });

    document.querySelectorAll('.status-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.status-filter-btn').forEach(b => {
                b.classList.remove('border-secondary', 'text-secondary');
                b.classList.add('border-white/10', 'text-gray-600');
            });
            this.classList.remove('border-white/10', 'text-gray-600');
            this.classList.add('border-secondary', 'text-secondary');
            applyPedidoFilters();
        });
    });

    function applyProdutoFilters() {
        const q   = document.getElementById('produto-search')?.value.toLowerCase() || '';
        const cat = document.getElementById('produto-cat-filter')?.value || '';
        document.querySelectorAll('#produtos-tbody tr').forEach(row => {
            const nome   = row.dataset.nome || '';
            const rowCat = row.dataset.cat  || '';
            row.style.display = ((!q || nome.includes(q)) && (!cat || rowCat === cat)) ? '' : 'none';
        });
    }
    document.getElementById('produto-search')?.addEventListener('input', applyProdutoFilters);
    document.getElementById('produto-cat-filter')?.addEventListener('change', applyProdutoFilters);

    document.getElementById('usuario-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#usuarios-tbody tr').forEach(row => {
            row.style.display = (row.dataset.usuario || '').includes(q) ? '' : 'none';
        });
    });

    const modalStatus   = document.getElementById('modal-status');
    const modalStatusId = document.getElementById('modal-status-id');
    let currentOrderId  = null;

    document.querySelectorAll('.btn-change-status').forEach(btn => {
        btn.addEventListener('click', function() {
            currentOrderId = this.dataset.id;
            if (modalStatusId) modalStatusId.textContent = '#' + String(currentOrderId).padStart(4, '0');
            modalStatus?.classList.remove('hidden');
        });
    });

    const closeModalStatus = () => modalStatus?.classList.add('hidden');
    document.getElementById('modal-status-close')?.addEventListener('click', closeModalStatus);
    modalStatus?.addEventListener('click', e => { if (e.target === modalStatus) closeModalStatus(); });

    document.querySelectorAll('.btn-status-choice').forEach(btn => {

        btn.addEventListener('click', async function() {
            if (!currentOrderId) return;
            const newStatus = this.dataset.status;
            const res = await patchJson(`/admin/pedidos/${currentOrderId}/status`, { status: newStatus });
            if (res.ok) {
                const badge = document.getElementById(`badge-status-${currentOrderId}`);
                if (badge) {
                    badge.className = `btn-change-status status-pill ${STATUS_CLASSES[newStatus] || ''}`;
                    badge.dataset.id = currentOrderId;
                    badge.innerHTML = `<span class="material-symbols-outlined text-[13px]">${STATUS_ICONS[newStatus]}</span>${STATUS_LABELS[newStatus]}<span class="material-symbols-outlined text-[11px]">expand_more</span>`;
                }
                document.querySelector(`tr[data-id="${currentOrderId}"]`)?.setAttribute('data-status', newStatus);
                closeModalStatus();
                showToast('Status atualizado!');
            } else {
                showToast('Erro ao atualizar status.', 'error');
            }
        });
    });

    function bindToggleProduto(btn) {
        btn.addEventListener('click', async function() {
            const id  = this.dataset.id;
            const res = await patchJson(`/admin/produtos/${id}/toggle`, {});
            if (res.ok) {
                const d    = await res.json();
                const span = document.getElementById(`produto-status-${id}`);
                if (span) {
                    span.className = 'badge ' + (d.ativo ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-500');
                    span.innerHTML = `<span class="material-symbols-outlined text-[12px]">${d.ativo ? 'check_circle' : 'cancel'}</span>${d.ativo ? 'Ativo' : 'Inativo'}`;
                }
                this.textContent  = d.ativo ? 'Desativar' : 'Ativar';
                this.dataset.ativo = d.ativo ? '1' : '0';
                showToast(d.ativo ? 'Produto ativado!' : 'Produto desativado!');
            }
        });
    }
    document.querySelectorAll('.btn-toggle-produto').forEach(bindToggleProduto);

    function bindToggleDestaque(btn) {
        btn.addEventListener('click', async function() {
            const id  = this.dataset.id;
            const res = await patchJson(`/admin/produtos/${id}/toggle-destaque`, {});
            if (res.ok) {
                const d    = await res.json();
                const icon = document.getElementById(`destaque-icon-${id}`);
                if (icon) {
                    icon.style.fontVariationSettings = `'FILL' ${d.destaque ? '1' : '0'}`;
                    icon.className = `material-symbols-outlined text-xl ${d.destaque ? 'text-secondary' : 'text-gray-700'}`;
                }
                this.dataset.destaque = d.destaque ? '1' : '0';
                this.title = d.destaque ? 'Remover Destaque' : 'Tornar Destaque';
            }
        });
    }
    document.querySelectorAll('.btn-toggle-destaque').forEach(bindToggleDestaque);

    document.querySelectorAll('.btn-toggle-admin').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Alterar perfil deste usuário?')) return;
            const id  = this.dataset.id;
            const res = await patchJson(`/admin/usuarios/${id}/toggle-admin`, {});
            if (res.ok) {
                const d     = await res.json();
                const badge = document.getElementById(`usuario-badge-${id}`);
                if (badge) {
                    badge.className = 'badge ' + (d.is_admin ? 'bg-secondary/10 text-secondary' : 'bg-white/5 text-gray-500');
                    badge.innerHTML = `<span class="material-symbols-outlined text-[12px]">${d.is_admin ? 'admin_panel_settings' : 'person'}</span>${d.is_admin ? 'Admin' : 'Cliente'}`;
                }
                this.textContent = d.is_admin ? 'Remover Admin' : 'Tornar Admin';
                showToast(d.is_admin ? 'Usuário promovido a Admin!' : 'Admin removido com sucesso.');
            }
        });
    });

    const modalProduto   = document.getElementById('modal-produto');
    const formProduto    = document.getElementById('form-produto');
    const modalTitle     = document.getElementById('modal-produto-titulo');
    const editIdInput    = document.getElementById('produto-edit-id');
    const fImagem        = document.getElementById('f-imagem');
    const imgPreview     = document.getElementById('img-preview');
    const imgPlaceholder = document.getElementById('img-preview-placeholder');
    const imgLabelText   = document.getElementById('img-label-text');

    fImagem?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                imgPreview.src = e.target.result;
                imgPreview.classList.remove('hidden');
                imgPlaceholder?.classList.add('hidden');
                if (imgLabelText) imgLabelText.textContent = this.files[0].name;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    function resetImagePreview(existingUrl = '') {
        fImagem.value = '';
        if (existingUrl) {
            imgPreview.src = existingUrl;
            imgPreview.classList.remove('hidden');
            imgPlaceholder?.classList.add('hidden');
            if (imgLabelText) imgLabelText.textContent = 'Imagem atual (clique para trocar)';
        } else {
            imgPreview.src = '';
            imgPreview.classList.add('hidden');
            imgPlaceholder?.classList.remove('hidden');
            if (imgLabelText) imgLabelText.textContent = 'Clique para escolher foto…';
        }
    }

    function openProdutoModal(modo = 'novo', dados = {}) {
        formProduto?.reset();
        editIdInput.value = dados.id || '';
        modalTitle.textContent = modo === 'novo' ? 'Novo Produto' : 'Editar Produto';
        document.getElementById('btn-salvar-produto').textContent = modo === 'novo' ? 'Salvar Produto' : 'Atualizar Produto';

        if (modo === 'editar' && dados.id) {
            document.getElementById('f-nome').value       = dados.nome       || '';
            document.getElementById('f-descricao').value  = dados.descricao  || '';
            document.getElementById('f-preco').value      = dados.preco      || '';
            document.getElementById('f-categoria').value  = dados.categoria  || '';
            document.getElementById('f-ativo').checked    = dados.ativo    === '1';
            document.getElementById('f-destaque').checked = dados.destaque === '1';
            resetImagePreview(dados.imagem || '');
        } else {
            resetImagePreview();
            document.getElementById('f-ativo').checked    = true;
            document.getElementById('f-destaque').checked = false;
        }

        modalProduto?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeProdutoModal() {
        modalProduto?.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('btn-novo-produto')?.addEventListener('click', () => openProdutoModal('novo'));
    document.getElementById('modal-produto-close')?.addEventListener('click', closeProdutoModal);
    document.getElementById('modal-produto-cancel')?.addEventListener('click', closeProdutoModal);
    modalProduto?.addEventListener('click', e => { if (e.target === modalProduto) closeProdutoModal(); });

    function bindEditarProduto(btn) {
        btn.addEventListener('click', function() {
            openProdutoModal('editar', {
                id:        this.dataset.id,
                nome:      this.dataset.nome,
                descricao: this.dataset.descricao,
                preco:     this.dataset.preco,
                categoria: this.dataset.categoria,
                ativo:     this.dataset.ativo,
                destaque:  this.dataset.destaque,
                imagem:    this.dataset.imagem,
            });
        });
    }
    document.querySelectorAll('.btn-editar-produto').forEach(bindEditarProduto);

    formProduto?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const isEdicao  = !!editIdInput.value;
        const btnSalvar = document.getElementById('btn-salvar-produto');
        btnSalvar.disabled     = true;
        btnSalvar.textContent  = 'Salvando…';

        const fd = new FormData(this);
        if (!this.querySelector('#f-ativo').checked)    fd.delete('ativo');
        if (!this.querySelector('#f-destaque').checked) fd.delete('destaque');

        const url    = isEdicao ? `/admin/produtos/${editIdInput.value}` : '/admin/produtos';
        const method = isEdicao ? 'PUT' : 'POST';

        const res = await postFormData(url, fd, method);
        if (res.ok) {
            const d = await res.json();
            if (!isEdicao) {
                const tbody = document.getElementById('produtos-tbody');
                if (tbody) {
                    const row = buildProdutoRow(d.produto);
                    tbody.prepend(row);
                    bindToggleProduto(row.querySelector('.btn-toggle-produto'));
                    bindToggleDestaque(row.querySelector('.btn-toggle-destaque'));
                    bindEditarProduto(row.querySelector('.btn-editar-produto'));
                }
                showToast('Produto cadastrado com sucesso!');
            } else {
                const row = document.getElementById(`produto-row-${editIdInput.value}`);
                if (row) {
                    const prod   = d.produto;
                    const imgSrc = prod.imagem ? (prod.imagem.startsWith('http') ? prod.imagem : `/storage/${prod.imagem}`) : '';
                    const imgEl  = row.querySelector('img, .w-10.h-10.rounded-full');
                    if (imgEl && imgEl.tagName === 'IMG') imgEl.src = imgSrc || '';
                    const nome  = row.querySelector('.font-semibold.text-white');
                    if (nome) nome.textContent = prod.nome;
                    const cat   = row.querySelector('td:nth-child(2)');
                    if (cat) cat.textContent = prod.categoria?.nome || '—';
                    const preco = row.querySelector('.text-secondary.font-bold');
                    if (preco) preco.textContent = `R$ ${parseFloat(prod.preco).toLocaleString('pt-BR',{minimumFractionDigits:2})}`;
                }
                showToast('Produto atualizado com sucesso!');
            }
            closeProdutoModal();
        } else {
            const err = await res.json().catch(() => ({}));
            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Erro ao salvar.');
            showToast(msg, 'error');
        }

        btnSalvar.disabled    = false;
        btnSalvar.textContent = isEdicao ? 'Atualizar Produto' : 'Salvar Produto';
    });

    function buildProdutoRow(prod) {
        const tr = document.createElement('tr');
        tr.className     = 'hover:bg-white/[0.015] transition-colors';
        tr.id            = `produto-row-${prod.id}`;
        tr.dataset.nome  = prod.nome.toLowerCase();
        tr.dataset.cat   = prod.categoria?.nome || '';
        const imgSrc = prod.imagem ? (prod.imagem.startsWith('http') ? prod.imagem : `/storage/${prod.imagem}`) : '';
        const imgHtml = imgSrc
            ? `<img src="${imgSrc}" class="w-10 h-10 rounded-full object-cover bg-gray-800 border border-white/10 shrink-0" alt="${prod.nome}">`
            : `<div class="w-10 h-10 rounded-full bg-gray-900 border border-white/10 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-gray-700 text-[18px]">image</span></div>`;
        const preco = parseFloat(prod.preco).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        tr.innerHTML = `
            <td class="td">
                <div class="flex items-center gap-3">
                    ${imgHtml}
                    <div class="min-w-0">
                        <p class="font-semibold text-white text-sm truncate">${prod.nome}</p>
                        <p class="text-[11px] text-gray-600 truncate max-w-[160px]">${(prod.descricao||'').substring(0,38)}</p>
                    </div>
                </div>
            </td>
            <td class="td text-gray-500 text-xs">${prod.categoria?.nome || '—'}</td>
            <td class="td text-right font-bold text-secondary">R$ ${preco}</td>
            <td class="td text-center">
                <button class="btn-toggle-destaque hover:scale-110 transition-transform" data-id="${prod.id}" data-destaque="${prod.destaque?'1':'0'}" title="${prod.destaque?'Remover Destaque':'Tornar Destaque'}">
                    <span id="destaque-icon-${prod.id}" class="material-symbols-outlined text-xl ${prod.destaque?'text-secondary':'text-gray-700'}" style="font-variation-settings:'FILL' ${prod.destaque?'1':'0'}">star</span>
                </button>
            </td>
            <td class="td text-center">
                <span id="produto-status-${prod.id}" class="badge ${prod.ativo?'bg-green-500/15 text-green-400':'bg-red-500/15 text-red-500'}">
                    <span class="material-symbols-outlined text-[12px]">${prod.ativo?'check_circle':'cancel'}</span>
                    ${prod.ativo?'Ativo':'Inativo'}
                </span>
            </td>
            <td class="td text-center">
                <div class="flex items-center gap-2 justify-center">
                    <button class="btn-editar-produto p-1.5 text-gray-600 hover:text-secondary transition-colors rounded-full hover:bg-secondary/10"
                        data-id="${prod.id}" data-nome="${prod.nome}" data-descricao="${prod.descricao||''}" data-preco="${prod.preco}" data-categoria="${prod.categoria_id||''}" data-ativo="${prod.ativo?'1':'0'}" data-destaque="${prod.destaque?'1':'0'}" data-imagem="${imgSrc}">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button class="btn-toggle-produto px-3 py-1 text-[11px] font-bold border border-white/10 text-gray-500 hover:border-secondary/40 hover:text-secondary rounded-full transition-all" data-id="${prod.id}" data-ativo="${prod.ativo?'1':'0'}">
                        ${prod.ativo?'Desativar':'Ativar'}
                    </button>
                </div>
            </td>`;
        return tr;
    }

    const modalCategoria = document.getElementById('modal-categoria');
    const catNomeInput   = document.getElementById('f-cat-nome');

    function openCategoriaModal()  { catNomeInput.value = ''; modalCategoria?.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeCategoriaModal() { modalCategoria?.classList.add('hidden'); document.body.style.overflow = ''; }

    document.getElementById('btn-nova-categoria')?.addEventListener('click', openCategoriaModal);
    document.getElementById('modal-categoria-close')?.addEventListener('click', closeCategoriaModal);
    document.getElementById('modal-categoria-cancel')?.addEventListener('click', closeCategoriaModal);
    modalCategoria?.addEventListener('click', e => { if (e.target === modalCategoria) closeCategoriaModal(); });

    document.getElementById('btn-salvar-categoria')?.addEventListener('click', async function() {
        const nome = catNomeInput?.value.trim();
        if (!nome) { showToast('Informe um nome para a categoria.', 'error'); return; }
        this.disabled     = true;
        this.textContent  = 'Criando…';

        const fd = new FormData();
        fd.append('nome', nome);
        const res = await postFormData('/admin/categorias', fd, 'POST');
        if (res.ok) {
            const d = await res.json();
            const selCat = document.getElementById('f-categoria');
            if (selCat) selCat.append(new Option(d.categoria.nome, d.categoria.id));
            const selFlt = document.getElementById('produto-cat-filter');
            if (selFlt) selFlt.append(new Option(d.categoria.nome, d.categoria.nome));
            closeCategoriaModal();
            showToast(`Categoria "${d.categoria.nome}" criada!`);
        } else {
            const err = await res.json().catch(() => ({}));
            showToast(err.message || 'Erro ao criar categoria.', 'error');
        }
        this.disabled    = false;
        this.textContent = 'Criar Categoria';
    });

    let avaliacoesFiltroNota = '';
    let avaliacoesData       = [];

    async function loadAvaliacoes() {
        const container = document.getElementById('avaliacoes-container');
        const loading   = document.getElementById('avaliacoes-loading');
        if (loading) loading.style.display = 'flex';

        const url = `/admin/avaliacoes${avaliacoesFiltroNota ? '?nota=' + avaliacoesFiltroNota : ''}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
        if (!res.ok) { showToast('Erro ao carregar avaliações.', 'error'); return; }

        avaliacoesData = await res.json();
        renderAvaliacoes();
    }

    function renderAvaliacoes() {
        const container = document.getElementById('avaliacoes-container');
        if (!container) return;

        if (!avaliacoesData.length) {
            container.innerHTML = `<div class="flex items-center justify-center py-16 text-gray-600 text-sm">Nenhuma avaliação encontrada.</div>`;
            return;
        }

        container.innerHTML = avaliacoesData.map(av => {
            const estrelas = Array.from({length:5}, (_,i) =>
                `<span class="material-symbols-outlined text-[15px] ${i < av.nota ? 'text-secondary' : 'text-gray-700'}" style="font-variation-settings:'FILL' ${i < av.nota ? '1' : '0'}">star</span>`
            ).join('');
            const date = new Date(av.created_at).toLocaleDateString('pt-BR', { day:'2-digit', month:'short', year:'numeric' });
            return `
            <div class="px-4 md:px-5 py-5 hover:bg-white/[0.012] transition-colors border-b border-white/[0.03]" id="av-row-${av.id}">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="w-9 h-9 rounded-full bg-secondary/10 border border-secondary/20 flex items-center justify-center text-secondary font-bold text-xs">
                            ${(av.user?.name || '?').substring(0,2).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-semibold text-white text-sm">${av.user?.name || '—'}</p>
                            <p class="text-[10px] text-gray-600">${date}</p>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="flex">${estrelas}</div>
                            ${av.produto ? `<span class="text-[11px] text-gray-600 bg-white/[0.04] px-2 py-0.5 rounded-full">${av.produto.nome}</span>` : ''}
                        </div>
                        ${av.comentario ? `<p class="text-sm text-gray-300">${av.comentario}</p>` : '<p class="text-xs text-gray-700 italic">Sem comentário.</p>'}
                        ${av.resposta_admin
                            ? `<div class="bg-secondary/5 border-l-2 border-secondary/30 pl-3 py-2 rounded-r-xl">
                                <p class="text-[10px] text-secondary font-bold mb-1">Resposta da loja</p>
                                <p class="text-xs text-gray-400" id="av-resposta-txt-${av.id}">${av.resposta_admin}</p>
                               </div>`
                            : `<button class="btn-responder-avaliacao text-[11px] text-secondary border border-secondary/20 px-3 py-1 rounded-full hover:bg-secondary/10 transition-all font-semibold" data-id="${av.id}">
                                   Responder
                               </button>`
                        }
                    </div>
                </div>
            </div>`;
        }).join('');

        container.querySelectorAll('.btn-responder-avaliacao').forEach(btn => {
            btn.addEventListener('click', function() { abrirModalResposta(this.dataset.id); });
        });
    }

    document.querySelectorAll('.avaliacao-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.avaliacao-filter-btn').forEach(b => {
                b.classList.remove('border-secondary', 'text-secondary');
                b.classList.add('border-white/10', 'text-gray-600');
            });
            this.classList.remove('border-white/10', 'text-gray-600');
            this.classList.add('border-secondary', 'text-secondary');
            avaliacoesFiltroNota = this.dataset.nota;
            loadAvaliacoes();
        });
    });

    const modalAvaliacao = document.getElementById('modal-avaliacao');
    let currentAvId      = null;

    function abrirModalResposta(id) {
        currentAvId = id;
        const av      = avaliacoesData.find(a => String(a.id) === String(id));
        const detalhes = document.getElementById('avaliacao-detalhes');
        if (av && detalhes) {
            const estrelas = Array.from({length:5}, (_,i) =>
                `<span class="material-symbols-outlined text-[14px] ${i < av.nota ? 'text-secondary' : 'text-gray-700'}">star</span>`
            ).join('');
            detalhes.innerHTML = `
                <div class="flex gap-1 mb-1">${estrelas}</div>
                <p class="text-xs font-semibold text-gray-300">${av.user?.name || '—'}</p>
                <p class="text-sm text-gray-400 mt-1">${av.comentario || 'Sem comentário.'}</p>`;
        }
        document.getElementById('f-resposta-avaliacao').value = '';
        modalAvaliacao?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAvaliacaoModal() { modalAvaliacao?.classList.add('hidden'); document.body.style.overflow = ''; }
    document.getElementById('modal-avaliacao-close')?.addEventListener('click', closeAvaliacaoModal);
    document.getElementById('modal-avaliacao-cancel')?.addEventListener('click', closeAvaliacaoModal);
    modalAvaliacao?.addEventListener('click', e => { if (e.target === modalAvaliacao) closeAvaliacaoModal(); });

    document.getElementById('btn-enviar-resposta')?.addEventListener('click', async function() {
        const resposta = document.getElementById('f-resposta-avaliacao')?.value.trim();
        if (!resposta) { showToast('Escreva uma resposta.', 'error'); return; }
        this.disabled    = true;
        this.textContent = 'Enviando…';

        const res = await patchJson(`/admin/avaliacoes/${currentAvId}/responder`, { resposta });
        if (res.ok) {
            showToast('Resposta enviada!');
            closeAvaliacaoModal();
            loadAvaliacoes();
        } else {
            showToast('Erro ao enviar resposta.', 'error');
        }
        this.disabled    = false;
        this.textContent = 'Enviar Resposta';
    });

    async function deleteJson(url) {
        return fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
    }

    function bindDeletarProduto(btn) {
        btn.addEventListener('click', async function() {
            const id   = this.dataset.id;
            const nome = this.dataset.nome || `#${id}`;
            if (!confirm(`Excluir o produto "${nome}"? Esta ação não pode ser desfeita.`)) return;
            const res = await deleteJson(`/admin/produtos/${id}`);
            if (res.ok) {
                document.getElementById(`produto-row-${id}`)?.remove();
                showToast('Produto excluído com sucesso!');
            } else {
                showToast('Erro ao excluir produto.', 'error');
            }
        });
    }
    document.querySelectorAll('.btn-deletar-produto').forEach(bindDeletarProduto);

    document.querySelectorAll('.btn-deletar-categoria').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id   = this.dataset.id;
            const nome = this.dataset.nome || `#${id}`;
            if (!confirm(`Excluir a categoria "${nome}"? Só é possível se não houver produtos vinculados.`)) return;
            const res = await deleteJson(`/admin/categorias/${id}`);
            if (res.ok) {
                document.getElementById(`categoria-row-${id}`)?.remove();
                showToast(`Categoria "${nome}" excluída!`);
            } else {
                const d = await res.json().catch(() => ({}));
                showToast(d.error || 'Erro ao excluir categoria.', 'error');
            }
        });
    });

    const modalOrderDetail = document.getElementById('modal-order-detail');
    const detailContent    = document.getElementById('order-detail-content');

    async function loadOrderDetails(id) {
        if (!modalOrderDetail || !detailContent) return;
        detailContent.innerHTML = '<div class="flex justify-center py-8"><span class="material-symbols-outlined text-secondary animate-spin text-3xl">progress_activity</span></div>';
        modalOrderDetail.classList.remove('hidden');

        const res = await fetch(`/admin/pedidos/${id}/detalhes`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
        if (!res.ok) { detailContent.innerHTML = '<p class="text-red-400 text-center py-8">Erro ao carregar pedido.</p>'; return; }
        const p = await res.json();

        const METODO_LABELS = { pix:'PIX', cartao_credito:'Crédito', cartao_debito:'Débito', dinheiro:'Dinheiro' };
        const itensHtml = p.itens.map(i => `
            <div class="flex items-center gap-3 py-2 border-b border-white/[0.04]">
                ${i.imagem
                    ? `<img src="${i.imagem}" class="w-10 h-10 rounded-lg object-cover bg-gray-800 shrink-0">`
                    : `<div class="w-10 h-10 rounded-lg bg-gray-900 flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-gray-700 text-[16px]">image</span></div>`}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">${i.nome}</p>
                    <p class="text-[11px] text-gray-600">x${i.quantidade} × R$ ${i.preco}</p>
                </div>
                <span class="text-sm font-bold text-secondary shrink-0">R$ ${(parseFloat(i.preco.replace(',','.')) * i.quantidade).toLocaleString('pt-BR',{minimumFractionDigits:2})}</span>
            </div>`).join('');

        detailContent.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-black/20 rounded-xl p-3 space-y-0.5">
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest">Cliente</p>
                        <p class="text-sm font-semibold text-white">${p.cliente}</p>
                        <p class="text-[11px] text-gray-600">${p.email}</p>
                    </div>
                    <div class="bg-black/20 rounded-xl p-3 space-y-0.5">
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest">Pagamento</p>
                        <p class="text-sm font-semibold text-white">${METODO_LABELS[p.pagamento] || p.pagamento}</p>
                        <p class="text-[11px] text-gray-600">Total: R$ ${p.total}</p>
                    </div>
                    <div class="bg-black/20 rounded-xl p-3 space-y-0.5 col-span-2">
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest">Endereço</p>
                        <p class="text-sm text-gray-300">${p.endereco}</p>
                    </div>
                    ${p.observacoes ? `<div class="bg-amber-900/10 border border-amber-800/30 rounded-xl p-3 col-span-2">
                        <p class="text-[10px] text-amber-500 uppercase tracking-widest mb-1">Observações</p>
                        <p class="text-sm text-gray-300">${p.observacoes}</p>
                    </div>` : ''}
                </div>
                <div>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mb-2">Itens do Pedido</p>
                    <div>${itensHtml}</div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-[11px] text-gray-600">
                    <span>Criado em: ${p.created_at}</span>
                    <span class="text-right">Atualizado: ${p.updated_at}</span>
                </div>
            </div>`;
    }

    document.getElementById('modal-order-detail-close')?.addEventListener('click', () => modalOrderDetail?.classList.add('hidden'));
    modalOrderDetail?.addEventListener('click', e => { if (e.target === modalOrderDetail) modalOrderDetail.classList.add('hidden'); });

    document.querySelectorAll('.btn-change-status').forEach(btn => {
        btn.removeEventListener('click', btn._statusHandler);
    });
    document.querySelectorAll('.btn-ver-detalhes-pedido').forEach(btn => {
        btn.addEventListener('click', function() {
            loadOrderDetails(this.dataset.id);
        });
    });
});
