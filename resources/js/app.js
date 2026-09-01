import setupCatalog from './catalog.js';
import setupPdp from './pdp.js';
import setupHeader from './header.js';
import setupScope from './scope.js';

function setupProductMega() {
    const mega = document.querySelector('[data-product-mega]');
    if (!mega) return;

    const tabs = Array.from(mega.querySelectorAll('[data-mega-cat]'));
    const panels = Array.from(mega.querySelectorAll('[data-mega-panel]'));

    const activate = (slug) => {
        tabs.forEach((t) => t.setAttribute('aria-selected', String(t.dataset.megaCat === slug)));
        panels.forEach((p) => {
            const on = p.dataset.megaPanel === slug;
            p.classList.toggle('hidden', !on);
            p.classList.toggle('grid', on);
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('mouseenter', () => activate(tab.dataset.megaCat));
        tab.addEventListener('focus', () => activate(tab.dataset.megaCat));
    });
}

function setupCatalogPage() {
    document.querySelectorAll('[data-catalog-filter]').forEach((form) => {
        form.querySelectorAll('input[type="checkbox"]').forEach((cb) =>
            cb.addEventListener('change', () => form.submit()));
    });
    document.querySelectorAll('[data-catalog-sort]').forEach((form) => {
        form.querySelector('select')?.addEventListener('change', () => form.submit());
    });
    const search = document.querySelector('[data-catalog-search]');
    if (search && search.form) {
        let t;
        search.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => search.form.submit(), 500);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setupCatalog();
    setupPdp();
    setupHeader();
    setupScope();
    setupProductMega();
    setupCatalogPage();

    const megaItems = Array.from(document.querySelectorAll('.mega-nav-item'));
    const hoverPointer = window.matchMedia('(hover: hover) and (pointer: fine)');

    const setMegaState = (item, isOpen) => {
        item.classList.toggle('is-open', isOpen);
        item.querySelector('[data-mega-trigger]')?.setAttribute('aria-expanded', String(isOpen));
    };

    const closeMegaMenus = (except = null) => {
        megaItems.forEach((item) => {
            if (item !== except) {
                setMegaState(item, false);
            }
        });
    };

    megaItems.forEach((item) => {
        const trigger = item.querySelector('[data-mega-trigger]');

        item.addEventListener('pointerenter', () => {
            if (!hoverPointer.matches) return;
            closeMegaMenus(item);
            trigger?.setAttribute('aria-expanded', 'true');
        });

        item.addEventListener('pointerleave', () => {
            if (!hoverPointer.matches) return;
            setMegaState(item, false);
        });

        item.addEventListener('focusin', () => {
            closeMegaMenus(item);
            trigger?.setAttribute('aria-expanded', 'true');
        });

        item.addEventListener('focusout', () => {
            window.setTimeout(() => {
                if (!item.contains(document.activeElement)) {
                    setMegaState(item, false);
                }
            }, 0);
        });

        trigger?.addEventListener('click', (event) => {
            if (hoverPointer.matches) return;
            event.preventDefault();
            const willOpen = !item.classList.contains('is-open');
            closeMegaMenus(item);
            setMegaState(item, willOpen);
        });
    });

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) return;
        if (!target.closest('.mega-nav-item')) {
            closeMegaMenus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMegaMenus();
        }
    });
});
