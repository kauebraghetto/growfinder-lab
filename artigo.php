<?php
require_once 'config.php';

session_name(SESSION_NAME);
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

if (time() - ($_SESSION['login_time'] ?? 0) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: index.php?timeout=1');
    exit;
}

$email = $_SESSION['usuario_email'] ?? '';
$extra_css = 'assets/css/artigo.css';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: materiais.php');
    exit;
}

$pdo = db_connect();
$stmt = $pdo->prepare('SELECT * FROM conteudos WHERE id = ? AND status = ?');
$stmt->execute([$id, 'publicado']);
$conteudo = $stmt->fetch();

if (!$conteudo) {
    header('Location: materiais.php');
    exit;
}

if ($conteudo['formato'] === 'markdown') {
    require_once 'assets/Parsedown.php';
    $parsedown = new Parsedown();
    $parsedown->setSafeMode(true);
    $corpo = $parsedown->text($conteudo['corpo']);
} else {
    // Permite tags de formatação, remove script/style/iframe e similares
    $corpo = strip_tags($conteudo['corpo'],
        '<p><br><strong><b><em><i><a><ul><ol><li>' .
        '<h1><h2><h3><h4><h5><h6><img><table><thead><tbody>' .
        '<tr><td><th><code><pre><blockquote><hr><div><span>'
    );
}

$page_title = $conteudo['titulo'] . ' — Growfinder Lab';
include 'layouts/header.php';
?>

<div class="progress-bar" id="progress-bar"></div>
<nav class="artigo-toc" id="artigo-toc"></nav>
<button class="btn-topo" id="btn-topo" title="Voltar ao topo">↑</button>

<div class="artigo-page">
  <a class="artigo-voltar" href="materiais.php">← Materiais</a>

  <div class="artigo-header">
    <?php if ($conteudo['categoria']): ?>
      <span class="artigo-cat"><?= htmlspecialchars($conteudo['categoria']) ?></span>
    <?php endif; ?>
    <h1 class="artigo-titulo"><?= htmlspecialchars($conteudo['titulo']) ?></h1>
    <?php if ($conteudo['descricao']): ?>
      <p class="artigo-desc"><?= htmlspecialchars($conteudo['descricao']) ?></p>
    <?php endif; ?>
  </div>

  <div class="artigo-corpo">
    <?= $corpo ?>
  </div>
</div>

<?php include 'layouts/footer.php'; ?>

<script>
const bar      = document.getElementById('progress-bar');
const btnTopo  = document.getElementById('btn-topo');
const toc      = document.getElementById('artigo-toc');
const headings = document.querySelectorAll('.artigo-corpo h1');
const footer   = document.querySelector('footer');

// Scroll: progresso + botão topo + destaque do sumário
window.addEventListener('scroll', () => {
  const doc = document.documentElement;
  const pct = doc.scrollTop / (doc.scrollHeight - doc.clientHeight) * 100;
  bar.style.width = pct + '%';
  btnTopo.classList.toggle('visivel', window.scrollY > 300);
  const footerTop = footer.getBoundingClientRect().top;
  btnTopo.style.bottom = footerTop < window.innerHeight
    ? (window.innerHeight - footerTop + 16) + 'px'
    : '32px';

  let ativa = null;
  headings.forEach(h => {
    const parent = h.parentElement;
    const ref = parent.classList.contains('artigo-corpo') ? h : parent;
    if (ref.getBoundingClientRect().top <= 100) ativa = h;
  });
  if (!ativa && headings.length > 0) ativa = headings[0];
  toc.querySelectorAll('.toc-link').forEach(l => l.classList.remove('ativa'));
  if (ativa) {
    const link = toc.querySelector('a[href="#' + ativa.id + '"]');
    if (link) link.classList.add('ativa');
  }
});

// Botão topo
btnTopo.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// Sumário

if (headings.length > 1) {
  const label = document.createElement('div');
  label.className = 'toc-titulo';
  label.textContent = 'Neste artigo';
  toc.appendChild(label);

  const lista = document.createElement('ul');
  lista.className = 'toc-list';

  headings.forEach((h, i) => {
    if (!h.id) h.id = 'secao-' + i;
    const li = document.createElement('li');
    li.className = 'toc-item';
    const a = document.createElement('a');
    a.className = 'toc-link';
    a.href = '#' + h.id;
    a.textContent = h.textContent;
    a.addEventListener('click', e => {
      e.preventDefault();
      const parent = h.parentElement;
      const target = parent.classList.contains('artigo-corpo') ? h : parent;
      const top    = target.getBoundingClientRect().top + window.scrollY - 80;
      window.scrollTo({ top, behavior: 'smooth' });
      history.pushState(null, '', '#' + h.id);
    });
    li.appendChild(a);
    lista.appendChild(li);
  });

  toc.appendChild(lista);
  window.dispatchEvent(new Event('scroll'));
}
</script>