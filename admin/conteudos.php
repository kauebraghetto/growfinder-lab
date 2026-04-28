  <!-- LISTA CONTEÚDOS -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title">Conteúdos (<?= count($conteudos) ?>)</span>
    </div>
    <div class="admin-card-body flush">
      <?php if (empty($conteudos)): ?>
        <div class="empty-state">Nenhum conteúdo cadastrado.</div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Tipo</th>
            <th>Categoria</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($conteudos as $c): ?>
          <tr class="user-row">
            <td class="td-name"><?= htmlspecialchars($c['titulo']) ?></td>
            <td><?= $c['tipo'] === 'artigo' ? 'Artigo' : 'Arquivo' ?></td>
            <td><?= htmlspecialchars($c['categoria'] ?: '—') ?></td>
            <td>
              <?php if ($c['status'] === 'publicado'): ?>
                <span class="badge-ativo">Publicado</span>
              <?php else: ?>
                <span class="badge-inativo">Rascunho</span>
              <?php endif; ?>
            </td>
            <td class="td-date"><?= (new DateTime($c['criado_em']))->format('d/m/Y') ?></td>
            <td>
              <div class="acoes">
                <form method="POST" class="inline-form">
                  <input type="hidden" name="acao" value="cms_toggle">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <input type="hidden" name="status" value="<?= $c['status'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <?php if ($c['status'] === 'publicado'): ?>
                    <button class="btn-sm btn-suspender" type="submit">Despublicar</button>
                  <?php else: ?>
                    <button class="btn-sm btn-reativar" type="submit">Publicar</button>
                  <?php endif; ?>
                </form>
                <button class="btn-sm btn-reset-toggle" type="button" onclick="toggleEdit(<?= $c['id'] ?>)">Editar</button>
                <form method="POST" class="inline-form">
                  <input type="hidden" name="acao" value="cms_deletar">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <button class="btn-del" type="submit" title="Remover" onclick="return confirm('Remover este conteúdo?')">×</button>
                </form>
              </div>
            </td>
          </tr>

          <tr class="reset-row" id="edit-<?= $c['id'] ?>">
            <td colspan="6">
              <form method="POST" class="cms-edit-form">
                <input type="hidden" name="acao" value="cms_editar">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="cms-edit-grid">
                  <div class="field-wrap">
                    <label class="field-label">Título</label>
                    <input type="text" name="titulo" value="<?= htmlspecialchars($c['titulo']) ?>" required>
                  </div>
                  <div class="field-wrap">
                    <label class="field-label">Categoria</label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($c['categoria']) ?>">
                  </div>
                  <div class="field-wrap">
                    <label class="field-label">Tags (separar por vírgula)</label>
                    <input type="text" name="tags" value="<?= htmlspecialchars($c['tags']) ?>">
                  </div>
                  <div class="field-wrap">
                    <label class="field-label">Status</label>
                    <select name="status" style="width:100%;background:var(--bg);border:1px solid rgba(0,0,0,0.12);border-radius:7px;padding:10px 12px;font-family:var(--f);font-size:13px;outline:none;">
                      <option value="rascunho" <?= $c['status'] === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
                      <option value="publicado" <?= $c['status'] === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                    </select>
                  </div>
                  <div class="field-wrap">
                    <label class="field-label">Ordem</label>
                    <input type="number" name="ordem" value="<?= $c['ordem'] ?>" min="0">
                  </div>
                  <div class="field-wrap">
                    <label class="field-label">Descrição</label>
                    <input type="text" name="descricao" value="<?= htmlspecialchars($c['descricao'] ?? '') ?>">
                  </div>
                  <?php if ($c['tipo'] === 'artigo'): ?>
                  <div class="field-wrap" style="grid-column:1/-1">
                    <label class="field-label">Corpo (markdown ou html)</label>
                    <textarea name="corpo" rows="8" style="width:100%;background:var(--bg);border:1px solid rgba(0,0,0,0.12);border-radius:7px;padding:10px 12px;font-family:var(--f);font-size:13px;outline:none;resize:vertical;line-height:1.6"><?= htmlspecialchars($c['corpo'] ?? '') ?></textarea>
                  </div>
                  <?php endif; ?>
                  <div style="display:flex;gap:10px;align-items:center">
                    <button class="btn-reset-confirm" type="submit">Salvar</button>
                    <button class="btn-cancel" type="button" onclick="toggleEdit(<?= $c['id'] ?>)">Cancelar</button>
                  </div>
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

  <!-- NOVO ARTIGO -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title">Novo artigo</span>
    </div>
    <div class="admin-card-body">
      <form method="POST">
        <input type="hidden" name="acao" value="cms_novo_artigo">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="cms-form-grid">
          <div class="field-wrap">
            <label class="field-label">Título *</label>
            <input type="text" name="titulo" required>
          </div>
          <div class="field-wrap">
            <label class="field-label">Categoria</label>
            <input type="text" name="categoria">
          </div>
          <div class="field-wrap">
            <label class="field-label">Tags (vírgula)</label>
            <input type="text" name="tags">
          </div>
          <div class="field-wrap">
            <label class="field-label">Descrição</label>
            <input type="text" name="descricao">
          </div>
          <div class="field-wrap">
            <label class="field-label">Status</label>
            <select name="status" style="width:100%;background:var(--bg);border:1px solid rgba(0,0,0,0.12);border-radius:7px;padding:10px 12px;font-family:var(--f);font-size:13px;outline:none;">
              <option value="rascunho">Rascunho</option>
              <option value="publicado">Publicado</option>
            </select>
          </div>
          <div class="field-wrap">
            <label class="field-label">Ordem</label>
            <input type="number" name="ordem" value="0" min="0">
          </div>
          <div class="field-wrap" style="grid-column:1/-1">
            <label class="field-label">Corpo (markdown ou html)</label>
            <textarea name="corpo" rows="10" style="width:100%;background:var(--bg);border:1px solid rgba(0,0,0,0.12);border-radius:7px;padding:10px 12px;font-family:var(--f);font-size:13px;outline:none;resize:vertical;line-height:1.6"></textarea>
          </div>
        </div>
        <div style="margin-top:12px">
          <button class="btn-sm btn-add" type="submit">+ Criar artigo</button>
        </div>
      </form>
    </div>
  </div>

  <!-- UPLOAD ARQUIVO -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title">Upload de arquivo</span>
    </div>
    <div class="admin-card-body">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="cms_upload">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="cms-form-grid">
          <div class="field-wrap" style="grid-column:1/-1">
            <label class="field-label">Arquivo *</label>
            <input type="file" name="arquivo" accept=".pdf,.doc,.docx,.xls,.xlsx,.md" required style="font-size:13px">
          </div>
          <div class="field-wrap">
            <label class="field-label">Título *</label>
            <input type="text" name="titulo" required>
          </div>
          <div class="field-wrap">
            <label class="field-label">Categoria</label>
            <input type="text" name="categoria">
          </div>
          <div class="field-wrap">
            <label class="field-label">Tags (vírgula)</label>
            <input type="text" name="tags">
          </div>
          <div class="field-wrap">
            <label class="field-label">Descrição</label>
            <input type="text" name="descricao">
          </div>
          <div class="field-wrap">
            <label class="field-label">Status</label>
            <select name="status" style="width:100%;background:var(--bg);border:1px solid rgba(0,0,0,0.12);border-radius:7px;padding:10px 12px;font-family:var(--f);font-size:13px;outline:none;">
              <option value="rascunho">Rascunho</option>
              <option value="publicado">Publicado</option>
            </select>
          </div>
          <div class="field-wrap">
            <label class="field-label">Ordem</label>
            <input type="number" name="ordem" value="0" min="0">
          </div>
        </div>
        <div style="margin-top:12px">
          <button class="btn-sm btn-add" type="submit">↑ Enviar arquivo</button>
        </div>
      </form>
    </div>
  </div>
