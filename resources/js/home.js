// Chỉ chạy khi body có class 'home-theme'
if (!document.body.classList.contains('home-theme')) {
    // Không phải trang Home thì thoát
  } else {
    // Reveal-on-scroll
    (function(){
      const items = document.querySelectorAll('[data-reveal]');
      if(!('IntersectionObserver' in window) || !items.length) {
        items.forEach(el=>el.classList.add('is-visible'));
        return;
      }
      const io = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{
          if(e.isIntersecting){
            e.target.classList.add('is-visible');
            io.unobserve(e.target);
          }
        });
      },{rootMargin:'0px 0px -10% 0px',threshold:0.1});
      items.forEach(el=>io.observe(el));
    })();
  
    // Button ripple
    (function(){
      document.addEventListener('click',function(e){
        const btn = e.target.closest('[data-ripple]');
        if(!btn) return;
        const circle = document.createElement('span');
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        circle.style.width = circle.style.height = size + 'px';
        circle.style.left = (e.clientX - rect.left - size/2) + 'px';
        circle.style.top = (e.clientY - rect.top - size/2) + 'px';
        circle.className = 'ripple';
        btn.appendChild(circle);
        setTimeout(()=>circle.remove(), 600);
      });
    })();
  
    // Inject ripple CSS
    (function(){
      const css = `.ripple{position:absolute;border-radius:50%;transform:scale(0);opacity:.4;background:#000;animation:ripple .6s ease-out;pointer-events:none}
                   @keyframes ripple{to{transform:scale(3);opacity:0}}`;
      const style = document.createElement('style');
      style.innerHTML = css; document.head.appendChild(style);
    })();
  }
  