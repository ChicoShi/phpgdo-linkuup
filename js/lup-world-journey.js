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
        const traveller = document.querySelector('.lup-scroll-traveller');
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
        const feet=[];
        for(let i=0;i<12;i++){const foot=document.createElement('span');foot.className='lup-world-foot';foot.innerHTML='<svg viewBox="0 0 14 28"><ellipse cx="7" cy="9" rx="5" ry="8"/><ellipse cx="7" cy="23" rx="3.5" ry="4"/></svg>';foot.style.transform=`rotateY(${-45+i*11}deg) rotateX(${-21+(i%2?3:-3)}deg) translateZ(calc(var(--globe-size) / 2 + 6px)) rotateZ(76deg)`;feet.push(foot);fragment.append(foot);}
        rotation.append(fragment);
        let frame=0;
        const draw=()=>{
            frame=0;
            if(document.hidden)return;
            const rect=chapter.getBoundingClientRect();
            const top=innerWidth<761?76:90;
            const p=clamp((top-rect.top)/Math.max(1,chapter.offsetHeight-stage.offsetHeight));
            const staticMode=reduced.matches || innerHeight<700;
            chapter.classList.toggle('lup-world-static',staticMode);
            const morph=staticMode?0:ease((p-.44)/.31);
            const travel=staticMode?0:ease((p-.8)/.19);
            rotation.style.transform=`rotateZ(-16deg) rotateY(${staticMode?-12:25-p*210}deg)`;
            sphere.style.transform=`scale(${1-morph*.76})`;
            sphere.style.opacity=String(1-ease((morph-.65)/.35));
            sphere.style.visibility=morph>.99?'hidden':'visible';
            pin.style.opacity=String(ease((morph-.1)/.7));
            pin.style.transform=`translate(-50%,-37%) scale(${.7+morph*.3})`;
            halo.style.opacity=String(.8*(1-travel));
            orbits.forEach((el,i)=>{el.style.opacity=String((1-morph)*.65);el.style.transform=`rotate(${-28+i*70+p*50}deg) scale(${1-morph*.35})`;});
            feet.forEach((foot,i)=>{const age=(p*.75+.05)-i*.032;foot.style.opacity=staticMode?(i<6?'.8':'0'):String(clamp(age/.035)*(1-ease((p-.43)/.18)));});
            // Handoff uses the actual page route position, so the pin does not jump.
            const source=space.getBoundingClientRect();
            const target=traveller.getBoundingClientRect();
            const cx=source.left+source.width/2, cy=source.top+source.height*.47;
            const dx=target.left+target.width/2-cx;
            const dy=target.top+target.height/2-cy;
            const safeY=stage.getBoundingClientRect().top-38-cy;
            let tx=0,ty=0;
            if(innerWidth<761){
                const right=innerWidth-16-cx;
                if(travel<.2){tx=right*ease(travel/.2);}
                else if(travel<.5){tx=right;ty=safeY*ease((travel-.2)/.3);}
                else if(travel<.8){tx=right+(dx-right)*ease((travel-.5)/.3);ty=safeY;}
                else{tx=dx;ty=safeY+(dy-safeY)*ease((travel-.8)/.2);}
            }else{
                if(travel<.28)ty=safeY*ease(travel/.28);
                else if(travel<.78){tx=dx*ease((travel-.28)/.5);ty=safeY;}
                else{tx=dx;ty=safeY+(dy-safeY)*ease((travel-.78)/.22);}
            }
            carrier.style.transform=`translate3d(${tx}px,${ty}px,0) scale(${1-Math.min(1,travel*4)*.83})`;
            carrier.style.opacity=String(1-ease((p-.975)/.025));
            if(staticMode)carrier.style.opacity='1';
            traveller.style.opacity=staticMode?'0':String(ease((p-.975)/.025));
            const current=Math.min(2,Math.floor(p*3));
            steps.forEach((li,i)=>li.classList.toggle('lup-step-current',staticMode||i===current));
        };
        const schedule=()=>{if(!frame)frame=requestAnimationFrame(draw);};
        window.addEventListener('scroll',schedule,{passive:true});
        window.addEventListener('resize',schedule,{passive:true});
        document.addEventListener('visibilitychange',schedule);
        reduced.addEventListener('change',schedule);
        new ResizeObserver(schedule).observe(space);
        schedule();
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
