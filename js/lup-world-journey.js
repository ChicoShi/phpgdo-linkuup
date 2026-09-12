/* Pre-rendered orthographic Earth + scroll-linked orbit. No render loop while idle. */
(() => {
    'use strict';
    const atlasURL=new URL('../www/img/linkuup-globe-atlas.webp',document.currentScript.src).href;
    const init=()=>{
        const chapter=document.querySelector('#lup-arrival-journey');
        const sphere=chapter?.querySelector('.lup-world-sphere');
        const source=chapter?.querySelector('.lup-world-pin');
        if(!sphere||!source)return;
        const stage=chapter.querySelector('.lup-journey-stage');
        const space=chapter.querySelector('.lup-journey-space');
        const scene=document.createElement('div');scene.className='lup-earth-scene';scene.setAttribute('aria-hidden','true');
        const makePin=cls=>{const el=document.createElement('span');el.className='lup-earth-orbiter '+cls;el.innerHTML=source.outerHTML.replace('class="lup-world-pin"','class="lup-earth-pin"').replaceAll('lup-pin-gradient','lup-orbit-'+cls);return el;};
        const back=makePin('orbit-back'),front=makePin('orbit-front');
        scene.append(back);
        const layers=[0,1].map(()=>{const crop=document.createElement('div');crop.className='lup-earth-crop';const img=new Image();img.src=atlasURL;img.alt='';img.draggable=false;img.className='lup-earth-atlas';crop.append(img);scene.append(crop);return {crop,img};});
        scene.append(front);sphere.replaceChildren(scene);sphere.classList.add('lup-earth-real');
        const reduced=matchMedia('(prefers-reduced-motion: reduce)');
        const clamp=x=>Math.max(0,Math.min(1,x));
        const ease=x=>{x=clamp(x);return x*x*(3-2*x);};
        let raf=0,shown=null,last=0;
        const draw=time=>{
            raf=0;if(document.hidden)return;
            const rect=chapter.getBoundingClientRect(),top=innerWidth<761?76:90;
            const target=clamp((top-rect.top)/Math.max(1,chapter.offsetHeight-stage.offsetHeight));
            const dt=last?Math.min(48,time-last):16;last=time;
            const still=reduced.matches||innerHeight<700;
            if(shown===null||still)shown=target;
            else shown+=(target-shown)*(1-Math.exp(-dt/90));
            if(Math.abs(shown-target)<.0001)shown=target;
            const p=still?.3:shown;
            const f=p*63,index=Math.floor(f),blend=f-index;
            [index,Math.min(63,index+1)].forEach((n,i)=>{layers[i].img.style.transform=`translate3d(${-(n%8)*12.5}%,${-Math.floor(n/8)*12.5}%,0)`;});
            layers[1].crop.style.opacity=String(blend);
            const bounds=sphere.getBoundingClientRect();
            const present=still?0:ease((innerHeight-bounds.top)/140)*ease((bounds.bottom-80)/100);
            const orbit=clamp((p-.10)/.80),a=Math.PI+orbit*Math.PI*4;
            const depth=Math.sin(a),rx=bounds.width*.57,ry=bounds.height*.20;
            let x=Math.cos(a)*rx,y=Math.sin(a)*ry;
            // Tilt the orbit; the back half is occluded by the real sphere image.
            const tilt=-.30,tx=x*Math.cos(tilt)-y*Math.sin(tilt),ty=x*Math.sin(tilt)+y*Math.cos(tilt);
            const entry=ease(p/.1),exit=ease((p-.9)/.1);
            x=tx-(1-entry)*35+exit*35;y=ty-(1-entry)*20+exit*12;
            const scale=.86+(depth+1)*.12;
            [back,front].forEach(el=>el.style.transform=`translate3d(${x}px,${y}px,0) rotate(${Math.cos(a)*10}deg) scale(${scale})`);
            const frontWeight=ease((depth+.08)/.16);
            front.style.opacity=String(present*frontWeight);back.style.opacity=String(present*(1-frontWeight));
            document.dispatchEvent(new CustomEvent('lup:earth-presence',{detail:{presence:present}}));
            const current=Math.min(2,Math.floor(p*3));
            chapter.querySelectorAll('.lup-arrival-flow li').forEach((li,i)=>li.classList.toggle('lup-step-current',still||i===current));
            if(!still&&Math.abs(shown-target)>.0001)raf=requestAnimationFrame(draw);
        };
        const schedule=()=>{if(!raf){last=0;raf=requestAnimationFrame(draw);}};
        addEventListener('scroll',schedule,{passive:true});addEventListener('resize',schedule,{passive:true});
        document.addEventListener('visibilitychange',schedule);reduced.addEventListener('change',schedule);
        new ResizeObserver(schedule).observe(space);schedule();
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
