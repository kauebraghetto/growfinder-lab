<?php
session_name('gf_admin_session');
session_start();

if (empty($_SESSION['admin'])) {
    header('Location: admin-login.php');
    exit;
}

// Timeout de sessão admin: 2 horas
if (time() - ($_SESSION['admin_time'] ?? 0) > 2 * 60 * 60) {
    session_destroy();
    header('Location: admin-login.php?timeout=1');
    exit;
}

require_once 'config.php';

$msg = '';
$erro = '';

try {
    $pdo = db_connect();
} catch (Exception $e) {
    die('Erro de conexão com o banco.');
}

// ── FUNÇÃO DE E-MAIL ────────────────────────────
function enviar_boas_vindas($nome, $email, $senha) {
    $remetente_nome  = 'Growfinder Lab';
    $remetente_email = 'noreply@growfinder.com.br';
    $smtp_host       = 'smtp.hostinger.com';
    $smtp_port       = 465;
    $smtp_user       = 'noreply@growfinder.com.br';
    $smtp_pass       = 'Luma@2026';

    $assunto  = 'Seu acesso ao Growfinder Lab';
    $link     = 'https://lab.growfinder.com.br';

    $corpo = "
    <html><body style='font-family:sans-serif;color:#000;max-width:520px;margin:0 auto;padding:32px 16px'>
      <p>Olá, <strong>$nome</strong>!</p>
      <p>Seu acesso ao Growfinder Lab foi criado. Abaixo estão suas credenciais:</p>
      <p><strong>E-mail:</strong> $email<br>
         <strong>Senha inicial:</strong> $senha</p>
      <p style='margin:24px 0'>
        <a href='$link' style='background:#FF4B34;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:bold'>Acessar o Growfinder Lab</a>
      </p>
      <p style='font-size:13px;color:#555'>Recomendamos que você altere sua senha após o primeiro acesso. Para isso, acesse <strong>Meu Perfil</strong> após fazer login.</p>
      <p style='font-size:13px;color:#555'>Qualquer dúvida, entre em contato comigo diretamente.</p>
      <p>Abraços,<br>Kauê Braghetto<br>Growfinder</p>
    </body></html>";

    // Conectar via SMTP com stream socket
    $socket = fsockopen('ssl://' . $smtp_host, $smtp_port, $errno, $errstr, 10);
    if (!$socket) return false;

    $read = fgets($socket, 512);

    // EHLO
    fputs($socket, "EHLO growfinder.com.br\r\n");
    while ($line = fgets($socket, 512)) { if (substr($line, 3, 1) === ' ') break; }

    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($smtp_user) . "\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($smtp_pass) . "\r\n");
    $auth = fgets($socket, 512);
    if (substr($auth, 0, 3) !== '235') { fclose($socket); return false; }

    // MAIL FROM
    fputs($socket, "MAIL FROM:<$remetente_email>\r\n");
    fgets($socket, 512);

    // RCPT TO
    fputs($socket, "RCPT TO:<$email>\r\n");
    fgets($socket, 512);

    // DATA
    fputs($socket, "DATA\r\n");
    fgets($socket, 512);

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

// ── AÇÕES ──────────────────────────────────────

// Cadastrar novo mentorado
if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$nome || !$email || !$senha) {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } else {
        try {
            $hash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, ativo) VALUES (?, ?, ?, 1)');
            $stmt->execute([$nome, $email, $hash]);

            // Enviar e-mail de boas-vindas
            $email_enviado = enviar_boas_vindas($nome, $email, $senha);
            $msg = "Mentorado \"$nome\" cadastrado com sucesso.";
            $msg .= $email_enviado ? ' E-mail de boas-vindas enviado.' : ' (Falha ao enviar e-mail — verifique as configurações SMTP.)';
        } catch (Exception $e) {
            $erro = 'E-mail já cadastrado ou erro ao salvar.';
        }
    }
}

// Ativar / desativar
if (isset($_POST['acao']) && $_POST['acao'] === 'toggle') {
    $id    = (int)($_POST['id'] ?? 0);
    $ativo = (int)($_POST['ativo'] ?? 0);
    $novo  = $ativo ? 0 : 1;
    $stmt  = $pdo->prepare('UPDATE usuarios SET ativo = ? WHERE id = ?');
    $stmt->execute([$novo, $id]);
    $msg = $novo ? 'Acesso reativado.' : 'Acesso suspenso.';
}

// Resetar senha
if (isset($_POST['acao']) && $_POST['acao'] === 'resetar') {
    $id        = (int)($_POST['id'] ?? 0);
    $senha_new = $_POST['senha_reset'] ?? '';
    if (strlen($senha_new) < 8) {
        $erro = 'A nova senha deve ter pelo menos 8 caracteres.';
    } else {
        $hash = password_hash($senha_new, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
        $msg = 'Senha resetada com sucesso.';
    }
}

// Deletar
if (isset($_POST['acao']) && $_POST['acao'] === 'deletar') {
    $id   = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $msg = 'Mentorado removido.';
}

// ── LISTAR USUÁRIOS ─────────────────────────────
$usuarios = $pdo->query('SELECT * FROM usuarios ORDER BY criado_em DESC')->fetchAll();

$page_title = 'Growfinder Lab · Admin';
include 'layouts/header-admin.php';
?>

<div class="admin-main">
  <div class="admin-page-title">Administração</div>
  <div class="page-sub">Gerencie os mentorados do Growfinder Lab.</div>

  <?php if ($msg): ?>
    <div class="msg-box ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="msg-box err"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <!-- CADASTRAR -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title">Cadastrar novo mentorado</span>
    </div>
    <div class="admin-card-body">
      <form method="POST">
        <input type="hidden" name="acao" value="cadastrar">
        <div class="form-grid">
          <div class="field-wrap">
            <label class="field-label">Nome</label>
            <input type="text" name="nome" placeholder="Nome completo" required>
          </div>
          <div class="field-wrap">
            <label class="field-label">E-mail</label>
            <input type="email" name="email" placeholder="email@exemplo.com" required>
          </div>
          <div class="field-wrap">
            <label class="field-label">Senha inicial</label>
            <input type="password" name="senha" placeholder="mín. 8 caracteres" required>
          </div>
          <button class="btn-add" type="submit">+ Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- LISTA -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title">Mentorados (<?= count($usuarios) ?>)</span>
    </div>
    <div class="admin-card-body flush">
      <?php if (empty($usuarios)): ?>
        <div class="empty-state">Nenhum mentorado cadastrado.</div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Status</th>
            <th>Último acesso</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>

          <!-- Linha principal -->
          <tr class="user-row">
            <td class="td-name"><?= htmlspecialchars($u['nome']) ?></td>
            <td class="td-email"><?= htmlspecialchars($u['email']) ?></td>
            <td>
              <?php if ($u['ativo']): ?>
                <span class="badge-ativo">Ativo</span>
              <?php else: ?>
                <span class="badge-inativo">Suspenso</span>
              <?php endif; ?>
            </td>
            <td class="td-date">
              <?= $u['ultimo_acesso'] ? (new DateTime($u['ultimo_acesso']))->format('d/m/Y H:i') : '—' ?>
            </td>
            <td>
              <div class="acoes">

                <!-- Toggle ativo/inativo -->
                <form method="POST" class="inline-form">
                  <input type="hidden" name="acao" value="toggle">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <input type="hidden" name="ativo" value="<?= $u['ativo'] ?>">
                  <?php if ($u['ativo']): ?>
                    <button class="btn-sm btn-suspender" type="submit" onclick="return confirm('Suspender acesso de <?= htmlspecialchars($u['nome']) ?>?')">Suspender</button>
                  <?php else: ?>
                    <button class="btn-sm btn-reativar" type="submit">Reativar</button>
                  <?php endif; ?>
                </form>

                <!-- Abrir reset de senha -->
                <button class="btn-sm btn-reset-toggle" type="button" onclick="toggleReset(<?= $u['id'] ?>)">Resetar senha</button>

                <!-- Deletar -->
                <form method="POST" class="inline-form">
                  <input type="hidden" name="acao" value="deletar">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button class="btn-del" type="submit" title="Remover" onclick="return confirm('Remover <?= htmlspecialchars($u['nome']) ?> permanentemente?')">×</button>
                </form>

              </div>
            </td>
          </tr>

          <!-- Linha de reset (expandida) -->
          <tr class="reset-row" id="reset-<?= $u['id'] ?>">
            <td colspan="5">
              <form method="POST">
                <input type="hidden" name="acao" value="resetar">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <div class="reset-inner">
                  <span class="reset-label">Nova senha para <?= htmlspecialchars($u['nome']) ?></span>
                  <input class="reset-input" type="password" name="senha_reset" placeholder="mínimo 8 caracteres" required>
                  <button class="btn-reset-confirm" type="submit">Confirmar</button>
                  <button class="btn-cancel" type="button" onclick="toggleReset(<?= $u['id'] ?>)">Cancelar</button>
                </div>
              </form>
            </td>
          </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php include 'layouts/footer.php'; ?>

<script>
  function toggleReset(id) {
    const row = document.getElementById('reset-' + id);
    row.classList.toggle('open');
    if (row.classList.contains('open')) {
      row.querySelector('input[type=password]').focus();
    }
  }
</script>
