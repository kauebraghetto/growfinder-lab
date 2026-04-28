  <!-- CADASTRAR -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title">Cadastrar novo mentorado</span>
    </div>
    <div class="admin-card-body">
      <form method="POST">
        <input type="hidden" name="acao" value="cadastrar">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
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
        </div>
        <div style="margin-top:12px">
          <button class="btn-sm btn-add" type="submit">+ Cadastrar</button>
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

                <form method="POST" class="inline-form">
                  <input type="hidden" name="acao" value="toggle">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <input type="hidden" name="ativo" value="<?= $u['ativo'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <?php if ($u['ativo']): ?>
                    <button class="btn-sm btn-suspender" type="submit" onclick="return confirm('Suspender acesso de <?= htmlspecialchars($u['nome']) ?>?')">Suspender</button>
                  <?php else: ?>
                    <button class="btn-sm btn-reativar" type="submit">Reativar</button>
                  <?php endif; ?>
                </form>

                <button class="btn-sm btn-reset-toggle" type="button" onclick="toggleReset(<?= $u['id'] ?>)">Resetar senha</button>

                <form method="POST" class="inline-form">
                  <input type="hidden" name="acao" value="deletar">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <button class="btn-del" type="submit" title="Remover" onclick="return confirm('Remover <?= htmlspecialchars($u['nome']) ?> permanentemente?')">×</button>
                </form>

              </div>
            </td>
          </tr>

          <tr class="reset-row" id="reset-<?= $u['id'] ?>">
            <td colspan="5">
              <form method="POST">
                <input type="hidden" name="acao" value="resetar">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
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
