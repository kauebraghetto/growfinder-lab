# Growfinder Lab

Plataforma de mentoria da Growfinder — área exclusiva para mentorados com materiais, guias e recursos de apoio.

**URL:** https://lab.growfinder.com.br

---

## Stack

- PHP 8+ (sem framework)
- MySQL via PDO
- CSS puro (sem preprocessador)
- Hospedagem: Hostinger (Apache)
- Deploy: FTP/SFTP direto (sem build step)

---

## Estrutura

```
├── index.php              # Login de mentorados
├── materiais.php          # Página principal (conteúdos do banco)
├── artigo.php             # Exibição de artigos (Markdown/HTML)
├── perfil.php             # Perfil e troca de senha
├── esqueci-senha.php      # Solicitação de reset de senha
├── reset-senha.php        # Redefinição de senha
├── logout.php             # Encerrar sessão
├── admin-login.php        # Login do admin
├── admin.php              # Painel admin (mentorados + CMS de conteúdos)
├── admin-logout.php       # Encerrar sessão admin
├── config.php             # Credenciais do banco (não commitado)
├── assets/
│   ├── Parsedown.php      # Parser Markdown
│   └── css/               # base, layout, components, login, admin, artigo
├── layouts/
│   ├── header.php
│   ├── footer.php
│   └── header-admin.php
└── uploads/               # Arquivos enviados via CMS (.htaccess protege execução)
```

---

## Autenticação

- **Mentorados:** sessão `gf_lab_session`, timeout 8h
- **Admin:** sessão `gf_admin_session`, timeout 2h, máx. 5 tentativas de login

---

## Banco de dados

### Tabela: `usuarios`

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT | PK |
| nome | VARCHAR(255) | Nome completo |
| email | VARCHAR(255) | E-mail único |
| senha | VARCHAR(255) | Hash bcrypt |
| ativo | TINYINT | 1 = ativo, 0 = suspenso |
| criado_em | DATETIME | Data de cadastro |
| ultimo_acesso | DATETIME | Último login |
| reset_token | VARCHAR(64) | Token de reset de senha |
| reset_expires | DATETIME | Expiração do token |

### Tabela: `conteudos`

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT | PK |
| tipo | ENUM | `artigo` ou `arquivo` |
| formato | ENUM | `markdown` ou `html` |
| titulo | VARCHAR(255) | Título do conteúdo |
| descricao | TEXT | Descrição curta |
| corpo | LONGTEXT | Corpo do artigo (Markdown ou HTML) |
| arquivo | VARCHAR(255) | Nome do arquivo em `uploads/` |
| categoria | VARCHAR(100) | Agrupamento em materiais.php |
| tags | VARCHAR(255) | Tags separadas por vírgula |
| status | ENUM | `publicado` ou `rascunho` |
| ordem | INT | Ordenação dentro da categoria |
| criado_em | DATETIME | Data de criação |
| atualizado_em | DATETIME | Última atualização |

---

## Configuração local

1. Configure um servidor Apache + PHP + MySQL (ex: XAMPP ou Laragon)
2. Aponte para este diretório
3. Importe o banco e crie o arquivo `config.php`:

```php
<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'growfinderlab');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME',    'gf_lab_session');
define('SESSION_TIMEOUT', 28800);

function db_connect() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}
```

---

## Deploy

Via WinSCP ou FileZilla para `public_html/lab/` na Hostinger. Não subir `config.php`.

---

## CMS de Conteúdos

O painel admin possui um CMS para publicar artigos e upload de arquivos sem editar código. Conteúdos são exibidos dinamicamente em `materiais.php` agrupados por categoria.

- Artigos: corpo em Markdown, renderizado via Parsedown
- Arquivos: upload para `uploads/` (extensões permitidas: .pdf, .doc, .docx, .xls, .xlsx, .md)
- Status: `publicado` (visível) ou `rascunho` (oculto)
