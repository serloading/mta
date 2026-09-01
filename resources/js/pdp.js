// Product detail page behaviours. Progressive enhancement: without JS the
// page shows every panel stacked and the gallery falls back to plain links.

function initGallery(root) {
    const mainImg = root.querySelector('[data-pdp-main-img]');
    const thumbs = Array.from(root.querySelectorAll('[data-pdp-thumb]'));
    if (!mainImg || thumbs.length < 2) return;

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const src = thumb.dataset.full || thumb.querySelector('img')?.src;
            if (!src) return;
            mainImg.src = src;
            thumbs.forEach((t) => t.setAttribute('aria-current', String(t === thumb)));
        });
    });
}

function initLightbox(root) {
    const box = root.querySelector('[data-pdp-lightbox]');
    const boxImg = box?.querySelector('img');
    const mainImg = root.querySelector('[data-pdp-main-img]');
    const triggers = Array.from(root.querySelectorAll('[data-pdp-zoom]'));
    if (!box || !boxImg || (!mainImg && triggers.length === 0)) return;

    const open = () => {
        boxImg.src = mainImg ? mainImg.src : boxImg.src;
        box.classList.add('is-open');
        document.body.classList.add('pdp-noscroll');
        box.querySelector('[data-pdp-lightbox-close]')?.focus();
    };
    const close = () => {
        box.classList.remove('is-open');
        document.body.classList.remove('pdp-noscroll');
    };

    triggers.forEach((t) => t.addEventListener('click', open));
    box.addEventListener('click', (e) => {
        if (e.target === box || e.target.closest('[data-pdp-lightbox-close]')) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && box.classList.contains('is-open')) close();
    });
}

function initTabs(root) {
    const tabs = Array.from(root.querySelectorAll('[data-pdp-tab]'));
    const panels = Array.from(root.querySelectorAll('[data-pdp-panel]'));
    if (tabs.length < 2 || panels.length < 2) return;

    const bar = root.querySelector('[data-pdp-tabsbar]');

    const activate = (id, { scroll = false, focus = false } = {}) => {
        tabs.forEach((tab) => {
            const on = tab.dataset.pdpTab === id;
            tab.setAttribute('aria-selected', String(on));
            tab.tabIndex = on ? 0 : -1;
            if (on && focus) tab.focus();
        });
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.pdpPanel !== id;
        });
        if (scroll && bar) {
            const y = window.scrollY + bar.getBoundingClientRect().top - (bar.offsetHeight + 8);
            window.scrollTo({ top: y, behavior: 'smooth' });
        }
    };

    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => {
            activate(tab.dataset.pdpTab, { scroll: true });
            history.replaceState(null, '', '#' + tab.dataset.pdpTab);
        });
        tab.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            e.preventDefault();
            const next = e.key === 'ArrowRight' ? (i + 1) % tabs.length : (i - 1 + tabs.length) % tabs.length;
            activate(tabs[next].dataset.pdpTab, { focus: true });
        });
    });

    const fromHash = (location.hash || '').replace('#', '');
    const initial = tabs.some((t) => t.dataset.pdpTab === fromHash) ? fromHash : tabs[0].dataset.pdpTab;
    activate(initial);
}

export default function setupPdp() {
    const root = document.querySelector('[data-pdp]');
    if (!root) return;
    initGallery(root);
    initLightbox(root);
    initTabs(root);
}
