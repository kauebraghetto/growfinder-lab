<?php // layouts/header.php — requer: $email (string) ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title ?? 'Growfinder Lab') ?></title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<?php if (!empty($extra_css)): ?>
<link rel="stylesheet" href="<?= $extra_css ?>">
<?php endif; ?>
</head>
<body>
<header<?php if (!empty($header_class)) echo ' class="' . htmlspecialchars($header_class) . '"'; ?>>
  <a class="wordmark" href="materiais.php">Growfinder <span>Lab</span></a>
  <div class="header-right">
    <span class="user-info"><?= htmlspecialchars($email) ?></span>
<?php if (!empty($header_links)): ?>
<?php foreach ($header_links as $link): ?>
    <a class="btn-header" href="<?= $link['href'] ?>"><?= $link['label'] ?></a>
<?php endforeach; ?>
<?php else: ?>
    <a class="btn-header" href="perfil.php">Meu perfil</a>
    <a class="btn-header" href="logout.php">Sair</a>
<?php endif; ?>
  </div>
</header>
