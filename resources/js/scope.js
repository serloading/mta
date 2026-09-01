export default function setupScope() {
    const toolbar = document.querySelector('[data-scope-toolbar]');
    if (!toolbar) return;

    const chips = Array.from(toolbar.querySelectorAll('[data-scope-filter]'));
    const search = document.querySelector('[data-scope-search]');
    const countBadge = document.querySelector('[data-scope-search-count]');
    const blocks = Array.from(document.querySelectorAll('[data-scope-block]'));
    const empty = document.querySelector('[data-scope-empty]');
    const totalGroups = document.querySelectorAll('[data-scope-card]').length;

    let activeFilter = 'all';

    const norm = (s) =>
        (s || '')
            .toLocaleLowerCase('tr')
            .replace(/[İI]/g, 'i')
            .replace(/\s+/g, ' ')
            .trim();

    const markRail = (slug) => {
        chips.forEach((c) => c.classList.toggle('is-active', c.dataset.scopeFilter === slug));
    };

    const apply = () => {
        const q = norm(search ? search.value : '');
        let anyVisible = false;
        let visibleCards = 0;

        blocks.forEach((block) => {
            const catMatch = activeFilter === 'all' || block.dataset.cat === activeFilter;
            let blockHasVisibleCard = false;

            block.querySelectorAll('[data-scope-card]').forEach((card) => {
                const textMatch = q === '' || norm(card.textContent).includes(q);
                const visible = catMatch && textMatch;
                card.hidden = !visible;
                if (visible) {
                    blockHasVisibleCard = true;
                    visibleCards += 1;
                }
                if (q !== '' && textMatch && catMatch) {
                    card.open = true;
                } else if (q === '') {
                    card.open = false;
                }
            });

            block.hidden = !blockHasVisibleCard;
            if (blockHasVisibleCard) anyVisible = true;
        });

        if (empty) empty.hidden = anyVisible;
        if (countBadge) {
            const filtering = q !== '' || activeFilter !== 'all';
            countBadge.textContent = filtering ? `${visibleCards} sonuç` : `${totalGroups} grup`;
        }
    };

    chips.forEach((chip) => {
        chip.addEventListener('click', (event) => {
            event.preventDefault();
            activeFilter = chip.dataset.scopeFilter;
            markRail(activeFilter);
            apply();

            const targetId = activeFilter === 'all' ? 'kapsam-top' : activeFilter;
            const target = document.getElementById(targetId);
            if (target) {
                const header = document.querySelector('.site-header');
                const offset = (header ? header.offsetHeight : 73) + 24;
                const top = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' });
            }
        });
    });

    if (search) {
        let t;
        search.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(apply, 140);
        });
    }

    // Scrollspy — yalnızca filtre "Tümü" ve arama boşken rayı senkron tut
    if ('IntersectionObserver' in window && blocks.length) {
        const spy = new IntersectionObserver(
            (entries) => {
                if (activeFilter !== 'all' || (search && search.value.trim() !== '')) return;
                const visible = entries
                    .filter((e) => e.isIntersecting)
                    .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0];
                if (visible) markRail(visible.target.dataset.cat);
            },
            { rootMargin: '-120px 0px -65% 0px', threshold: 0 },
        );
        blocks.forEach((b) => spy.observe(b));
    }

    // Deep link: /kapsam#grup-id o kartı açar
    if (window.location.hash) {
        const card = document.querySelector(`${window.location.hash}[data-scope-card]`);
        if (card) card.open = true;
    }
}
