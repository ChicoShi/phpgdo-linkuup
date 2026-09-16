/* Globe atlas renderer; the shared route controller supplies scroll progress. */
(() => {
    'use strict';
    const init=()=>{
        const chapter=document.querySelector('#lup-arrival-journey');
        const sphere=chapter?.querySelector('.lup-world-sphere');
        const source=chapter?.querySelector('.lup-world-pin');
        if(!sphere||!source)return;
        // PHP supplies the module URL. currentScript points into /assets in
        // production and cannot be used to resolve module images.
        const atlasURL=sphere.dataset.atlasUrl,zoomURL=sphere.dataset.zoomUrl;
        if(!atlasURL)return;
        const scene=document.createElement('div');scene.className='lup-earth-scene';scene.setAttribute('aria-hidden','true');
        const layers=[0,1].map(()=>{const crop=document.createElement('div');crop.className='lup-earth-crop';const img=new Image();img.src=atlasURL;img.alt='';img.draggable=false;img.className='lup-earth-atlas';crop.append(img);scene.append(crop);return {crop,img};});
        const zoomImage=new Image();zoomImage.className='lup-earth-zoom';zoomImage.alt='';zoomImage.draggable=false;zoomImage.decoding='async';zoomImage.style.opacity='0';
        let zoomReady=false;zoomImage.src=zoomURL;
        zoomImage.decode().then(()=>{zoomReady=true;scene.dataset.zoomReady='true';dispatchEvent(new Event('scroll'));}).catch(()=>{});
        scene.append(zoomImage);
        sphere.replaceChildren(scene);sphere.classList.add('lup-earth-real');
        document.addEventListener('lup:route-frame',event=>{
            const {progress:p,still}=event.detail;
            scene.style.transform='none';
            const zoomBlend=zoomReady?Math.max(0,Math.min(1,(p-.985)/.015)):0;
            zoomImage.style.opacity=String(zoomBlend*zoomBlend*(3-2*zoomBlend));
            const f=p*63,index=Math.floor(f),blend=f-index;
            [index,Math.min(63,index+1)].forEach((n,i)=>{layers[i].img.style.transform=`translate3d(${-(n%8)*12.5}%,${-Math.floor(n/8)*12.5}%,0)`;});
            layers[1].crop.style.opacity=String(blend);
            const current=Math.min(2,Math.floor(p*3));
            chapter.querySelectorAll('.lup-arrival-flow li').forEach((li,i)=>li.classList.toggle('lup-step-current',still||i===current));
        });
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
