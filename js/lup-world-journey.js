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
        const inside = (x,y,poly) => {let yes=false;for(let i=0,j=poly.length-1;i<poly.length;j=i++){const a=poly[i],b=poly[j];if(((a[1]>y)!==(b[1]>y))&&(x<(b[0]-a[0])*(y-a[1])/(b[1]-a[1])+a[0]))yes=!yes;}return yes;};
        const fragment=document.createDocumentFragment();
        for(let i=0;i<8;i++){const ring=document.createElement('span');ring.className='lup-world-meridian';ring.style.transform=`rotateY(${i*22.5}deg)`;fragment.append(ring);}
        for(const lat of [-60,-30,0,30,60]){const ring=document.createElement('span');ring.className='lup-world-latitude';const rad=lat*Math.PI/180;ring.style.width=ring.style.height=`${Math.cos(rad)*100}%`;ring.style.transform=`translate(-50%,-50%) translateY(calc(var(--globe-size) * ${-Math.sin(rad)/2})) rotateX(90deg)`;fragment.append(ring);}
        for(let lat=-54;lat<=78;lat+=6){for(let lon=-174;lon<180;lon+=6){if(!land.some(p=>inside(lon,lat,p)))continue;const dot=document.createElement('b');dot.className='lup-world-land';dot.style.transform=`rotateY(${lon}deg) rotateX(${-lat}deg) translateZ(calc(var(--globe-size) / 2))`;fragment.append(dot);}}
        rotation.append(fragment);
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
            rotation.style.transform=`rotateZ(-12deg) rotateY(${staticMode?-12:-12-p*120}deg)`;
            sphere.style.transform='none';sphere.style.opacity='1';sphere.style.visibility='visible';
            pin.style.opacity='0';halo.style.opacity='0';
            orbits.forEach(el=>el.style.opacity='0');
            carrier.style.transform='none';carrier.style.opacity='1';
            document.dispatchEvent(new CustomEvent('lup:world-progress',{detail:{progress:p,staticMode}}));
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
