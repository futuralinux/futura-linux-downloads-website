(() => {
  const root = document.documentElement;
  const toggle = document.getElementById('theme-toggle');
  const themeColor = document.querySelector('meta[name="theme-color"]');
  const storageKey = 'futura-theme';
  const cookieName = 'futura-theme';

  function readCookie() {
    const match = document.cookie.match(/(?:^|; )futura-theme=(dark|light)(?:;|$)/);
    return match ? match[1] : null;
  }

  function saveTheme(theme) {
    try { localStorage.setItem(storageKey, theme); } catch (_error) { /* Cookie remains available. */ }
    document.cookie = `${cookieName}=${theme}; Domain=.futuralinux.org; Path=/; Max-Age=31536000; SameSite=Lax; Secure`;
  }

  function applyTheme(theme, save = false) {
    const nextTheme = theme === 'light' ? 'light' : 'dark';
    root.dataset.theme = nextTheme;
    if (toggle) {
      const nextLabel = nextTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
      toggle.setAttribute('aria-label', nextLabel);
      toggle.setAttribute('title', nextLabel);
      toggle.setAttribute('aria-pressed', String(nextTheme === 'light'));
    }
    if (themeColor) themeColor.setAttribute('content', nextTheme === 'dark' ? '#0b1220' : '#f6f8fc');
    if (save) {
      saveTheme(nextTheme);
    }
  }

  let initialTheme = readCookie();
  if (!initialTheme) {
    try {
      const saved = localStorage.getItem(storageKey);
      if (saved === 'light' || saved === 'dark') initialTheme = saved;
    } catch (_error) { /* Fall back to system preference. */ }
  }
  if (!initialTheme) {
    initialTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
  }
  applyTheme(initialTheme);
  if (toggle) toggle.addEventListener('click', () => applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark', true));
})();
