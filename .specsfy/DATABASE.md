# Banco de dados

Mapa de persistência do sistema. Modelo inicial sugerido para
**Laravel**.

Para Laravel, descreva módulos de domínio, fronteiras HTTP/console e use `database/migrations` como primeira evidência do mapa de dados.

## Fontes de dados

<!-- specsfy:database:start -->
| Fonte | Tecnologia/forma | Evidência |
| --- | --- | --- |
| Desenvolvimento e testes desta entrega | PostgreSQL | migration `2026_09_28_000001_create_deals_tables.php` (checks e índices parciais); `php artisan migrate:status` |
| Exemplo de configuração | SQLite | `.env.example` (`DB_CONNECTION`); não representa o banco usado nesta entrega |
| Estrutura | Schema/migration | `database/migrations/0001_01_01_000000_create_users_table.php` |
| Estrutura | Schema/migration | `database/migrations/0001_01_01_000001_create_cache_table.php` |
| Estrutura | Schema/migration | `database/migrations/0001_01_01_000002_create_jobs_table.php` |
| Estrutura | Schema/migration | `database/migrations/2026_09_26_000001_add_role_and_status_to_users_table.php` |
| Estrutura | Schema/migration | `database/migrations/2026_09_26_000002_create_projects_tables.php` |
| Estrutura | Schema/migration | `database/migrations/2026_09_27_000001_create_funnels_and_stages_tables.php` |
| Estrutura | Schema/migration | `database/migrations/2026_09_28_000001_create_deals_tables.php` |

## Estruturas detectadas

| Estrutura | Tipo | Campos | Relações | Fonte |
| --- | --- | --- | --- | --- |
| users | Tabela | id:id, name:string, email:string, email_verified_at:timestamp, password:string, token:string, created_at:timestamp, id:string, user_id:foreignId, ip_address:string, user_agent:text, payload:longText, last_activity:integer | user_id | `database/migrations/0001_01_01_000000_create_users_table.php` |
| password_reset_tokens | Tabela | id:id, name:string, email:string, email_verified_at:timestamp, password:string, token:string, created_at:timestamp, id:string, user_id:foreignId, ip_address:string, user_agent:text, payload:longText, last_activity:integer | user_id | `database/migrations/0001_01_01_000000_create_users_table.php` |
| sessions | Tabela | id:id, name:string, email:string, email_verified_at:timestamp, password:string, token:string, created_at:timestamp, id:string, user_id:foreignId, ip_address:string, user_agent:text, payload:longText, last_activity:integer | user_id | `database/migrations/0001_01_01_000000_create_users_table.php` |
| cache | Tabela | key:string, value:mediumText, expiration:integer, owner:string | Não detectadas | `database/migrations/0001_01_01_000001_create_cache_table.php` |
| cache_locks | Tabela | key:string, value:mediumText, expiration:integer, owner:string | Não detectadas | `database/migrations/0001_01_01_000001_create_cache_table.php` |
| jobs | Tabela | id:id, queue:string, payload:longText, attempts:unsignedTinyInteger, reserved_at:unsignedInteger, available_at:unsignedInteger, created_at:unsignedInteger, id:string, name:string, total_jobs:integer, pending_jobs:integer, failed_jobs:integer, failed_job_ids:longText, options:mediumText, cancelled_at:integer, created_at:integer, finished_at:integer, uuid:string, connection:text, queue:text, exception:longText, failed_at:timestamp | Não detectadas | `database/migrations/0001_01_01_000002_create_jobs_table.php` |
| job_batches | Tabela | id:id, queue:string, payload:longText, attempts:unsignedTinyInteger, reserved_at:unsignedInteger, available_at:unsignedInteger, created_at:unsignedInteger, id:string, name:string, total_jobs:integer, pending_jobs:integer, failed_jobs:integer, failed_job_ids:longText, options:mediumText, cancelled_at:integer, created_at:integer, finished_at:integer, uuid:string, connection:text, queue:text, exception:longText, failed_at:timestamp | Não detectadas | `database/migrations/0001_01_01_000002_create_jobs_table.php` |
| failed_jobs | Tabela | id:id, queue:string, payload:longText, attempts:unsignedTinyInteger, reserved_at:unsignedInteger, available_at:unsignedInteger, created_at:unsignedInteger, id:string, name:string, total_jobs:integer, pending_jobs:integer, failed_jobs:integer, failed_job_ids:longText, options:mediumText, cancelled_at:integer, created_at:integer, finished_at:integer, uuid:string, connection:text, queue:text, exception:longText, failed_at:timestamp | Não detectadas | `database/migrations/0001_01_01_000002_create_jobs_table.php` |
| users | Tabela | role:string, deactivated_at:timestamp, must_change_password:boolean | Não detectadas | `database/migrations/2026_09_26_000001_add_role_and_status_to_users_table.php` |
| projects | Tabela | id:id, name:string, archived_at:timestamp, project_id:foreignId, user_id:foreignId | project_id, user_id | `database/migrations/2026_09_26_000002_create_projects_tables.php` |
| project_user | Tabela | id:id, name:string, archived_at:timestamp, project_id:foreignId, user_id:foreignId | project_id, user_id | `database/migrations/2026_09_26_000002_create_projects_tables.php` |
| funnels | Tabela | id:id, project_id:foreignId, name:string, archived_at:timestamp, funnel_id:foreignId, position:integer | project_id, funnel_id | `database/migrations/2026_09_27_000001_create_funnels_and_stages_tables.php` |
| stages | Tabela | id:id, project_id:foreignId, name:string, archived_at:timestamp, funnel_id:foreignId, position:integer | project_id, funnel_id | `database/migrations/2026_09_27_000001_create_funnels_and_stages_tables.php` |
| people | Tabela | id:id, project_id:foreignId, name:string, phone:string?, email:string?, created_at:timestamp, updated_at:timestamp | project_id | `database/migrations/2026_09_28_000001_create_deals_tables.php` |
| loss_reasons | Tabela | id:id, project_id:foreignId, name:string, deactivated_at:timestamp?, created_at:timestamp, updated_at:timestamp | project_id | `database/migrations/2026_09_28_000001_create_deals_tables.php` |
| deals | Tabela | id:id, funnel_id:foreignId, stage_id:foreignId, person_id:foreignId, value:decimal(12,2)?, next_step:string, next_step_date:date, status:string, loss_reason_id:foreignId?, closed_at:timestamp?, created_at:timestamp, updated_at:timestamp | funnel_id, stage_id, person_id, loss_reason_id | `database/migrations/2026_09_28_000001_create_deals_tables.php` |
| deal_user | Tabela | deal_id:foreignId, user_id:foreignId | deal_id, user_id | `database/migrations/2026_09_28_000001_create_deals_tables.php` |
| deal_notes | Tabela | id:id, deal_id:foreignId, user_id:foreignId, body:text, created_at:timestamp, updated_at:timestamp | deal_id, user_id | `database/migrations/2026_09_28_000001_create_deals_tables.php` |
<!-- specsfy:database:end -->

## Decisões, ownership e retenção

Registre finalidade, ownership, classificação, retenção, constraints e decisões
que não estejam explícitas nos schemas.

Modelo de domínio aprovado (ainda sem migrations): ver PROJECT.md — projetos, funis, etapas, pessoas (por projeto), negócios, anotações, motivos de perda, atribuição gestor↔projeto.

## SPEC-0001 — acesso, gestores e projetos

| Tabela | Campo/índice | Tipo e regra | Relação / retenção |
| --- | --- | --- | --- |
| users | role | string, padrão `gestor`; `adm` ou `gestor` na aplicação | conta preservada |
| users | deactivated_at | timestamp nulo = ativo | desativação reversível |
| users | must_change_password | boolean padrão false | troca obrigatória na senha provisória |
| projects | id, name, archived_at, created_at, updated_at | chave primária; nome único por `lower(name)`; arquivamento reversível | projeto preservado |
| project_user | project_id, user_id | chave composta, FKs cascade | N:N gestor–projeto; vínculo de inativo preservado no formulário |

Migrations: `2026_09_26_000001_add_role_and_status_to_users_table.php` e `2026_09_26_000002_create_projects_tables.php`. Retenção indefinida até política LGPD específica.

## SPEC-0002 — funis e etapas

| Tabela | Campo/índice | Tipo e regra | Relação / retenção |
| --- | --- | --- | --- |
| funnels | id, project_id, name, archived_at, created_at, updated_at | `project_id` FK; `name` obrigatório e único por `(project_id, lower(name))`; `archived_at` nulo indica ativo | N:1 `projects`, exclusão em cascata pelo FK; funil arquivado é preservado |
| stages | id, funnel_id, name, position, created_at, updated_at | `funnel_id` FK; `name` obrigatório e único por `(funnel_id, lower(name))`; `position` inteiro contínuo a partir de 1 na aplicação | N:1 `funnels`, exclusão em cascata pelo FK; etapa pode ser apagada se restar ao menos uma |

Migration: `database/migrations/2026_09_27_000001_create_funnels_and_stages_tables.php`. Índices funcionais `funnels_project_name_lower_unique` e `stages_funnel_name_lower_unique`. O funil novo e suas quatro etapas padrão são criados na mesma transação. Não há exclusão de funil nesta fatia.

## SPEC-0003 — pessoas, negócios e motivos de perda

O inventário acima foi conferido contra `2026_09_28_000001_create_deals_tables.php` e o schema PostgreSQL aplicado. A tabela abaixo detalha as constraints de cada estrutura.

| Tabela | Campos principais | Índices e integridade | Relações / retenção |
| --- | --- | --- | --- |
| `people` | `id`, `project_id`, `name`, `phone`, `email`, timestamps | `phone` contém 8–15 dígitos ou é nulo; telefone ou e-mail obrigatório; único `(project_id, phone)` e `(project_id, lower(email))` | Projeto 1:N pessoas; pessoa preservada |
| `loss_reasons` | `id`, `project_id`, `name`, `deactivated_at`, timestamps | único `(project_id, lower(name))`; desativação lógica | Projeto 1:N motivos; motivos usados continuam referenciados |
| `deals` | `id`, `funnel_id`, `stage_id`, `person_id`, `value` decimal(12,2), `next_step`, `next_step_date`, `status`, `loss_reason_id`, `closed_at`, timestamps | status `open`, `won` ou `lost`; único parcial `(funnel_id, person_id)` para `open`; índice `(funnel_id, status, next_step_date)` | Funil, etapa, pessoa e motivo; negócio preservado |
| `deal_user` | `deal_id`, `user_id` | chave primária composta | negócio N:N responsáveis |
| `deal_notes` | `id`, `deal_id`, `user_id`, `body`, timestamps | FKs; sem edição ou exclusão pela aplicação | negócio 1:N anotações, autor obrigatório |

O evento `Project::created` insere cinco motivos padrão; a migration faz o backfill em projetos anteriores. O seeder local cria cinco negócios demonstrativos idempotentes. Negócios, pessoas e anotações não possuem rota de exclusão. Retenção indefinida até política LGPD específica.
