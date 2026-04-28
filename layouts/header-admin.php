<?php // layouts/header-admin.php ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title ?? 'Growfinder Lab · Admin') ?></title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<header class="header-admin">
  <div class="header-left">
    <div class="wordmark">Growfinder <span>Lab</span></div>
  </div>
  <nav class="admin-nav">
    <a href="admin.php?page=mentorados" class="admin-nav-link <?= ($page ?? 'mentorados') === 'mentorados' ? 'active' : '' ?>">Mentorados</a>
    <a href="admin.php?page=conteudos" class="admin-nav-link <?= ($page ?? '') === 'conteudos' ? 'active' : '' ?>">Conteúdos</a>
  </nav>
  <div class="header-right">
    <span class="admin-badge">Admin</span>
    <a class="btn-sair" href="admin-logout.php">Sair</a>
  </div>
</header>
