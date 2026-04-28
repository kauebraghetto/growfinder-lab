<?php
require_once 'config.php';

session_name(SESSION_NAME);
session_start();

$erro = '';

// Se já está logado, vai para materiais
if (!empty($_SESSION['usuario_id'])) {
    header('Location: materiais.php');
    exit;
}

// Rate limiting: máx 5 tentativas, bloqueio de 15 minutos
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
if (!isset($_SESSION['login_locked_until'])) $_SESSION['login_locked_until'] = 0;

$bloqueado = time() < $_SESSION['login_locked_until'];

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bloqueado) {
        $minutos = ceil(($_SESSION['login_locked_until'] - time()) / 60);
        $erro = "Muitas tentativas. Tente novamente em {$minutos} minuto(s).";
    } else {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email && $senha) {
            try {
                $pdo = db_connect();
                $stmt = $pdo->prepare('SELECT id, nome, senha, ativo FROM usuarios WHERE email = ?');
                $stmt->execute([$email]);
                $usuario = $stmt->fetch();

                if ($usuario && $usuario['ativo'] && password_verify($senha, $usuario['senha'])) {
                    // Login válido
                    session_regenerate_id(true);
                    $_SESSION['usuario_id']    = $usuario['id'];
                    $_SESSION['usuario_nome']  = $usuario['nome'];
                    $_SESSION['usuario_email'] = $email;
                    $_SESSION['login_time']    = time();
                    $_SESSION['login_attempts'] = 0;

                    // Registrar último acesso
                    $upd = $pdo->prepare('UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?');
                    $upd->execute([$usuario['id']]);

                    header('Location: materiais.php');
                    exit;
                } else {
                    $_SESSION['login_attempts']++;
                    if ($_SESSION['login_attempts'] >= 5) {
                        $_SESSION['login_locked_until'] = time() + 15 * 60;
                        $_SESSION['login_attempts'] = 0;
                        $erro = 'Muitas tentativas. Tente novamente em 15 minutos.';
                    } else {
                        $erro = 'E-mail ou senha incorretos.';
                    }
                }
            } catch (Exception $e) {
                $erro = 'Erro de conexão. Tente novamente.';
            }
        } else {
            $erro = 'Preencha todos os campos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Growfinder Lab · Acesso</title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/login.css">
<?php if ($erro): ?>
<style>input[type="email"], input[type="password"] { border-color: var(--accent); }</style>
<?php endif; ?>
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
    <div class="card-sub">Área exclusiva para mentorados. Acesse com seu e-mail e senha.</div>

    <form method="POST" action="">
      <div class="field-wrap">
        <label class="field-label" for="email">E-mail</label>
        <input type="email" id="email" name="email"
               placeholder="seu@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required autocomplete="email">
      </div>

      <div class="field-wrap">
        <label class="field-label" for="senha">Senha</label>
        <input type="password" id="senha" name="senha"
               placeholder="••••••••"
               required autocomplete="current-password">
      </div>

      <button class="btn-enter" type="submit">Entrar →</button>

      <?php if ($erro): ?>
        <div class="error-msg"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <div style="margin-top:12px;text-align:center">
        <a href="esqueci-senha.php" style="font-size:13px;color:#555">Esqueci minha senha</a>
      </div>
    </form>
  </div>

  <div class="footer-note">growfinder.com.br · Acesso restrito</div>
</div>

</body>
</html>
