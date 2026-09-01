import setupCatalog from './catalog.js';
import setupPdp from './pdp.js';
import setupHeader from './header.js';
import setupScope from './scope.js';

document.addEventListener('DOMContentLoaded', () => {
    setupCatalog();
    setupPdp();
    setupHeader();
    setupScope();

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
