(() => {
  const root = document.documentElement;
  const themeButtons = document.querySelectorAll('.theme-toggle');
  const menuButton = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');

  const savedTheme = localStorage.getItem('gummibeer-theme');
  if (savedTheme === 'dark' || savedTheme === 'light') root.dataset.theme = savedTheme;

  themeButtons.forEach(button => button.addEventListener('click', () => {
    root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('gummibeer-theme', root.dataset.theme);
  }));

  if (menuButton && nav) {
    menuButton.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      menuButton.setAttribute('aria-expanded', String(open));
    });

    const currentPath = window.location.pathname;
    nav.querySelectorAll('a:not(.code-button)').forEach(link => {
      const path = new URL(link.href, window.location.origin).pathname;
      link.classList.remove('active');
      link.removeAttribute('aria-current');
      if ((currentPath === '/' && path === '/' && !link.hash) || (path !== '/' && (currentPath === path || currentPath.startsWith(`${path}/`)))) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  document.querySelectorAll('a[href*="#"]').forEach(link => {
    link.addEventListener('click', () => {
      if (nav) nav.classList.remove('open');
      if (menuButton) menuButton.setAttribute('aria-expanded', 'false');
    });
  });

  const copyButton = document.querySelector('.copy-link');
  if (copyButton) {
    copyButton.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(location.href);
        const old = copyButton.textContent;
        copyButton.textContent = '✓';
        setTimeout(() => copyButton.textContent = old, 1200);
      } catch (_) {}
    });
  }

  const tocLinks = [...document.querySelectorAll('.toc a')];
  const sections = tocLinks.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);
  if (sections.length) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        document.querySelectorAll('.toc li').forEach(li => li.classList.remove('active'));
        const active = document.querySelector(`.toc a[href="#${entry.target.id}"]`);
        active?.parentElement.classList.add('active');
      });
    }, { rootMargin: '-20% 0px -65% 0px' });
    sections.forEach(section => observer.observe(section));
  }
})();
