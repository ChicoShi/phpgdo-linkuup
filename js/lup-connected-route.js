/* One decorative pin and one path connect all chapters; never changes selections. */
(() => {
    'use strict';
    const init=()=>{
        const main=document.querySelector('main.lup-arrival');
        const globe=main?.querySelector('.lup-world-pin');
        if(!globe)return;
        const pin=main.querySelector('.lup-scroll-traveller');
        pin.innerHTML=globe.outerHTML.replace('class="lup-world-pin"','class="lup-route-symbol"').replaceAll('lup-pin-gradient','lup-route-gradient');
        const legs=document.createElement('span');legs.className='lup-route-legs';
        legs.innerHTML='<svg viewBox="0 0 28 20"><path class="leg-left" d="M10 1v11l-6 4"/><path class="leg-right" d="M18 1v11l6 4"/></svg>';
        pin.append(legs);
        const prints=Array.from({length:36},(_,i)=>{const el=document.createElement('span');el.className='lup-route-footprint';el.setAttribute('aria-hidden','true');el.innerHTML='<svg viewBox="0 0 12 24"><ellipse cx="6" cy="7" rx="4" ry="6"/><ellipse cx="6" cy="19" rx="3" ry="4"/></svg>';main.append(el);return el;});
        const map=main.querySelector('.lup-scroll-map');
        const track=map.querySelector('.lup-scroll-track'),light=map.querySelector('.lup-scroll-light');
        const chapter=main.querySelector('#lup-arrival-journey');
        const stage=chapter.querySelector('.lup-journey-stage');
        const sphere=chapter.querySelector('.lup-world-sphere');
        const categorySection=main.querySelector('#lup-arrival-categories');
        let categoryStage=categorySection.querySelector('.lup-category-stage');
        // Compatibility with a cached server-rendered template during local updates.
        if(!categoryStage){categoryStage=document.createElement('div');categoryStage.className='lup-category-stage';while(categorySection.firstChild)categoryStage.append(categorySection.firstChild);categorySection.append(categoryStage);}
        const categoryIcons=[...main.querySelectorAll('.lup-destination-icon')];
        const stepIcons=[...chapter.querySelectorAll('.lup-arrival-flow li > i')];
        [...categoryIcons,...stepIcons].forEach(el=>el.classList.add('lup-route-dock'));
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
            if(staticMode){pin.style.opacity='0';prints.forEach(el=>el.style.opacity='0');[...targets,...categoryIcons,...stepIcons].forEach(el=>el.style.setProperty('--dock',0));return;}
            const m=main.getBoundingClientRect();
            const desired=clamp((innerHeight*.56-m.top)/main.offsetHeight)*main.offsetHeight;
            const dt=last?Math.min(64,time-last):16;last=time;
            if(!y)y=desired;
            y=mix(y,desired,1-Math.exp(-dt/180));
            const route=onPath(y);
            let x=route.x,py=route.y,scale=1,opacity=1;
            const cr=chapter.getBoundingClientRect();
            const pastWorld=world.progress>=.999 || cr.bottom<0;
            const wp=world.progress;
            const center=point(sphere);
            const radius=center.r.width*.48;
            // Two complete loops; the single shared pin is visible from the hero onward.
            const angle=-Math.PI/2+clamp((wp-.1)/.58)*Math.PI*4;
            const orbit={x:center.x+Math.cos(angle)*radius,y:center.y+Math.sin(angle)*radius*.72};
            const walking=cr.top<=(innerWidth<761?76:90) && wp>=.1 && wp<.68 && !pastWorld;
            const walkProgress=clamp((wp-.1)/.58);
            legs.style.opacity=walking?'1':'0';
            const gait=Math.sin(walkProgress*Math.PI*72);
            legs.querySelector('.leg-left').style.transform=`rotate(${gait*24}deg)`;
            legs.querySelector('.leg-right').style.transform=`rotate(${-gait*24}deg)`;
            prints.forEach((el,i)=>{
                const step=(i+1)/36,a=-Math.PI/2+step*Math.PI*4,age=walkProgress-step;
                const alpha=walking && age>=0?clamp(age*180)*clamp(1-age/.28):0;
                const px=center.x+Math.cos(a)*(radius+(i%2?3:-3)),fy=center.y+Math.sin(a)*radius*.72;
                el.style.transform=`translate3d(${px-4}px,${fy+12}px,0) rotate(${a*180/Math.PI+90}deg) scale(${.65+.25*(Math.sin(a)+1)})`;
                el.style.opacity=String(alpha*(Math.sin(a)<0?.23:.8));
            });
            if(cr.top> (innerWidth<761?76:90)) {
                const approach=ease(1-(cr.top-(innerWidth<761?76:90))/innerHeight);
                x=mix(rail,center.x,approach);py=mix(desired,center.y-radius*.72,approach);
            } else if(!pastWorld) {
                if(wp<.1){const t=ease(wp/.1);x=mix(rail,orbit.x,t);py=mix(center.y-radius,orbit.y,t)-Math.sin(t*Math.PI)*65;}
                else if(wp<.68){x=orbit.x;py=orbit.y-Math.abs(gait)*3;scale=.75+.2*(Math.sin(angle)+1);opacity=.45+.275*(Math.sin(angle)+1);}
                else {
                    const positions=[{x:center.x,y:center.y-radius*.72},...stepIcons.map(point),{x:rail,y:point(stepIcons[2]).y+60}];
                    const q=clamp((wp-.68)/.32)*(positions.length-1),i=Math.min(positions.length-2,Math.floor(q)),t=ease(q-i);
                    x=mix(positions[i].x,positions[i+1].x,t);py=mix(positions[i].y,positions[i+1].y,t)-Math.sin(t*Math.PI)*24;
                }
            }
            const catRect=categorySection.getBoundingClientRect();
            const catProgress=clamp(((innerWidth<761?76:90)-catRect.top)/Math.max(1,categorySection.offsetHeight-categoryStage.offsetHeight));
            if(pastWorld && catRect.top<innerHeight*.65 && catRect.bottom>innerHeight*.4){
                const positions=[{x:rail,y:point(categoryIcons[0]).y},...categoryIcons.map(point),point(targets[0]),{x:rail,y:point(targets[0]).y+80}];
                const q=catProgress*(positions.length-1),i=Math.min(positions.length-2,Math.floor(q)),raw=q-i;
                // Hold at each symbol before a short curved hop to the next.
                const t=ease(clamp((raw-.3)/.7));
                x=mix(positions[i].x,positions[i+1].x,t);py=mix(positions[i].y,positions[i+1].y,t)-Math.sin(t*Math.PI)*28;
            }
            let merge=0;
            [...dockPoints,...categoryIcons.map(el=>({...point(el),el})),...stepIcons.map(el=>({...point(el),el}))].forEach(p=>{
                const distance=Math.hypot(p.x-x,p.y-py);
                const strength=ease(1-distance/42);
                p.el.style.setProperty('--dock',strength.toFixed(3));
                merge=Math.max(merge,strength);
            });
            pin.style.transform=`translate3d(${x-12}px,${py-16}px,0) scale(${scale*(1-merge*.22)})`;
            pin.style.opacity=String(opacity*(1-merge*.65));
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
