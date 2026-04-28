<?php
require_once 'config.php';

session_name(SESSION_NAME);
session_start();

// Verificar autenticação e timeout de sessão
if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

if (time() - ($_SESSION['login_time'] ?? 0) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: index.php?timeout=1');
    exit;
}

$nome = $_SESSION['usuario_nome'];
$email = $_SESSION['usuario_email'];
$page_title = 'Growfinder Lab · Materiais';
include 'layouts/header.php';
?>

<div class="page-header">
  <div class="page-greeting">Bem-vindo, <?= htmlspecialchars($nome) ?></div>
  <div class="page-title">Materiais de <em>Mentoria</em></div>
</div>

<div class="mat-main">

  <div class="mat-section-header">
    <span class="mat-section-title">Fundamentos</span>
    <span class="mat-section-count">1 material</span>
  </div>

  <div class="grid">
    <a class="mat-card" href="guia-llm.php" target="_blank">
      <div class="mat-card-top">
        <div class="mat-tag">Guia · 6 módulos</div>
        <div class="mat-title">Entendendo os LLMs: como a IA que você usa foi criada</div>
      </div>
      <div class="mat-card-body">
        <div class="mat-desc">Do dado bruto ao assistente alinhado. Conceitos técnicos em linguagem de negócio — do que é um LLM até agentes e o panorama dos próximos dois anos.</div>
        <div class="mat-meta">
          <div class="mat-chips">
            <span class="mat-chip">LLM</span>
            <span class="mat-chip">Transformer</span>
            <span class="mat-chip">Agentes</span>
          </div>
          <span class="mat-arrow">→</span>
        </div>
      </div>
    </a>
  </div>

  <div class="mat-divider"></div>

  <div class="mat-section-header">
    <span class="mat-section-title">Em breve</span>
    <span class="mat-section-count">próximos conteúdos</span>
  </div>

  <div class="grid">

    <div class="mat-card soon">
      <div class="mat-card-top">
        <span class="soon-badge">Em breve</span>
        <div class="mat-tag">Guia prático</div>
        <div class="mat-title">Qual IA usar para cada tipo de tarefa</div>
      </div>
      <div class="mat-card-body">
        <div class="mat-desc">Um mapa prático de ferramentas: quando usar ChatGPT, Claude, Gemini e ferramentas especializadas — e por quê.</div>
        <div class="mat-meta">
          <div class="mat-chips">
            <span class="mat-chip">Ferramentas</span>
            <span class="mat-chip">Comparativo</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mat-card soon">
      <div class="mat-card-top">
        <span class="soon-badge">Em breve</span>
        <div class="mat-tag">Conceito</div>
        <div class="mat-title">O que são agentes de IA — e como funcionam na prática</div>
      </div>
      <div class="mat-card-body">
        <div class="mat-desc">Da definição técnica aos casos de uso reais em PMEs e manufatura. Como orquestrar agentes para automatizar processos complexos.</div>
        <div class="mat-meta">
          <div class="mat-chips">
            <span class="mat-chip">Agentes</span>
            <span class="mat-chip">Automação</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mat-card soon">
      <div class="mat-card-top">
        <span class="soon-badge">Em breve</span>
        <div class="mat-tag">Guia prático</div>
        <div class="mat-title">Como escrever prompts que funcionam</div>
      </div>
      <div class="mat-card-body">
        <div class="mat-desc">Técnicas de prompt engineering para resultados consistentes e de alta qualidade nas tarefas do dia a dia da sua empresa.</div>
        <div class="mat-meta">
          <div class="mat-chips">
            <span class="mat-chip">Prompts</span>
            <span class="mat-chip">Produtividade</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include 'layouts/footer.php'; ?>
