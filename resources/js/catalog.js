// Catalog listing behaviours (brand + category pages).
// Progressive enhancement: the page is fully usable server-rendered without JS.

const VIEW_KEY = 'mta:catalog:view';
const SORT_KEY = 'mta:catalog:sort';

function initCatalog(root) {
    const grid = root.querySelector('[data-grid]');
    const cards = grid ? Array.from(grid.querySelectorAll('[data-card]')) : [];

    /* ---- view toggle (grid / list) ---- */
    const viewButtons = Array.from(root.querySelectorAll('[data-view]'));

    const applyView = (view) => {
        if (!grid) return;
        grid.classList.toggle('is-list', view === 'list');
        viewButtons.forEach((btn) => {
            btn.setAttribute('aria-pressed', String(btn.dataset.view === view));
        });
    };

    let storedView = null;
    try { storedView = localStorage.getItem(VIEW_KEY); } catch (e) { /* ignore */ }
    if (storedView === 'list') applyView('list');

    viewButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const view = btn.dataset.view;
            applyView(view);
            try { localStorage.setItem(VIEW_KEY, view); } catch (e) { /* ignore */ }
        });
    });

    /* ---- client-side sort ---- */
    const sortSelect = root.querySelector('[data-sort]');

    if (sortSelect && grid && cards.length > 1) {
        cards.forEach((card, i) => { card.dataset.idx = String(i); });

        const sortCards = (mode) => {
            const sorted = cards.slice().sort((a, b) => {
                if (mode === 'az' || mode === 'za') {
                    const cmp = (a.dataset.name || '').localeCompare(b.dataset.name || '', 'tr');
                    return mode === 'az' ? cmp : -cmp;
                }
                return Number(a.dataset.idx) - Number(b.dataset.idx);
            });
            sorted.forEach((card) => grid.appendChild(card));
        };

        let storedSort = null;
        try { storedSort = localStorage.getItem(SORT_KEY); } catch (e) { /* ignore */ }
        if (storedSort && storedSort !== 'recommended') {
            sortSelect.value = storedSort;
            sortCards(storedSort);
        }

        sortSelect.addEventListener('change', () => {
            sortCards(sortSelect.value);
            try { localStorage.setItem(SORT_KEY, sortSelect.value); } catch (e) { /* ignore */ }
        });
    }

    /* ---- mobile filter drawer ---- */
    const drawer = root.querySelector('[data-drawer-open]') ? document.getElementById('cui-filter-drawer') : null;
    const backdrop = root.querySelector('[data-drawer-backdrop]');
    const openButtons = Array.from(root.querySelectorAll('[data-drawer-open]'));
    const closeButtons = Array.from(root.querySelectorAll('[data-drawer-close]'));
    let lastFocused = null;

    const openDrawer = () => {
        if (!drawer) return;
        lastFocused = document.activeElement;
        drawer.removeAttribute('inert');
        document.body.classList.add('cui-drawer-open', 'cui-noscroll');
        drawer.querySelector('[data-drawer-close]')?.focus();
    };

    const closeDrawer = () => {
        if (!drawer) return;
        document.body.classList.remove('cui-drawer-open', 'cui-noscroll');
        drawer.setAttribute('inert', '');
        if (lastFocused instanceof HTMLElement) lastFocused.focus();
    };

    openButtons.forEach((btn) => btn.addEventListener('click', openDrawer));
    closeButtons.forEach((btn) => btn.addEventListener('click', closeDrawer));
    backdrop?.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.classList.contains('cui-drawer-open')) {
            closeDrawer();
        }
    });
}

export default function setupCatalog() {
    document.querySelectorAll('[data-catalog]').forEach(initCatalog);
}
