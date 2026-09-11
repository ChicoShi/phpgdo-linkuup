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
        const main = document.querySelector('.lup-arrival');
        const scene = document.querySelector('.lup-place-scene');
        const scroller = document.getElementById('page-content-wrapper');
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
        const track = document.querySelector('.lup-scroll-track');
        const light = document.querySelector('.lup-scroll-light');
        const traveller = document.querySelector('.lup-scroll-traveller');
        const heroPath = document.querySelector('.lup-hero-route');
        const runner = document.querySelector('.lup-map-runner');
        const heroLength = heroPath.getTotalLength();
        let trackLength = 0;
        let frame = 0;
        let ambient = 0;
        let sceneVisible = true;
        const buildRoute = () => {
            const width = main.clientWidth, height = main.offsetHeight;
            const gutter = Math.max(12, (width - 1160) / 2 + 14);
            document.querySelector('.lup-scroll-map').setAttribute('viewBox', `0 0 ${width} ${height}`);
            const stops = [...main.querySelectorAll(':scope > section')].map(el => el.offsetTop + 70);
            let d = `M ${gutter} 70`;
            let previous = 70;
            stops.slice(1).forEach((y, i) => {
                const bend = gutter + (width < 760 ? 7 : 24) * (i % 2 ? -1 : 1);
                d += ` C ${bend} ${previous + (y - previous) * .4}, ${bend} ${previous + (y - previous) * .7}, ${gutter} ${y}`;
                previous = y;
            });
            track.setAttribute('d', d);
            light.setAttribute('d', d);
            trackLength = track.getTotalLength();
        };
        const draw = () => {
            frame = 0;
            if (reduced.matches || document.hidden) return;
            const mainRect = main.getBoundingClientRect();
            const progress = Math.max(0, Math.min(1, (innerHeight * .55 - mainRect.top) / main.offsetHeight));
            const point = track.getPointAtLength(trackLength * progress);
            traveller.style.transform = `translate3d(${point.x - 9}px,${point.y - 24}px,0)`;
            const rect = scene.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > innerHeight) return;
            const depth = Math.max(-1, Math.min(1, (innerHeight / 2 - rect.top) / innerHeight));
            scene.querySelector('.lup-neighbourhood').style.transform = `perspective(900px) rotateX(${depth * 9}deg) rotateY(${depth * -7}deg)`;
        };
        const schedule = () => { if (!frame) frame = requestAnimationFrame(draw); };
        const animate = time => {
            ambient = 0;
            if (reduced.matches || document.hidden || !sceneVisible) return;
            const progress = (time % 14000) / 14000;
            const point = heroPath.getPointAtLength(heroLength * progress);
            runner.style.transform = `translate(${point.x - 85}px,${point.y - 335}px)`;
            ambient = requestAnimationFrame(animate);
        };
        const resume = () => {
            if (ambient) cancelAnimationFrame(ambient);
            ambient = 0;
            if (!reduced.matches && !document.hidden && sceneVisible) ambient = requestAnimationFrame(animate);
            if (reduced.matches) {
                scene.querySelector('.lup-neighbourhood').style.transform = '';
                runner.style.transform = '';
            }
            schedule();
        };
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(entries => { sceneVisible = entries[0].isIntersecting; resume(); }).observe(scene);
            const reveal = new IntersectionObserver(entries => entries.forEach(entry => {
                entry.target.classList.toggle('lup-motion-visible', entry.isIntersecting);
            }), {threshold:.12});
            main.querySelectorAll('section').forEach(section => reveal.observe(section));
        }
        scroller?.addEventListener('scroll', schedule, {passive:true});
        window.addEventListener('scroll', schedule, {passive:true});
        window.addEventListener('resize', () => { buildRoute(); schedule(); }, {passive:true});
        document.addEventListener('visibilitychange', resume);
        reduced.addEventListener('change', resume);
        if ('ResizeObserver' in window) new ResizeObserver(() => { buildRoute(); schedule(); }).observe(main);
        const story = document.querySelector('.lup-destination-story');
        const destinations = [...document.querySelectorAll('.lup-destination')];
        destinations.forEach(button => button.addEventListener('click', () => {
            destinations.forEach(other => { other.classList.toggle('is-selected', other === button); other.setAttribute('aria-pressed', String(other === button)); });
            story.querySelector('strong').textContent = button.lastElementChild.textContent;
            story.querySelector('p > span').textContent = button.dataset.description;
            story.style.setProperty('--story-color', button.style.getPropertyValue('--place-color'));
        }));
        document.querySelector('.lup-back-top').addEventListener('click', event => {
            event.preventDefault();
            const behavior = reduced.matches ? 'instant' : 'smooth';
            scroller?.scrollTo({top:0, behavior});
            window.scrollTo({top:0, behavior});
        });
        buildRoute();
        resume();
    };
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', ready) : ready();
})();
