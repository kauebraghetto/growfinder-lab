# AGENTS.md

## Stack
- PHP + MySQL (PDO)
- Vanilla CSS with custom properties (design tokens)
- No build tools, no framework

## Database
- `config.php` exports `db_connect(): PDO`
- Credentials: `u725319500_gflab` / `u725319500500_growfinderlab` @ `localhost`

## Session Names
- Users: `gf_lab_session` (8h timeout)
- Admins: `gf_admin_session` (2h timeout)
- Both check `$_SESSION['usuario_id']` or `$_SESSION['admin']`

## Layout System
- Pages include `layouts/header.php` then output content, then `layouts/footer.php`
- Admin uses `layouts/header-admin.php`

## Design Tokens
CSS custom properties in `assets/css/base.css`: `--bg`, `--ink`, `--accent` (red), `--muted`

## Content Components (in `assets/css/components.css`)
- Cards: `.mat-card`, `.mat-section-header`, `.mat-tag`, `.mat-chip`, `.mat-arrow`
- Grid: `.grid`

## Active Work: CMS Implementation
See `cms-implementacao.md` — plan to move hardcoded content from `materiais.php` to a `conteudos` DB table with admin CRUD.

## Files to Remove (post-CMS)
- `guia-llm.php`, `guia-template.php`
- `modulos/llm/` folder

## Security
- Rate limit: 5 login attempts → 15min lock (stored in session)
- Admin SMTP creds in `admin.php` (not `config.php`)