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

$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['usuario_email'];
$page_title = 'Growfinder Lab · Materiais';

$pdo = db_connect();
$conteudos = $pdo->query("SELECT * FROM conteudos WHERE status = 'publicado' ORDER BY ordem ASC, criado_em DESC")->fetchAll();

$categorias = [];
foreach ($conteudos as $c) {
    $cat = $c['categoria'] ?: 'Sem categoria';
    if (!isset($categorias[$cat])) $categorias[$cat] = [];
    $categorias[$cat][] = $c;
}

include 'layouts/header.php';
?>

<div class="page-header">
  <div class="page-greeting">Bem-vindo, <?= htmlspecialchars($nome) ?></div>
  <div class="page-title">Materiais de <em>Mentoria</em></div>
</div>

<div class="mat-main">

<?php foreach ($categorias as $cat => $items): ?>
  <div class="mat-section-header">
    <span class="mat-section-title"><?= htmlspecialchars($cat) ?></span>
    <span class="mat-section-count"><?= count($items) === 1 ? '1 material' : count($items) . ' materiais' ?></span>
  </div>

  <div class="grid">
  <?php foreach ($items as $c): ?>
    <?php if ($c['tipo'] === 'artigo'): ?>
    <a class="mat-card" href="artigo.php?id=<?= $c['id'] ?>">
      <div class="mat-card-top">
        <div class="mat-card-top-meta">
          <div class="mat-tag"><?= htmlspecialchars($c['categoria'] ?: 'Artigo') ?></div>
          <span class="mat-type">Artigo</span>
        </div>
        <div class="mat-title"><?= htmlspecialchars($c['titulo']) ?></div>
      </div>
      <div class="mat-card-body">
        <?php if ($c['descricao']): ?>
        <div class="mat-desc"><?= htmlspecialchars($c['descricao']) ?></div>
        <?php endif; ?>
        <div class="mat-meta">
          <div class="mat-chips">
          <?php if ($c['tags']): ?>
            <?php foreach (explode(',', $c['tags']) as $tag): ?>
              <?php $tag = trim($tag); if ($tag): ?>
            <span class="mat-chip"><?= htmlspecialchars($tag) ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          </div>
          <span class="mat-arrow">→</span>
        </div>
      </div>
    </a>
    <?php else: ?>
    <?php $ext = strtolower(pathinfo($c['arquivo'], PATHINFO_EXTENSION)); ?>
    <a class="mat-card" href="uploads/<?= htmlspecialchars($c['arquivo']) ?>" <?= $ext !== 'pdf' ? 'download' : 'target="_blank"' ?>>
      <div class="mat-card-top">
        <div class="mat-card-top-meta">
          <div class="mat-tag mat-tag-download"><?= strtoupper(pathinfo($c['arquivo'], PATHINFO_EXTENSION)) ?></div>
          <span class="mat-type mat-type-download">Download</span>
        </div>
        <div class="mat-title"><?= htmlspecialchars($c['titulo']) ?></div>
      </div>
      <div class="mat-card-body">
        <?php if ($c['descricao']): ?>
        <div class="mat-desc"><?= htmlspecialchars($c['descricao']) ?></div>
        <?php endif; ?>
        <div class="mat-meta">
          <div class="mat-chips">
          <?php if ($c['tags']): ?>
            <?php foreach (explode(',', $c['tags']) as $tag): ?>
              <?php $tag = trim($tag); if ($tag): ?>
            <span class="mat-chip"><?= htmlspecialchars($tag) ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          </div>
          <span class="mat-arrow" style="font-size:14px">↓</span>
        </div>
      </div>
    </a>
    <?php endif; ?>
  <?php endforeach; ?>
  </div>

<?php endforeach; ?>

<?php if (empty($categorias)): ?>
  <div class="mat-section-header">
    <span class="mat-section-title">Nenhum material publicado ainda.</span>
  </div>
<?php endif; ?>

</div>

<?php include 'layouts/footer.php'; ?>
