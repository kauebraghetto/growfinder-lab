<?php
// ─────────────────────────────────────────
// CREDENCIAIS DO ADMIN — altere após o primeiro acesso
define('ADMIN_USER', 'kaue');
define('ADMIN_PASS', '$2y$10$IfXw4.wnTm/zyOwv4yjhYeazp/YMr3sK0MBmx/WDVs0ITpGbzCpmO');
// ─────────────────────────────────────────

session_name('gf_admin_session');
session_start();

$erro = '';

if (!empty($_SESSION['admin'])) {
    header('Location: admin.php');
    exit;
}

// Rate limiting: máx 5 tentativas, bloqueio de 15 minutos
if (!isset($_SESSION['admin_attempts'])) $_SESSION['admin_attempts'] = 0;
if (!isset($_SESSION['admin_locked_until'])) $_SESSION['admin_locked_until'] = 0;

$bloqueado = time() < $_SESSION['admin_locked_until'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bloqueado) {
        $minutos = ceil(($_SESSION['admin_locked_until'] - time()) / 60);
        $erro = "Muitas tentativas. Tente novamente em {$minutos} minuto(s).";
    } else {
        $user = trim($_POST['usuario'] ?? '');
        $pass = $_POST['senha'] ?? '';

        if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS)) {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            $_SESSION['admin_time'] = time();
            $_SESSION['admin_attempts'] = 0;
            header('Location: admin.php');
            exit;
        } else {
            $_SESSION['admin_attempts']++;
            if ($_SESSION['admin_attempts'] >= 5) {
                $_SESSION['admin_locked_until'] = time() + 15 * 60;
                $_SESSION['admin_attempts'] = 0;
                $erro = 'Muitas tentativas. Tente novamente em 15 minutos.';
            } else {
                $tentativas_restantes = 5 - $_SESSION['admin_attempts'];
                $erro = "Credenciais incorretas. {$tentativas_restantes} tentativa(s) restante(s).";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Growfinder Lab · Admin</title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-login">

<div class="bg-pattern"></div>
<div class="login-card">
  <div class="login-brand">
    <div class="login-wordmark">Growfinder <span style="color:var(--accent)">Lab</span></div>
    <div class="login-slogan">Acesso restrito ao administrador.</div>
  </div>
  <div class="login-card-body">
    <div class="login-card-title">Painel <span class="admin-badge">Admin</span></div>
    <div class="login-card-sub">Gerencie os mentorados do Growfinder Lab.</div>
    <form method="POST">
      <div class="login-field-wrap">
        <label class="login-field-label" for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" placeholder="usuário admin" autocomplete="username" required>
      </div>
      <div class="login-field-wrap">
        <label class="login-field-label" for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••••••" autocomplete="current-password" required>
      </div>
      <button class="login-btn" type="submit">Entrar →</button>
      <?php if ($erro): ?>
        <div class="login-erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
    </form>
  </div>
  <div class="login-footer-note">growfinder.com.br · Admin</div>
</div>

</body>
</html>
