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
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="bg-pattern"></div>

<svg class="sun-deco" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="200" cy="200" r="120" fill="white"/>
  <line x1="200" y1="20" x2="200" y2="60" stroke="white" stroke-width="8"/>
  <line x1="200" y1="340" x2="200" y2="380" stroke="white" stroke-width="8"/>
  <line x1="20" y1="200" x2="60" y2="200" stroke="white" stroke-width="8"/>
  <line x1="340" y1="200" x2="380" y2="200" stroke="white" stroke-width="8"/>
  <line x1="62" y1="62" x2="90" y2="90" stroke="white" stroke-width="8"/>
  <line x1="310" y1="310" x2="338" y2="338" stroke="white" stroke-width="8"/>
  <line x1="338" y1="62" x2="310" y2="90" stroke="white" stroke-width="8"/>
  <line x1="90" y1="310" x2="62" y2="338" stroke="white" stroke-width="8"/>
  <circle cx="200" cy="200" r="60" fill="black"/>
</svg>

<div class="card">
  <div class="card-body">
    <div class="card-title">Growfinder <span style="color:var(--accent)">Lab</span></div>
    <div class="card-sub"><span style="display:inline-block;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;background:var(--accent);color:var(--ink);padding:3px 8px;border-radius:3px;vertical-align:middle;margin-right:8px">Admin</span>Acesso restrito ao administrador.</div>
    <form method="POST">
      <div class="field-wrap">
        <label class="field-label" for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" placeholder="usuário admin" autocomplete="username" required>
      </div>
      <div class="field-wrap">
        <label class="field-label" for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••••••" autocomplete="current-password" required>
      </div>
      <button class="btn-enter" type="submit">Entrar →</button>
      <?php if ($erro): ?>
        <div class="error-msg"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
    </form>
  </div>
  <div class="footer-note">growfinder.com.br · Admin</div>
</div>

</body>
</html>
