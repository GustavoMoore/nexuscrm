# Especificação integrada: Quadro de negócios

| Campo | Valor |
| --- | --- |
| Formato | Specsfy/2.0 |
| ID | SPEC-0003 |
| Slug | 0003-quadro-negocios |
| Status | Defined |
| Effort | 5 |
| Effort updated at | 2026-09-27 |
| Effort rationale | Cinco tabelas, regra de pessoa única com duplicidade, kanban com arrastar nativo, painel de detalhe e ajuste em apagar etapa; sem integração externa. |
| ClickUp Task | |
| Milestones | Núcleo (MVP) — fatia 3 |
| Definition Gate | Passed |
| Plan Gate | Pending |
| Delivery Gate | Pending |
| Evidence Contract | 1 |
| Interface para pessoas | Sim — gestor e adm trabalham negócios no quadro; adm mantém motivos de perda |
| Atualizada em | 2026-09-27 |

## Ato I — Definir

### 1. Problema e resultado

#### Problema

Os funis e etapas existem (SPEC-0002), mas não há onde registrar os negócios
nem acompanhar em que etapa cada um está, qual o próximo passo e quem cuida dele.

#### Resultado desejado

O gestor cria negócios ligados a uma pessoa do projeto, vê e move esses negócios
em kanban ou em lista, registra anotações e encerra como Ganho ou Perdido (com
motivo). O adm define os responsáveis de cada negócio e mantém os motivos de
perda de cada projeto.

#### Métricas de sucesso

- Criar negócio exige só 4 campos obrigatórios (qualitativo: sem tela de cadastro de pessoa antes).
- Zero pessoas duplicadas por telefone ou e-mail dentro de um projeto (índices únicos + testes).
- 100% das rotas de negócio negam acesso a quem não vê o funil (testes HTTP).

### 2. Research e esclarecimentos

#### Researchs executados

- Nenhum research externo: a fatia reutiliza a stack e os componentes das SPEC-0001 e SPEC-0002; arrastar usa a API nativa de drag-and-drop do navegador.

#### Fontes e contexto consultados

- `specs/backlog/0003-quadro-negocios.md` (P1–P10), `specs/defined/0002-funis-etapas/spec.md`, `app/Models/{Project,Funnel,Stage}.php`, `app/Policies/FunnelPolicy.php`, `app/Http/Controllers/{ProjectController,FunnelController,StageController}.php`, `routes/web.php`, `resources/js/components/`, `package.json`.

#### Documentação consultada

- Laravel 12 (validação, `scopeBindings`, transações) e Inertia 2 (`router.get` com `preserveState`, partial reload), documentação oficial, acesso 2026-09-27.

#### Artefatos de pesquisa armazenados

- Nenhum.

#### Dúvidas respondidas

- **Q**: Quem é responsável pelo negócio? → **A**: Um ou mais gestores; quem cria vira responsável; o adm adiciona/remove (só gestores atribuídos, mínimo 1). O quadro mostra todos os negócios do projeto a quem o atende (P1).
- **Q**: O que acontece com Ganho/Perdido? → **A**: Sai do quadro e da lista padrão; filtro "Ganhos / Perdidos" mostra; pode reabrir (P2).
- **Q**: O que é obrigatório ao criar? → **A**: Nome + telefone ou e-mail + próximo passo com data; valor opcional; começa na primeira etapa; pessoa existente é reaproveitada (P3).
- **Q**: Como reconhecer pessoa repetida? → **A**: Telefone ou e-mail (qualquer um que bata) no projeto; negócio aberto no mesmo funil bloqueia com link para ele; encerrado não bloqueia (P4).
- **Q**: Como mover? → **A**: Arrastar no kanban ou menu de etapa; muda na hora; próximo passo intocado (P5).
- **Q**: Motivos de perda? → **A**: Lista por projeto, mantida pelo adm; 5 padrão (P6).
- **Q**: Lista? → **A**: Pessoa, etapa, valor, próximo passo + data, responsáveis; busca nome/telefone/e-mail; ordem pela data do próximo passo, atrasados primeiro (P7).
- **Q**: Card? → **A**: Pessoa, valor, próximo passo + data com marca de atraso, iniciais; ordem igual à lista; topo da coluna com contagem e soma; busca filtra o kanban (P8).
- **Q**: Abrir negócio? → **A**: Painel lateral (tela cheia no celular); edita tudo; anotações só acrescentam; Ganhar/Perder/Reabrir; adm mexe em responsáveis ali (P9).
- **Q**: Excluir e reabrir? → **A**: Ninguém exclui; reabrir volta à etapa de origem (P10).

#### Dúvidas abertas

- Nenhuma.

### 3. Escopo e atores

#### Incluído

- Pessoas por projeto, criadas junto com o negócio (sem tela própria de pessoas).
- Negócios: criar, mover, editar, anotar, ganhar, perder, reabrir; visões kanban e lista; busca; filtro de encerrados.
- Responsáveis por negócio (N:N) geridos pelo adm.
- Motivos de perda por projeto, com 5 padrões, mantidos pelo adm.
- Apagar etapa com negócios exige etapa de destino (DEC-002 da SPEC-0002).

#### Fora de escopo

- Minha agenda com os negócios do usuário (fatia 4).
- Excluir negócio ou pessoa; tela de pessoas; importar contatos; mesclar pessoas.
- Ordenação manual dos cards, ordenação por coluna e filtros por etapa/responsável.
- Notificações, histórico de mudanças de etapa, relatórios e metas.
- Paginação (volume esperado baixo; ver NFR-002 e riscos).

#### Atores

- **Gestor**: trabalha negócios dos funis ativos dos projetos atribuídos.
- **Adm**: tudo o que o gestor faz, em qualquer projeto, mais responsáveis e motivos de perda.

### 4. Princípios e restrições do projeto

- **PR-001**: Autorização no servidor; esconder botão não é proteção.
- **PR-002**: Negócio e pessoa não são apagados nesta fatia.
- **PR-003**: Menor código que funciona; reutilizar componentes existentes; sem dependência nova (arrastar é nativo do navegador).
- **PR-004**: Testes em Postgres `nexuscrm_test` com `DatabaseTransactions`.
- **PR-005**: Regras de unicidade garantidas também no banco (índices), não só na aplicação.

### 5. Histórias de usuário

#### US-001 — Gestor cria negócio (P1)

Como gestor, quero criar um negócio informando só o essencial da pessoa e o
próximo passo, para registrar a oportunidade sem burocracia e sem duplicar contatos.

**Por que P1**: sem negócio não há quadro.
**Teste independente**: gestor cria negócio; a pessoa existe no projeto e o negócio está na primeira etapa com ele como responsável.
**Requisitos**: FR-001, FR-002, FR-008

#### US-002 — Gestor vê os negócios em kanban e lista (P1)

Como gestor, quero ver os negócios do funil por etapa ou em lista, com os
atrasados em destaque e busca, para saber o que fazer primeiro.

**Por que P1**: é a tela de trabalho diário.
**Teste independente**: quadro traz colunas com contagem, soma e cards em ordem de data.
**Requisitos**: FR-003, FR-004

#### US-003 — Gestor trabalha o negócio (P1)

Como gestor, quero mover, editar, anotar e encerrar negócios, para manter o
quadro fiel à realidade.

**Por que P1**: movimentação e encerramento são o valor do CRM.
**Teste independente**: mover muda a etapa; perder exige motivo; reabrir volta à etapa.
**Requisitos**: FR-005, FR-006, FR-007

#### US-004 — Adm governa responsáveis, motivos e etapas (P2)

Como adm, quero definir responsáveis, manter motivos de perda e apagar etapas
com negócios sem perder nenhum, para manter o controle dos projetos.

**Por que P2**: o gestor já opera sem isso; completa o controle do adm.
**Teste independente**: adm troca responsáveis, cria motivo e apaga etapa escolhendo destino.
**Requisitos**: FR-008, FR-009, FR-010

### 6. Cenários BDD de aceite

#### AC-001 — Gestor cria negócio com pessoa nova

**Cobre**: US-001, FR-001, FR-008

```gherkin
@US-001 @FR-001 @FR-008 @AC-001
Feature: Gestor cria negócio com pessoa nova

  Scenario: Criar negócio
    Given o gestor G atribuído ao projeto "Alfa" e o funil "Lançamento" com etapas padrão
    When G envia POST /projetos/{alfa}/funis/{f}/negocios com name "Ana Souza", phone "(11) 98888-7777", next_step "Ligar", next_step_date amanhã e sem value
    Then existe a pessoa "Ana Souza" no projeto "Alfa" com phone "11988887777"
    And existe um negócio aberto dessa pessoa na etapa "Novo contato" com value nulo
    And G é o único responsável do negócio
    And a resposta redireciona ao quadro com flash "Negócio criado."
```

#### AC-002 — Campos obrigatórios e valor válido

**Cobre**: US-001, FR-001, NFR-003

```gherkin
@US-001 @FR-001 @NFR-003 @AC-002
Feature: Campos obrigatórios e valor válido

  Scenario: Dados inválidos
    Given o gestor G atribuído ao projeto "Alfa" e o funil "Lançamento"
    When G cria um negócio sem phone e sem email
    Then recebe erro de validação no campo phone e nenhum negócio é criado
    When G cria um negócio sem name, sem next_step ou sem next_step_date
    Then recebe erro de validação no campo correspondente
    When G cria um negócio com value "-10" ou email "nao-e-email"
    Then recebe erro de validação no campo value ou email
```

#### AC-003 — Pessoa existente é reconhecida pelo telefone

**Cobre**: US-001, FR-002

```gherkin
@US-001 @FR-002 @AC-003
Feature: Pessoa existente é reconhecida pelo telefone

  Scenario: Mesmo telefone com outra formatação
    Given a pessoa "Ana" do projeto "Alfa" com phone "11988887777" e sem email
    And o funil "Perpétuo" do projeto "Alfa" sem negócio da Ana
    When G cria no "Perpétuo" um negócio com name "Ana S.", phone "11 98888-7777" e email "ana@x.com"
    Then nenhuma pessoa nova é criada e o negócio pertence à "Ana"
    And o nome da Ana continua "Ana" e o email vazio passa a ser "ana@x.com"
```

#### AC-004 — Pessoa existente é reconhecida pelo e-mail

**Cobre**: US-001, FR-002

```gherkin
@US-001 @FR-002 @AC-004
Feature: Pessoa existente é reconhecida pelo e-mail

  Scenario: Mesmo e-mail com maiúsculas
    Given a pessoa "Bia" do projeto "Alfa" com email "bia@x.com"
    When G cria no funil "Perpétuo" um negócio com name "Bia", email "BIA@X.com"
    Then o negócio pertence à pessoa "Bia" existente
    But a mesma combinação no projeto "Beta" cria uma pessoa nova no "Beta"
```

#### AC-005 — Negócio aberto no mesmo funil bloqueia a criação

**Cobre**: US-001, FR-002, FR-001, NFR-003

```gherkin
@US-001 @FR-002 @FR-001 @NFR-003 @AC-005
Feature: Negócio aberto no mesmo funil bloqueia a criação

  Scenario: Duplicidade no funil
    Given a pessoa "Ana" com um negócio aberto N1 no funil "Lançamento"
    When G cria no "Lançamento" um negócio com o telefone da Ana
    Then recebe erro de validação no campo person com a mensagem "Esta pessoa já tem um negócio aberto neste funil."
    And a sessão flash traz existing_deal_id = N1 para o link
    And nenhum negócio novo existe
    Given N1 marcado como Ganho
    When G repete a criação
    Then um negócio novo aberto é criado para a Ana
```

#### AC-006 — Telefone e e-mail de pessoas diferentes são recusados

**Cobre**: US-001, FR-002

```gherkin
@US-001 @FR-002 @AC-006
Feature: Telefone e e-mail de pessoas diferentes são recusados

  Scenario: Conflito
    Given no projeto "Alfa" a pessoa "Ana" com phone "11988887777" e a pessoa "Bia" com email "bia@x.com"
    When G cria um negócio com phone "11988887777" e email "bia@x.com"
    Then recebe erro de validação no campo email "Telefone e e-mail pertencem a pessoas diferentes."
```

#### AC-007 — Quadro kanban entrega colunas, ordem, totais e atraso

**Cobre**: US-002, FR-003, FR-004, NFR-002

```gherkin
@US-002 @FR-003 @FR-004 @NFR-002 @AC-007
Feature: Quadro kanban entrega colunas, ordem, totais e atraso

  Scenario: Props do kanban
    Given o relógio fixado com Carbon::setTestNow em 2026-10-01 12:00 America/Sao_Paulo
    And o funil "Lançamento" com negócios abertos na etapa "Novo contato": X (data ontem, valor 100), Y (data hoje, valor nulo), Z (data amanhã, valor 50,50)
    And um negócio Ganho W na mesma etapa
    When G abre GET /projetos/{p}/funis/{f}/negocios
    Then o componente é negocios/index
    And stages traz as 4 etapas em ordem, cada uma com count e total
    And a etapa "Novo contato" tem count 3, total "150.50" e deals na ordem X, Y, Z
    And X tem overdue verdadeiro; Y e Z têm overdue falso
    And cada deal traz person.name, value, next_step, next_step_date e assignees com name e initials
    And W não aparece
```

#### AC-008 — Busca por nome, telefone ou e-mail

**Cobre**: US-002, FR-004, FR-003

```gherkin
@US-002 @FR-004 @FR-003 @AC-008
Feature: Busca por nome, telefone ou e-mail

  Scenario: Filtrar
    Given negócios abertos de "Ana Souza" (11988887777), "Bia" (bia@x.com) e "Caio"
    When G abre o quadro com q "souza"
    Then só o negócio da Ana aparece, e os totais das colunas refletem o filtro
    When G abre com q "98888-77"
    Then só o negócio da Ana aparece
    When G abre com q "BIA@"
    Then só o negócio da Bia aparece
```

#### AC-009 — Lista e filtro de encerrados

**Cobre**: US-002, FR-004, FR-007

```gherkin
@US-002 @FR-004 @FR-007 @AC-009
Feature: Lista e filtro de encerrados

  Scenario: Visões
    Given o funil com os negócios abertos A e B e os encerrados G (Ganho) e P (Perdido, motivo "Preço")
    When G abre o quadro com view "lista"
    Then deals traz A e B em uma única lista ordenada por next_step_date, com stage.name
    When G abre com closed "1"
    Then a visão é lista (closed força lista) e deals traz somente P e G, ordenados por closed_at mais recente primeiro, com status e loss_reason.name, e a busca q continua valendo
```

#### AC-010 — Mover negócio de etapa

**Cobre**: US-003, FR-005, NFR-003, NFR-002

```gherkin
@US-003 @FR-005 @NFR-003 @NFR-002 @AC-010
Feature: Mover negócio de etapa

  Scenario: Arrastar ou escolher etapa
    Given o negócio N aberto em "Novo contato" com next_step "Ligar"
    When G envia PATCH /projetos/{p}/funis/{f}/negocios/{n} só com stage_id de "Proposta"
    Then N está em "Proposta" e next_step continua "Ligar"
    When G envia stage_id de uma etapa de outro funil
    Then recebe erro de validação em stage_id e N continua em "Proposta"
```

#### AC-011 — Editar negócio e dados da pessoa

**Cobre**: US-003, FR-006, FR-002

```gherkin
@US-003 @FR-006 @FR-002 @AC-011
Feature: Editar negócio e dados da pessoa

  Scenario: Editar no painel
    Given o negócio N da pessoa "Ana" e a pessoa "Bia" com phone "11977776666" no mesmo projeto
    When G envia PATCH de N com next_step "Enviar proposta", next_step_date depois de amanhã, value "900", name "Ana Souza"
    Then N e a pessoa refletem os novos valores
    When G envia PATCH de N com phone "11977776666"
    Then recebe erro de validação em phone e a Ana não muda
    When G envia PATCH de N com phone e email vazios
    Then recebe erro de validação em phone
```

#### AC-012 — Anotações só acrescentam

**Cobre**: US-003, FR-006

```gherkin
@US-003 @FR-006 @AC-012
Feature: Anotações só acrescentam

  Scenario: Anotar
    Given o negócio N
    When G envia POST /projetos/{p}/funis/{f}/negocios/{n}/anotacoes com body "Pediu desconto"
    Then N tem a anotação com autor G e data de criação
    When G envia body vazio
    Then recebe erro de validação em body
    And não existe rota PATCH nem DELETE de anotação (405/404)
```

#### AC-013 — Ganhar tira o negócio do quadro

**Cobre**: US-003, FR-007, FR-003

```gherkin
@US-003 @FR-007 @FR-003 @AC-013
Feature: Ganhar tira o negócio do quadro

  Scenario: Ganhar
    Given o negócio aberto N na etapa "Proposta"
    When G envia POST .../negocios/{n}/ganhar
    Then N tem status "won", closed_at preenchido e stage_id "Proposta" preservado
    And N não aparece no quadro nem na lista padrão
```

#### AC-014 — Perder exige motivo ativo do projeto

**Cobre**: US-003, FR-007, FR-009

```gherkin
@US-003 @FR-007 @FR-009 @AC-014
Feature: Perder exige motivo ativo do projeto

  Scenario: Perder
    Given o negócio aberto N no projeto "Alfa"
    When G envia POST .../negocios/{n}/perder sem loss_reason_id, com um motivo do projeto "Beta" ou com um motivo desativado do "Alfa"
    Then recebe erro de validação em loss_reason_id e N continua aberto
    When G envia com o motivo ativo "Preço" do "Alfa"
    Then N tem status "lost" e loss_reason "Preço"
```

#### AC-015 — Reabrir volta à etapa de origem

**Cobre**: US-003, FR-007, FR-002

```gherkin
@US-003 @FR-007 @FR-002 @AC-015
Feature: Reabrir volta à etapa de origem

  Scenario: Reabrir
    Given o negócio N da Ana, Perdido na etapa "Proposta" com motivo "Preço"
    When G envia POST .../negocios/{n}/reabrir
    Then N tem status "open", etapa "Proposta", loss_reason nulo e closed_at nulo
    Given N encerrado de novo e um negócio aberto N2 da Ana no mesmo funil
    When G tenta reabrir N
    Then recebe erro de validação em person, a sessão flash traz existing_deal_id = N2, e N continua encerrado
```

#### AC-016 — Adm gerencia responsáveis

**Cobre**: US-004, FR-008, NFR-001

```gherkin
@US-004 @FR-008 @NFR-001 @AC-016
Feature: Adm gerencia responsáveis

  Scenario: Responsáveis
    Given o negócio N com responsável G1 e os gestores G2 (atribuído ao projeto) e G3 (não atribuído)
    When o adm envia PUT .../negocios/{n}/responsaveis com user_ids [G1, G2]
    Then N tem responsáveis G1 e G2
    When o adm envia user_ids [] ou [G3]
    Then recebe erro de validação em user_ids e os responsáveis não mudam
    Given G1 desatribuído do projeto e G2 desativado depois de virar responsável
    When o adm envia user_ids [G1, G2]
    Then a lista é aceita (IDs já presentes são mantidos)
    And um gestor desativado que ainda não é responsável é recusado
    When G1 envia PUT de responsáveis
    Then recebe 403
```

#### AC-017 — Adm cria negócio escolhendo responsáveis

**Cobre**: US-004, FR-008, FR-001

```gherkin
@US-004 @FR-008 @FR-001 @AC-017
Feature: Adm cria negócio escolhendo responsáveis

  Scenario: Criação pelo adm
    Given o adm, o projeto "Alfa" com o gestor atribuído G1
    When o adm cria um negócio sem user_ids
    Then recebe erro de validação em user_ids
    When o adm cria com user_ids [G1]
    Then o negócio é criado com responsável G1 e o adm não é responsável
    And quando um gestor cria, user_ids enviado é ignorado e o criador é o responsável
```

#### AC-018 — Projeto nasce com motivos de perda padrão

**Cobre**: US-004, FR-009

```gherkin
@US-004 @FR-009 @AC-018
Feature: Projeto nasce com motivos de perda padrão

  Scenario: Padrões
    When o adm cria o projeto "Gama"
    Then "Gama" tem os motivos ativos "Preço", "Sem resposta", "Comprou de outro", "Sem interesse", "Outro"
    And um projeto criado direto pelo model (Project::create) também recebe os 5 motivos
```

#### AC-019 — Adm mantém motivos de perda

**Cobre**: US-004, FR-009, NFR-001

```gherkin
@US-004 @FR-009 @NFR-001 @AC-019
Feature: Adm mantém motivos de perda

  Scenario: Manter lista
    Given o projeto "Alfa" com os motivos padrão e o negócio N Perdido por "Preço"
    When o adm cria "Prazo", renomeia "Preço" para "Preço alto" e desativa "Preço alto"
    Then "Prazo" existe; o motivo desativado não aparece em loss_reasons do quadro
    And N continua exibindo loss_reason "Preço alto"
    When o adm cria "prazo" de novo
    Then recebe erro de validação em name
    When um gestor tenta qualquer dessas ações
    Then recebe 403
```

#### AC-020 — Apagar etapa com negócios exige destino

**Cobre**: US-004, FR-010, NFR-003

```gherkin
@US-004 @FR-010 @NFR-003 @AC-020
Feature: Apagar etapa com negócios exige destino

  Scenario: Realocar
    Given a etapa "Proposta" com um negócio aberto A e um Perdido P
    When o adm envia DELETE .../etapas/{proposta} sem destination_stage_id
    Then recebe erro de validação em destination_stage_id e a etapa continua
    When o adm envia com destination_stage_id de "Negociação"
    Then "Proposta" deixa de existir e A e P estão em "Negociação"
```

#### AC-021 — Destino inválido e etapa vazia

**Cobre**: US-004, FR-010

```gherkin
@US-004 @FR-010 @AC-021
Feature: Destino inválido e etapa vazia

  Scenario: Limites do destino
    Given a etapa "Proposta" com negócios e o funil "Perpétuo" do mesmo projeto
    When o adm envia destination_stage_id da própria "Proposta" ou de uma etapa do "Perpétuo"
    Then recebe erro de validação em destination_stage_id
    Given a etapa "Qualificação" sem negócios
    When o adm a apaga sem destination_stage_id
    Then ela é apagada como na SPEC-0002
```

#### AC-022 — Isolamento de negócios

**Cobre**: NFR-001, FR-005, FR-006

```gherkin
@NFR-001 @FR-005 @FR-006 @AC-022
Feature: Isolamento de negócios

  Scenario: Acesso negado
    Given o gestor A atribuído só ao projeto "Alfa" e o negócio N do funil F no projeto "Beta"
    When A abre o quadro de F ou envia PATCH, ganhar, perder, reabrir ou anotação em N
    Then recebe 403 e nada muda
    Given o negócio M do funil F1 do "Alfa"
    When A envia PATCH /projetos/{alfa}/funis/{f2}/negocios/{m}
    Then recebe 404
    Given o funil F1 arquivado
    When A abre o quadro de F1
    Then recebe 404
    When o adm abre o quadro de F1
    Then vê os negócios com can.manage falso
    And qualquer POST/PATCH/PUT de negócio em F1 pelo adm retorna 403
```

#### AC-023 — Painel do negócio entrega o detalhe

**Cobre**: US-003, FR-006, NFR-002

```gherkin
@US-003 @FR-006 @NFR-002 @AC-023
Feature: Painel do negócio entrega o detalhe

  Scenario: Abrir negócio
    Given o negócio N com 2 anotações e responsáveis G1 e G2
    When G abre o quadro com negocio={n}
    Then a prop deal traz person (name, phone, email), stage_id, value, next_step, next_step_date, status, loss_reason, assignees, notes do mais novo ao mais antigo com author e created_at
    And loss_reasons traz só os motivos ativos do projeto e project_managers traz os gestores atribuídos
    And can.manage_assignees é verdadeiro só para o adm
    When G abre com negocio de outro funil
    Then deal é nulo
```

#### AC-024 — Reabrir após a etapa ser apagada

**Cobre**: US-004, FR-010, FR-007

```gherkin
@US-004 @FR-010 @FR-007 @AC-024
Feature: Reabrir após a etapa ser apagada

  Scenario: Etapa realocada
    Given o negócio P Perdido na etapa "Proposta"
    When o adm apaga "Proposta" com destino "Negociação"
    And G reabre P
    Then P está aberto na etapa "Negociação"
```

#### AC-025 — Negócio encerrado não se move nem se edita

**Cobre**: US-003, FR-005, FR-007

```gherkin
@US-003 @FR-005 @FR-007 @AC-025
Feature: Negócio encerrado não se move nem se edita

  Scenario: Encerrado
    Given o negócio N Ganho
    When G envia PATCH de N com stage_id, next_step, value ou name
    Then recebe erro de validação em status e nem N nem a pessoa mudam
    When G envia ganhar ou perder em N
    Then recebe erro de validação em status
    But anotações continuam permitidas em N
```

### 7. Requisitos

#### Funcionais

- **FR-001**: Quem vê o funil (FunnelPolicy::view) deve criar negócio nele informando name (obrigatório, ≤ 255), phone e/ou email (ao menos um; email válido ≤ 255; phone com 8 a 15 dígitos após remover o resto), next_step (obrigatório, ≤ 255), next_step_date (data obrigatória) e value opcional (decimal ≥ 0, 2 casas). O negócio nasce aberto na etapa de menor position do funil.
- **FR-002**: A pessoa é única por projeto por telefone (só dígitos) e por e-mail (minúsculo). Ao criar/editar: se phone ou email bater com uma pessoa do projeto, ela é reaproveitada (campos vazios dela são completados, os preenchidos não mudam); se phone e email baterem com pessoas diferentes, erro. Uma pessoa tem no máximo 1 negócio aberto por funil: criar ou reabrir nessa situação é recusado com erro e o id do negócio aberto em flash `existing_deal_id`.
- **FR-003**: O quadro de um funil mostra só negócios abertos, agrupados por etapa na ordem das etapas; cada coluna informa contagem e soma dos valores; cards e lista ordenados por next_step_date e id; negócio com next_step_date anterior a hoje (America/Sao_Paulo) é marcado como atrasado.
- **FR-004**: A visão lista mostra pessoa, etapa, valor, próximo passo + data e responsáveis dos negócios abertos; a busca `q` (sem diferenciar maiúsculas) filtra por nome, e-mail ou dígitos do telefone, em kanban, lista e encerrados; o filtro de encerrados (`closed=1`) sempre usa a visão lista e mostra Ganhos e Perdidos com status e motivo, ordenados por closed_at mais recente primeiro. Busca por telefone só é aplicada quando q tem 3 ou mais dígitos; `%` e `_` de q são escapados.
- **FR-005**: Um negócio aberto muda de etapa por PATCH com stage_id de uma etapa do mesmo funil, sem alterar outros campos.
- **FR-006**: O painel do negócio permite editar next_step, next_step_date, value, stage_id e dados da pessoa com os mesmos nomes de campo da criação (name, phone, email; ao menos phone ou email; phone ou email de outra pessoa do projeto → erro) e acrescentar anotações (body obrigatório, ≤ 5000) com autor e data; anotações não são editadas nem apagadas.
- **FR-007**: Um negócio aberto pode ser marcado como Ganho ou Perdido (Perdido exige motivo ativo do projeto); encerrado registra closed_at, preserva a etapa e recusa qualquer PATCH, inclusive dos dados da pessoa (anotações continuam). Reabrir volta a aberto na etapa atual do negócio, limpando motivo e closed_at.
- **FR-008**: Cada negócio tem ao menos 1 responsável. Gestor que cria vira o responsável; adm que cria informa user_ids. Só o adm substitui a lista de responsáveis. IDs novos na lista precisam ser gestor ativo e atribuído ao projeto; IDs já responsáveis podem ser mantidos mesmo se desativados ou desatribuídos depois.
- **FR-009**: Cada projeto tem motivos de perda (nome obrigatório, ≤ 255, único no projeto sem diferenciar maiúsculas). Projeto novo e projetos existentes recebem Preço, Sem resposta, Comprou de outro, Sem interesse, Outro. O adm cria, renomeia e desativa; desativado sai da escolha mas continua nos negócios que o usam.
- **FR-010**: Apagar uma etapa que tem negócios (abertos ou encerrados) exige destination_stage_id de outra etapa do mesmo funil; todos os negócios são movidos antes da remoção, na mesma transação. Etapa sem negócios segue a SPEC-0002.

#### Não funcionais

- **NFR-001**: Autorização no servidor: funil ou projeto arquivado deixa o quadro só leitura para todos (o adm ainda vê; qualquer escrita de negócio retorna 403); negócios herdam FunnelPolicy::view (projeto visível + funil ativo para gestor); responsáveis e motivos só no grupo `can:adm`; rotas aninhadas com `scopeBindings` (404 para IDs trocados). **Verificação**: testes HTTP (AC-016, AC-019, AC-022).
- **NFR-002**: Tela operável por teclado e leitor de tela: mover sempre disponível por menu além do arrastar; atraso indicado por texto e ícone, não só cor; painel com foco preso e Esc; listagem com eager loading (sem N+1). **Verificação**: props Inertia (AC-007, AC-023) e inspeção manual no VISUAL.
- **NFR-003**: Nenhuma operação deixa dado inconsistente: criação, reabertura e apagar etapa rodam em transação; o índice parcial de negócio aberto por pessoa e funil é a garantia final contra duplicidade concorrente. **Verificação**: AC-002, AC-005, AC-010, AC-020.

#### Erros e casos-limite

- Sem phone e sem email → erro no campo phone.
- phone e email de pessoas diferentes → erro no campo email.
- Pessoa com negócio aberto no funil → erro + link; encerrado não bloqueia.
- stage_id de outro funil → erro de validação.
- Perder sem motivo, com motivo desativado ou de outro projeto → erro.
- Mover/editar/ganhar/perder negócio encerrado → erro no campo status.
- Reabrir com outro negócio aberto da mesma pessoa no funil → erro + link.
- Apagar etapa com negócios sem destino, ou destino inválido → erro.
- Funil arquivado → quadro 404 para gestor e só leitura para o adm (escrita 403); projeto arquivado → gestor já não vê (SPEC-0001).
- Responsável desativado ou desatribuído continua listado e pode ser mantido; não pode ser adicionado como novo.

## Ato II — Projetar e provar

### 8. Plano técnico

#### Contexto existente

- SPEC-0001/0002 entregues: `Project`, `Funnel`, `Stage`, `ProjectPolicy`, `FunnelPolicy`, controllers, `Gate adm`, componentes `PageHeader`, `FlashMessage`, `EmptyState`, `ConfirmDialog`, `Table`, painéis `*-form-sheet.tsx`, shadcn `Select`, `Checkbox`, `Avatar`, `Badge`, `ToggleGroup`, `Sheet`, `Dialog`.

#### Arquitetura e módulos

- Sem Policy nova: leitura usa `Gate::authorize('view', $funnel)` (mais a checagem de funil arquivado já usada em FunnelController::show); toda escrita de negócio também exige funil e projeto ativos (checagem privada no DealController → 403); ações do adm ficam no grupo `can:adm`.
- Um controller por recurso: `DealController` (index, store, update, note, win, lose, reopen, assignees) e `LossReasonController`.
- Resolução de pessoa em um método privado do `DealController`, usado por store e update.
- Página única `negocios/index` com estado na query string (`view`, `q`, `closed`, `negocio`), para o link de duplicidade e o botão voltar funcionarem.

#### Migrations

- `2026_09_28_000001_create_deals_tables` (aditiva) — ver seção 9. Aplicar em `nexuscrm` e `nexuscrm_test` com `migrate`.

#### Models

- `Person`, `Deal`, `DealNote`, `LossReason`; relações novas em `Project` (people, lossReasons, motivos padrão no evento `created`), `Funnel` (deals) e `Stage` (deals).

#### Controllers e casos de uso

- `DealController` e `LossReasonController` (novos); `ProjectController::show` envia loss_reasons ao adm; `StageController::destroy` passa a receber `Request` e realoca.

#### Views e experiência

- `pages/negocios/index.tsx` (novo), `components/deal-form-sheet.tsx`, `components/deal-sheet.tsx`, `components/loss-reason-form-sheet.tsx`; ajustes em `pages/projetos/show.tsx` e `pages/funis/show.tsx`.

#### Queries e repositórios

- Um select de negócios do funil com `with(['person', 'users', 'stage', 'lossReason'])`; agrupamento, contagem e soma por etapa feitos em PHP sobre o resultado (mesmo filtro da busca).
- Busca: `whereHas('person', ...)` com `ilike` em name/email e `like` em phone com os dígitos de `q`.

#### Jobs e processamento assíncrono

- Não aplicável.

#### Estrutura de arquivos

```text
database/migrations/2026_09_28_000001_create_deals_tables.php
app/Models/{Person,Deal,DealNote,LossReason}.php
app/Http/Controllers/{DealController,LossReasonController}.php
app/Http/Middleware/HandleInertiaRequests.php
resources/js/pages/negocios/index.tsx
resources/js/components/{deal-form-sheet,deal-sheet,loss-reason-form-sheet}.tsx
tests/Feature/Deals/{DealTest,DealBoardTest,LossReasonTest,DealStageTest}.php
```

### 9. Modelo de dados

#### Entidades

| Entidade | Identidade | Atributos e regras | Relações |
| --- | --- | --- | --- |
| Person (`people`) | id | project_id FK cascade; name ≤ 255; phone nulo com check `phone ~ '^[0-9]{8,15}$'`; email nulo; check phone ou email não nulos; únicos `(project_id, phone)` e `(project_id, lower(email))` (NULL não colide no Postgres) | N:1 Project; 1:N Deal |
| Deal (`deals`) | id | funnel_id FK cascade; stage_id FK restrict; person_id FK restrict; value decimal(12,2) nulo; next_step ≤ 255; next_step_date date; status `open`/`won`/`lost` (check); loss_reason_id FK nulo restrict; closed_at nulo; timestamps; único parcial `(funnel_id, person_id) where status = 'open'`; índice `(funnel_id, status, next_step_date)` | N:1 Funnel, Stage, Person, LossReason; N:N User; 1:N DealNote |
| deal_user | (deal_id, user_id) | FKs cascade | N:N Deal–User |
| DealNote (`deal_notes`) | id | deal_id FK cascade; user_id FK restrict; body text; timestamps | N:1 Deal, User |
| LossReason (`loss_reasons`) | id | project_id FK cascade; name ≤ 255; deactivated_at nulo; único `(project_id, lower(name))` | N:1 Project |

#### Estados e transições

| Entidade | Estado atual | Evento | Próximo estado | Invariantes |
| --- | --- | --- | --- | --- |
| Deal | open | ganhar | won | closed_at preenchido; etapa preservada |
| Deal | open | perder (motivo ativo) | lost | loss_reason_id e closed_at preenchidos |
| Deal | won/lost | reabrir | open | sem outro aberto da pessoa no funil; motivo e closed_at nulos |
| LossReason | ativo | desativar | inativo | continua referenciado pelos negócios |
| Stage | com negócios | apagar com destino | removida | todos os negócios movidos ao destino |

#### Migração e retenção

- Migration aditiva; insere os 5 motivos padrão para projetos existentes. Negócios, pessoas e anotações não são apagados (PR-002); retenção indefinida até política LGPD específica.

### 10. Interfaces e contratos

#### Interface para pessoas

- **Há interface para pessoas**: Sim — quadro de negócios (kanban/lista), painéis de novo negócio e detalhe, motivos de perda no projeto, destino ao apagar etapa.

#### Stack e convenções de interface

- React 19 + TypeScript via Inertia 2; `useForm` e `router`; shadcn/ui já instalado (Sheet, Dialog, Select, Checkbox, Avatar, Badge, ToggleGroup, Table, Input, Label); ícones lucide (`Plus`, `Search`, `AlertTriangle`, `GripVertical`, `Trophy`, `XCircle`, `RotateCcw`); layout `AppSidebarLayout`; DESIGNSYSTEM.MD e INTERFACE.md. Sem dependência nova: arrastar com `draggable` + `onDragStart/onDragOver/onDrop` nativos.

#### Telas e responsabilidades

- **Quadro do funil** (`/projetos/{p}/funis/{f}/negocios`, adm e gestores atribuídos): kanban/lista, busca, encerrados, novo negócio, painel do negócio.
- **Detalhe do projeto** (existente): seção Motivos de perda (adm); navegação da SPEC-0002 preservada.
- **Funil** (existente): botão "Abrir quadro"; apagar etapa com escolha de destino.

#### Fluxo de informação e navegação

- Projetos → projeto → funil (linha) → Funil → "Abrir quadro" → Quadro. Quadro → "Etapas" → página do funil.
- Card/linha → `?negocio=<id>` abre o painel; fechar remove o parâmetro.
- Novo negócio → painel → salvar → quadro com flash; duplicidade → erro + "Ver negócio".
- Breadcrumbs: `Projetos / <projeto> / <funil> / Quadro`.

#### Menus e navegação principal

- Sem novo item de menu (acesso pelo projeto). Minha agenda continua vazia até a fatia 4.

#### Formulários e ações

- **Novo negócio** (painel): Nome*, Telefone, E-mail (um dos dois*), Valor (R$), Próximo passo*, Data* (input date), Responsáveis (adm). Ação "Criar negócio".
- **Detalhe** (painel): mesmos campos editáveis + Etapa (Select); Anotações (textarea + "Adicionar"); Ganhar; Perder (Dialog com motivo obrigatório); Reabrir (encerrados); Responsáveis (adm, Checkbox).
- **Motivo de perda** (painel): Nome*. Desativar com ConfirmDialog.
- **Apagar etapa**: ConfirmDialog com Select "Mover negócios para" quando a etapa tem negócios.
- Erros no campo; painel permanece aberto.

#### Composição e disposição

- Cabeçalho: `PageHeader` (título do funil, ações "Novo negócio", "Etapas"); barra: busca, ToggleGroup Quadro/Lista, filtro Ganhos/Perdidos.
- Kanban: colunas de largura fixa (~288px) em rolagem horizontal; em 390px uma coluna por vez com scroll-snap.
- Lista: `Table` em largura total com rolagem horizontal no mobile.

#### Blocos React e componentes selecionados

| Tela | Bloco React | Responsabilidade | Arquivo previsto | Componente ou composição | Origem | Reuso ou extensão |
| --- | --- | --- | --- | --- | --- | --- |
| Quadro | página | composição, query string, arrastar | `pages/negocios/index.tsx` | `ToggleGroup`, `Input`, `Table`, `Badge`, `Avatar`, `Select` | shadcn/ui | novo |
| Quadro | DealFormSheet | criar negócio | `components/deal-form-sheet.tsx` | `Sheet`, `Input`, `Label`, `Checkbox`, `InputError` | shadcn/ui | novo, segue project-form-sheet |
| Quadro | DealSheet | detalhe, anotações, encerrar | `components/deal-sheet.tsx` | `Sheet`, `Select`, `Dialog`, `Checkbox`, `Input` | shadcn/ui | novo |
| Projeto | LossReasonFormSheet | criar/renomear motivo | `components/loss-reason-form-sheet.tsx` | `Sheet`, `Input`, `Label` | shadcn/ui | novo, segue stage-form-sheet |
| Todas | PageHeader, FlashMessage, EmptyState, ConfirmDialog | cabeçalho, feedback, vazio, confirmação | `components/*` | — | SPEC-0001 | reuso; ConfirmDialog recebe conteúdo extra (Select de destino) |

#### Estados e acessibilidade

- Vazio: funil sem negócios → "Nenhum negócio ainda" com "Novo negócio"; busca sem resultado → "Nada encontrado para <q>"; coluna vazia mostra área de soltar.
- Atrasado: Badge "Atrasado" com ícone, além da cor.
- Carregando: botões desabilitados durante envio (`processing`).
- Anotações: `textarea` nativo com as classes do `Input` (não há `ui/textarea`).
- Teclado: card focável (Enter abre o painel); "Mover para" via Select; arrastar é atalho, não caminho único.
- Sucesso: `flash.success` ("Negócio criado.", "Negócio movido.", "Negócio ganho." etc.).

#### Contrato CRUD

- Todas as telas usam o mesmo `PageHeader`. A lista usa o `DataGrid` do projeto (materializado como `Table` shadcn na SPEC-0001) em largura total, com coluna `ID`; a linha abre o painel do negócio, onde fica editar. Negócio não tem apagar (PR-002): o equivalente é marcar Perdido. Motivo de perda: "apagar" é **Desativar**.

#### Revisão visual durante o desenvolvimento

- Durante a implementação e no Delivery Gate, conferir em 1440px e 390px, nos estados vazio, com dados, atrasado, erro de validação, duplicidade e sem permissão: bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra).
- Registrar a forma de conferência, viewport, estados, achados e ajustes em cada tarefa com interface; tarefas sem interface registram `Não aplicável` com motivo.

#### APIs expostas

- Rotas web Inertia (grupo `auth`, `scopeBindings`): `GET|POST /projetos/{project}/funis/{funnel}/negocios`; `PATCH .../negocios/{deal}`; `POST .../negocios/{deal}/anotacoes|ganhar|perder|reabrir`.
- Grupo `can:adm`: `PUT .../negocios/{deal}/responsaveis`; `POST /projetos/{project}/motivos-perda`; `PATCH /projetos/{project}/motivos-perda/{lossReason}`; `POST .../motivos-perda/{lossReason}/desativar`; `DELETE .../etapas/{stage}` passa a aceitar `destination_stage_id`.

#### APIs externas utilizadas

- Nenhuma.

#### Documentação das APIs consultadas

- Não aplicável.

#### Eventos e outros contratos

- Flash `existing_deal_id` (inteiro) compartilhado em `flash.existing_deal_id` pelo HandleInertiaRequests, usado pelo link "Ver negócio".

### 11. Estratégia TDD

- **Integração/contrato**: testes HTTP por rota e papel (403/404/422), `assertInertia` para props e `assertSessionHas('existing_deal_id')`.
- **BDD/aceite**: Gherkin da seção 6 orienta os casos; sem `.feature`.
- **Runner TDD**: PHPUnit (`php artisan test`) em `nexuscrm_test`, todos os testes desta fatia em `tests/Feature/Deals/` (IDs AC colidem com as specs anteriores; a rastreabilidade roda só nessa pasta); métodos `test_a_c_NNN_*` (forma que o Pint produz) com marcador `// SPECSFY: AC-NNN`; filtro que não roda nenhum teste conta como falha.
- **E2E**: Não aplicável; arrastar e teclado verificados manualmente no VISUAL.

#### Evidência RED-GREEN-REFACTOR

- RED registrado em `storage/logs/red-0003.txt` pelo implementador.

### 12. Plano de testes e rastreabilidade

| AC | Arquivo de teste | Tarefas |
| --- | --- | --- |
| AC-001 | tests/Feature/Deals/DealTest.php | T001 |
| AC-002 | tests/Feature/Deals/DealTest.php | T002 |
| AC-003 | tests/Feature/Deals/DealTest.php | T003 |
| AC-004 | tests/Feature/Deals/DealTest.php | T004 |
| AC-005 | tests/Feature/Deals/DealTest.php | T005 |
| AC-006 | tests/Feature/Deals/DealTest.php | T006 |
| AC-007 | tests/Feature/Deals/DealBoardTest.php | T007 |
| AC-008 | tests/Feature/Deals/DealBoardTest.php | T008 |
| AC-009 | tests/Feature/Deals/DealBoardTest.php | T009 |
| AC-010 | tests/Feature/Deals/DealTest.php | T010 |
| AC-011 | tests/Feature/Deals/DealTest.php | T011 |
| AC-012 | tests/Feature/Deals/DealTest.php | T012 |
| AC-013 | tests/Feature/Deals/DealTest.php | T013 |
| AC-014 | tests/Feature/Deals/DealTest.php | T014 |
| AC-015 | tests/Feature/Deals/DealTest.php | T015 |
| AC-016 | tests/Feature/Deals/DealTest.php | T016 |
| AC-017 | tests/Feature/Deals/DealTest.php | T017 |
| AC-018 | tests/Feature/Deals/LossReasonTest.php | T018 |
| AC-019 | tests/Feature/Deals/LossReasonTest.php | T019 |
| AC-020 | tests/Feature/Deals/DealStageTest.php | T020 |
| AC-021 | tests/Feature/Deals/DealStageTest.php | T021 |
| AC-022 | tests/Feature/Deals/DealTest.php | T022 |
| AC-023 | tests/Feature/Deals/DealBoardTest.php | T023 |
| AC-024 | tests/Feature/Deals/DealStageTest.php | T024 |
| AC-025 | tests/Feature/Deals/DealTest.php | T025 |

### 13. Validações

#### Gate do Ato I — Definição

- **Resultado**: Passed (2026-09-27)
- **Comando**: `node .agents/skills/specsfy-04-validate/scripts/validate_spec.mjs specs/defined/0003-quadro-negocios/spec.md`
- **Achados**: validação estrutural sem erros; revisão independente (18 achados) incorporada; aprovação do responsável em 2026-09-27.

#### Gate do Ato II — Plano

- **Resultado**: Pending
- **Comando**: `node .agents/skills/specsfy-05-tasks/scripts/validate_tasks.mjs specs/defined/0003-quadro-negocios/spec.md`
- **Achados**: Pending.

#### Gate do Ato III — Entrega

- **Resultado**: Pending
- **Comando**: `node .agents/skills/specsfy-06-tdd-bdd/scripts/check_traceability.mjs specs/defined/0003-quadro-negocios/spec.md tests/Feature/Deals`
- **Achados**: Pending.

### 14. Tarefas

Formato:
`- [ ] TNNN [P?] [TIPO] [US-NNN?] Ação com caminho — Refs: IDs — Depends: IDs|none`

#### Fase 1 — RED TDD informado pelo BDD

- [ ] T001 [P] [TEST] [TDD] [US-001] Derivar do AC-001 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-001, FR-001, FR-008, AC-001 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-001; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-001`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_001` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T002 [P] [TEST] [TDD] [US-001] Derivar do AC-002 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-001, FR-001, NFR-003, AC-002 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-002; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-002`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_002` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T003 [P] [TEST] [TDD] [US-001] Derivar do AC-003 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-001, FR-002, AC-003 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-003; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-003`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_003` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T004 [P] [TEST] [TDD] [US-001] Derivar do AC-004 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-001, FR-002, AC-004 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-004; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-004`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_004` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T005 [P] [TEST] [TDD] [US-001] Derivar do AC-005 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-001, FR-002, FR-001, NFR-003, AC-005 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-005; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-005`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_005` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T006 [P] [TEST] [TDD] [US-001] Derivar do AC-006 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-001, FR-002, AC-006 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-006; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-006`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_006` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T007 [P] [TEST] [TDD] [US-002] Derivar do AC-007 caso(s) falhando em tests/Feature/Deals/DealBoardTest.php — Refs: US-002, FR-003, FR-004, NFR-002, AC-007 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-007; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-007`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_007` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T008 [P] [TEST] [TDD] [US-002] Derivar do AC-008 caso(s) falhando em tests/Feature/Deals/DealBoardTest.php — Refs: US-002, FR-004, FR-003, AC-008 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-008; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-008`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_008` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T009 [P] [TEST] [TDD] [US-002] Derivar do AC-009 caso(s) falhando em tests/Feature/Deals/DealBoardTest.php — Refs: US-002, FR-004, FR-007, AC-009 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-009; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-009`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_009` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T010 [P] [TEST] [TDD] [US-003] Derivar do AC-010 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-005, NFR-003, NFR-002, AC-010 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-010; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-010`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_010` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T011 [P] [TEST] [TDD] [US-003] Derivar do AC-011 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-006, FR-002, AC-011 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-011; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-011`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_011` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T012 [P] [TEST] [TDD] [US-003] Derivar do AC-012 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-006, AC-012 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-012; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-012`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_012` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T013 [P] [TEST] [TDD] [US-003] Derivar do AC-013 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-007, FR-003, AC-013 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-013; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-013`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_013` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T014 [P] [TEST] [TDD] [US-003] Derivar do AC-014 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-007, FR-009, AC-014 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-014; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-014`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_014` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T015 [P] [TEST] [TDD] [US-003] Derivar do AC-015 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-007, FR-002, AC-015 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-015; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-015`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_015` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T016 [P] [TEST] [TDD] [US-004] Derivar do AC-016 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-004, FR-008, NFR-001, AC-016 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-016; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-016`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_016` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T017 [P] [TEST] [TDD] [US-004] Derivar do AC-017 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-004, FR-008, FR-001, AC-017 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-017; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-017`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_017` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T018 [P] [TEST] [TDD] [US-004] Derivar do AC-018 caso(s) falhando em tests/Feature/Deals/LossReasonTest.php — Refs: US-004, FR-009, AC-018 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-018; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-018`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_018` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T019 [P] [TEST] [TDD] [US-004] Derivar do AC-019 caso(s) falhando em tests/Feature/Deals/LossReasonTest.php — Refs: US-004, FR-009, NFR-001, AC-019 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-019; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-019`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_019` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T020 [P] [TEST] [TDD] [US-004] Derivar do AC-020 caso(s) falhando em tests/Feature/Deals/DealStageTest.php — Refs: US-004, FR-010, NFR-003, AC-020 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-020; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-020`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_020` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T021 [P] [TEST] [TDD] [US-004] Derivar do AC-021 caso(s) falhando em tests/Feature/Deals/DealStageTest.php — Refs: US-004, FR-010, AC-021 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-021; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-021`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_021` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T022 [P] [TEST] [TDD] [US-003] Derivar do AC-022 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, NFR-001, FR-005, FR-006, AC-022 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-022; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-022`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_022` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T023 [P] [TEST] [TDD] [US-003] Derivar do AC-023 caso(s) falhando em tests/Feature/Deals/DealBoardTest.php — Refs: US-003, FR-006, NFR-002, AC-023 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-023; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-023`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_023` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T024 [P] [TEST] [TDD] [US-004] Derivar do AC-024 caso(s) falhando em tests/Feature/Deals/DealStageTest.php — Refs: US-004, FR-010, FR-007, AC-024 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-024; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-024`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_024` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T025 [P] [TEST] [TDD] [US-003] Derivar do AC-025 caso(s) falhando em tests/Feature/Deals/DealTest.php — Refs: US-003, FR-005, FR-007, AC-025 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-025; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-025`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_025` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.

#### Fase 2 — Fundação de dados

- [ ] T026 [CODE] [MIGRATION] [US-001] Criar people, deals, deal_user, deal_notes e loss_reasons em database/migrations/2026_09_28_000001_create_deals_tables.php — Refs: US-001, US-004, FR-001, FR-002, FR-009, FR-010, AC-001, AC-005, AC-018 — Depends: T001, T005, T018
  - [ ] **PREP**: Confirmar RED e banco de teste separado.
  - [ ] **EXECUTE**: Estrutura da seção 9, com índices parciais e FKs indicados; `deals.stage_id` com `restrictOnDelete`; na mesma migration inserir os 5 motivos padrão em cada projeto existente; aplicar com `migrate` em teste e dev (nunca fresh).
  - [ ] **VERIFY**: `php artisan migrate --env=testing` e `migrate:status --env=testing` com Ran; consultar `\d deals` e `\d people` no Postgres de teste; consultar que cada projeto existente tem os 5 motivos (backfill de FR-009, não testável com DatabaseTransactions).
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T027 [CODE] [US-001] Models app/Models/{Person,Deal,DealNote,LossReason}.php e relações em Project, Funnel e Stage — Refs: US-001, US-004, FR-001, FR-009, AC-001, AC-018 — Depends: T026
  - [ ] **PREP**: Ler Funnel e Project.
  - [ ] **EXECUTE**: Person (project, deals; mutators: phone só dígitos, email minúsculo e trim); Deal (funnel, stage, person, users, notes mais novas primeiro, lossReason; scope open(); casts value decimal:2, next_step_date date, closed_at datetime); DealNote (deal, user); LossReason (project; scope active()); Project cria os 5 motivos padrão no evento `created` do model (cobre controller, testes e seed); ProjectController não muda para isso.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter=test_a_c_018` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase 3 — US-001 Criar negócio

- [ ] T028 [CODE] [US-001] DealController::store e resolução de pessoa em app/Http/Controllers/DealController.php, com rotas em routes/web.php e flash em app/Http/Middleware/HandleInertiaRequests.php — Refs: US-001, US-004, FR-001, FR-002, FR-008, NFR-001, NFR-003, AC-001, AC-002, AC-003, AC-004, AC-005, AC-006, AC-017 — Depends: T027, T002, T003, T004, T006, T017
  - [ ] **PREP**: Ler FunnelController e FunnelPolicy.
  - [ ] **EXECUTE**: Rotas `/projetos/{project}/funis/{funnel}/negocios...` com `scopeBindings()` no grupo `auth`; autorização por `Gate::authorize('view', $funnel)`. store em transação: validar; achar pessoa por phone OU email no projeto (conflito entre duas pessoas → erro em email); criar se não achar, completar phone/email vazios se achar; bloquear se houver negócio aberto da pessoa no funil (erro em person + flash existing_deal_id); compartilhar `flash.existing_deal_id` em app/Http/Middleware/HandleInertiaRequests.php; capturar `UniqueConstraintViolationException` FORA de `DB::transaction` (no Postgres a transação fica abortada): colisão em people → repetir a operação uma vez (reaproveita a pessoa); colisão em deals → mesmo erro de duplicidade; etapa = primeira do funil; responsáveis = criador (gestor) ou user_ids obrigatórios (adm, só gestores atribuídos).
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter='test_a_c_00[1-6]|test_a_c_017'` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase 4 — US-002 Quadro e lista

- [ ] T029 [CODE] [US-002] DealController::index em app/Http/Controllers/DealController.php — Refs: US-002, US-003, FR-003, FR-004, FR-006, NFR-001, NFR-002, AC-007, AC-008, AC-009, AC-022, AC-023 — Depends: T028, T007, T008, T009, T022, T023
  - [ ] **PREP**: Ler FunnelController::show.
  - [ ] **EXECUTE**: Render `negocios/index` com project, funnel, stages (id, name, position, count, total), deals (abertos ou encerrados via `closed`), view, q, loss_reasons ativos, project_managers, deal (quando `negocio` pertence ao funil), can (manage, manage_assignees). Ordem: next_step_date asc, id asc. overdue = next_step_date < hoje em America/Sao_Paulo. Busca: ilike em name/email e em phone quando q tiver dígitos. Eager load person, users, stage, lossReason; funil arquivado → 404 para gestor como em FunnelController::show.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter='test_a_c_00[789]|test_a_c_02[23]'` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase 5 — US-003 Trabalhar o negócio

- [ ] T030 [CODE] [US-003] DealController update, note, win, lose e reopen em app/Http/Controllers/DealController.php — Refs: US-003, FR-002, FR-005, FR-006, FR-007, NFR-003, AC-010, AC-011, AC-012, AC-013, AC-014, AC-015, AC-025 — Depends: T029, T010, T011, T012, T013, T014, T015, T025
  - [ ] **PREP**: Ler Deal e regra de pessoa do T028.
  - [ ] **EXECUTE**: update: campos `sometimes` (stage_id do mesmo funil, next_step, next_step_date, value, name, phone, email da pessoa; pessoa mantém phone ou email; phone/email de outra pessoa → erro); recusar se encerrado. note: body obrigatório ≤ 5000, autor = usuário. win/lose: só aberto; lose exige motivo ativo do projeto; closed_at = now. reopen: bloqueia se houver outro aberto da pessoa no funil (erro em person + flash existing_deal_id), capturando também a violação do índice fora da transação; limpa loss_reason e closed_at.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter='test_a_c_01[0-5]|test_a_c_025'` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase 6 — US-004 Administração

- [ ] T031 [CODE] [US-004] DealController::assignees e LossReasonController em app/Http/Controllers/LossReasonController.php — Refs: US-004, FR-008, FR-009, NFR-001, AC-016, AC-018, AC-019 — Depends: T028, T016, T019
  - [ ] **PREP**: Ler ProjectController (validação de nome único já existente).
  - [ ] **EXECUTE**: assignees: PUT no grupo `can:adm`, user_ids ≥ 1; IDs novos só gestores ativos atribuídos ao projeto, IDs já presentes aceitos; `sync`. LossReasonController store/update/deactivate em `/projetos/{project}/motivos-perda` no grupo `can:adm`, nome obrigatório ≤ 255 e único no projeto sem diferenciar maiúsculas; ProjectController::show envia loss_reasons (id, name, deactivated_at) ao adm.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals --filter='test_a_c_01[6-9]'` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T032 [CODE] [US-004] Destino obrigatório ao apagar etapa com negócios em app/Http/Controllers/StageController.php — Refs: US-004, FR-010, NFR-003, AC-020, AC-021, AC-024 — Depends: T027, T020, T021, T024
  - [ ] **PREP**: Ler StageController::destroy.
  - [ ] **EXECUTE**: Se a etapa tiver negócios (abertos ou encerrados): exigir destination_stage_id de outra etapa do mesmo funil; mover todos os negócios na mesma transação antes de apagar; sem negócios, comportamento atual.
  - [ ] **VERIFY**: `php artisan test tests/Feature/Deals/DealStageTest.php` e `php artisan test tests/Feature/StageTest.php` verdes (SPEC-0002 sem regressão).
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase de interface

- [ ] T033 [CODE] [US-002] Página resources/js/pages/negocios/index.tsx com kanban, lista, busca e filtro de encerrados — Refs: US-002, US-003, FR-003, FR-004, FR-005, NFR-002, AC-007, AC-008, AC-009, AC-010 — Depends: T029, T030
  - [ ] **PREP**: Carregar `$specsfy-specialist-react-ui-components` e `$specsfy-specialist-shadcn-ui`; ler DESIGNSYSTEM.MD e INTERFACE.md; reutilizar PageHeader, FlashMessage, EmptyState, ConfirmDialog, Table, Badge, Select, Sheet e o padrão de project-form-sheet.tsx.
  - [ ] **EXECUTE**: Kanban com colunas por etapa (cabeçalho: nome, contagem, soma em R$), cards (pessoa, valor, próximo passo + data, selo "Atrasado" com ícone e texto, iniciais em Avatar); arrastar com HTML5 drag-and-drop nativo (sem biblioteca) enviando PATCH stage_id com `preserveScroll`; menu "Mover para" (Select) no card e na lista como alternativa de teclado e celular. Alternância Quadro/Lista (ToggleGroup), busca com debounce via `router.get` e `preserveState`, filtro "Ganhos / Perdidos". Mobile: colunas roláveis na horizontal com scroll-snap.
  - [ ] **VERIFY**: Arrastar, mover por menu, buscar e alternar visões como adm e gestor; `npx tsc --noEmit`.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T034 [CODE] [US-003] Painéis resources/js/components/deal-form-sheet.tsx (novo negócio) e resources/js/components/deal-sheet.tsx (detalhe) — Refs: US-001, US-003, US-004, FR-001, FR-006, FR-007, FR-008, NFR-002, AC-001, AC-005, AC-011, AC-012, AC-013, AC-014, AC-015, AC-016, AC-023 — Depends: T033, T031
  - [ ] **PREP**: Carregar `$specsfy-specialist-react-ui-components` e `$specsfy-specialist-shadcn-ui`; ler DESIGNSYSTEM.MD e INTERFACE.md; reutilizar PageHeader, FlashMessage, EmptyState, ConfirmDialog, Table, Badge, Select, Sheet e o padrão de project-form-sheet.tsx.
  - [ ] **EXECUTE**: deal-form-sheet: nome, telefone, e-mail, valor (input number step 0.01), próximo passo, data (input date nativo), responsáveis (Checkbox, só adm); em erro de duplicidade mostrar link "Ver negócio" que abre `?negocio=<existing_deal_id>`. deal-sheet: abre por `?negocio=` (Sheet à direita, largura total no celular); edita pessoa, etapa, valor, próximo passo e data; anotações (textarea + lista com autor e data); Ganhar, Perder (Dialog com Select de motivo obrigatório), Reabrir; responsáveis editáveis só pelo adm.
  - [ ] **VERIFY**: Criar, duplicar (link), editar, anotar, ganhar, perder, reabrir; foco preso e Esc no painel; `npx tsc --noEmit`.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T035 [CODE] [US-004] Motivos de perda e acesso ao quadro em resources/js/pages/projetos/show.tsx, resources/js/pages/funis/show.tsx e seleção de destino em confirmação de apagar etapa — Refs: US-004, US-002, FR-003, FR-009, FR-010, NFR-002, AC-019, AC-020, AC-021 — Depends: T031, T032, T033
  - [ ] **PREP**: Carregar `$specsfy-specialist-react-ui-components` e `$specsfy-specialist-shadcn-ui`; ler DESIGNSYSTEM.MD e INTERFACE.md; reutilizar PageHeader, FlashMessage, EmptyState, ConfirmDialog, Table, Badge, Select, Sheet e o padrão de project-form-sheet.tsx.
  - [ ] **EXECUTE**: Projeto: seção "Motivos de perda" (adm) com tabela, criar/renomear em painel e desativar com ConfirmDialog. Funil: botão "Abrir quadro"; o ConfirmDialog passa a aceitar `children` e exibir erro; o de apagar etapa sempre traz o Select "Mover negócios (se houver) para" e o servidor decide se é obrigatório; `funis/show.tsx` ganha `onError` no apagar.
  - [ ] **VERIFY**: Fluxo como adm e gestor; `npx tsc --noEmit`.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase final — Documentação e qualidade

- [ ] T036 [DOC] [US-004] Atualizar .specsfy/DATABASE.md, INTERFACE.md e database/seeders/DatabaseSeeder.php (negócios de exemplo) — Refs: US-001, US-004, FR-001, FR-009, AC-001, AC-018 — Depends: T026, T033, T034, T035
  - [ ] **PREP**: Ler migrations e componentes criados.
  - [ ] **EXECUTE**: Registrar tabelas, índices, relações e componentes com consumidores; seed de dev com 5 negócios de exemplo no funil de exemplo (1 atrasado, 1 ganho).
  - [ ] **VERIFY**: `node .agents/skills/specsfy-setup/scripts/monitor_context.mjs --project . --check` CURRENT.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa é documentação.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T037 [TEST] [US-002] Executar regressão completa em tests/Feature e checks estáticos — Refs: US-001, US-002, US-003, US-004, FR-001, FR-003, NFR-001, NFR-002, NFR-003, AC-001, AC-007, AC-022 — Depends: T026, T027, T028, T029, T030, T031, T032, T033, T034, T035, T036
  - [ ] **PREP**: Identificar suites e gates.
  - [ ] **EXECUTE**: Rodar `php artisan test`, `npx tsc --noEmit`, `npm run lint`, `npm run build` e `check_traceability.mjs`.
  - [ ] **VERIFY**: Todos verdes; nenhum `RefreshDatabase`.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado. Conferência final antes da aprovação do responsável.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

### 15. Ordem de execução

- Caminho crítico: T001–T025 (RED) → T026 → T027 → T028 → T029 → T030 → T031, T032 → T033 → T034 → T035 → T036 → T037.
- Tarefas paralelas: T001–T025 entre si; T031 e T032.
- Estratégia de MVP: fatia inteira (US-001 a US-003 entregam o quadro utilizável; US-004 completa o controle do adm).

## Ato III — Entregar e validar

### 16. Dependências, riscos e suposições

#### Dependências

- SPEC-0001 e SPEC-0002 entregues; container `nexuscrm-pgsql` rodando.

#### Riscos

- Negócio movido para uma etapa enquanto o adm a apaga → FK restrict gera erro 500; aceito (um adm, janela mínima).
- Arrastar nativo não funciona em toque no celular → no mobile o caminho é o menu "Mover para" (P5 já previa menu no celular).
- Volume acima de ~500 negócios abertos por funil deixa o quadro lento sem paginação → medir; paginar por coluna quando acontecer.
- Duas criações simultâneas com o mesmo telefone → o índice único recusa a segunda pessoa; o controller repete uma vez e reaproveita a pessoa. Negócio aberto duplicado → o índice parcial recusa e vira erro de duplicidade.

#### Suposições

- Pessoa reconhecida com phone/email já preenchidos: o valor digitado diferente é ignorado e o flash de sucesso avisa ("Pessoa existente reaproveitada; os dados dela não mudaram.").
- Telefone guardado só com dígitos e exibido assim nesta fatia (sem máscara por país).
- "Hoje" para atraso é calculado no fuso America/Sao_Paulo; o fuso da aplicação não muda.
- Criador adm não vira responsável (adm não é gestor atribuído); ele escolhe os responsáveis.
- Seed de dev cria negócios de exemplo no funil de exemplo.

### 17. Decisões

- **DEC-001**: Responsáveis N:N; criador gestor vira responsável; adm gerencia; mínimo 1 — responsável (P1, 2026-09-26).
- **DEC-002**: Encerrados saem do quadro; filtro Ganhos/Perdidos; reabrir permitido — responsável (P2).
- **DEC-003**: Obrigatórios nome + telefone ou e-mail + próximo passo com data; valor opcional — responsável (P3).
- **DEC-004**: Pessoa reconhecida por telefone ou e-mail; 1 negócio aberto por pessoa e funil — responsável (P4).
- **DEC-005**: Mover por arrastar ou menu, sem exigir próximo passo — responsável (P5); arrastar com API nativa, sem biblioteca — orquestrador (PR-003).
- **DEC-006**: Motivos de perda por projeto com 5 padrões — responsável (P6).
- **DEC-007**: Lista e cards ordenados por data do próximo passo; sem ordenação manual — responsável (P7, P8).
- **DEC-008**: Detalhe em painel lateral aberto por query string; anotações imutáveis — responsável (P9); query string — orquestrador.
- **DEC-009**: Sem exclusão de negócio; reabrir volta à etapa em que está — responsável (P10).
- **DEC-010**: Apagar etapa realoca também negócios encerrados, para reabrir ter etapa válida — orquestrador, derivado de P10.
- **DEC-011**: Sem Policy nova; ações de negócio herdam FunnelPolicy::view — orquestrador (PR-003).
- **DEC-012**: Funil ou projeto arquivado deixa o quadro só leitura também para o adm — orquestrador, após revisão independente; reversível.
- **DEC-013**: Removidos por YAGNI após revisão: reativar motivo, meta numérica de desempenho, linha do funil abrindo o quadro, prop deals_count — orquestrador.

### 18. Definition of Done

- [x] `Definition Gate` está `Passed`.
- [ ] `Plan Gate` está `Passed`.
- [ ] `Delivery Gate` está `Passed`.
- [ ] Todos os cenários `AC` aplicáveis passam.
- [ ] Todos os requisitos possuem evidência de verificação.
- [ ] Todas as tarefas na seção 14 estão concluídas.
- [ ] `php artisan test`, `npx tsc --noEmit` e `npm run lint` passam.
- [ ] Responsável aprovou visualmente em http://localhost:8000.
