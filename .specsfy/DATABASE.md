# Banco de dados

Mapa de persistência do sistema. Modelo inicial sugerido para
**Laravel**.

Para Laravel, descreva módulos de domínio, fronteiras HTTP/console e use `database/migrations` como primeira evidência do mapa de dados.

## Fontes de dados

<!-- specsfy:database:start -->
| Fonte | Tecnologia | Configuração segura | Evidência |
| --- | --- | --- | --- |
| Principal (dev) | PostgreSQL 17 (container `nexuscrm-pgsql`) | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | `.env` |
| Testes | PostgreSQL 17, db `nexuscrm_test` | mesmas variáveis | `.env.testing` |
| Produção | Supabase gerenciado (Postgres) | mesmas variáveis | decisão 2026-09-26 |

## Estruturas

| Estrutura | Tipo | Campos | Relações | Fonte |
| --- | --- | --- | --- | --- |
| users | tabela | id, name, email, password, email_verified_at, remember_token, timestamps | — | `database/migrations/0001_01_01_000000_create_users_table.php` |
| sessions, password_reset_tokens | tabela | padrão Laravel | users | idem |
| cache, jobs | tabela | padrão Laravel | — | `0001_01_01_000001/2` |
<!-- specsfy:database:end -->

## Decisões, ownership e retenção

Registre finalidade, ownership, classificação, retenção, constraints e decisões
que não estejam explícitas nos schemas.

Modelo de domínio aprovado (ainda sem migrations): ver PROJECT.md — projetos, funis, etapas, pessoas (por projeto), negócios, anotações, motivos de perda, atribuição gestor↔projeto.
