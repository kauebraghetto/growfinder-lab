<?php
require_once 'config.php';
session_name(SESSION_NAME);
session_start();

if (!empty($_SESSION['usuario_id'])) {
    header('Location: materiais.php');
    exit;
}

$token       = trim($_GET['token'] ?? '');
$erro        = '';
$sucesso     = '';
$token_valido = false;
$usuario_id  = null;

if (!$token) {
    header('Location: index.php');
    exit;
}

try {
    $pdo  = db_connect();
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE reset_token = ? AND reset_expires > NOW() AND ativo = 1');
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $token_valido = true;
        $usuario_id   = $usuario['id'];
    }
} catch (Exception $e) {
    $erro = 'Erro de conexão. Tente novamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valido) {
    $senha_nova = $_POST['senha_nova'] ?? '';
    $senha_conf = $_POST['senha_conf'] ?? '';

    if (!$senha_nova || !$senha_conf) {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha_nova) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha_nova !== $senha_conf) {
        $erro = 'As senhas não coincidem.';
    } else {
        $hash = password_hash($senha_nova, PASSWORD_BCRYPT);
        $upd  = $pdo->prepare('UPDATE usuarios SET senha = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
        $upd->execute([$hash, $usuario_id]);
        $sucesso     = 'Senha redefinida com sucesso!';
        $token_valido = false;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Growfinder Lab · Redefinir senha</title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="bg-pattern"></div>

<div class="card">
  <div class="card-body">
    <div class="card-title">Growfinder <span style="color:var(--accent)">Lab</span></div>

    <?php if ($sucesso): ?>
      <div class="card-sub"><?= htmlspecialchars($sucesso) ?></div>
      <div style="margin-top:24px;text-align:center">
        <a href="index.php" class="btn-enter" style="display:inline-block;text-decoration:none">Ir para o login →</a>
      </div>

    <?php elseif (!$token_valido): ?>
      <div class="card-sub">Este link é inválido ou expirou.</div>
      <div style="margin-top:24px;text-align:center">
        <a href="esqueci-senha.php" class="btn-enter" style="display:inline-block;text-decoration:none">Solicitar novo link →</a>
      </div>

    <?php else: ?>
      <div class="card-sub">Escolha uma nova senha para sua conta.</div>
      <form method="POST" action="">
        <div class="field-wrap">
          <label class="field-label" for="senha_nova">Nova senha</label>
          <input type="password" id="senha_nova" name="senha_nova"
                 placeholder="mínimo 8 caracteres" required>
        </div>
        <div class="field-wrap">
          <label class="field-label" for="senha_conf">Confirmar nova senha</label>
          <input type="password" id="senha_conf" name="senha_conf"
                 placeholder="••••••••" required>
        </div>

        <button class="btn-enter" type="submit">Salvar nova senha →</button>

        <?php if ($erro): ?>
          <div class="error-msg"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
      </form>
    <?php endif; ?>
  </div>

  <div class="footer-note">growfinder.com.br · Acesso restrito</div>
</div>

</body>
</html>
