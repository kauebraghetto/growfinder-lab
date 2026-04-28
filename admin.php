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
    $remetente_nome  = SMTP_FROM_NAME;
    $remetente_email = SMTP_FROM_EMAIL;
    $smtp_host       = SMTP_HOST;
    $smtp_port       = SMTP_PORT;
    $smtp_user       = SMTP_USER;
    $smtp_pass       = SMTP_PASS;

    $assunto  = 'Seu acesso ao Growfinder Lab';
    $link     = 'https://lab.growfinder.com.br';
    $nome_safe  = htmlspecialchars($nome,  ENT_QUOTES, 'UTF-8');
    $email_safe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $senha_safe = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

    $corpo = "
    <html><body style='font-family:sans-serif;color:#000;max-width:520px;margin:0 auto;padding:32px 16px'>
      <p>Olá, <strong>$nome_safe</strong>!</p>
      <p>Seu acesso ao Growfinder Lab foi criado. Abaixo estão suas credenciais:</p>
      <p><strong>E-mail:</strong> $email_safe<br>
         <strong>Senha inicial:</strong> $senha_safe</p>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

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

// Deletar mentorado
if (isset($_POST['acao']) && $_POST['acao'] === 'deletar') {
    $id   = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $msg = 'Mentorado removido.';
}

// ── AÇÕES CMS ──────────────────────────────────

$extensoes_permitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'md'];

// Novo artigo
if (isset($_POST['acao']) && $_POST['acao'] === 'cms_novo_artigo') {
    $titulo     = trim($_POST['titulo'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $corpo      = $_POST['corpo'] ?? '';
    $categoria  = trim($_POST['categoria'] ?? '');
    $tags       = trim($_POST['tags'] ?? '');
    $status     = $_POST['status'] ?? 'rascunho';
    $ordem      = (int)($_POST['ordem'] ?? 0);

    if (!$titulo) {
        $erro = 'O título é obrigatório.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO conteudos (tipo, formato, titulo, descricao, corpo, categoria, tags, status, ordem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(['artigo', 'markdown', $titulo, $descricao, $corpo, $categoria, $tags, $status, $ordem]);
        $msg = "Artigo \"$titulo\" criado.";
    }
}

// Upload de arquivo
if (isset($_POST['acao']) && $_POST['acao'] === 'cms_upload') {
    $titulo     = trim($_POST['titulo'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $categoria  = trim($_POST['categoria'] ?? '');
    $tags       = trim($_POST['tags'] ?? '');
    $status     = $_POST['status'] ?? 'rascunho';
    $ordem      = (int)($_POST['ordem'] ?? 0);

    if (!$titulo) {
        $erro = 'O título é obrigatório.';
    } elseif (empty($_FILES['arquivo']['name'])) {
        $erro = 'Selecione um arquivo.';
    } else {
        $ext      = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
        $max_size = 10 * 1024 * 1024; // 10 MB
        $mimes_permitidos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/markdown',
        ];

        if (!in_array($ext, $extensoes_permitidas)) {
            $erro = 'Extensão não permitida. Use: ' . implode(', ', $extensoes_permitidas);
        } elseif ($_FILES['arquivo']['size'] > $max_size) {
            $erro = 'Arquivo muito grande. Máximo permitido: 10 MB.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $_FILES['arquivo']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $mimes_permitidos)) {
                $erro = 'Tipo de arquivo não permitido.';
            }
        }

        if (!$erro) {
            $nome_arquivo = uniqid() . '.' . $ext;
            $destino = __DIR__ . '/uploads/' . $nome_arquivo;
            if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
                $stmt = $pdo->prepare('INSERT INTO conteudos (tipo, formato, titulo, descricao, arquivo, categoria, tags, status, ordem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute(['arquivo', 'html', $titulo, $descricao, $nome_arquivo, $categoria, $tags, $status, $ordem]);
                $msg = "Arquivo \"$titulo\" enviado.";
            } else {
                $erro = 'Erro ao salvar o arquivo.';
            }
        }
    }
}

// Toggle status conteudo
if (isset($_POST['acao']) && $_POST['acao'] === 'cms_toggle') {
    $id     = (int)($_POST['id'] ?? 0);
    $atual  = $_POST['status'] ?? 'rascunho';
    $novo   = $atual === 'publicado' ? 'rascunho' : 'publicado';
    $stmt   = $pdo->prepare('UPDATE conteudos SET status = ? WHERE id = ?');
    $stmt->execute([$novo, $id]);
    $msg = $novo === 'publicado' ? 'Conteúdo publicado.' : 'Conteúdo movido para rascunho.';
}

// Deletar conteudo
if (isset($_POST['acao']) && $_POST['acao'] === 'cms_deletar') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt_arq = $pdo->prepare('SELECT arquivo FROM conteudos WHERE id = ? AND tipo = ?');
    $stmt_arq->execute([$id, 'arquivo']);
    $arq = $stmt_arq->fetch();
    if ($arq && $arq['arquivo']) {
        $caminho = __DIR__ . '/uploads/' . $arq['arquivo'];
        if (file_exists($caminho)) unlink($caminho);
    }
    $stmt = $pdo->prepare('DELETE FROM conteudos WHERE id = ?');
    $stmt->execute([$id]);
    $msg = 'Conteúdo removido.';
}

// Editar conteudo
if (isset($_POST['acao']) && $_POST['acao'] === 'cms_editar') {
    $id         = (int)($_POST['id'] ?? 0);
    $titulo     = trim($_POST['titulo'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $corpo      = $_POST['corpo'] ?? '';
    $categoria  = trim($_POST['categoria'] ?? '');
    $tags       = trim($_POST['tags'] ?? '');
    $status     = $_POST['status'] ?? 'rascunho';
    $ordem      = (int)($_POST['ordem'] ?? 0);

    if (!$titulo) {
        $erro = 'O título é obrigatório.';
    } else {
        $stmt = $pdo->prepare('UPDATE conteudos SET titulo=?, descricao=?, corpo=?, categoria=?, tags=?, status=?, ordem=? WHERE id=?');
        $stmt->execute([$titulo, $descricao, $corpo, $categoria, $tags, $status, $ordem, $id]);
        $msg = "Conteúdo \"$titulo\" atualizado.";
    }
}

// ── LISTAR USUÁRIOS ─────────────────────────────
$usuarios = $pdo->query('SELECT * FROM usuarios ORDER BY criado_em DESC')->fetchAll();

$conteudos = $pdo->query('SELECT * FROM conteudos ORDER BY status ASC, criado_em DESC')->fetchAll();

$page = $_GET['page'] ?? 'mentorados';
if (!in_array($page, ['mentorados', 'conteudos'])) $page = 'mentorados';

$page_title = 'Growfinder Lab · Admin';
include 'layouts/header-admin.php';
?>

<div class="admin-main">
  <div class="admin-page-title">Administração</div>
  <div class="page-sub"><?= $page === 'mentorados' ? 'Gerencie os mentorados do Growfinder Lab.' : 'Gerencie os conteúdos do Growfinder Lab.' ?></div>

  <?php if ($msg): ?>
    <div class="msg-box ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if ($erro): ?>
    <div class="msg-box err"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <?php if ($page === 'mentorados'): ?>
    <?php include 'admin/mentorados.php'; ?>
  <?php else: ?>
    <?php include 'admin/conteudos.php'; ?>
  <?php endif; ?>

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

  function toggleEdit(id) {
    const row = document.getElementById('edit-' + id);
    row.classList.toggle('open');
  }
</script>
