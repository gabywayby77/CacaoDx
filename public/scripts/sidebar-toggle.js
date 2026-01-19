// Sidebar toggle behavior (same as dashboard)
(function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('overlay');
  
    if (!sidebar || !toggle || !overlay) return;
  
    function setAria(expanded) {
      toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }
  
    function isMobile() { return window.innerWidth <= 900; }
  
    toggle.addEventListener('click', () => {
      if (isMobile()) {
        const open = sidebar.classList.toggle('open');
        overlay.classList.toggle('show', open);
        setAria(open);
        document.body.classList.toggle('no-scroll', open);
      } else {
        const collapsed = sidebar.classList.toggle('collapsed');
        setAria(!collapsed);
      }
    });
  
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
      document.body.classList.remove('no-scroll');
      setAria(false);
    });
  })();
  