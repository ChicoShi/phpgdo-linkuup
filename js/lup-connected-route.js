/* One decorative pin and one path connect all chapters; never changes selections. */
(() => {
    'use strict';
    const init=()=>{
        const main=document.querySelector('main.lup-arrival');
        const globe=main?.querySelector('.lup-world-pin');
        if(!globe)return;
        const pin=main.querySelector('.lup-scroll-traveller');
        pin.innerHTML=globe.outerHTML.replace('class="lup-world-pin"','class="lup-route-symbol"').replaceAll('lup-pin-gradient','lup-route-gradient');
        const map=main.querySelector('.lup-scroll-map');
        const track=map.querySelector('.lup-scroll-track'),light=map.querySelector('.lup-scroll-light');
        const chapter=main.querySelector('#lup-arrival-journey');
        const stage=chapter.querySelector('.lup-journey-stage');
        const reduced=matchMedia('(prefers-reduced-motion: reduce)');
        const targets=[main.querySelector('.lup-story-orbit'),...main.querySelectorAll('.lup-arrival-principle-flow > div > i'),main.querySelector('.lup-arrival-invitation > a')];
        targets.forEach(el=>el.classList.add('lup-route-dock'));
        const clamp=x=>Math.max(0,Math.min(1,x));
        const ease=x=>{x=clamp(x);return x*x*(3-2*x);};
        const mix=(a,b,p)=>a+(b-a)*p;
        let world={progress:0,staticMode:false},nodes=[],dockPoints=[],rail=12,frame=0,dirty=true,y=0,last=0;
        const point=(el)=>{const r=el.getBoundingClientRect(),m=main.getBoundingClientRect();return {x:r.left+r.width/2-m.left,y:r.top+r.height/2-m.top,r};};
        const build=()=>{
            rail=Math.max(12,(main.clientWidth-1160)/2+14);
            const m=main.getBoundingClientRect();
            nodes=[{x:rail,y:70}];dockPoints=[];
            const add=(x,y)=>nodes.push({x,y});
            const story=point(targets[0]);
            add(rail,story.y-105);add(story.x,story.y-48);add(story.x,story.y);add(story.x,story.y+45);add(rail,story.y+100);
            dockPoints.push({...story,el:targets[0]});
            const first=point(targets[1]),lastIcon=point(targets[3]);
            const section=main.querySelector('#lup-arrival-principles').getBoundingClientRect();
            const before=innerWidth<761?first.y-45:section.top-m.top+16;
            add(rail,before-35);add(first.x,before);
            targets.slice(1,4).forEach(el=>{const p=point(el);add(p.x,p.y);dockPoints.push({...p,el});});
            add(lastIcon.x,lastIcon.y+60);add(rail,lastIcon.y+100);
            const final=point(targets[4]);final.x=final.r.left-m.left-22;
            add(rail,final.y-48);add(final.x,final.y);add(rail,final.y+95);
            dockPoints.push({...final,el:targets[4]});
            // The path has monotonically increasing Y. Cubics stay in the margins
            // above/below content and approach each icon vertically.
            let d=`M${nodes[0].x} ${nodes[0].y}`;
            for(let i=1;i<nodes.length;i++){const a=nodes[i-1],b=nodes[i],mid=(a.y+b.y)/2;d+=` C${a.x} ${mid},${b.x} ${mid},${b.x} ${b.y}`;}
            map.setAttribute('viewBox',`0 0 ${main.clientWidth} ${main.offsetHeight}`);
            track.setAttribute('d',d);light.setAttribute('d',d);
            dirty=false;
        };
        const onPath=(readY)=>{
            // Binary search the SVG by Y so scroll and visual position agree.
            let lo=0,hi=track.getTotalLength(),p;
            for(let i=0;i<16;i++){const mid=(lo+hi)/2;p=track.getPointAtLength(mid);if(p.y<readY)lo=mid;else hi=mid;}
            return track.getPointAtLength((lo+hi)/2);
        };
        const draw=time=>{
            frame=0;if(document.hidden)return;
            if(dirty)build();
            const staticMode=reduced.matches||innerHeight<700;
            main.classList.toggle('lup-connected-static',staticMode);
            if(staticMode){pin.style.opacity='0';targets.forEach(el=>el.style.setProperty('--dock',0));return;}
            const m=main.getBoundingClientRect();
            const desired=clamp((innerHeight*.56-m.top)/main.offsetHeight)*main.offsetHeight;
            const dt=last?Math.min(64,time-last):16;last=time;
            if(!y)y=desired;
            y=mix(y,desired,1-Math.exp(-dt/180));
            const route=onPath(y);
            let x=route.x,py=route.y,scale=1,opacity=1;
            const pastWorld=world.progress>=.999 || chapter.getBoundingClientRect().bottom<0;
            const handoff=ease((world.progress-.9)/.1);
            if(!pastWorld){
                const source=point(globe);
                const endY=stage.getBoundingClientRect().bottom-m.top+28;
                // Exit below the chapter, never diagonally through its copy.
                const viaY=Math.max(source.y,endY);
                if(handoff<.5){x=source.x;py=mix(source.y,viaY,ease(handoff*2));}
                else{x=mix(source.x,rail,ease((handoff-.5)*2));py=viaY;}
                scale=mix(source.r.width/24,1,ease(handoff));
                opacity=ease((world.progress-.88)/.02);
            }else{
                const tail=chapter.getBoundingClientRect().bottom-m.top+28;
                const settle=ease((desired-tail+innerHeight*.3)/(innerHeight*.3));
                x=mix(rail,x,settle);py=mix(tail,py,settle);
            }
            let merge=0;
            dockPoints.forEach(p=>{
                const distance=Math.hypot(p.x-x,p.y-py);
                const strength=pastWorld?ease(1-distance/65):0;
                p.el.style.setProperty('--dock',strength.toFixed(3));
                merge=Math.max(merge,strength);
            });
            pin.style.transform=`translate3d(${x-12}px,${py-16}px,0) scale(${scale*(1-merge*.22)})`;
            pin.style.opacity=String(opacity*(1-merge));
            if(Math.abs(y-desired)>.15)frame=requestAnimationFrame(draw);
        };
        const schedule=()=>{if(!frame){last=0;frame=requestAnimationFrame(draw);}};
        document.addEventListener('lup:world-progress',e=>{world=e.detail;schedule();});
        window.addEventListener('scroll',schedule,{passive:true});
        window.addEventListener('resize',()=>{dirty=true;schedule();},{passive:true});
        reduced.addEventListener('change',schedule);
        document.addEventListener('visibilitychange',schedule);
        new ResizeObserver(()=>{dirty=true;schedule();}).observe(main);
        main.querySelectorAll('.lup-destination').forEach(el=>el.addEventListener('click',()=>{dirty=true;schedule();}));
        schedule();
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
