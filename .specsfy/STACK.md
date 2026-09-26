# Stack do sistema

Documente tecnologias estruturais e a evidência executável que confirma cada
uma. Preserve decisões humanas nas seções livres deste arquivo.

## Inventário detectado

<!-- specsfy:stack:start -->
| Camada | Tecnologia | Evidência |
| --- | --- | --- |
| Framework | Laravel | `composer.json` |
| Linguagem | PHP | `composer.json` |
| Biblioteca | React | `package.json` |
| Runtime | Node.js | `package.json` |
<!-- specsfy:stack:end -->

## Decisões e observações do projeto

| Camada | Decisão | Evidência / origem |
| --- | --- | --- |
| Servidor | Laravel 12 (PHP 8.5 local) | `composer.json` |
| Ponte UI | Inertia 2 (sem API separada) | `inertiajs/inertia-laravel` |
| Telas | React 19 + TypeScript 5 (manter; `tsc --noEmit` é gate de toda fatia) | `package.json`, `tsconfig.json` |
| Estilo | Tailwind 4 + shadcn/ui (+ ReUI conforme contrato Specsfy) | `components.json` |
| Autenticação | Nativa do Laravel (starter kit), usuários em tabelas Laravel | decisão do usuário, 2026-09-26 |
| Banco produção | Supabase gerenciado (plano Pro), usado só como Postgres | decisão do usuário, 2026-09-26 |
| Banco dev | Container `nexuscrm-pgsql` (postgres:17-alpine), 127.0.0.1:54329, db `nexuscrm` | `.env` |
| Banco testes | Mesmo container, db `nexuscrm_test`, `DatabaseTransactions` | `.env.testing` |
| WhatsApp | WAHA (não Evolution: licença da Evolution exige aviso de marca na UI) | decisão 2026-09-26 |
| WhatsApp oficial | Meta Cloud API direto, depois; provedor gravado por número | decisão 2026-09-26 |
| Agenda | Google Calendar API | pedido do usuário |
| Produção | VPS roda Laravel + WAHA; banco no Supabase | decisão 2026-09-26 |

Pendente: contrato Specsfy exige Laravel Octane + Open Swoole. Verificar
compatibilidade do Open Swoole com PHP 8.5 numa spec própria antes do deploy;
não bloqueia o desenvolvimento local.

Segurança obrigatória no Supabase: desligar a Data API (PostgREST) ou revogar
`anon`/`authenticated` do schema `public` — as tabelas Laravel não têm RLS.
