<div id="global-toast-container" class="fixed top-20 right-4 z-[9999] flex flex-col gap-3 w-80 max-w-full">
    <!-- Toasts serão injetados aqui -->
</div>

<div id="global-popup-container" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <!-- Popups persistentes (modais) serão injetados aqui -->
    <div id="global-popup-content" class="bg-background-card border border-secondary/30 rounded-xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col transform transition-all scale-95 opacity-0">
        <div class="p-5 border-b border-gray-800 flex items-center justify-between">
            <h3 id="gp-title" class="font-display font-bold text-white text-xl flex items-center gap-2">
                <span id="gp-icon" class="material-symbols-outlined text-secondary">notifications_active</span>
                <span id="gp-title-text">Aviso</span>
            </h3>
            <button id="gp-close" class="text-gray-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <p id="gp-message" class="text-gray-300">Mensagem aqui</p>
        </div>
        <div class="p-5 border-t border-gray-800 bg-background-dark flex justify-end gap-3" id="gp-actions">
            <!-- Botões -->
        </div>
    </div>
</div>

<style>
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    .toast-enter { animation: slideInRight 0.3s ease-out forwards; }
    .toast-leave { animation: fadeOut 0.3s ease-in forwards; }
    
    .popup-open { scale: 1 !important; opacity: 1 !important; }
</style>

<script>
    window.GlobalAlerts = {
        /**
         * Mostra um toast que desaparece sozinho (canto superior direito)
         */
        toast: function(message, type = 'info', duration = 4000) {
            const container = document.getElementById('global-toast-container');
            const toast = document.createElement('div');
            
            let icon = 'info';
            let colorClass = 'border-blue-500/50 bg-blue-900/20 text-blue-400';
            
            if (type === 'success') { icon = 'check_circle'; colorClass = 'border-green-500/50 bg-green-900/20 text-green-400'; }
            if (type === 'warning') { icon = 'warning'; colorClass = 'border-yellow-500/50 bg-yellow-900/20 text-yellow-400'; }
            if (type === 'error') { icon = 'error'; colorClass = 'border-red-500/50 bg-red-900/20 text-red-400'; }

            toast.className = `toast-enter flex items-start gap-3 p-4 rounded-xl border ${colorClass} shadow-lg backdrop-blur-md`;
            toast.innerHTML = `
                <span class="material-symbols-outlined shrink-0">${icon}</span>
                <p class="text-sm font-semibold text-white leading-relaxed flex-1">${message}</p>
                <button class="text-gray-400 hover:text-white shrink-0 ml-2 focus:outline-none" onclick="this.parentElement.remove()">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            `;
            
            container.appendChild(toast);
            
            if (duration > 0) {
                setTimeout(() => {
                    toast.classList.replace('toast-enter', 'toast-leave');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }
        },

        /**
         * Mostra um popup persistente no centro da tela
         */
        popup: function(title, message, type = 'info', actions = []) {
            const wrapper = document.getElementById('global-popup-container');
            const content = document.getElementById('global-popup-content');
            const elTitle = document.getElementById('gp-title-text');
            const elIcon = document.getElementById('gp-icon');
            const elMsg = document.getElementById('gp-message');
            const elActions = document.getElementById('gp-actions');
            const btnClose = document.getElementById('gp-close');
            
            let icon = 'notifications_active';
            let iconColor = 'text-secondary';
            if (type === 'success') { icon = 'check_circle'; iconColor = 'text-green-500'; }
            if (type === 'warning') { icon = 'warning'; iconColor = 'text-yellow-500'; }
            if (type === 'error') { icon = 'error'; iconColor = 'text-red-500'; }

            elTitle.textContent = title;
            elIcon.textContent = icon;
            elIcon.className = `material-symbols-outlined ${iconColor}`;
            elMsg.innerHTML = message;
            
            elActions.innerHTML = '';
            
            if (actions.length === 0) {
                // Default fechar action se não houver nenhuma
                actions.push({ text: 'Fechar', color: 'bg-gray-800 text-white', onClick: () => window.GlobalAlerts.closePopup() });
            }

            actions.forEach(act => {
                const btn = document.createElement('button');
                btn.className = `px-5 py-2 font-bold rounded-lg transition-colors text-sm ${act.color || 'bg-secondary text-primary hover:bg-[#c2884a]'}`;
                btn.textContent = act.text;
                if (act.href) {
                    btn.onclick = () => window.location.href = act.href;
                } else {
                    btn.onclick = (e) => {
                        if(act.onClick) act.onClick(e);
                        window.GlobalAlerts.closePopup();
                    };
                }
                elActions.appendChild(btn);
            });

            btnClose.onclick = () => window.GlobalAlerts.closePopup();
            
            wrapper.classList.remove('hidden');
            wrapper.classList.add('flex');
            // Timeout pequeno para trigger CSS transition
            setTimeout(() => {
                content.classList.add('popup-open');
            }, 10);
        },

        closePopup: function() {
            const wrapper = document.getElementById('global-popup-container');
            const content = document.getElementById('global-popup-content');
            content.classList.remove('popup-open');
            setTimeout(() => {
                wrapper.classList.add('hidden');
                wrapper.classList.remove('flex');
            }, 300); // match transition duration
        }
    };
</script>

@auth
    @if(Auth::user()->is_admin)
        <script>
            // Polling Global para o Admin (se ele não estiver no dashboard)
            // Se ele estiver no dashboard, o admin.js também tem polling, mas precisamos unificar para não tocar 2x
            // Vamos usar o GlobalAlerts.popup para novos pedidos
            document.addEventListener('DOMContentLoaded', function() {
                let lastKnownOrderIdGlobal = localStorage.getItem('lastKnownOrderIdGlobal') || 0;
                
                function playToneGlobal(freq, type, duration, startTime, vol=2.0) {
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = type;
                        osc.frequency.setValueAtTime(freq, audioCtx.currentTime + startTime);
                        gain.gain.setValueAtTime(vol, audioCtx.currentTime + startTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + startTime + duration);
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start(audioCtx.currentTime + startTime);
                        osc.stop(audioCtx.currentTime + startTime + duration);
                    } catch(e) {}
                }

                function playChimeNewOrderGlobal() {
                    // Sequência longa e forte — 4 toques repetidos em volume máximo
                    const notes = [
                        [523.25, 0.0],
                        [659.25, 0.15],
                        [783.99, 0.30],
                        [1046.50, 0.45],
                        [783.99, 0.70],
                        [1046.50, 0.90],
                        [1318.51, 1.10],
                        [1046.50, 1.35],
                        [1318.51, 1.55],
                        [1567.98, 1.75],
                    ];
                    notes.forEach(([freq, start]) => {
                        playToneGlobal(freq, 'sine', 0.5, start, 3.0);
                        playToneGlobal(freq * 2, 'triangle', 0.3, start + 0.02, 2.0);
                    });
                }

                setInterval(async () => {
                    try {
                        const res = await fetch('/admin/pedidos/api/ativos');
                        if (!res.ok) return;
                        const data = await res.json();
                        
                        // Atualiza kpis se estiver no dashboard
                        if (window.location.pathname.includes('/admin')) {
                            // O admin.js atualizará a tabela
                        }
                        
                        if (data.latest_id > 0) {
                            if (lastKnownOrderIdGlobal == 0) {
                                // Primeira carga, apenas atualiza o ID
                                lastKnownOrderIdGlobal = data.latest_id;
                                localStorage.setItem('lastKnownOrderIdGlobal', lastKnownOrderIdGlobal);
                            } else if (data.latest_id > lastKnownOrderIdGlobal) {
                                // Tem pedido novo!
                                lastKnownOrderIdGlobal = data.latest_id;
                                localStorage.setItem('lastKnownOrderIdGlobal', lastKnownOrderIdGlobal);
                                
                                playChimeNewOrderGlobal();
                                
                                window.GlobalAlerts.popup(
                                    'NOVO PEDIDO!',
                                    `Um novo pedido acabou de chegar na loja. Acesse o Dashboard para preparar.`,
                                    'success',
                                    [
                                        { text: 'Fechar Alerta', color: 'bg-transparent border border-gray-600 text-gray-300 hover:bg-gray-800' },
                                        { text: 'Ir para Dashboard', href: '/admin', color: 'bg-secondary text-primary hover:bg-[#c2884a]' }
                                    ]
                                );
                            }
                        }
                    } catch(e) {}
                }, 10000);
            });
        </script>
    @endif
@endauth

{{-- ═══ Polling de status da loja (todas as páginas, a cada 30s) ═══ --}}
<script>
    (function() {
        // Guarda o estado inicial enviado pelo servidor (evita falso alerta na 1ª checagem)
        let _storeWasOpen = {{ $storeIsOpen ? 'true' : 'false' }};

        async function checkStoreStatus() {
            try {
                const res = await fetch('/api/store-status', { cache: 'no-store' });
                if (!res.ok) return;
                const data = await res.json();
                const isOpen = !!data.is_open;

                if (isOpen !== _storeWasOpen) {
                    _storeWasOpen = isOpen;
                    // Notifica o usuário
                    if (typeof window.GlobalAlerts !== 'undefined') {
                        if (isOpen) {
                            window.GlobalAlerts.toast('A loja está aberta agora! Pedidos aceitos.', 'success', 6000);
                        } else {
                            window.GlobalAlerts.toast('A loja acabou de fechar. Pedidos suspensos.', 'warning', 8000);
                        }
                    }
                    // Atualiza body para CSS poder reagir
                    document.body.classList.toggle('store-closed', !isOpen);
                    // Atualiza botões de checkout visiveis
                    document.querySelectorAll('[data-store-gate]').forEach(el => {
                        el.disabled = !isOpen;
                        el.classList.toggle('opacity-50', !isOpen);
                        el.classList.toggle('cursor-not-allowed', !isOpen);
                    });
                    // Atualiza eventuais indicadores de status na page
                    document.querySelectorAll('[data-store-status-label]').forEach(el => {
                        el.textContent = isOpen ? 'Aberta' : 'Fechada';
                    });
                }
            } catch(e) {}
        }

        // Primeira checagem após 15s (para não interferir com o carregamento)
        setTimeout(checkStoreStatus, 15000);
        // Polling a cada 30s
        setInterval(checkStoreStatus, 30000);
    })();
</script>
