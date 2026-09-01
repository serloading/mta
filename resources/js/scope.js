export default function setupScope() {
    const toolbar = document.querySelector('[data-scope-toolbar]');
    if (!toolbar) return;

    const chips = Array.from(toolbar.querySelectorAll('[data-scope-filter]'));
    const search = toolbar.querySelector('[data-scope-search]');
    const blocks = Array.from(document.querySelectorAll('[data-scope-block]'));
    const empty = document.querySelector('[data-scope-empty]');

    let activeFilter = 'all';

    const norm = (s) =>
        (s || '')
            .toLocaleLowerCase('tr')
            .replace(/[İI]/g, 'i')
            .replace(/\s+/g, ' ')
            .trim();

    const apply = () => {
        const q = norm(search ? search.value : '');
        let anyVisible = false;

        blocks.forEach((block) => {
            const catMatch = activeFilter === 'all' || block.dataset.cat === activeFilter;
            let blockHasVisibleCard = false;

            block.querySelectorAll('[data-scope-card]').forEach((card) => {
                const textMatch = q === '' || norm(card.textContent).includes(q);
                const visible = catMatch && textMatch;
                card.hidden = !visible;
                if (visible) blockHasVisibleCard = true;
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
    };

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            activeFilter = chip.dataset.scopeFilter;
            chips.forEach((c) => c.classList.toggle('is-active', c === chip));
            apply();

            if (activeFilter !== 'all') {
                const target = document.getElementById(activeFilter);
                if (target) {
                    const header = document.querySelector('.site-header');
                    const offset = (header ? header.offsetHeight : 73) + toolbar.offsetHeight + 16;
                    const top = target.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' });
                }
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

    // Deep link: /kapsam#grup-id opens that card
    if (window.location.hash) {
        const card = document.querySelector(`${window.location.hash}[data-scope-card]`);
        if (card) card.open = true;
    }
}
