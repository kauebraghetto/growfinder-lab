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
├── materiais.php          # Página principal de conteúdos
├── perfil.php             # Perfil e troca de senha
├── guia-llm.php           # Guia LLM (6 módulos)
├── esqueci-senha.php      # Solicitação de reset de senha
├── reset-senha.php        # Redefinição de senha
├── logout.php             # Encerrar sessão
├── admin-login.php        # Login do admin
├── admin.php              # Painel admin (mentorados + conteúdos)
├── admin-logout.php       # Encerrar sessão admin
├── config.php             # Credenciais do banco (não commitado)
├── assets/
│   └── css/               # base, layout, components, login, admin
├── layouts/
│   ├── header.php
│   ├── footer.php
│   └── header-admin.php
└── modulos/
    └── llm/               # mod1.php – mod6.php
```

---

## Autenticação

- **Mentorados:** sessão `gf_lab_session`, timeout 8h
- **Admin:** sessão `gf_admin_session`, timeout 2h, máx. 5 tentativas de login

---

## Banco de dados

Tabela principal: `usuarios`

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

## Roadmap

- [ ] CMS dinâmico para publicação de artigos e uploads (ver `cms-implementacao.md`)
