/* One navigation controller for the whole LinkUUp backend. */
document.documentElement.classList.add('lup-backend-ui');
(() => {
    'use strict';
    // Keep the framework's real forms, field names, ACL controls and submit handlers.
    // Only their presentation changes; a non-JS page retains its original accordions.
    const enhanceAccountSettings = (content, de) => {
        if (!/\/account[.;](allsettings|settings)[.;]/i.test(location.pathname)) return;
        const blocks = [...content.querySelectorAll('.gdt-accordeon')].filter(el => el.querySelector('.accordion-collapse[id^="acc_"] form'));
        if (!blocks.length) return;
        const make = (tag, cls, text) => {
            const el = document.createElement(tag);
            if (cls) el.className = cls;
            if (text) el.textContent = text;
            return el;
        };
        const names = {
            LinkUUp:['Dein Profil','Your profile','user','Was dich ausmacht und wo du zu Hause bist.','What makes you you, and where you call home.'],
            AboutMe:['Über dich','About you','comment','Erzähl etwas von dir.','Tell people a little about yourself.'],
            User:['Konto & Sichtbarkeit','Account & visibility','shield-alt','Deine Kontodaten und die Sichtbarkeit deines Profils.','Your account details and who can see your profile.'],
            Birthday:['Geburtstag','Birthday','birthday-cake','Dein Geburtsdatum und wer dein Alter sehen darf.','Your date of birth and who can see your age.'],
            Friends:['Freunde','Friends','users','Bestimme, wer dich hinzufügen und deine Freunde sehen darf.','Choose who can add you and see your friends.'],
            Gallery:['Galerie','Gallery','images','Entscheide, wer deine Bilder sehen darf.','Choose who can see your photos.'],
            Contact:['Kontakt','Contact','address-book','Deine zusätzlichen Kontaktmöglichkeiten.','Your other ways to stay in touch.'],
            Country:['Herkunft & Wohnort','Home & origin','globe','Wo du lebst und woher du kommst.','Where you live and where you are from.'],
            Language:['Sprache','Language','language','LinkUUp in deiner Sprache.','LinkUUp in your language.'],
            Date:['Datum & Uhrzeit','Date & time','clock','Zeitzone und Anzeige deiner Aktivität.','Time zone and how your activity is displayed.']
        };
        const previous = blocks.find(el => el.querySelector('.gdt-error,.is-invalid')) || blocks.find(el => el.querySelector('.accordion-collapse.show'));
        const head = make('header','lup-settings-heading');
        head.append(make('span','lup-settings-eyebrow',de?'PERSÖNLICH EINSTELLEN':'MAKE IT YOURS'),make('h1','',de?'Dein Konto':'Your account'),make('p','',de?'Deine Angaben. Deine Sichtbarkeit. Dein LinkUUp.':'Your details. Your visibility. Your LinkUUp.'));
        const layout = make('div','lup-settings-layout');
        const nav = make('nav','lup-settings-nav');
        nav.setAttribute('aria-label',de?'Kontobereiche':'Account sections');
        const mobile = make('label','lup-settings-picker',de?'Bereich':'Section');
        const select = make('select');
        mobile.append(select);
        const panels = make('div','lup-settings-panels');
        const entries = blocks.map(block => {
            const panel = block.querySelector('.accordion-collapse');
            const key = panel.id.slice(4);
            const fallback = block.querySelector('.accordion-button')?.textContent.trim() || key;
            const config = names[key] || [fallback,fallback,'sliders-h','',''];
            const title = config[de?0:1];
            const button = make('button','lup-settings-tab');
            button.type = 'button'; button.setAttribute('aria-controls',panel.id);
            const icon = make('i','fas fa-'+config[2]); icon.setAttribute('aria-hidden','true');
            button.append(icon,make('span','',title));
            const option = make('option','',title); option.value = panel.id;
            block.classList.add('lup-settings-section');
            block.querySelector('.accordion-header').hidden = true;
            panel.classList.remove('collapse','collapsing'); panel.classList.add('show');
            panel.style.removeProperty('height');
            const formHead = block.querySelector('.card-header');
            const heading = formHead.querySelector('.card-title');
            heading.textContent = title; heading.id = panel.id+'-title';
            panel.setAttribute('role','region'); panel.setAttribute('aria-labelledby',heading.id);
            if (config[de?3:4]) formHead.append(make('p','lup-settings-description',config[de?3:4]));
            const mark = make('span','lup-settings-mark');
            const markIcon = icon.cloneNode(true); mark.append(markIcon); formHead.prepend(mark);
            block.querySelectorAll('.gdt-form-fields > *').forEach(field => {
                if (field.querySelector('textarea,[name="lup_status"],[name="lup_profile_outside_visible"],.editormd')) field.classList.add('lup-field-wide');
                const controls = [...field.querySelectorAll('input:not([type=hidden]),select,textarea')];
                if (controls.length && controls.every(el=>el.disabled)) field.classList.add('lup-field-readonly');
            });
            // GDT_Submit dispatches by name/presence; its value is only the translated label.
            block.querySelectorAll('input[type="submit"]').forEach(submit => {
                submit.value = de ? 'Änderungen speichern' : 'Save changes';
            });
            return {key,block,panel,button,option};
        }).sort((a,b) => {
            const rank=key=>{const i=Object.keys(names).indexOf(key);return i<0?99:i;};
            return rank(a.key)-rank(b.key);
        });
        const choose = (id, updateHash = false) => {
            const active = entries.find(entry=>entry.panel.id===id) || entries[0];
            entries.forEach(entry=>{
                entry.block.hidden = entry!==active;
                entry.button.setAttribute('aria-pressed',String(entry===active));
            });
            select.value=active.panel.id;
            if (updateHash) history.replaceState(history.state,'','#'+active.panel.id);
            // Editors/autocompletes keep their original instances; notify resize after reveal.
            requestAnimationFrame(()=>window.dispatchEvent(new Event('resize')));
        };
        entries.forEach(entry=>{
            nav.append(entry.button); select.append(entry.option); panels.append(entry.block);
            entry.button.addEventListener('click',()=>choose(entry.panel.id,true));
        });
        select.addEventListener('change',()=>choose(select.value,true));
        window.addEventListener('hashchange',()=>choose(location.hash.slice(1)));
        const intro = [...content.children].find(el=>el.matches('.gdt-panel:not(.gdt-accordeon)') && !el.querySelector('form,.gdt-error,.alert-danger'));
        if (intro) intro.hidden = true;
        const bar = content.querySelector('.lup-section-nav');
        if (bar) {
            const wrapper = bar.closest('.card');
            const tools = make('details','lup-settings-tools');
            tools.append(make('summary','',de?'Weitere Kontoaktionen':'More account actions'),bar);
            content.append(tools);
            if (wrapper && !wrapper.textContent.trim() && !wrapper.querySelector('input,select,textarea,img,form')) wrapper.remove();
            if (de) bar.querySelectorAll('a').forEach(link => {
                const text = link.textContent.trim();
                if (text==='Delete Account') {
                    [...link.childNodes].filter(node=>node.nodeType===Node.TEXT_NODE).forEach(node=>node.remove());
                    link.append(document.createTextNode('Konto löschen'));
                }
            });
        }
        layout.append(nav,panels);
        content.prepend(head,mobile,layout);
        content.classList.add('lup-account-settings');
        choose(previous?.querySelector('.accordion-collapse')?.id || location.hash.slice(1));
        // Fix missing/technical labels locally, without changing API fields or ACL values.
        const fieldLabels = de ? {lup_profile_outside_visible:'Profil auch außerhalb einer Location zeigen',lup_state:'Bundesland / Region',lup_city:'Wohnort',age_visible:'Wer darf dein Alter sehen?',activity_accuracy:'Genauigkeit der Aktivitätsanzeige',gallery_acl:'Wer darf deine Galerie sehen?'} : {lup_profile_outside_visible:'Show profile outside a location',lup_state:'State / region',lup_city:'City'};
        Object.entries(fieldLabels).forEach(([id,text])=>{
            const label = content.querySelector('label[for="'+id+'"]');
            if (!label) return;
            [...label.childNodes].filter(node=>node.nodeType===Node.TEXT_NODE).forEach(node=>node.remove());
            label.prepend(document.createTextNode(text));
        });
        // The legacy Markdown editor starts split in half, even on a phone.
        // Use its own preview toggle once; editing and the optional preview remain intact.
        content.querySelectorAll('.wysiwyg.gdt-editor-markdown').forEach(editor => {
            const adaptEditor = () => {
                const toggle = editor.querySelector('.editormd-toolbar .fa-eye-slash[name="watch"]');
                if (!toggle) return;
                observer.disconnect();
                requestAnimationFrame(()=>toggle.closest('a')?.click());
            };
            const observer = new MutationObserver(adaptEditor);
            observer.observe(editor,{childList:true,subtree:true});
            adaptEditor();
        });
    };
    const enhanceCredits = (content, de) => {
        if (!/\/paymentcredits[.;]ordercredits[.;]/i.test(location.pathname)) return;
        const field=content.querySelector('input[name="co_credits"]');
        const form=field?.closest('.gdt-form');
        const pricing=window.LUP_CREDITS_PRICING;
        if (!form || !pricing || !Number.isFinite(pricing.unitPrice) || pricing.unitPrice<=0 || !Number.isSafeInteger(pricing.minCredits) || pricing.minCredits<1) return;
        const make=(tag,cls,text)=>{const el=document.createElement(tag);if(cls)el.className=cls;if(text)el.textContent=text;return el;};
        let money;
        try {money=new Intl.NumberFormat(de?'de-DE':'en-GB',{style:'currency',currency:pricing.currency});} catch {return;}
        const number=new Intl.NumberFormat(de?'de-DE':'en-GB');
        const hero=make('header','lup-credits-hero');
        const intro=make('div','lup-credits-intro');
        intro.append(make('span','lup-credits-eyebrow','LINKUUP CREDITS'),make('h1','',de?'Mach mehr aus deinem Ort.':'Make more of your place.'),make('p','',de?'Dein Guthaben für eigene Treffpunkte, mehr Sichtbarkeit und Nachrichten, die weiterreichen.':'Your balance for your own meeting places, more visibility and messages that reach further.'));
        const wallet=make('div','lup-credits-wallet');
        const icon=make('i','fas fa-coins');icon.setAttribute('aria-hidden','true');wallet.append(icon);
        const link=[...document.querySelectorAll('#navbarSupportedContent a')].find(a=>/paymentcredits[.;]ordercredits/i.test(a.pathname));
        const balance=link?.textContent.match(/\(([^)]+)\)/)?.[1];
        wallet.append(make('span','',de?'Dein Guthaben':'Your balance'),make('strong','',balance===undefined?'Credits':balance),make('small','',balance===undefined?'LinkUUp':de?'Credits auf deinem Konto':'Credits in your account'));
        hero.append(intro,wallet);
        const order=make('section','lup-credits-order');
        const selection=make('div','lup-credits-selection');
        selection.append(make('h2','',de?'Wie viel hast du vor?':'What do you have in mind?'),make('p','',de?'Wähle eine Menge oder trage deinen eigenen Betrag an Credits ein.':'Choose an amount or enter your own number of credits.'));
        const packs=make('div','lup-credits-packs');packs.setAttribute('aria-label',de?'Credits-Menge auswählen':'Choose a credit amount');
        const titles=de?['Zum Start','Mehr Spielraum','Große Pläne']:['Get started','More possibilities','Bigger plans'];
        const buttons=[1,2,4].map((factor,index)=>{
            const amount=pricing.minCredits*factor;
            const button=make('button','lup-credit-pack');button.type='button';button.dataset.amount=String(amount);
            button.append(make('span','lup-pack-title',titles[index]),make('strong','',number.format(amount)),make('span','lup-pack-unit','Credits'),make('span','lup-pack-price',money.format(Math.round(amount*pricing.unitPrice*100)/100)),make('small','',de?'Basispreis':'Base price'));
            button.addEventListener('click',()=>{field.value=String(amount);field.dispatchEvent(new Event('input',{bubbles:true}));field.dispatchEvent(new Event('change',{bubbles:true}));});
            packs.append(button);return button;
        });
        selection.append(packs,make('p','lup-credit-pack-note',de?'Basispreise, zuzüglich möglicher Steuern und Zahlungsgebühren.':'Base prices, plus applicable taxes and payment fees.'));
        const heading=form.querySelector('.card-title');if(heading)heading.textContent=de?'Deine Auswahl':'Your selection';
        const label=form.querySelector('label[for="'+field.id+'"]');
        if(label){[...label.childNodes].filter(n=>n.nodeType===Node.TEXT_NODE).forEach(n=>n.remove());label.prepend(document.createTextNode(de?'Anzahl Credits':'Number of credits'));}
        field.inputMode='numeric';
        const quote=make('div','lup-credit-quote');
        quote.append(make('span','',de?'Basispreis deiner Auswahl':'Base price of your selection'));
        const price=make('output');price.setAttribute('aria-live','polite');quote.append(price);
        const note=make('p','lup-credit-price-note',de?'Basispreis ohne Steuern und mögliche Zahlungsgebühren. Der Gesamtbetrag richtet sich nach Rechnungsadresse und Zahlungsart; prüfe ihn im Bestellablauf.':'Base price excludes taxes and possible payment fees. The total depends on billing address and payment method; review it during checkout.');
        const actions=form.querySelector('.gdt-form-actions');actions.before(quote,note);
        const submit=form.querySelector('input[type=submit]');if(submit)submit.value=de?'Weiter zur Übersicht':'Review selection';
        const update=()=>{
            const raw=field.value.trim(),amount=/^\d+$/.test(raw)?Number(raw):NaN;
            const valid=Number.isSafeInteger(amount)&&amount>0;
            price.textContent=valid?money.format(Math.round(amount*pricing.unitPrice*100)/100):(de?'Menge eingeben':'Enter an amount');
            buttons.forEach(button=>button.setAttribute('aria-pressed',String(valid&&Number(button.dataset.amount)===amount)));
        };
        field.addEventListener('input',update);field.addEventListener('change',update);update();
        order.append(selection,form);
        const uses=make('section','lup-credits-uses');uses.append(make('h2','',de?'Deine Credits. Dein nächster Schritt.':'Your credits. Your next step.'));
        const grid=make('div','lup-credit-uses-grid');
        const features=de?[
            ['map-marker-alt','Eigene Treffpunkte','Locations anlegen und ihre Sichtbarkeit erweitern.',''],
            ['bullhorn','Ein Impuls an viele Orte','Mit einem Shout besetzte Locations erreichen.',''],
            ['street-view','Nachrichten im Umkreis','Menschen gezielt rund um deinen Standort ansprechen.','Geplant'],
            ['glass-cheers','Direkt an der Bar','Mit Credits bei Partner-Locations bezahlen.','Geplant']
        ]:[
            ['map-marker-alt','Your own meeting places','Create locations and extend their visibility.',''],
            ['bullhorn','Reach more places','Send a shout to occupied locations.',''],
            ['street-view','Messages nearby','Reach people around your location.','Planned'],
            ['glass-cheers','At the bar','Pay with credits at partner locations.','Planned']
        ];
        features.forEach(([symbol,title,text,status])=>{
            const card=make('article','lup-credit-use'+(status?' lup-credit-planned':''));
            const mark=make('i','fas fa-'+symbol);mark.setAttribute('aria-hidden','true');card.append(mark);
            if(status)card.append(make('span','lup-credit-status',status));
            card.append(make('h3','',title),make('p','',text));grid.append(card);
        });
        uses.append(grid,make('p','lup-credit-roadmap-note',de?'Geplante Funktionen sind noch nicht nutzbar. Ihre Verfügbarkeit ist nicht Bestandteil dieses Credit-Kaufs.':'Planned features are not available yet. Their availability is not included in this credit purchase.'));
        content.classList.add('lup-credits-page');content.prepend(hero,order,uses);
    };
    const enhanceOrders = (content, de) => {
        if (!/\/payment[.;]yourorders[.;]/i.test(location.pathname)) return;
        const table=content.querySelector('.gdt-table table'), form=table?.closest('form');
        if (!table || !form) return;
        const make=(tag,cls,text)=>{const e=document.createElement(tag);if(cls)e.className=cls;if(text)e.textContent=text;return e;};
        content.classList.add('lup-orders-page');
        const hero=make('header','lup-orders-hero');
        hero.append(make('span','lup-orders-kicker','DEIN LINKUUP'),make('h1','',de?'Deine Bestellungen':'Your orders'),make('p','',de?'Alles an einem Ort: deine Käufe, Zahlungen und der Stand deiner Bestellungen.':'Your purchases, payments and order progress, together in one place.'));
        content.prepend(hero);
        const nav=content.querySelector('.lup-section-nav');
        if(nav){nav.classList.add('lup-orders-links');hero.after(nav);}
        const caption=form.querySelector('.gdo-table-caption');
        if(caption)caption.classList.add('lup-orders-count');
        const details=make('details','lup-order-filters');
        details.append(make('summary','',de?'Bestellungen filtern':'Filter orders'));
        const grid=make('div','lup-order-filter-grid');details.append(grid);
        const heads=[...table.querySelectorAll('thead th')];
        heads.forEach((th,index)=>{
            const inputs=[...th.querySelectorAll('input,select')];
            const title=th.querySelector('.gdt-table-order')?.textContent.trim() || (index===1?(de?'Beleg':'Receipt'):(de?'Details':'Details'));
            th.setAttribute('scope','col');
            if(!th.textContent.trim())th.append(document.createTextNode(title));
            table.querySelectorAll('tbody tr').forEach(row=>{if(row.cells[index])row.cells[index].dataset.label=title;});
            if(!inputs.length)return;
            const group=make('fieldset','lup-order-filter');group.append(make('legend','',title));
            inputs.forEach((input,n)=>{
                const label=make('label','',inputs.length>1?(n===0?(de?'Von':'From'):(de?'Bis':'To')):(de?'Suchtext':'Search text'));
                label.append(input);group.append(label);
            });grid.append(group);
        });
        const active=[...grid.querySelectorAll('input,select')].some(e=>e.value.trim());
        details.open=active;
        const actions=make('div','lup-order-filter-actions');
        const apply=make('button','',de?'Filter anwenden':'Apply filters');apply.type='submit';
        const clear=make('a','',de?'Filter zurücksetzen':'Clear filters');clear.href=location.pathname+'?_lang='+(de?'de':'en');
        actions.append(apply,clear);details.append(actions);form.prepend(details);
        const scroll=table.closest('.lup-table-scroll');
        if(!table.querySelector('tbody tr')){
            scroll.hidden=true;
            const empty=make('section','lup-orders-empty');
            const icon=make('i','fas fa-receipt');icon.setAttribute('aria-hidden','true');empty.append(icon);
            empty.append(make('h2','',active?(de?'Keine passenden Bestellungen':'No matching orders'):(de?'Dein nächster Moment wartet.':'Your next moment awaits.')),
                make('p','',active?(de?'Passe deine Filter an oder zeige wieder alle Bestellungen.':'Adjust your filters or show all orders again.'):(de?'Hier erscheinen deine Bestellungen, sobald du etwas bestellst. Entdecke, was du mit LinkUUp Credits machen kannst.':'Your orders will appear here when you place one. Discover what you can do with LinkUUp Credits.')));
            const cta=make('a','lup-orders-cta',active?(de?'Alle Bestellungen anzeigen':'Show all orders'):(de?'Credits entdecken':'Explore credits'));
            cta.href=active?clear.href:'/paymentcredits.ordercredits.html?_lang='+(de?'de':'en');empty.append(cta);scroll.after(empty);
        }
    };
    const enhanceProfile = (content, de) => {
        if (!/\/user[.;]profile[.;]/i.test(location.pathname)) return;
        const fields=content.querySelector('.gdt-card-fields'), card=fields?.closest('.card');
        if(!fields || !card)return;
        content.classList.add('lup-person-page');card.classList.add('lup-person-card');
        const make=(tag,cls,text)=>{const el=document.createElement(tag);el.className=cls;if(text)el.textContent=text;return el;};
        const names=de?['Über mich','Aktivität','Kontakt & Einstellungen','Technische Angaben']:['About me','Activity','Contact & settings','Technical details'];
        const groups=names.map((name,i)=>{const section=make(i===3?'details':'section','lup-person-section');section.append(make(i===3?'summary':'h2','',name));const list=make('div','lup-person-facts');section.append(list);return {section,list};});
        const nodes=[...fields.children];
        for(let i=0;i<nodes.length;i++){
            const label=nodes[i],value=nodes[i+1];
            if(!(label.matches('label,.gdt-card-label') && value?.matches('span,.gdt-card-message')))continue;
            i++;
            const text=[...label.childNodes].filter(n=>n.nodeType===Node.TEXT_NODE).map(n=>n.textContent).join('').trim().replace(/[:\s]+$/,'');
            let group=0;
            if(/IP$|Bildschirm|Geräteversion|Geschwindigkeit|screen|user.agent|speed/i.test(text))group=3;
            else if(/Aktivität|Profiltreffer|Likes|High Fives|Registriert|beschäftigt|activity|views|registered|status/i.test(text))group=1;
            else if(/Mail|Telegram|WhatsApp|URL|Adresse|Addresse|Zeitzone|Aktivierungs|mitteilen|outside_visible|timezone|address|activation/i.test(text))group=2;
            const fallback=label.querySelector('.gdo-utf8-icon-text,.gdo-utf8-icon-select,.gdo-utf8-icon-dog,.gdo-utf8-icon-account_balance,.gdo-utf8-icon-telegram');
            if(fallback){const symbol=document.createElement('i');symbol.className='fas fa-'+(/Religion/i.test(text)?'landmark':/pet|Haustier/i.test(text)?'paw':/City|State/i.test(text)?'map-marker-alt':'circle');symbol.setAttribute('aria-hidden','true');fallback.replaceChildren(symbol);}
            const aliases={'__lup_profile_outside_visible':de?'Profil außerhalb einer Location':'Profile outside a location','Living State':de?'Bundesland / Region':'State / region','Living City':de?'Wohnort':'City'};
            if(aliases[text]){[...label.childNodes].filter(n=>n.nodeType===Node.TEXT_NODE).forEach(n=>n.remove());label.append(document.createTextNode(aliases[text]));}
            const item=make('div','lup-person-fact');
            if(value.matches('.gdt-card-message'))item.classList.add('lup-person-story');
            const raw=value.textContent.trim();if(!raw || /^(Nicht angegeben|Not specified|---.*---)$/i.test(raw))item.classList.add('lup-person-empty');
            item.append(label,value);groups[group].list.append(item);
        }
        groups.forEach(({section,list})=>{if(list.children.length)fields.append(section);});
        const edit=content.querySelector('.gdt-panel a[href*="account.allsettings"]');
        if(edit){const panel=edit.closest('.card');edit.textContent=de?'Profil bearbeiten':'Edit profile';edit.classList.add('lup-person-edit');card.querySelector('.card-header')?.append(edit);if(panel && panel!==card)panel.remove();}
    };
    const ready = () => {
        document.body.classList.add('lup-backend');
        const de = (document.documentElement.lang || window.GDO_LANGUAGE || 'de').startsWith('de');
        const labels = de ? {open:'Menü öffnen',close:'Menü schließen',nav:'Navigation',tag:'Zusammen unterwegs',account:'Konto öffnen',areas:'Konto & Bereiche',section:'Bereich auswählen'} : {open:'Open menu',close:'Close menu',nav:'Navigation',tag:'Together nearby',account:'Open account menu',areas:'Account & areas',section:'Choose a section'};
        const trigger = document.getElementById('sidebarToggle');
        const drawer = document.getElementById('sidebar-wrapper');
        if (!trigger || !drawer) return;
        if (drawer.dataset.lupShellReady) return;
        drawer.dataset.lupShellReady = '1';
        // Keep the brand and controls fixed; module links get their own row.
        // Move the existing anchors so permissions, destinations and handlers survive.
        const topbar = trigger.closest('nav.navbar');
        const brandSlot = topbar?.querySelector('.navbar-brand');
        const topLinks = document.getElementById('top');
        const home = topLinks?.querySelector('a[href*="linkuup.welcome"],a[href*="linkuup;welcome"]');
        topbar?.classList.add('lup-shell-header');
        topbar?.classList.remove('bg-light', 'navbar-expand-lg', 'border-bottom');
        drawer.classList.remove('bg-body-tertiary', 'border-end');
        if (home && brandSlot) {
            home.classList.add('lup-shell-brand');
            home.setAttribute('aria-label', 'LinkUUp');
            home.innerHTML = 'link<span>uup</span>';
            brandSlot.append(home);
        }
        if (topLinks && brandSlot) {
            topLinks.querySelectorAll('.gdt-link').forEach(item => {
                if (!item.querySelector('a,button') && !item.textContent.trim()) item.remove();
            });
            if (topLinks.querySelector('a[href]')) {
                topLinks.classList.add('lup-context-nav');
                topLinks.setAttribute('aria-label', labels.section);
                topLinks.setAttribute('role', 'navigation');
                topbar.append(topLinks);
            } else topLinks.hidden = true;
        }
        drawer.setAttribute('aria-label', labels.nav);
        trigger.innerHTML = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M4 7h16M4 12h11M4 17h16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>';
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
        const welcome = nav?.querySelector('a[href*="linkuup.welcome"],a[href*="linkuup;welcome"]')?.closest('li');
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
            if (open) {
                closeAccount();
                drawer.dataset.lupAnimated = '1';
            }
            document.body.classList.toggle('lup-arrival-menu-open', open);
            trigger.setAttribute('aria-expanded', String(open));
            trigger.setAttribute('aria-label', open ? labels.close : labels.open);
            drawer.inert = !open;
            drawer.setAttribute('aria-hidden', String(!open));
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
            content.querySelectorAll('.gdt-bar, .lup-admin-nav').forEach(bar => {
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
            enhanceAccountSettings(content, de);
            enhanceCredits(content, de);
            enhanceOrders(content, de);
            enhanceProfile(content, de);
            // Filtered and asynchronously loaded tables need the same treatment.
            let tableFrame=0;
            new MutationObserver(records => {
                if (tableFrame || !records.some(record => [...record.addedNodes].some(node => node.nodeType===1 && (node.matches('table') || node.querySelector('table'))))) return;
                tableFrame=requestAnimationFrame(()=>{tableFrame=0;decorateTables();});
            }).observe(content,{childList:true,subtree:true});
        }
        document.querySelectorAll('.lup-context-nav a[href],.lup-section-nav a[href]').forEach(link => {
            const target = new URL(link.href);
            const current = new URL(location.href);
            const params = url => [...url.searchParams].filter(([key]) => !['_lang','lang'].includes(key)).sort().toString();
            if (target.origin === current.origin && target.pathname === current.pathname && params(target) === params(current)) link.setAttribute('aria-current', 'page');
        });
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
        if (account) account.innerHTML = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20v-2a7 7 0 0 1 14 0v2"/></svg>';
        if (accountPanel) {
            accountPanel.setAttribute('aria-label', labels.areas);
            const title = document.createElement('div');
            title.className = 'lup-account-title';
            title.textContent = labels.areas;
            accountPanel.prepend(title);
            // Give generic framework links a recognisable icon, preserving real avatars.
            const accountIcons = {register:'fa-user-plus',login:'fa-sign-in-alt',account:'fa-cog',admin:'fa-shield-alt',friends:'fa-users',gallery:'fa-images',logs:'fa-history',payment:'fa-receipt',paymentcredits:'fa-coins',linkuup:'fa-link'};
            accountPanel.querySelectorAll('a[href]').forEach(link => {
                const icon = link.querySelector('.gdo-icon .fa-link');
                const module = new URL(link.href).pathname.split('/').pop().split(/[.;]/)[0].toLowerCase();
                if (icon && accountIcons[module]) {
                    const name = /login[.;]logout/i.test(link.pathname) ? 'fa-sign-out-alt' : accountIcons[module];
                    icon.classList.replace('fa-link', name);
                    icon.setAttribute('aria-hidden', 'true');
                }
            });
            accountPanel.addEventListener('shown.bs.collapse', () => accountPanel.querySelector('a[href]')?.focus());
        }
        // Correct labels only on the login view; no form fields or validation change.
        if (/\/login\.form\.html$/i.test(location.pathname)) {
            const heading = document.querySelector('#content-wrap .card-title');
            if (heading) heading.textContent = de ? 'Willkommen zurück' : 'Welcome back';
            const identity = document.querySelector('label[for="login"]');
            if (identity?.firstChild?.nodeType === Node.TEXT_NODE) identity.firstChild.textContent = de ? 'E-Mail oder Nutzername ' : 'Email or username ';
        }
    };
    const initialize = () => {try {ready();} finally {document.documentElement.classList.remove('lup-shell-pending');}};
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initialize) : initialize();
})();
