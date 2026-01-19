document.addEventListener('click', function (e) {
    const link = e.target.closest('a[data-spa]');
    if (!link) return;
  
    e.preventDefault();
  
    const url = link.getAttribute('href');
    const content = document.getElementById('spa-content');
  
    // Loading state
    content.classList.add('loading');
  
    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
  
      const newContent = doc.querySelector('#spa-content');
      if (!newContent) {
        window.location.href = url; // fallback
        return;
      }
  
      content.innerHTML = newContent.innerHTML;
  
      // Update URL
      history.pushState({}, '', url);
  
      // Update active sidebar
      document.querySelectorAll('.menu li').forEach(li => li.classList.remove('active'));
      link.closest('li')?.classList.add('active');
  
      content.classList.remove('loading');
    })
    .catch(() => {
      window.location.href = url;
    });
  });
  
  // Back / forward support
  window.addEventListener('popstate', () => {
    location.reload();
  });
  