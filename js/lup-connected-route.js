/* A native sticky route marker: no scroll sampling or chapter handoff. */
(() => {
    'use strict';
    const init=()=>{
        const main=document.querySelector('main.lup-arrival');
        const pin=main?.querySelector('.lup-scroll-traveller');
        const source=main?.querySelector('.lup-world-pin');
        const map=main?.querySelector('.lup-scroll-map');
        if(!pin||!source||!map)return;
        pin.innerHTML=source.outerHTML.replace('class="lup-world-pin"','class="lup-route-symbol"').replaceAll('lup-pin-gradient','lup-route-gradient');
        const layer=document.createElement('div');layer.className='lup-route-layer';layer.setAttribute('aria-hidden','true');
        main.append(layer);layer.append(pin);
        const layout=()=>{
            const rail=Math.max(16,(main.clientWidth-1160)/2+14);
            layer.style.setProperty('--route-x',`${rail}px`);
            map.setAttribute('viewBox',`0 0 ${main.clientWidth} ${main.offsetHeight}`);
            map.querySelectorAll('path').forEach(path=>path.setAttribute('d',`M${rail} 70 V${main.offsetHeight}`));
        };
        new ResizeObserver(layout).observe(main);
        document.addEventListener('lup:earth-presence',e=>{pin.style.opacity=String(.8*(1-e.detail.presence));});
        layout();
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
