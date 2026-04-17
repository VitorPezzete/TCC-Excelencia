document.addEventListener('DOMContentLoaded', function() {
    const btnAcc    = document.getElementById('btn-acessibilidade');
    const painelAcc = document.getElementById('painel-acessibilidade');
    const html      = document.documentElement;
    const label     = document.getElementById('acc-font-size-label');

    btnAcc?.addEventListener('click', function(e) {
        e.stopPropagation();
        painelAcc?.classList.toggle('hidden');
    });
    
    document.addEventListener('click', function(e) {
        if (!btnAcc?.contains(e.target) && !painelAcc?.contains(e.target)) {
            painelAcc?.classList.add('hidden');
        }
    });

    let st = {
        font:     parseInt(localStorage.getItem('acc-font')     || '16'),
        contrast: localStorage.getItem('acc-contrast') === '1',
        motion:   localStorage.getItem('acc-motion')   === '1',
        dyslexia: localStorage.getItem('acc-dyslexia') === '1',
        spacing:  localStorage.getItem('acc-spacing')  === '1',
        links:    localStorage.getItem('acc-links')    === '1',
    };

    function applyFont(v) {
        html.style.fontSize = v + 'px';
        localStorage.setItem('acc-font', v);
        if (label) label.textContent = v + 'px';
    }

    function applyToggle(key, cls, btnId) {
        html.classList.toggle(cls, st[key]);
        localStorage.setItem('acc-' + key, st[key] ? '1' : '0');
        const btn = document.getElementById(btnId);
        if (btn) btn.textContent = st[key] ? 'Desligar' : 'Ligar';
    }

    function applyAll() {
        applyFont(st.font);
        applyToggle('contrast', 'high-contrast',   'acc-contrast');
        applyToggle('motion',   'reduce-motion',    'acc-motion');
        applyToggle('dyslexia', 'dyslexia-font',    'acc-dyslexia');
        applyToggle('spacing',  'extra-spacing',    'acc-spacing');
        applyToggle('links',    'highlight-links',  'acc-links');
    }

    applyAll();

    document.getElementById('acc-font-inc')?.addEventListener('click', function() { st.font = Math.min(st.font + 2, 26); applyFont(st.font); });
    document.getElementById('acc-font-dec')?.addEventListener('click', function() { st.font = Math.max(st.font - 2, 12); applyFont(st.font); });

    ['contrast','motion','dyslexia','spacing','links'].forEach(function(key) {
        document.getElementById('acc-' + key)?.addEventListener('click', function() {
            st[key] = !st[key];
            var clsMap = { contrast:'high-contrast', motion:'reduce-motion', dyslexia:'dyslexia-font', spacing:'extra-spacing', links:'highlight-links' };
            applyToggle(key, clsMap[key], 'acc-' + key);
        });
    });

    document.getElementById('acc-reset')?.addEventListener('click', function() {
        st = { font: 16, contrast: false, motion: false, dyslexia: false, spacing: false, links: false };
        ['acc-font','acc-contrast','acc-motion','acc-dyslexia','acc-spacing','acc-links'].forEach(function(k) { localStorage.removeItem(k); });
        applyAll();
    });
});
