/* ── Chart ─────────────────────────────────────────── */
(function(){
  const enrollments = [42,67,58,89,74,95];
  const completions = [28,54,49,71,61,82];
  const months = ['Jan','Feb','Mar','Apr','May','Jun'];
  const maxVal = Math.max(...enrollments);
  const container = document.getElementById('chartBars');
  if(!container) return;

  months.forEach((m,i) => {
    const col = document.createElement('div');
    col.style.cssText = 'flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%';

    const barsWrap = document.createElement('div');
    barsWrap.style.cssText = 'display:flex;gap:3px;align-items:flex-end;height:100%;width:100%';

    const b1 = document.createElement('div');
    const h1 = Math.round((enrollments[i]/maxVal)*100);
    b1.style.cssText = `flex:1;height:${h1}%;background:linear-gradient(180deg,#a78bfa,#7c3aed88);border-radius:5px 5px 0 0;cursor:pointer;transition:opacity .15s,filter .15s;min-width:0`;
    b1.title = `${m} enrollments: ${enrollments[i]}`;
    b1.addEventListener('mouseenter',()=>{b1.style.opacity='.8';b1.style.filter='brightness(1.15)'});
    b1.addEventListener('mouseleave',()=>{b1.style.opacity='1';b1.style.filter='none'});

    const b2 = document.createElement('div');
    const h2 = Math.round((completions[i]/maxVal)*100);
    b2.style.cssText = `flex:1;height:${h2}%;background:linear-gradient(180deg,#2dd4bf,#0d948888);border-radius:5px 5px 0 0;cursor:pointer;transition:opacity .15s,filter .15s;min-width:0`;
    b2.title = `${m} completions: ${completions[i]}`;
    b2.addEventListener('mouseenter',()=>{b2.style.opacity='.8';b2.style.filter='brightness(1.15)'});
    b2.addEventListener('mouseleave',()=>{b2.style.opacity='1';b2.style.filter='none'});

    barsWrap.appendChild(b1);
    barsWrap.appendChild(b2);

    const lbl = document.createElement('div');
    lbl.textContent = m;
    lbl.style.cssText = 'font-size:10px;color:var(--text4);font-weight:600;margin-top:6px;font-family:var(--font)';

    col.appendChild(barsWrap);
    col.appendChild(lbl);
    container.appendChild(col);
  });
})();



/* ── Sidebar active on click ─────────────────────────── */
document.querySelectorAll('.sb-item').forEach(item=>{
  item.addEventListener('click',function(e){
    e.preventDefault();
    document.querySelectorAll('.sb-item').forEach(i=>i.classList.remove('active'));
    this.classList.add('active');
  });
});
(function () {
    /* Accordion groups */
    document.querySelectorAll('.sb-group').forEach(function (group) {
        var trigger = group.querySelector('.sb-group-trigger');
        if (!trigger) return;

        trigger.addEventListener('click', function () {
            var isOpen = group.classList.contains('open');

            /* close all sibling groups */
            group.closest('.sb-nav')
                 .querySelectorAll('.sb-group')
                 .forEach(function (g) {
                     if (g !== group) {
                         g.classList.remove('open');
                         var t = g.querySelector('.sb-group-trigger');
                         if (t) t.setAttribute('aria-expanded', 'false');
                     }
                 });

            group.classList.toggle('open', !isOpen);
            trigger.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    /* Mobile sidebar toggle */
    var sidebar  = document.getElementById('mainSidebar');
    var overlay  = document.getElementById('sbOverlay');
    var menuBtn  = document.getElementById('sbMenuBtn'); /* wire this in topbar if you add a hamburger */

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    /* Expose global toggle for topbar hamburger button */
    window.toggleSidebar = function () {
        var open = sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('show', open);
    };
})();