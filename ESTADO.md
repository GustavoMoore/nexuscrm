# NexusCRM — ponto de retomada

Atualizado: 2026-09-26 (noite).

## Onde estamos

- Fatia 1 (Acesso) e fatia 2 (Funis) entregues e aprovadas. Último commit de código: `4ec2dcf`.
- Fatia 3 (Quadro de negócios) em discovery: `specs/backlog/0003-quadro-negocios.md`.
  - P1: negócio tem 1+ responsáveis; quem cria vira responsável; adm gerencia.
  - P2: Ganho/Perdido saem do quadro; filtro mostra encerrados; pode reabrir.
  - P3: obrigatórios nome + telefone OU e-mail + próximo passo com data; valor opcional; pessoa existente é reaproveitada.
  - P4: repetida = telefone OU e-mail bate; negócio aberto no mesmo funil bloqueia com link (encerrado não bloqueia).
  - P5: arrasta no kanban ou menu de etapa (lista/celular); muda na hora; próximo passo intocado.
  - P6: motivos de perda por projeto, adm mantém; 5 motivos padrão ao criar projeto.
  - P7: lista com pessoa/etapa/valor/próximo passo/responsáveis; busca nome-tel-email; ordem por data do próximo passo, atrasados primeiro.
  - P8: card = pessoa, valor, próximo passo+data (atraso), iniciais; ordem igual à lista; topo com contagem e soma; busca filtra kanban.
  - P9: painel lateral (tela cheia no celular); edita tudo; anotações só acrescentam; Ganhar/Perder/Reabrir; adm mexe em responsáveis ali.
  - P10: ninguém exclui; reabrir volta à etapa de origem.
  - SPEC-0003 APROVADA (Defined) em `specs/defined/0003-quadro-negocios/spec.md`.
  - 27/09 01:40: Codex implementou T001–T037 (NÃO commitado). Gates re-rodados pelo orquestrador: 69 testes/637 asserções verdes, tsc/lint/build/diff-check ok, sem overflow 1440/390. Prints em ~/.hermes/cache/scratch/nexus-{board,sheet}-{1440,390}.png (script nexus-shots.cjs).
  - Próximo: aprovação visual do usuário → commit `feat: SPEC-0003` + marcar gates na spec + `graphify update .`.
- Acesso pelo celular: `tailscale serve` no Windows → http://gustavo.tailb201f7.ts.net:8000 (tailnet only). Usa build de produção (public/hot removido); após mudança de front, `npm run build`.

## Como retomar

1. `cd ~/projetos/nexuscrm && unset NODE_CHANNEL_FD NODE_CHANNEL_SERIALIZATION_MODE NODE_APP_INSTANCE && composer run dev` → http://localhost:8000 (adm@nexus.test / gestor@nexus.test, senha `password`, só dev).
2. Postgres: container `nexuscrm-pgsql` (127.0.0.1:54329).
3. Fluxo: discovery (1 pergunta por vez, opções numeradas) → spec Specsfy → Codex implementa (`relay.mjs --lane implement --network`) → orquestrador roda testes/tsc/lint/build + prints 1440/390 → aprovação visual.
