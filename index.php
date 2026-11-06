<?php
declare(strict_types=1);

$isoDirectory = getenv('ISO_DIRECTORY') ?: '/srv/isos';
$files = [];

if (is_dir($isoDirectory) && is_readable($isoDirectory)) {
    $entries = scandir($isoDirectory, SCANDIR_SORT_NONE);
    if ($entries !== false) {
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || !preg_match('/\.iso$/i', $entry)) {
                continue;
            }
            // Only expose regular files directly inside the configured ISO directory.
            $path = $isoDirectory . DIRECTORY_SEPARATOR . $entry;
            if (!is_file($path) || is_link($path) || !is_readable($path)) {
                continue;
            }
            $files[] = [
                'name' => $entry,
                'size' => filesize($path) ?: 0,
                'modified' => filemtime($path) ?: 0,
            ];
        }
    }
}

usort($files, static fn(array $a, array $b): int => $b['modified'] <=> $a['modified'] ?: strcasecmp($a['name'], $b['name']));

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function formatBytes(int $bytes): string {
    if ($bytes <= 0) return 'Unknown size';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $power = (int) floor(log($bytes, 1024));
    $power = min($power, count($units) - 1);
    return number_format($bytes / (1024 ** $power), $power === 0 ? 0 : 2) . ' ' . $units[$power];
}
?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <meta name="theme-color" content="#0b1220">
  <meta name="description" content="Download Futura Linux installation images and release files.">
  <title>Download Futura Linux — ISO releases</title>
  <link rel="icon" href="/assets/futura-linux-logo.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/styles.css">
  <script src="/assets/theme.js" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="https://futuralinux.org/" aria-label="Futura Linux home">
      <img src="/assets/futura-linux-logo.png" width="52" height="52" alt="">
      <span class="brand-name">Futura <span>Linux</span></span>
    </a>
    <nav class="nav-links" aria-label="Main navigation">
      <a href="https://futuralinux.org/">Home</a>
      <a href="#downloads" aria-current="page">Downloads</a>
      <a href="#about">About</a>
    </nav>
    <div class="nav-actions">
      <button class="theme-toggle" id="theme-toggle" type="button" aria-label="Switch to light mode" title="Switch theme">
        <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 14.1A8.7 8.7 0 0 1 9.9 3.2 8.8 8.8 0 1 0 20.8 14.1Z"/></svg>
      </button>
      <a class="button button-small button-primary" href="https://futuralinux.org/">Visit Futura <span aria-hidden="true">↗</span></a>
    </div>
  </div>
</header>
<main id="main">
  <section class="hero download-hero">
    <div class="container hero-copy">
      <div class="eyebrow"><span class="status-dot"></span> Official Futura Linux downloads</div>
      <h1>Find your <span class="gradient-text">next starting point.</span></h1>
      <p class="hero-lede">Download Futura Linux installation images. This page updates automatically whenever a new ISO is added to the release directory.</p>
      <div class="hero-footnote"><span class="tiny-mark">✳</span> Arch Linux foundations. A desktop made approachable.</div>
    </div>
  </section>

  <section class="section downloads-section" id="downloads">
    <div class="container">
      <div class="section-heading downloads-heading">
        <div>
          <p class="section-kicker">Release repository</p>
          <h2>Available <span class="gradient-text">downloads.</span></h2>
          <p>Choose an ISO to start your Futura Linux installation. New files appear here automatically.</p>
        </div>
        <div class="release-summary"><span class="summary-mark">↧</span><strong><?= count($files) ?></strong><small><?= count($files) === 1 ? 'ISO available' : 'ISOs available' ?></small></div>
      </div>

      <div class="download-toolbar">
        <label class="search-box" for="iso-search"><span aria-hidden="true">⌕</span><input id="iso-search" type="search" placeholder="Search ISO files…" autocomplete="off"></label>
        <span class="sort-note">Newest releases first</span>
      </div>

      <?php if ($files): ?>
      <div class="iso-list" id="iso-list">
        <?php foreach ($files as $file): ?>
          <?php $downloadUrl = '/files/' . rawurlencode($file['name']); ?>
          <article class="iso-card" data-filename="<?= h(strtolower($file['name'])) ?>">
            <div class="iso-icon" aria-hidden="true"><span>▤</span><small>ISO</small></div>
            <div class="iso-info">
              <h3><?= h($file['name']) ?></h3>
              <div class="iso-meta"><span><?= h(formatBytes((int)$file['size'])) ?></span><i></i><span>Updated <?= h(date('M j, Y', (int)$file['modified'])) ?></span></div>
            </div>
            <a class="button button-primary download-button" href="<?= h($downloadUrl) ?>" download>
              <span>Download</span><span aria-hidden="true">↓</span>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="no-results" id="no-results" hidden>No ISO files match your search.</p>
      <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">✳</div>
        <h3>No ISO releases yet</h3>
        <p>There are no ISO files in the release directory right now. Check back soon — new releases will appear here automatically.</p>
      </div>
      <?php endif; ?>

      <div class="download-note"><span class="note-icon">ⓘ</span><p><strong>Before installing</strong><br>For your security, verify downloaded images against checksums or signatures published with the release.</p></div>
    </div>
  </section>
  <section class="section about-section" id="about">
    <div class="container about-inner">
      <div class="about-copy"><p class="section-kicker">A thoughtful starting point</p><h2>Built on Arch.<br><span class="gradient-text">Ready for you.</span></h2><p>Futura Linux brings together the flexibility of Arch Linux, the KDE Plasma desktop and a more approachable installation experience.</p></div>
      <div class="about-stats"><div><span class="stat-mark">↗</span><strong>Rolling</strong><small>Continuously evolving</small></div><div><span class="stat-mark green-stat">✳</span><strong>Flexible</strong><small>Make it your own</small></div><div><span class="stat-mark orange-stat">◇</span><strong>Open</strong><small>Built in the open</small></div></div>
    </div>
  </section>
</main>
<footer class="site-footer"><div class="container footer-inner"><a class="brand footer-brand" href="https://futuralinux.org/"><img src="/assets/futura-linux-logo.png" width="48" height="48" alt=""><span class="brand-name">Futura <span>Linux</span></span></a><span>Independent project · Based on Arch Linux</span><a class="back-top" href="#main">Back to top ↑</a></div></footer>
<script>
(() => {
  const input = document.getElementById('iso-search');
  const list = document.getElementById('iso-list');
  const empty = document.getElementById('no-results');
  if (!input || !list) return;
  input.addEventListener('input', () => {
    const query = input.value.trim().toLocaleLowerCase();
    let visible = 0;
    list.querySelectorAll('.iso-card').forEach(card => {
      const match = card.dataset.filename.includes(query);
      card.hidden = !match;
      if (match) visible++;
    });
    if (empty) empty.hidden = visible !== 0;
  });
})();
</script>
</body>
</html>
