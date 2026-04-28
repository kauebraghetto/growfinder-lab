# Plano de Implementação — CMS de Conteúdos

## Contexto

O site lab.growfinder.com.br precisa suportar publicação recorrente de conteúdos (artigos em Markdown e uploads de arquivos) sem editar código. Hoje o único conteúdo (Guia LLM) está hardcoded em `materiais.php` e distribuído em arquivos PHP. O objetivo é criar um sistema simples de gerenciamento no admin existente, com exibição dinâmica em `materiais.php` organizada por categoria.

**Arquivos críticos:**
- `materiais.php` — exibição dos conteúdos (hoje hardcoded)
- `admin.php` — painel admin (receberá seção de conteúdos)
- `guia-llm.php`, `guia-template.php`, `modulos/llm/mod1-6.php` — serão removidos
- `assets/css/components.css` — estilos dos cards (classes mat-card, mat-tag, etc.)
- `layouts/header.php`, `layouts/footer.php` — reutilizados

---

## Tasks Atômicas (sequenciadas)

### Fase 1 — Banco de Dados

**Task 1: Criar tabela `conteudos`**
SQL no phpMyAdmin:
```sql
CREATE TABLE conteudos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  tipo        ENUM('artigo','arquivo') NOT NULL DEFAULT 'artigo',
  formato     ENUM('markdown','html') NOT NULL DEFAULT 'markdown',
  titulo      VARCHAR(255) NOT NULL,
  descricao   TEXT,
  corpo       LONGTEXT,
  arquivo     VARCHAR(255),
  categoria   VARCHAR(100),
  tags        VARCHAR(255),
  status      ENUM('publicado','rascunho') NOT NULL DEFAULT 'rascunho',
  ordem       INT DEFAULT 0,
  criado_em   DATETIME DEFAULT NOW(),
  atualizado_em DATETIME DEFAULT NOW() ON UPDATE NOW()
);
```

**Task 2: Migrar Guia LLM para o banco**
- Concatenar HTML dos módulos mod1–mod6 em uma string
- INSERT com: tipo=artigo, formato=html, titulo="Entendendo os LLMs...", categoria="Fundamentos", tags="LLM,Transformer,Agentes", status=publicado
- Feito via script PHP de migração (executado uma vez, depois deletado)

---

### Fase 2 — Biblioteca Markdown

**Task 3: Adicionar Parsedown**
- Baixar `Parsedown.php` de parsedown.erusev.com (arquivo único ~80KB)
- Colocar em `assets/Parsedown.php`

---

### Fase 3 — Exibição de Artigos

**Task 4: Criar `artigo.php`**
- Recebe `?id=X`, busca conteúdo no banco
- Se `formato=markdown`: usa Parsedown para renderizar
- Se `formato=html`: renderiza diretamente
- Reutiliza `layouts/header.php` e `layouts/footer.php`
- Verificação de autenticação + timeout de sessão

**Task 5: Criar `assets/css/artigo.css`**
- Estilos de leitura: tipografia, headings, código, blockquotes
- Compatível com as design tokens existentes (--bg, --ink, --accent)

---

### Fase 4 — Atualização do materiais.php

**Task 6: Atualizar `materiais.php`**
- Remove conteúdo hardcoded (Guia LLM e "Em breve")
- Lê conteúdos com `status=publicado` do banco, agrupados por `categoria`
- Ordena por `ordem ASC, criado_em DESC`
- Cards artigo: link para `artigo.php?id=X`, seta →
- Cards arquivo: link para download em `uploads/`, ícone ↓ + badge com extensão
- Section header dinâmico: nome da categoria + contagem
- Mantém estrutura visual existente (classes mat-card, mat-section-header, grid)

---

### Fase 5 — Pasta de Uploads

**Task 7: Criar pasta `uploads/` com proteção**
- Criar `uploads/.htaccess` bloqueando execução de PHP
- Extensões permitidas no upload: .pdf, .doc, .docx, .xls, .xlsx, .md

---

### Fase 6 — Admin CMS

**Task 8: Adicionar seção de conteúdos no `admin.php`**
- Lista todos os conteúdos (publicados e rascunhos) com ações: editar, excluir, toggle status
- Formulário "Novo artigo": título, descrição, corpo (textarea Markdown), categoria, tags, status
- Formulário "Upload de arquivo": título, descrição, arquivo (input file), categoria, tags, status
- Validação de extensão no upload (whitelist)
- Arquivo salvo em `uploads/` com nome único (uniqid + extensão original)

---

### Fase 7 — Limpeza

**Task 9: Remover arquivos obsoletos (local + servidor)**
- `guia-llm.php`
- `guia-template.php`
- Pasta `modulos/` inteira

---

## Organização visual em materiais.php

```
[ Fundamentos ] — 2 materiais
  [card artigo] [card arquivo]

[ Ferramentas ] — 1 material
  [card artigo]
```

- Categorias aparecem dinamicamente conforme conteúdos cadastrados
- Cards artigo: tag com categoria, título, descrição, chips de tags, → seta
- Cards arquivo: tag com tipo de arquivo (PDF, Word...), título, descrição, ↓ download

---

## Verificação pós-implementação

1. Admin: criar um artigo de teste → verificar se aparece em materiais.php
2. Admin: fazer upload de um PDF → verificar se aparece e faz download
3. Guia LLM migrado: acessar pelo card → conteúdo renderiza corretamente
4. Rascunho: criar conteúdo como rascunho → não aparece em materiais.php
5. Remover arquivos obsoletos → confirmar que `guia-llm.php` retorna 404
