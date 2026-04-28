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
$page_title = 'Artigo — Growfinder Lab';
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
    $corpo = $parsedown->text($conteudo['corpo']);
} else {
    $corpo = $conteudo['corpo'];
}

$page_title = $conteudo['titulo'] . ' — Growfinder Lab';
include 'layouts/header.php';
?>

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