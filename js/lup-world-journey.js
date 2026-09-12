/* A scroll-led globe: explore, walk, become a destination. No tracking data. */
(() => {
    'use strict';
    const init = () => {
        const chapter = document.querySelector('#lup-arrival-journey');
        const rotation = chapter?.querySelector('.lup-world-rotation');
        if (!rotation) return;
        const stage = chapter.querySelector('.lup-journey-stage');
        const space = chapter.querySelector('.lup-journey-space');
        const sphere = chapter.querySelector('.lup-world-sphere');
        const carrier = chapter.querySelector('.lup-world-carrier');
        const pin = chapter.querySelector('.lup-world-pin');
        const halo = chapter.querySelector('.lup-world-halo');
        const orbits = [...chapter.querySelectorAll('.lup-world-orbit')];
        const steps = [...chapter.querySelectorAll('.lup-arrival-flow li')];
        const reduced = matchMedia('(prefers-reduced-motion: reduce)');
        const clamp = x => Math.max(0, Math.min(1,x));
        const ease = x => { x=clamp(x); return x*x*(3-2*x); };
        // Stylised continent outlines; decorative, not a geographic data layer.
        const land = [
            [[-168,70],[-130,72],[-110,55],[-82,50],[-60,48],[-83,25],[-100,18],[-114,32],[-140,55]],
            [[-80,12],[-61,9],[-36,-6],[-49,-26],[-69,-55],[-76,-28]],
            [[-18,35],[11,37],[35,30],[50,10],[36,-26],[18,-35],[7,-9],[-15,6]],
            [[-10,36],[-9,58],[20,72],[52,68],[73,75],[130,65],[170,55],[145,38],[121,20],[105,2],[80,9],[68,25],[43,34]],
            [[111,-12],[135,-11],[154,-25],[145,-40],[114,-34]],
            [[-53,60],[-22,66],[-25,82],[-55,82]]
        ];
        // A single clipped vector surface replaces hundreds of separate 3-D dots.
        const ns='http://www.w3.org/2000/svg';
        const surface=document.createElementNS(ns,'svg');
        surface.setAttribute('viewBox','0 0 720 360');
        surface.setAttribute('preserveAspectRatio','none');
        surface.classList.add('lup-world-surface');
        surface.setAttribute('aria-hidden','true');
        land.forEach(outline=>{
            const shape=document.createElementNS(ns,'path');
            const points=outline.map(([lon,lat])=>[(lon+180)*2,(90-lat)*2]);
            let d='';
            points.forEach((v,i)=>{
                const prev=points[(i+points.length-1)%points.length],next=points[(i+1)%points.length];
                const a=[v[0]+(prev[0]-v[0])*.18,v[1]+(prev[1]-v[1])*.18];
                const b=[v[0]+(next[0]-v[0])*.18,v[1]+(next[1]-v[1])*.18];
                d+=`${i?'L':'M'}${a} Q${v} ${b} `;
            });
            shape.setAttribute('d',d+'Z');
            surface.append(shape);
        });
        rotation.append(surface);
        let frame=0, shown=0, lastTime=0, started=false;
        const draw=(time)=>{
            frame=0;
            if(document.hidden)return;
            const rect=chapter.getBoundingClientRect();
            const top=innerWidth<761?76:90;
            const targetProgress=clamp((top-rect.top)/Math.max(1,chapter.offsetHeight-stage.offsetHeight));
            const dt=lastTime?Math.min(64,time-lastTime):16;
            lastTime=time;
            if(!started){shown=targetProgress;started=true;}
            shown+=(targetProgress-shown)*(1-Math.exp(-dt/100));
            if(Math.abs(targetProgress-shown)<.0001)shown=targetProgress;
            const p=shown;
            const staticMode=reduced.matches || innerHeight<700;
            chapter.classList.toggle('lup-world-static',staticMode);
            surface.style.transform=`translate3d(${-20-(staticMode?.35:p)*12}%,0,0)`;
            sphere.style.transform='none';sphere.style.opacity='1';sphere.style.visibility='visible';
            pin.style.opacity='0';halo.style.opacity='0';
            orbits.forEach(el=>el.style.opacity='0');
            carrier.style.transform='none';carrier.style.opacity='1';
            const current=Math.min(2,Math.floor(p*3));
            steps.forEach((li,i)=>li.classList.toggle('lup-step-current',staticMode||i===current));
            if(!staticMode && Math.abs(targetProgress-shown)>.0001)frame=requestAnimationFrame(draw);
        };
        const schedule=()=>{if(!frame){lastTime=0;frame=requestAnimationFrame(draw);}};
        window.addEventListener('scroll',schedule,{passive:true});
        window.addEventListener('resize',schedule,{passive:true});
        document.addEventListener('visibilitychange',schedule);
        reduced.addEventListener('change',schedule);
        new ResizeObserver(schedule).observe(space);
        schedule();
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
