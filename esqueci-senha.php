<?php
require_once 'config.php';
session_name(SESSION_NAME);
session_start();

if (!empty($_SESSION['usuario_id'])) {
    header('Location: materiais.php');
    exit;
}

$msg  = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $erro = 'Informe seu e-mail.';
    } else {
        try {
            $pdo  = db_connect();
            $stmt = $pdo->prepare('SELECT id, nome FROM usuarios WHERE email = ? AND ativo = 1');
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);

                $upd = $pdo->prepare('UPDATE usuarios SET reset_token = ?, reset_expires = ? WHERE id = ?');
                $upd->execute([$token, $expires, $usuario['id']]);

                enviar_reset($usuario['nome'], $email, $token);
            }

            // Sempre exibir a mesma mensagem (não revela se o e-mail existe)
            $msg = 'Se esse e-mail estiver cadastrado, você receberá um link em breve.';

        } catch (Exception $e) {
            $erro = 'Erro de conexão. Tente novamente.';
        }
    }
}

function enviar_reset($nome, $email, $token) {
    $remetente_nome  = 'Growfinder Lab';
    $remetente_email = 'noreply@growfinder.com.br';
    $smtp_host       = 'smtp.hostinger.com';
    $smtp_port       = 465;
    $smtp_user       = 'noreply@growfinder.com.br';
    $smtp_pass       = 'Luma@2026';

    $assunto = 'Redefinição de senha — Growfinder Lab';
    $link    = 'https://lab.growfinder.com.br/reset-senha.php?token=' . $token;

    $corpo = "
    <html><body style='font-family:sans-serif;color:#000;max-width:520px;margin:0 auto;padding:32px 16px'>
      <p>Olá, <strong>$nome</strong>!</p>
      <p>Recebemos uma solicitação para redefinir sua senha no Growfinder Lab.</p>
      <p style='margin:24px 0'>
        <a href='$link' style='background:#FF4B34;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:bold'>Redefinir minha senha</a>
      </p>
      <p style='font-size:13px;color:#555'>Este link expira em <strong>1 hora</strong>. Se você não solicitou a redefinição, ignore este e-mail.</p>
      <p>Abraços,<br>Kauê Braghetto<br>Growfinder</p>
    </body></html>";

    $socket = fsockopen('ssl://' . $smtp_host, $smtp_port, $errno, $errstr, 10);
    if (!$socket) return false;

    fgets($socket, 512);
    fputs($socket, "EHLO growfinder.com.br\r\n");
    while ($line = fgets($socket, 512)) { if (substr($line, 3, 1) === ' ') break; }
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($smtp_user) . "\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($smtp_pass) . "\r\n");
    $auth = fgets($socket, 512);
    if (substr($auth, 0, 3) !== '235') { fclose($socket); return false; }

    fputs($socket, "MAIL FROM:<$remetente_email>\r\n"); fgets($socket, 512);
    fputs($socket, "RCPT TO:<$email>\r\n");             fgets($socket, 512);
    fputs($socket, "DATA\r\n");                         fgets($socket, 512);

    $headers  = "From: $remetente_nome <$remetente_email>\r\n";
    $headers .= "To: $nome <$email>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($assunto) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";

    fputs($socket, $headers . "\r\n" . chunk_split(base64_encode($corpo)) . "\r\n.\r\n");
    fgets($socket, 512);
    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Growfinder Lab · Esqueci minha senha</title>
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="bg-pattern"></div>

<div class="card">
  <div class="card-body">
    <div class="card-title">Growfinder <span style="color:var(--accent)">Lab</span></div>
    <div class="card-sub">Informe seu e-mail para receber o link de redefinição de senha.</div>

    <?php if ($msg): ?>
      <div class="error-msg" style="color:#000;background:#f0f0f0;border:none"><?= htmlspecialchars($msg) ?></div>
    <?php else: ?>
    <form method="POST" action="">
      <div class="field-wrap">
        <label class="field-label" for="email">E-mail</label>
        <input type="email" id="email" name="email"
               placeholder="seu@email.com"
               required autocomplete="email">
      </div>

      <button class="btn-enter" type="submit">Enviar link →</button>

      <?php if ($erro): ?>
        <div class="error-msg"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>
    </form>
    <?php endif; ?>

    <div style="margin-top:16px;text-align:center">
      <a href="index.php" style="font-size:13px;color:#555">← Voltar ao login</a>
    </div>
  </div>

  <div class="footer-note">growfinder.com.br · Acesso restrito</div>
</div>

</body>
</html>
