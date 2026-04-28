# Growfinder Lab

Plataforma de mentoria da Growfinder — área exclusiva para mentorados com materiais, guias e recursos de apoio.

**URL:** https://lab.growfinder.com.br

---

## Funcionalidades

### Área do mentorado
- Login com e-mail e senha (bloqueio após 5 tentativas incorretas por 15 min)
- Listagem de materiais agrupados por categoria, com filtro e busca
- Leitura de artigos em Markdown ou HTML com sumário automático e barra de progresso
- Download de arquivos disponibilizados pelo admin
- Troca de senha via perfil (exige senha atual)
- Recuperação de senha via e-mail com token de uso único (expira em 1h)

### Painel admin
- Login independente com sessão separada e timeout de 2h
- Gestão de mentorados: cadastro, suspensão/reativação, reset de senha e remoção
- E-mail automático de boas-vindas ao cadastrar um novo mentorado (via SMTP)
- CMS de conteúdos: criação e edição de artigos, upload de arquivos
- Controle de status por conteúdo (`publicado` ou `rascunho`)
- Ordenação personalizada dos conteúdos por categoria

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
├── materiais.php          # Listagem de conteúdos
├── artigo.php             # Leitura de artigos (Markdown/HTML)
├── perfil.php             # Perfil e troca de senha
├── esqueci-senha.php      # Solicitação de reset de senha
├── reset-senha.php        # Redefinição de senha via token
├── logout.php             # Encerrar sessão
├── admin-login.php        # Login do admin
├── admin.php              # Painel admin (mentorados + CMS)
├── admin-logout.php       # Encerrar sessão admin
├── admin/
│   ├── mentorados.php     # UI de gestão de mentorados
│   └── conteudos.php      # UI do CMS de conteúdos
├── config.php             # Credenciais e configurações (não commitado)
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

## Segurança

- Senhas armazenadas com bcrypt
- Consultas SQL via PDO com prepared statements (sem SQL injection)
- Proteção CSRF em todos os formulários do painel admin
- Rate limiting por sessão nos logins (5 tentativas → bloqueio de 15 min)
- `session_regenerate_id()` após cada login
- Timeout de sessão: 8h (mentorados) e 2h (admin)
- Parsedown em modo seguro (bloqueia HTML bruto em Markdown)
- Conteúdo HTML sanitizado com allowlist de tags antes de renderizar
- Upload validado por extensão e MIME type, limite de 10 MB
- `.htaccess` na pasta `uploads/` bloqueia execução de PHP
- Headers HTTP: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`
- Credenciais SMTP centralizadas em `config.php` (fora do repositório)

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

1. Configure um servidor Apache + PHP 8+ + MySQL (ex: XAMPP ou Laragon)
2. Aponte para este diretório
3. Importe o banco e crie o arquivo `config.php`:

```php
<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'growfinderlab');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('SMTP_HOST',       'smtp.exemplo.com');
define('SMTP_PORT',       465);
define('SMTP_USER',       'noreply@exemplo.com');
define('SMTP_PASS',       '');
define('SMTP_FROM_NAME',  'Growfinder Lab');
define('SMTP_FROM_EMAIL', 'noreply@exemplo.com');

define('SESSION_NAME',    'gf_lab_session');
define('SESSION_TIMEOUT', 28800);

function db_connect(): PDO {
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
