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

$erro = '';
$sucesso = '';

// Buscar dados completos do usuário
try {
    $pdo = db_connect();
    $stmt = $pdo->prepare('SELECT nome, email, criado_em, ultimo_acesso FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();
} catch (Exception $e) {
    $usuario = [
        'nome'          => $_SESSION['usuario_nome'],
        'email'         => $_SESSION['usuario_email'],
        'criado_em'     => null,
        'ultimo_acesso' => null,
    ];
}

// Processar troca de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'] ?? '';
    $senha_nova  = $_POST['senha_nova'] ?? '';
    $senha_conf  = $_POST['senha_conf'] ?? '';

    if (!$senha_atual || !$senha_nova || !$senha_conf) {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha_nova) < 8) {
        $erro = 'A nova senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha_nova !== $senha_conf) {
        $erro = 'A nova senha e a confirmação não coincidem.';
    } else {
        try {
            $stmt2 = $pdo->prepare('SELECT senha FROM usuarios WHERE id = ?');
            $stmt2->execute([$_SESSION['usuario_id']]);
            $row = $stmt2->fetch();

            if ($row && password_verify($senha_atual, $row['senha'])) {
                $hash = password_hash($senha_nova, PASSWORD_BCRYPT);
                $upd  = $pdo->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
                $upd->execute([$hash, $_SESSION['usuario_id']]);
                $sucesso = 'Senha alterada com sucesso.';
            } else {
                $erro = 'Senha atual incorreta.';
            }
        } catch (Exception $e) {
            $erro = 'Erro de conexão. Tente novamente.';
        }
    }
}

// Formatar datas
function fmt_data($dt) {
    if (!$dt) return '—';
    $d = new DateTime($dt);
    return $d->format('d/m/Y \à\s H:i');
}

$email = $_SESSION['usuario_email'] ?? '';
$page_title = 'Growfinder Lab · Perfil';
$header_links = [
    ['href' => 'materiais.php', 'label' => 'Materiais'],
    ['href' => 'logout.php', 'label' => 'Sair'],
];
include 'layouts/header.php';
?>

<div class="perfil-main">
  <div class="perfil-page-title">Meu perfil</div>

  <!-- SEÇÃO: DADOS DA CONTA -->
  <div class="section">
    <div class="section-header">
      <span class="section-title">Dados da conta</span>
    </div>
    <div class="section-body">

      <div class="avatar-row">
        <div class="avatar"><?= mb_strtoupper(mb_substr($usuario['nome'], 0, 1)) ?></div>
        <div class="avatar-info">
          <div class="name"><?= htmlspecialchars($usuario['nome']) ?></div>
          <div class="email"><?= htmlspecialchars($usuario['email']) ?></div>
        </div>
      </div>

      <div class="info-row">
        <span class="info-label">Membro desde</span>
        <span class="info-value mono"><?= fmt_data($usuario['criado_em']) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Último acesso</span>
        <span class="info-value mono"><?= fmt_data($usuario['ultimo_acesso']) ?></span>
      </div>

    </div>
  </div>

  <!-- SEÇÃO: TROCAR SENHA -->
  <div class="section">
    <div class="section-header">
      <span class="section-title">Segurança</span>
    </div>
    <div class="section-body">
      <form method="POST" action="">

        <div class="field-wrap">
          <label class="field-label" for="senha_atual">Senha atual</label>
          <input type="password" id="senha_atual" name="senha_atual" placeholder="••••••••" required>
        </div>

        <div class="form-divider"></div>

        <div class="field-wrap">
          <label class="field-label" for="senha_nova">Nova senha</label>
          <input type="password" id="senha_nova" name="senha_nova" placeholder="••••••••" required>
          <div class="hint">Mínimo 8 caracteres.</div>
        </div>

        <div class="field-wrap">
          <label class="field-label" for="senha_conf">Confirmar nova senha</label>
          <input type="password" id="senha_conf" name="senha_conf" placeholder="••••••••" required>
        </div>

        <button class="btn-salvar" type="submit">Salvar nova senha</button>

        <?php if ($erro): ?>
          <div class="msg erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
          <div class="msg sucesso"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

      </form>
    </div>
  </div>

</div>

<?php include 'layouts/footer.php'; ?>
