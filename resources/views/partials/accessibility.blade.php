<button id="btn-acessibilidade"
    class="fixed bottom-6 right-6 z-50 w-13 h-13 w-12 h-12 rounded-full bg-secondary shadow-lg shadow-secondary/30 flex items-center justify-center text-primary hover:scale-110 hover:shadow-secondary/50 transition-all"
    title="Acessibilidade" aria-label="Opções de acessibilidade">
    <span class="material-symbols-outlined text-[22px]">accessibility_new</span>
</button>

<div id="painel-acessibilidade"
    class="hidden fixed bottom-20 right-6 z-50 bg-[#1a1008] border border-secondary/20 rounded-2xl shadow-2xl p-5 w-72 space-y-4 backdrop-blur-sm">
    <div class="flex items-center gap-2 mb-1">
        <span class="material-symbols-outlined text-secondary text-[18px]">accessibility_new</span>
        <p class="text-xs font-bold text-secondary uppercase tracking-widest">Acessibilidade</p>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-300">Tamanho da fonte</span>
        <div class="flex gap-1">
            <button id="acc-font-dec" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 font-bold text-sm transition-colors" aria-label="Diminuir fonte">A−</button>
            <button id="acc-font-inc" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 font-bold text-sm transition-colors" aria-label="Aumentar fonte">A+</button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-300">Alto contraste</span>
        <button id="acc-contrast" class="px-3 py-1 text-xs font-bold rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 transition-colors min-w-[60px]">Ligar</button>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-300">Sem animações</span>
        <button id="acc-motion" class="px-3 py-1 text-xs font-bold rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 transition-colors min-w-[60px]">Ligar</button>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-300">Fonte legível</span>
        <button id="acc-dyslexia" class="px-3 py-1 text-xs font-bold rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 transition-colors min-w-[60px]">Ligar</button>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-300">Espaçamento extra</span>
        <button id="acc-spacing" class="px-3 py-1 text-xs font-bold rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 transition-colors min-w-[60px]">Ligar</button>
    </div>

    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-300">Realçar links</span>
        <button id="acc-links" class="px-3 py-1 text-xs font-bold rounded-full bg-white/5 border border-white/10 text-gray-400 hover:text-secondary hover:border-secondary/40 transition-colors min-w-[60px]">Ligar</button>
    </div>

    <div class="pt-2 border-t border-white/[0.06] flex items-center justify-between">
        <button id="acc-reset" class="text-xs text-gray-600 hover:text-red-400 transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[13px]">restart_alt</span> Redefinir tudo
        </button>
        <span id="acc-font-size-label" class="text-[10px] text-gray-700">16px</span>
    </div>
</div>

