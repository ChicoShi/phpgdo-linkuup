/* One reversible scroll timeline owns the pin, globe and decorative contacts. */
(() => {
    'use strict';
    const init=()=>{
        const main=document.querySelector('main.lup-arrival');
        const pin=main?.querySelector('.lup-scroll-traveller');
        const source=main?.querySelector('.lup-world-pin');
        const chapter=main?.querySelector('#lup-arrival-journey');
        if(!pin||!source||!chapter)return;
        const sphere=chapter.querySelector('.lup-world-sphere'),stage=chapter.querySelector('.lup-journey-stage');
        const map=main.querySelector('.lup-scroll-map');
        pin.innerHTML=source.outerHTML.replace('class="lup-world-pin"','class="lup-route-symbol"').replaceAll('lup-pin-gradient','lup-route-gradient');
        // One actual pin node, outside section stacking contexts and never interactive.
        document.body.append(pin);pin.classList.add('lup-unified-pin');
        const icons=[main.querySelector('.lup-story-orbit'),...main.querySelectorAll('.lup-arrival-principle-flow > div > i')].filter(Boolean);
        icons.forEach(el=>el.classList.add('lup-route-dock'));
        const reduced=matchMedia('(prefers-reduced-motion: reduce)');
        const clamp=x=>Math.max(0,Math.min(1,x)),ease=x=>{x=clamp(x);return x*x*(3-2*x);};
        const mix=(a,b,t)=>a+(b-a)*t;
        let raf=0,shown=scrollY,last=0,geometry;
        const layout=()=>{
            const r=main.getBoundingClientRect(),c=chapter.getBoundingClientRect();
            const rail=r.left+Math.max(18,(r.width-1160)/2+14);
            geometry={rail,start:c.top+scrollY-(innerWidth<761?76:90),length:Math.max(1,chapter.offsetHeight-stage.offsetHeight),mainTop:r.top+scrollY,mainBottom:r.bottom+scrollY};
            geometry.stops=icons.map(el=>{const b=el.getBoundingClientRect();return {el,x:b.left+b.width/2,y:b.top+scrollY+b.height/2,t:b.top+scrollY+b.height/2-innerHeight*.55};});
            // Gentle lane bends stay in the margin; no text is crossed between chapters.
            const x=rail-r.left,h=main.offsetHeight;
            let d=`M ${x} 0`;
            for(let y=0;y<h;y+=320)d+=` C ${x+10} ${y+100},${x-10} ${y+220},${x} ${y+320}`;
            map.setAttribute('viewBox',`0 0 ${r.width} ${h}`);
            map.querySelectorAll('path').forEach(path=>path.setAttribute('d',d));
            schedule();
        };
        const draw=time=>{
            raf=0;if(document.hidden||!geometry)return;
            const still=reduced.matches||innerHeight<700;
            const dt=last?Math.min(48,time-last):16;last=time;
            shown=still?scrollY:shown+(scrollY-shown)*(1-Math.exp(-dt/75));
            if(Math.abs(scrollY-shown)<.02)shown=scrollY;
            const g=geometry,p=clamp((shown-g.start)/g.length);
            document.dispatchEvent(new CustomEvent('lup:route-frame',{detail:{progress:still?.3:p,still}}));
            let x=g.rail+6*Math.sin(shown/210),y=Math.min(innerHeight*.55,100+shown*.45),scale=1,rotation=6*Math.cos(shown/210),opacity=still?0:1;
            icons.forEach(el=>el.style.setProperty('--dock','0'));
            if(shown>=g.start&&shown<=g.start+g.length){
                const b=sphere.getBoundingClientRect(),a=Math.PI+ease((p-.16)/.68)*Math.PI*4;
                const dx=Math.cos(a)*b.width*.57,dy=Math.sin(a)*b.height*.20;
                const ox=b.left+b.width/2+dx*Math.cos(-.30)-dy*Math.sin(-.30);
                const oy=b.top+b.height/2+dx*Math.sin(-.30)+dy*Math.cos(-.30);
                const join=ease(p/.16)*(1-ease((p-.84)/.16));
                x=mix(x,ox,join);y=mix(y,oy,join);scale=mix(1,.94+(Math.sin(a)+1)*.12,join);rotation=mix(rotation,Math.cos(a)*10,join);
                // Occlude the same node only where its orbit passes behind the globe.
                const inside=Math.hypot((ox-b.left-b.width/2)/(b.width/2),(oy-b.top-b.height/2)/(b.height/2));
                const hidden=ease((-Math.sin(a)-.04)/.18)*ease((1.07-inside)/.16)*join;
                opacity*=1-hidden;
            }else if(shown>g.start+g.length){
                const stops=g.stops;
                // Midpoints divide contacts so neighbouring icons never compete.
                stops.forEach((s,i)=>{
                    const before=i?(stops[i-1].t+s.t)/2:Math.max(g.start+g.length,s.t-200);
                    const after=i<stops.length-1?(s.t+stops[i+1].t)/2:s.t+200;
                    if(shown<before||shown>after)return;
                    const t=shown<=s.t?ease((shown-before)/Math.max(1,s.t-before)):1-ease((shown-s.t)/Math.max(1,after-s.t));
                    x=mix(x,s.x-18,t);y=mix(y,s.y-scrollY-12,t);
                    rotation=mix(rotation,18,t);
                    s.el.style.setProperty('--dock',String(ease((t-.85)/.15)));
                });
            }
            opacity*=ease((g.mainBottom-scrollY-100)/160);
            pin.style.transform=`translate3d(${x-11}px,${y-16}px,0) rotate(${rotation}deg) scale(${scale})`;
            pin.style.opacity=String(opacity);
            if(!still&&shown!==scrollY)raf=requestAnimationFrame(draw);
        };
        const schedule=()=>{if(!raf)raf=requestAnimationFrame(draw);};
        addEventListener('scroll',schedule,{passive:true});addEventListener('resize',layout,{passive:true});
        reduced.addEventListener('change',layout);document.addEventListener('visibilitychange',schedule);
        new ResizeObserver(layout).observe(main);document.fonts?.ready.then(layout);layout();
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
