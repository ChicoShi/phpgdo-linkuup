/* Welcome interactions are isolated from account/admin forms. */
(() => {
    'use strict';
    const ready = () => {
        if (!document.body.classList.contains('lup-arrival-refresh')) return;
        const trigger = document.getElementById('sidebarToggle');
        const drawer = document.getElementById('sidebar-wrapper');
        if (trigger && drawer) {
            const shade = document.createElement('button');
            shade.className = 'lup-menu-shade';
            shade.setAttribute('aria-label', 'Menü schließen');
            shade.hidden = true;
            document.body.append(shade);
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'lup-menu-close';
            closeButton.textContent = 'Menü schließen';
            drawer.prepend(closeButton);
            trigger.setAttribute('aria-label', 'Menü öffnen');
            trigger.setAttribute('aria-controls', drawer.id);
            const setOpen = (open, restore = false) => {
                document.body.classList.toggle('lup-arrival-menu-open', open);
                trigger.setAttribute('aria-expanded', String(open));
                trigger.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
                drawer.inert = !open;
                shade.hidden = !open;
                if (open) drawer.querySelector('a,button')?.focus();
                else if (restore) trigger.focus();
            };
            trigger.addEventListener('click', event => {
                event.preventDefault();
                event.stopImmediatePropagation();
                setOpen(!document.body.classList.contains('lup-arrival-menu-open'));
            }, true);
            shade.addEventListener('click', () => setOpen(false, true));
            closeButton.addEventListener('click', () => setOpen(false, true));
            document.addEventListener('keydown', event => {
                if (!document.body.classList.contains('lup-arrival-menu-open')) return;
                if (event.key === 'Escape') setOpen(false, true);
                if (event.key === 'Tab') {
                    const links = [...drawer.querySelectorAll('a[href],button:not([disabled])')].filter(el => el.getClientRects().length);
                    if (!links.length) return;
                    if (event.shiftKey && document.activeElement === links[0]) { event.preventDefault(); links.at(-1).focus(); }
                    else if (!event.shiftKey && document.activeElement === links.at(-1)) { event.preventDefault(); links[0].focus(); }
                }
            });
            setOpen(false);
        }
        const plane = document.querySelector('.lup-place-plane');
        const scene = document.querySelector('.lup-place-scene');
        const scroller = document.getElementById('page-content-wrapper');
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
        let frame = 0;
        const draw = () => {
            frame = 0;
            if (reduced.matches || document.hidden) { plane.style.transform = ''; return; }
            const rect = scene.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > innerHeight) return;
            const progress = Math.max(-1, Math.min(1, (innerHeight / 2 - rect.top) / innerHeight));
            plane.style.transform = `rotateX(${48 - progress * 12}deg) rotateZ(${-28 + progress * 8}deg)`;
        };
        const schedule = () => { if (!frame) frame = requestAnimationFrame(draw); };
        if (plane && scene) {
            const signal = scene.querySelector('.lup-place-signal');
            if ('IntersectionObserver' in window) {
                new IntersectionObserver(entries => {
                    signal.style.animationPlayState = entries[0].isIntersecting ? 'running' : 'paused';
                }).observe(scene);
            }
            scroller?.addEventListener('scroll', schedule, {passive:true});
            window.addEventListener('scroll', schedule, {passive:true});
            window.addEventListener('resize', schedule, {passive:true});
            reduced.addEventListener('change', schedule);
            schedule();
        }
    };
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', ready) : ready();
})();
