// Header: scroll-collapse of the top utility bar, live catalog search,
// mobile search toggle + burger drawer.

const ICONS = {
    category: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
    brand: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.5 13.5 20.6a2 2 0 0 1-2.8 0l-7.3-7.3A2 2 0 0 1 3 12V4a1 1 0 0 1 1-1h8a2 2 0 0 1 1.3.6l7.3 7.3a2 2 0 0 1 0 2.6z"/><circle cx="7.5" cy="7.5" r="1"/></svg>',
};

function debounce(fn, wait) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), wait);
    };
}

function initScrollCollapse() {
    let last = window.scrollY;
    const update = () => {
        document.body.classList.toggle('is-scrolled', window.scrollY > 40);
        last = window.scrollY;
    };
    update();
    window.addEventListener('scroll', () => window.requestAnimationFrame(update), { passive: true });
}

function initMobile(root) {
    const burger = root.querySelector('[data-burger]');
    const drawer = root.querySelector('[data-mobile-drawer]');
    const searchToggle = root.querySelector('[data-search-toggle]');
    const search = root.querySelector('[data-search]');

    burger?.addEventListener('click', () => {
        const open = document.body.classList.toggle('is-drawer-open');
        burger.setAttribute('aria-expanded', String(open));
        if (drawer) drawer.hidden = !open;
        if (open) document.body.classList.remove('is-search-open');
    });

    searchToggle?.addEventListener('click', () => {
        const open = search?.classList.toggle('is-open');
        if (open) {
            document.body.classList.remove('is-drawer-open');
            if (drawer) drawer.hidden = true;
            search.querySelector('[data-search-input]')?.focus();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.body.classList.remove('is-drawer-open');
            if (drawer) drawer.hidden = true;
            search?.classList.remove('is-open');
        }
    });
}

function initSearch(root) {
    const form = root.querySelector('[data-search]');
    const input = form?.querySelector('[data-search-input]');
    const panel = form?.querySelector('[data-search-panel]');
    if (!form || !input || !panel) return;

    const endpoint = form.getAttribute('action');
    let items = [];
    let activeIndex = -1;

    const close = () => {
        panel.hidden = true;
        panel.innerHTML = '';
        items = [];
        activeIndex = -1;
    };

    const setActive = (i) => {
        items.forEach((el, idx) => el.classList.toggle('is-active', idx === i));
        activeIndex = i;
        items[i]?.scrollIntoView({ block: 'nearest' });
    };

    const render = (data) => {
        const q = data.query || '';
        const groups = [];
        if (data.categories?.length) groups.push(['Kategoriler', data.categories, 'category']);
        if (data.brands?.length) groups.push(['Markalar', data.brands, 'brand']);
        if (data.products?.length) groups.push(['Ürünler', data.products, 'product']);

        if (!groups.length) {
            panel.innerHTML = `<div class="mb-search-empty">"${q}" için sonuç yok</div>`;
            panel.hidden = false;
            items = [];
            return;
        }

        let html = '';
        for (const [label, rows, kind] of groups) {
            html += `<div class="mb-search-group-label">${label}</div>`;
            for (const r of rows) {
                const media = r.image
                    ? `<img src="${r.image}" alt="" loading="lazy">`
                    : `<span class="mb-search-item-ico">${ICONS[kind] || ICONS.category}</span>`;
                html += `<a class="mb-search-item" href="${r.url}">${media}<span class="mb-search-item-main"><strong>${escapeHtml(r.label)}</strong><span>${escapeHtml(r.sub || '')}</span></span></a>`;
            }
        }
        html += `<a class="mb-search-all" href="${endpoint}?q=${encodeURIComponent(q)}">"${escapeHtml(q)}" için tüm sonuçlar →</a>`;
        panel.innerHTML = html;
        panel.hidden = false;
        items = Array.from(panel.querySelectorAll('.mb-search-item, .mb-search-all'));
        activeIndex = -1;
    };

    const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    const query = debounce(async () => {
        const q = input.value.trim();
        if (q.length < 2) {
            close();
            return;
        }
        try {
            const res = await fetch(`${endpoint}?format=json&q=${encodeURIComponent(q)}`, {
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) return;
            const data = await res.json();
            if (input.value.trim() === q) render(data);
        } catch (e) {
            /* ignore network errors */
        }
    }, 180);

    input.addEventListener('input', query);
    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2 && panel.innerHTML) panel.hidden = false;
    });

    input.addEventListener('keydown', (e) => {
        if (panel.hidden || !items.length) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive((activeIndex + 1) % items.length);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive((activeIndex - 1 + items.length) % items.length);
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            items[activeIndex].click();
        } else if (e.key === 'Escape') {
            close();
        }
    });

    document.addEventListener('click', (e) => {
        if (!form.contains(e.target)) close();
    });

    // Cmd/Ctrl + K focuses search
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            root.querySelector('[data-search]')?.classList.add('is-open');
            input.focus();
            input.select();
        }
    });
}

export default function setupHeader() {
    const root = document.querySelector('[data-header]');
    if (!root) return;
    initScrollCollapse();
    initMobile(root);
    initSearch(root);
}
