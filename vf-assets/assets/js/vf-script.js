/* VF - scripts mínimos para controlar video y animaciones de onda */
(function(){
  'use strict';
  function ready(fn){document.readyState!='loading'?fn():document.addEventListener('DOMContentLoaded',fn)}
  ready(function(){
    var vid = document.querySelector('.vf-video video');
    if(vid){
      // autoplay muted on supported browsers
      vid.muted = true;
      var playPromise = vid.play();
      if(playPromise!==undefined){ playPromise.catch(function(){ /* autoplay blocked */ }); }
    }
    // Lazy add .playing when video plays
    if(vid){ vid.addEventListener('play',function(){ document.querySelector('.hero-video')?.classList.add('playing'); }); vid.addEventListener('pause',function(){ document.querySelector('.hero-video')?.classList.remove('playing'); }); }
    // Simple click-to-toggle play on poster overlay
    document.addEventListener('click', function(e){
      if(e.target.closest('.vf-video-toggle')){
        if(!vid) return; if(vid.paused) vid.play(); else vid.pause();
      }
    });
  });
})();
