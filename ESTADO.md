# NexusCRM — ponto de retomada

Atualizado: 2026-09-26 (noite).

## Onde estamos

- Fatia 1 (Acesso) e fatia 2 (Funis) entregues e aprovadas. Último commit de código: `4ec2dcf`.
- Fatia 3 (Quadro de negócios) em discovery: `specs/backlog/0003-quadro-negocios.md`.
  - P1: negócio tem 1+ responsáveis; quem cria vira responsável; adm gerencia.
  - P2: Ganho/Perdido saem do quadro; filtro mostra encerrados; pode reabrir.
  - Próxima pergunta: P3 (a definir: campos obrigatórios ao criar negócio / como criar a pessoa junto).

## Como retomar

1. `cd ~/projetos/nexuscrm && unset NODE_CHANNEL_FD NODE_CHANNEL_SERIALIZATION_MODE NODE_APP_INSTANCE && composer run dev` → http://localhost:8000 (adm@nexus.test / gestor@nexus.test, senha `password`, só dev).
2. Postgres: container `nexuscrm-pgsql` (127.0.0.1:54329).
3. Fluxo: discovery (1 pergunta por vez, opções numeradas) → spec Specsfy → Codex implementa (`relay.mjs --lane implement --network`) → orquestrador roda testes/tsc/lint/build + prints 1440/390 → aprovação visual.
