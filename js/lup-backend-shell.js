/* One navigation controller for the whole LinkUUp backend. */
document.documentElement.classList.add('lup-backend-ui');
(() => {
    'use strict';
    const ready = () => {
        document.body.classList.add('lup-backend');
        const de = (document.documentElement.lang || window.GDO_LANGUAGE || 'de').startsWith('de');
        const labels = de ? {open:'Menü öffnen',close:'Menü schließen',nav:'Navigation',tag:'Zusammen unterwegs',account:'Konto öffnen'} : {open:'Open menu',close:'Close menu',nav:'Navigation',tag:'Together nearby',account:'Open account menu'};
        const trigger = document.getElementById('sidebarToggle');
        const drawer = document.getElementById('sidebar-wrapper');
        if (!trigger || !drawer) return;
        drawer.setAttribute('aria-label', labels.nav);
        const header = document.createElement('div');
        header.className = 'lup-drawer-head';
        const brand = document.createElement('div');
        brand.innerHTML = '<b>link<span>uup</span></b><small></small>';
        brand.querySelector('small').textContent = labels.tag;
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'lup-menu-close';
        closeButton.textContent = '×';
        closeButton.setAttribute('aria-label', labels.close);
        header.append(brand, closeButton);
        drawer.prepend(header);
        const nav = drawer.querySelector('#leftnav');
        const welcome = nav?.querySelector('a[href*="linkuup.welcome"]')?.closest('li');
        if (welcome) nav.prepend(welcome);
        const lang = drawer.querySelector('.gdo-lang-switch');
        if (lang) {
            const languages = document.createElement('div');
            languages.className = 'lup-drawer-languages';
            languages.setAttribute('aria-label', de ? 'Sprache wählen' : 'Choose language');
            const item = lang.closest('li');
            languages.append(lang);
            drawer.append(languages);
            if (item && !item.textContent.trim()) item.hidden = true;
        }
        drawer.querySelectorAll('li').forEach(item => {
            if (!item.querySelector('a,button,input,select') && !item.textContent.trim()) item.hidden = true;
        });
        drawer.querySelectorAll('a[href]').forEach(link => {
            const target = new URL(link.href);
            const current = target.pathname === location.pathname || (document.querySelector('.lup-arrival') && target.pathname.includes('linkuup.welcome'));
            if (current && !link.closest('.gdo-lang-switch')) link.setAttribute('aria-current', 'page');
        });
        const shade = document.createElement('button');
        shade.type = 'button';
        shade.className = 'lup-menu-shade';
        shade.setAttribute('aria-label', labels.close);
        shade.hidden = true;
        document.body.append(shade);
        trigger.setAttribute('aria-controls', drawer.id);
        const page = document.getElementById('page-content-wrapper');
        const footer = document.querySelector('body > footer');
        let focusTimer;
        const setOpen = (open, restore = false) => {
            clearTimeout(focusTimer);
            document.body.classList.toggle('lup-arrival-menu-open', open);
            trigger.setAttribute('aria-expanded', String(open));
            trigger.setAttribute('aria-label', open ? labels.close : labels.open);
            drawer.inert = !open;
            if (page) page.inert = open;
            if (footer) footer.inert = open;
            shade.hidden = !open;
            if (open) focusTimer = setTimeout(() => {
                if (document.body.classList.contains('lup-arrival-menu-open')) closeButton.focus();
            }, 280);
            else if (restore) trigger.focus();
        };
        trigger.addEventListener('click', event => {
            event.preventDefault(); event.stopImmediatePropagation();
            setOpen(!document.body.classList.contains('lup-arrival-menu-open'));
        }, true);
        // Release the backdrop and inert state before following a menu destination.
        drawer.addEventListener('click', event => {
            const link=event.target.closest('a[href]');
            if (!link || event.defaultPrevented || event.button!==0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            if (link.matches('[data-bs-toggle], [aria-expanded]') || link.getAttribute('href')==='#') return;
            setOpen(false);
        });
        closeButton.addEventListener('click', () => setOpen(false, true));
        shade.addEventListener('click', () => setOpen(false, true));
        document.addEventListener('keydown', event => {
            if (!document.body.classList.contains('lup-arrival-menu-open')) return;
            if (event.key === 'Escape') setOpen(false, true);
            if (event.key === 'Tab') {
                const items = [...drawer.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled])')].filter(el => el.getClientRects().length);
                if (!drawer.contains(document.activeElement)) { event.preventDefault(); closeButton.focus(); }
                else if (event.shiftKey && document.activeElement === items[0]) { event.preventDefault(); items.at(-1)?.focus(); }
                else if (!event.shiftKey && document.activeElement === items.at(-1)) { event.preventDefault(); items[0]?.focus(); }
            }
        });
        window.addEventListener('pageshow', () => setOpen(false));
        setOpen(false);
        const content = document.getElementById('content-wrap');
        if (content) {
            content.querySelectorAll('.gdt-bar').forEach(bar => {
                if (bar.closest('form,.gdt-form,.gdt-list-item')) return;
                if (bar.querySelectorAll('a[href]').length > 1) bar.classList.add('lup-section-nav');
            });
            if (/\/friends[.;]/i.test(location.pathname)) {
                content.querySelectorAll('a[href]').forEach(link => {
                    const match = new URL(link.href).pathname.match(/friends[.;](requesting|requests|request|friendlist)\.html/i);
                    if (!match || !link.closest('.lup-section-nav')) return;
                    const names = {request:'Freund hinzufügen',friendlist:'Meine Freunde',requests:'Eingehende Anfragen',requesting:'Gesendete Anfragen'};
                    link.textContent = de ? names[match[1].toLowerCase()] : link.textContent.replace(/\s*\(%[ds]\)/g,'');
                    if (new URL(link.href).pathname === location.pathname) link.setAttribute('aria-current','page');
                });
            }
            const decorateTables = () => {
                content.querySelectorAll('table').forEach(table => {
                    if (table.closest('.editormd,.CodeMirror')) return;
                    table.classList.add('lup-data-table');
                    if (table.closest('.lup-table-scroll')) return;
                    const wrap=document.createElement('div');wrap.className='lup-table-scroll';
                    wrap.tabIndex=0;wrap.setAttribute('role','region');wrap.setAttribute('aria-label',de?'Tabelle horizontal scrollen':'Scroll table horizontally');
                    table.before(wrap);wrap.append(table);
                });
            };
            decorateTables();
            // Filtered and asynchronously loaded tables need the same treatment.
            let tableFrame=0;
            new MutationObserver(records => {
                if (tableFrame || !records.some(record => [...record.addedNodes].some(node => node.nodeType===1 && (node.matches('table') || node.querySelector('table'))))) return;
                tableFrame=requestAnimationFrame(()=>{tableFrame=0;decorateTables();});
            }).observe(content,{childList:true,subtree:true});
        }
        const accountPanel=document.getElementById('navbarSupportedContent');
        const closeAccount=()=>window.bootstrap?.Collapse?.getInstance(accountPanel)?.hide();
        document.addEventListener('keydown',e=>{if(e.key==='Escape' && accountPanel?.classList.contains('show')) {closeAccount();document.querySelector('.navbar-toggler')?.focus();}});
        document.addEventListener('click',e=>{if(accountPanel?.classList.contains('show')&&!e.target.closest('#navbarSupportedContent,.navbar-toggler'))closeAccount();});
        accountPanel?.addEventListener('click', event => {
            const link=event.target.closest('a[href]');
            if (!link || event.defaultPrevented || event.button!==0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.hasAttribute('data-bs-toggle')) return;
            closeAccount();
        });
        window.addEventListener('pageshow',closeAccount);
        const account = document.querySelector('.navbar-toggler');
        account?.setAttribute('aria-label', labels.account);
        // Correct labels only on the login view; no form fields or validation change.
        if (/\/login\.form\.html$/i.test(location.pathname)) {
            const heading = document.querySelector('#content-wrap .card-title');
            if (heading) heading.textContent = de ? 'Willkommen zurück' : 'Welcome back';
            const identity = document.querySelector('label[for="login"]');
            if (identity?.firstChild?.nodeType === Node.TEXT_NODE) identity.firstChild.textContent = de ? 'E-Mail oder Nutzername ' : 'Email or username ';
        }
    };
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', ready) : ready();
})();
