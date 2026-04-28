<?php
// guia-template.php
// Requer: $email, $page_title, $guia_titulo, $guia_modulos (array), $modulos_dir (string)
// Opcional: $header_links (array), $header_class, $footer_class

$header_class = 'site-header';
$footer_class = 'site-footer';
include 'layouts/header.php';
?>

<div class="shell">

  <aside class="sidebar">
    <div class="guide-title"><?= htmlspecialchars($guia_titulo) ?></div>

    <ul class="nav-list" id="navList">
<?php foreach ($guia_modulos as $i => $label): ?>
      <li class="nav-item">
        <button class="nav-btn<?= $i === 0 ? ' active' : '' ?>" onclick="goTo(<?= $i ?>)">
          <span class="nav-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span><?= htmlspecialchars($label) ?>
        </button>
      </li>
<?php endforeach; ?>
    </ul>

  </aside>

  <main class="guia-main">

<?php foreach ($guia_modulos as $i => $label): ?>
    <div class="module<?= $i === 0 ? ' active' : '' ?>" id="mod<?= $i ?>">
<?php include $modulos_dir . 'mod' . ($i + 1) . '.php'; ?>
    </div>

<?php endforeach; ?>

  </main>
</div>

<?php include 'layouts/footer.php'; ?>

<script>
  let current = 0;
  const total = <?= count($guia_modulos) ?>;

  function goTo(idx) {
    document.getElementById('mod' + current).classList.remove('active');
    document.querySelectorAll('.nav-btn')[current].classList.remove('active');

    current = idx;

    document.getElementById('mod' + current).classList.add('active');
    document.querySelectorAll('.nav-btn')[current].classList.add('active');

    document.querySelector('.guia-main').scrollTo({ top: 0, behavior: 'smooth' });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' && current < total - 1) goTo(current + 1);
    if (e.key === 'ArrowLeft' && current > 0) goTo(current - 1);
  });
</script>
