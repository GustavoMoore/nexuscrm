# Especificação integrada: Funis e etapas

| Campo | Valor |
| --- | --- |
| Formato | Specsfy/2.0 |
| ID | SPEC-0002 |
| Slug | 0002-funis-etapas |
| Status | Defined |
| Effort | 3 |
| Effort updated at | 2026-09-27 |
| Effort rationale | Duas tabelas, dois controllers, duas telas reutilizando os componentes da SPEC-0001; sem integração externa. |
| ClickUp Task | |
| Milestones | Núcleo (MVP) — fatia 2 |
| Definition Gate | Passed |
| Plan Gate | Passed |
| Delivery Gate | Pending |
| Evidence Contract | 1 |
| Interface para pessoas | Sim — adm configura funis e etapas; gestor consulta |
| Atualizada em | 2026-09-27 |

## Ato I — Definir

### 1. Problema e resultado

#### Problema

Cada projeto roda vários funis de marketing, cada um com etapas próprias, e o
CRM ainda não tem onde registrá-los. Sem funis e etapas não há onde colocar
negócios (fatia 3).

#### Resultado desejado

O adm cria funis dentro de um projeto; cada funil nasce com 4 etapas padrão que
o adm renomeia, reordena, amplia e reduz. O gestor atribuído vê os funis e
etapas do projeto, sem alterá-los.

#### Métricas de sucesso

- Adm cria um funil com etapas ajustadas em menos de 1 minuto.
- 100% das rotas de funil e etapa negam escrita ao gestor e leitura a quem não vê o projeto (testes HTTP).

### 2. Research e esclarecimentos

#### Researchs executados

- Nenhum research externo: a fatia reutiliza a stack e os componentes da SPEC-0001.

#### Fontes e contexto consultados

- `specs/defined/0001-acesso-adm-gestores/spec.md`, `specs/backlog/0002-funis-etapas.md`, `app/Models/Project.php`, `app/Policies/ProjectPolicy.php`, `app/Http/Controllers/ProjectController.php`, `routes/web.php`, `resources/js/pages/projetos/show.tsx`, `resources/js/components/project-form-sheet.tsx`.

#### Documentação consultada

- Laravel 12, Route Model Binding com `scopeBindings` (documentação oficial, acesso 2026-09-27).

#### Artefatos de pesquisa armazenados

- Nenhum.

#### Dúvidas respondidas

- **Q**: Como o funil novo começa? → **A**: Com 4 etapas padrão editáveis: Novo contato, Qualificação, Proposta, Negociação.
- **Q**: Apagar etapa com negócios? → **A**: O adm escolhe a etapa de destino antes; nenhum negócio fica sem etapa. Como ainda não existem negócios, a escolha de destino é entregue na fatia 3; aqui vale a regra de manter ao menos 1 etapa.
- **Q**: Limite de funis por projeto? → **A**: Sem trava.
- **Q**: Como reordenar? → **A**: Botões subir/descer (decisão do orquestrador; arrastar entra com o kanban).

#### Dúvidas abertas

- Nenhuma.

### 3. Escopo e atores

#### Incluído

- Funis por projeto: criar (com 4 etapas padrão), renomear, arquivar, desarquivar.
- Etapas por funil: adicionar no fim, renomear, subir/descer, apagar (mínimo 1).
- Seção de funis no detalhe do projeto e página do funil com suas etapas.
- Leitura para gestores atribuídos; escrita só do adm.

#### Fora de escopo

- Negócios, quadro kanban/lista, arrastar etapas, motivos de perda (fatia 3).
- Escolha de destino ao apagar etapa com negócios (fatia 3, quando existirem negócios).
- Apagar funil (só arquivar); copiar funil; limite de funis.

#### Atores

- **Adm**: configura funis e etapas de qualquer projeto.
- **Gestor**: consulta funis ativos dos projetos atribuídos.

### 4. Princípios e restrições do projeto

- **PR-001**: Autorização no servidor; esconder botão não é proteção.
- **PR-002**: Funil não é apagado, só arquivado.
- **PR-003**: Menor código que funciona; reutilizar componentes da SPEC-0001, sem dependência nova.
- **PR-004**: Testes em Postgres `nexuscrm_test` com `DatabaseTransactions`.

### 5. Histórias de usuário

#### US-001 — Adm gerencia funis do projeto (P1)

Como adm, quero criar, renomear e arquivar funis de um projeto, para refletir
os funis de marketing que cada projeto roda.

**Por que P1**: negócios pertencem a um funil.
**Teste independente**: adm cria funil; ele aparece no projeto com 4 etapas.
**Requisitos**: FR-001, FR-002, FR-006

#### US-002 — Adm ajusta as etapas do funil (P1)

Como adm, quero adicionar, renomear, reordenar e apagar etapas, para que cada
funil tenha o caminho real daquele processo comercial.

**Por que P1**: as etapas viram as colunas do quadro.
**Teste independente**: adm renomeia e reordena etapas; a ordem persiste.
**Requisitos**: FR-003, FR-004

#### US-003 — Gestor consulta os funis dos seus projetos (P1)

Como gestor, quero ver os funis e etapas dos projetos que atendo, sem poder
alterá-los, para trabalhar sobre a estrutura definida pelo adm.

**Por que P1**: isolamento e controle do adm são regras do cliente.
**Teste independente**: gestor vê funis do projeto atribuído e recebe 403 ao tentar alterar.
**Requisitos**: FR-005

### 6. Cenários BDD de aceite

#### AC-001 — Funil novo nasce com 4 etapas padrão

**Cobre**: US-001, FR-001, FR-006

```gherkin
@US-001 @FR-001 @FR-006 @AC-001
Feature: Funil novo nasce com 4 etapas padrão

  Scenario: Adm cria funil
    Given o adm e o projeto "Alfa"
    When ele envia POST /projetos/{alfa}/funis com name "Lançamento"
    Then o funil "Lançamento" existe no projeto "Alfa"
    And suas etapas são, em ordem, "Novo contato", "Qualificação", "Proposta", "Negociação"
    And ele é redirecionado à página do funil com flash de sucesso
```

#### AC-002 — Nome do funil obrigatório e único no projeto

**Cobre**: US-001, FR-001, NFR-002

```gherkin
@US-001 @FR-001 @NFR-002 @AC-002
Feature: Nome do funil obrigatório e único no projeto

  Scenario: Nome vazio ou repetido
    Given o projeto "Alfa" com o funil "Lançamento"
    When o adm cria no "Alfa" um funil com name "" ou "lançamento"
    Then recebe erro de validação no campo name
    But criar "Lançamento" no projeto "Beta" é aceito
```

#### AC-003 — Adm renomeia funil

**Cobre**: US-001, FR-001

```gherkin
@US-001 @FR-001 @AC-003
Feature: Adm renomeia funil

  Scenario: Renomear
    Given o funil "Lançamento"
    When o adm envia PATCH /projetos/{p}/funis/{f} com name "Perpétuo"
    Then o funil passa a se chamar "Perpétuo" e as etapas não mudam
```

#### AC-004 — Arquivar e desarquivar funil

**Cobre**: US-001, FR-002

```gherkin
@US-001 @FR-002 @AC-004
Feature: Arquivar e desarquivar funil

  Scenario: Funil arquivado some para o gestor
    Given o gestor atribuído ao projeto "Alfa" e o funil "Lançamento"
    When o adm envia POST /projetos/{p}/funis/{f}/arquivar
    Then o gestor não vê "Lançamento" nas props de /projetos/{p}
    And GET /projetos/{p}/funis/{f} pelo gestor retorna 404
    When o adm envia POST /projetos/{p}/funis/{f}/desarquivar
    Then o gestor volta a ver o funil
```

#### AC-005 — Sem limite de funis por projeto

**Cobre**: US-001, FR-001

```gherkin
@US-001 @FR-001 @AC-005
Feature: Sem limite de funis por projeto

  Scenario: Quinto funil
    Given o projeto "Alfa" com 4 funis ativos
    When o adm cria o quinto funil
    Then o funil é criado
```

#### AC-006 — Gestor vê funis e etapas do projeto atribuído

**Cobre**: US-003, FR-005, FR-002, FR-006

```gherkin
@US-003 @FR-005 @FR-002 @FR-006 @AC-006
Feature: Gestor vê funis e etapas do projeto atribuído

  Scenario: Leitura
    Given o gestor atribuído ao projeto "Alfa" com o funil ativo "Lançamento"
    When ele abre GET /projetos/{alfa}
    Then as props trazem funnels com "Lançamento" e can.manage falso
    When ele abre GET /projetos/{alfa}/funis/{f}
    Then as props trazem as etapas em ordem e can.manage falso
```

#### AC-007 — Funil de projeto alheio é negado

**Cobre**: US-003, FR-005, NFR-001

```gherkin
@US-003 @FR-005 @NFR-001 @AC-007
Feature: Funil de projeto alheio é negado

  Scenario: URL de outro projeto
    Given o gestor A atribuído só ao projeto "Alfa" e o funil "X" do projeto "Beta"
    When A abre GET /projetos/{beta}/funis/{x}
    Then recebe 403
    When A abre GET /projetos/{alfa}/funis/{x}
    Then recebe 404
```

#### AC-008 — Gestor não escreve em funis nem etapas

**Cobre**: US-003, FR-005, NFR-001, FR-004

```gherkin
@US-003 @FR-005 @NFR-001 @FR-004 @AC-008
Feature: Gestor não escreve em funis nem etapas

  Scenario: Requisição direta
    Given o gestor atribuído ao projeto "Alfa" com o funil "Lançamento"
    When ele envia POST de funil, PATCH de funil, arquivar, POST de etapa, PATCH de etapa, mover e DELETE de etapa
    Then todas retornam 403 e nada muda no banco
```

#### AC-009 — Adm adiciona etapa no fim

**Cobre**: US-002, FR-003

```gherkin
@US-002 @FR-003 @AC-009
Feature: Adm adiciona etapa no fim

  Scenario: Nova etapa
    Given o funil "Lançamento" com 4 etapas
    When o adm envia POST /projetos/{p}/funis/{f}/etapas com name "Fechamento"
    Then "Fechamento" é a quinta etapa
```

#### AC-010 — Adm renomeia etapa com nome único no funil

**Cobre**: US-002, FR-003, NFR-002

```gherkin
@US-002 @FR-003 @NFR-002 @AC-010
Feature: Adm renomeia etapa com nome único no funil

  Scenario: Renomear e validar
    Given o funil com as etapas padrão
    When o adm renomeia "Proposta" para "Proposta enviada"
    Then a etapa passa a se chamar "Proposta enviada"
    When o adm renomeia uma etapa para "" ou "qualificação"
    Then recebe erro de validação no campo name
```

#### AC-011 — Reordenar etapas com subir e descer

**Cobre**: US-002, FR-003

```gherkin
@US-002 @FR-003 @AC-011
Feature: Reordenar etapas com subir e descer

  Scenario: Mover
    Given o funil com as etapas padrão
    When o adm envia POST /projetos/{p}/funis/{f}/etapas/{proposta}/mover com direction "up"
    Then a ordem é "Novo contato", "Proposta", "Qualificação", "Negociação"
    When ele move a primeira etapa com direction "up" ou a última com "down"
    Then a ordem não muda e a resposta não é erro 500
```

#### AC-012 — Apagar etapa mantém ao menos uma

**Cobre**: US-002, FR-004

```gherkin
@US-002 @FR-004 @AC-012
Feature: Apagar etapa mantém ao menos uma

  Scenario: Apagar
    Given o funil com as etapas padrão
    When o adm envia DELETE /projetos/{p}/funis/{f}/etapas/{proposta}
    Then restam 3 etapas, na ordem relativa anterior
    Given um funil com uma única etapa
    When o adm tenta apagá-la
    Then recebe erro de validação e a etapa continua
```

#### AC-013 — Etapa de outro funil pela URL é negada

**Cobre**: US-002, FR-003, NFR-001, FR-004

```gherkin
@US-002 @FR-003 @NFR-001 @FR-004 @AC-013
Feature: Etapa de outro funil pela URL é negada

  Scenario: IDs trocados
    Given a etapa E do funil F1 e o funil F2 do mesmo projeto
    When o adm envia PATCH ou DELETE /projetos/{p}/funis/{f2}/etapas/{e}
    Then recebe 404 e E não muda
```

#### AC-014 — Telas de funis entregam as props esperadas

**Cobre**: US-001, FR-006, NFR-002, FR-002

```gherkin
@US-001 @FR-006 @NFR-002 @FR-002 @AC-014
Feature: Telas de funis entregam as props esperadas

  Scenario: Props Inertia
    Given o adm e o projeto "Alfa" com um funil ativo e um arquivado
    When ele abre GET /projetos/{alfa}
    Then o componente é projetos/show e funnels traz os dois com archived_at e contagem de etapas, e can.manage verdadeiro
    When ele abre GET /projetos/{alfa}/funis/{f}
    Then o componente é funis/show com project, funnel e stages (id, name, position)
```

### 7. Requisitos

#### Funcionais

- **FR-001**: O adm deve criar e renomear funis dentro de um projeto (nome obrigatório, ≤ 255, único no projeto sem diferenciar maiúsculas; sem limite de quantidade). Cada funil criado recebe, na mesma transação, as etapas "Novo contato", "Qualificação", "Proposta" e "Negociação", nessa ordem.
- **FR-002**: O adm deve arquivar e desarquivar funis; funil arquivado não aparece nem abre para gestores; o adm vê ativos e arquivados.
- **FR-003**: O adm deve adicionar etapa no fim, renomear (nome obrigatório, ≤ 255, único no funil sem diferenciar maiúsculas) e mover etapa uma posição acima ou abaixo; mover nas pontas não altera nada. Rotas aninhadas garantem que etapa e funil pertencem ao funil e ao projeto da URL (404 caso contrário).
- **FR-004**: O adm deve apagar etapas, desde que o funil mantenha ao menos 1 etapa; as posições restantes ficam contínuas.
- **FR-005**: O gestor deve ler funis ativos e etapas apenas de projetos que pode ver (ProjectPolicy::view); qualquer escrita de gestor em funil ou etapa retorna 403.
- **FR-006**: O detalhe do projeto deve listar os funis e o funil deve ter página própria com as etapas em ordem; criar/renomear usa painel lateral.

#### Não funcionais

- **NFR-001**: Autorização aplicada no servidor (Policy + middleware `can:adm` + `scopeBindings`). **Verificação**: testes HTTP (AC-007, AC-008, AC-013).
- **NFR-002**: Telas operáveis por teclado; botões subir/descer com `aria-label` e desabilitados nas pontas; painel com foco preso e Esc. **Verificação**: props Inertia (AC-014) + inspeção manual no item VISUAL.

#### Erros e casos-limite

- Nome de funil repetido no mesmo projeto → erro no campo; em outro projeto é permitido.
- Mover primeira etapa para cima ou última para baixo → sem mudança, sem erro.
- Apagar a única etapa → erro de validação.
- Etapa ou funil de outro pai na URL → 404.
- Projeto arquivado → gestor já não o vê (SPEC-0001), logo não vê seus funis.

## Ato II — Projetar e provar

### 8. Plano técnico

#### Contexto existente

- SPEC-0001 entregue: `Project`, `ProjectPolicy`, `ProjectController`, `Gate adm`, componentes `PageHeader`, `FlashMessage`, `EmptyState`, `ConfirmDialog`, `Table`, painel `project-form-sheet.tsx`; testes com `DatabaseTransactions`.

#### Arquitetura e módulos

- `FunnelPolicy`: `view` = `ProjectPolicy::view` do projeto e (adm ou funil ativo); escrita = adm.
- Rotas aninhadas em `routes/web.php` com `scopeBindings()`; escrita dentro do grupo `can:adm` existente.

#### Migrations

- `create_funnels_and_stages_tables`: `funnels` (project_id FK cascade, name, archived_at nulo, timestamps, único `(project_id, lower(name))`) e `stages` (funnel_id FK cascade, name, position inteiro, timestamps, único `(funnel_id, lower(name))`). Aditiva; aplicar em `nexuscrm` e `nexuscrm_test` com `migrate`.

#### Models

- `Funnel`: `project()`, `stages()` ordenado por `position`, scope `active()`, cria as 4 etapas padrão ao ser criado pelo controller (transação).
- `Stage`: `funnel()`.
- `Project`: `funnels()`.

#### Controllers e casos de uso

- `FunnelController`: `store`, `show`, `update`, `archive`, `unarchive`.
- `StageController`: `store`, `update`, `move` (`direction` up|down, troca `position` com a vizinha em transação), `destroy` (recusa a última; renumera).
- `ProjectController::show`: acrescenta `funnels` visíveis (id, name, archived_at, stages_count) e `can.manage`.

#### Views e experiência

- `pages/projetos/show.tsx`: seção Funis com tabela, "Novo funil", editar, arquivar/desarquivar, filtro Ativos/Arquivados (adm).
- `pages/funis/show.tsx`: lista de etapas com subir/descer, renomear, nova etapa, apagar.

#### Queries e repositórios

- Volume baixo; sem paginação. `withCount('stages')` na listagem.

#### Jobs e processamento assíncrono

- Não aplicável.

#### Estrutura de arquivos

```text
database/migrations/2026_09_27_000001_create_funnels_and_stages_tables.php
app/Models/{Funnel,Stage}.php
app/Policies/FunnelPolicy.php
app/Http/Controllers/{FunnelController,StageController}.php
resources/js/pages/funis/show.tsx
resources/js/components/{funnel-form-sheet,stage-form-sheet}.tsx
tests/Feature/{FunnelTest,StageTest}.php
```

### 9. Modelo de dados

#### Entidades

| Entidade | Identidade | Atributos e regras | Relações |
| --- | --- | --- | --- |
| Funnel | id | name obrigatório, único por projeto case-insensitive; archived_at nulo = ativo | N:1 Project; 1:N Stage |
| Stage | id | name obrigatório, único por funil case-insensitive; position contínua a partir de 1 | N:1 Funnel |

#### Estados e transições

| Entidade | Estado atual | Evento | Próximo estado | Invariantes |
| --- | --- | --- | --- | --- |
| Funnel | Ativo | adm arquiva | Arquivado | gestores perdem acesso |
| Funnel | Arquivado | adm desarquiva | Ativo | — |
| Stage | Existente | adm apaga | Removida | funil mantém ≥ 1 etapa |

#### Migração e retenção

- Migration aditiva. Funis não são apagados (PR-002); etapas são apagadas sem negócios associados nesta fatia.

### 10. Interfaces e contratos

#### Interface para pessoas

- **Há interface para pessoas**: Sim — detalhe do projeto e página do funil.

#### Stack e convenções de interface

- React 19 + TypeScript via Inertia; `useForm`; shadcn/ui já instalado; ícones lucide (`ArrowUp`, `ArrowDown`); layout `AppSidebarLayout`.

#### Telas e responsabilidades

- **Detalhe do projeto** (adm e gestores atribuídos): gestores atendentes e lista de funis; adm cria/edita/arquiva.
- **Funil** (adm e gestores atribuídos): etapas em ordem; adm adiciona, renomeia, move e apaga.

#### Fluxo de informação e navegação

- Projetos → projeto → linha do funil → Funil. "Novo funil" → painel → salvar → página do funil.
- Breadcrumbs: `Projetos / <projeto> / <funil>`.

#### Menus e navegação principal

- Sem novo item de menu; funis são acessados pelo projeto (Projetos → `/projetos/{id}`).

#### Formulários e ações

- **Funil** (painel lateral): Nome (obrigatório). Ação "Salvar".
- **Etapa** (painel lateral): Nome (obrigatório). Ação "Salvar".
- **Arquivar/Desarquivar funil** e **Apagar etapa**: confirmação em modal.
- **Subir/Descer**: botões por linha, ação imediata.
- Erros no campo, painel permanece aberto.

#### Composição e disposição

- Mesmo shell e `PageHeader` da SPEC-0001; tabelas em largura total; em mobile rolam horizontalmente.

#### Blocos React e componentes selecionados

| Tela | Bloco React | Responsabilidade | Arquivo previsto | Componente ou composição | Origem | Reuso ou extensão |
| --- | --- | --- | --- | --- | --- | --- |
| Projeto | FunnelFormSheet | criar/renomear funil | `components/funnel-form-sheet.tsx` | `Sheet`, `Input`, `Label`, `InputError` | shadcn/ui | novo, segue project-form-sheet |
| Funil | StageFormSheet | criar/renomear etapa | `components/stage-form-sheet.tsx` | `Sheet`, `Input`, `Label`, `InputError` | shadcn/ui | novo |
| Projeto, Funil | Table | listas | `components/ui/table.tsx` | `Table` | SPEC-0001 | reuso |
| Projeto, Funil | ConfirmDialog | arquivar, apagar | `components/confirm-dialog.tsx` | `Dialog` | SPEC-0001 | reuso |
| Projeto, Funil | PageHeader, FlashMessage, EmptyState | cabeçalho, feedback, vazio | `components/*` | — | SPEC-0001 | reuso |

#### Estados e acessibilidade

- Vazio: projeto sem funis → "Nenhum funil ainda" (adm vê "Novo funil").
- Erro: mensagens no campo com `aria-describedby`.
- Sucesso: `flash.success` ("Funil criado", "Etapa movida" etc.).
- Teclado: subir/descer com `aria-label="Subir <etapa>"`/`"Descer <etapa>"`, desabilitados nas pontas.

#### Contrato CRUD

- Todas as telas usam o mesmo `PageHeader`. As listas usam o `DataGrid` do projeto (materializado como `Table` shadcn na SPEC-0001) em largura total, com coluna `ID`; editar e apagar por linha. Funil: "apagar" é **Arquivar** (PR-002). Etapa: apagar real com confirmação, bloqueado na última.

#### Revisão visual durante o desenvolvimento

- Durante a implementação e no Delivery Gate, conferir em 1440px e 390px, nos estados vazio, com dados, erro de validação e sem permissão: bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra).
- Registrar a forma de conferência, viewport, estados, achados e ajustes em cada tarefa com interface; tarefas sem interface registram `Não aplicável` com motivo.

#### APIs expostas

- Rotas web Inertia: `POST /projetos/{project}/funis`; `GET|PATCH /projetos/{project}/funis/{funnel}`; `POST .../funis/{funnel}/arquivar|desarquivar`; `POST .../funis/{funnel}/etapas`; `PATCH|DELETE .../etapas/{stage}`; `POST .../etapas/{stage}/mover`.

#### APIs externas utilizadas

- Nenhuma.

#### Documentação das APIs consultadas

- Não aplicável.

#### Eventos e outros contratos

- Não aplicável.

### 11. Estratégia TDD

- **Integração/contrato**: testes HTTP por rota e papel (403/404/422) e `assertInertia`.
- **BDD/aceite**: Gherkin da seção 6 orienta os casos; sem `.feature`.
- **Runner TDD**: PHPUnit (`php artisan test`) em `nexuscrm_test`, métodos `test_AC_NNN_*` com marcador `// SPECSFY: AC-NNN`.
- **E2E**: Não aplicável; teclado verificado manualmente no VISUAL.

#### Evidência RED-GREEN-REFACTOR

- RED registrado em `storage/logs/red-0002.txt` pelo implementador.

### 12. Plano de testes e rastreabilidade

| AC | Arquivo de teste | Tarefas |
| --- | --- | --- |
| AC-001 | tests/Feature/FunnelTest.php | T001 |
| AC-002 | tests/Feature/FunnelTest.php | T002 |
| AC-003 | tests/Feature/FunnelTest.php | T003 |
| AC-004 | tests/Feature/FunnelTest.php | T004 |
| AC-005 | tests/Feature/FunnelTest.php | T005 |
| AC-006 | tests/Feature/FunnelTest.php | T006 |
| AC-007 | tests/Feature/FunnelTest.php | T007 |
| AC-008 | tests/Feature/FunnelTest.php | T008 |
| AC-009 | tests/Feature/StageTest.php | T009 |
| AC-010 | tests/Feature/StageTest.php | T010 |
| AC-011 | tests/Feature/StageTest.php | T011 |
| AC-012 | tests/Feature/StageTest.php | T012 |
| AC-013 | tests/Feature/StageTest.php | T013 |
| AC-014 | tests/Feature/FunnelTest.php | T014 |

### 13. Validações

#### Gate do Ato I — Definição

- **Resultado**: Passed (2026-09-27)
- **Comando**: `node .agents/skills/specsfy-04-validate/scripts/validate_spec.mjs specs/defined/0002-funis-etapas/spec.md`
- **Achados**: validação estrutural sem erros; aprovação do responsável em 2026-09-27.

#### Gate do Ato II — Plano

- **Resultado**: Passed (2026-09-27)
- **Comando**: `node .agents/skills/specsfy-05-tasks/scripts/validate_tasks.mjs specs/defined/0002-funis-etapas/spec.md`
- **Achados**: 23 tarefas, 14 RED antes do código; sem erros.

#### Gate do Ato III — Entrega

- **Resultado**: Pending
- **Comando**: `node .agents/skills/specsfy-06-tdd-bdd/scripts/check_traceability.mjs specs/defined/0002-funis-etapas/spec.md .`
- **Achados**: Pending.

### 14. Tarefas

Formato:
`- [ ] TNNN [P?] [TIPO] [US-NNN?] Ação com caminho — Refs: IDs — Depends: IDs|none`

#### Fase 1 — RED TDD informado pelo BDD

- [ ] T001 [P] [TEST] [TDD] [US-001] Derivar do AC-001 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-001, FR-001, FR-006, AC-001 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-001; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-001`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_001` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T002 [P] [TEST] [TDD] [US-001] Derivar do AC-002 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-001, FR-001, NFR-002, AC-002 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-002; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-002`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_002` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T003 [P] [TEST] [TDD] [US-001] Derivar do AC-003 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-001, FR-001, AC-003 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-003; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-003`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_003` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T004 [P] [TEST] [TDD] [US-001] Derivar do AC-004 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-001, FR-002, AC-004 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-004; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-004`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_004` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T005 [P] [TEST] [TDD] [US-001] Derivar do AC-005 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-001, FR-001, AC-005 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-005; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-005`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_005` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T006 [P] [TEST] [TDD] [US-003] Derivar do AC-006 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-003, FR-005, FR-002, FR-006, AC-006 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-006; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-006`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_006` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T007 [P] [TEST] [TDD] [US-003] Derivar do AC-007 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-003, FR-005, NFR-001, AC-007 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-007; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-007`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_007` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T008 [P] [TEST] [TDD] [US-003] Derivar do AC-008 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-003, FR-005, NFR-001, FR-004, AC-008 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-008; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-008`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_008` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T009 [P] [TEST] [TDD] [US-002] Derivar do AC-009 caso(s) falhando em tests/Feature/StageTest.php — Refs: US-002, FR-003, AC-009 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-009; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-009`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_009` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T010 [P] [TEST] [TDD] [US-002] Derivar do AC-010 caso(s) falhando em tests/Feature/StageTest.php — Refs: US-002, FR-003, NFR-002, AC-010 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-010; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-010`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_010` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T011 [P] [TEST] [TDD] [US-002] Derivar do AC-011 caso(s) falhando em tests/Feature/StageTest.php — Refs: US-002, FR-003, AC-011 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-011; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-011`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_011` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T012 [P] [TEST] [TDD] [US-002] Derivar do AC-012 caso(s) falhando em tests/Feature/StageTest.php — Refs: US-002, FR-004, AC-012 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-012; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-012`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_012` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T013 [P] [TEST] [TDD] [US-002] Derivar do AC-013 caso(s) falhando em tests/Feature/StageTest.php — Refs: US-002, FR-003, NFR-001, FR-004, AC-013 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-013; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-013`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_013` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.
- [ ] T014 [P] [TEST] [TDD] [US-001] Derivar do AC-014 caso(s) falhando em tests/Feature/FunnelTest.php — Refs: US-001, FR-006, NFR-002, FR-002, AC-014 — Depends: none
  - [ ] **PREP**: Ler o Gherkin do AC-014; confirmar `check_database_safety.mjs` = SAFE.
  - [ ] **EXECUTE**: Escrever o caso com marcador `// SPECSFY: AC-014`; usar `assertInertia` para props de tela; sem `.feature`.
  - [ ] **VERIFY**: `php artisan test --filter=AC_014` falha pela razão esperada (rota/tabela/regra ausente), não por erro de sintaxe.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa só materializa teste.
  - [ ] **EVIDENCE**: Registrar comando e motivo do RED na seção 11.
  - [ ] **IMPROVE**: Registrar lacuna de cobertura encontrada ou nenhuma.

#### Fase 2 — Fundação de dados

- [ ] T015 [CODE] [MIGRATION] [US-001] Criar funnels e stages em database/migrations/2026_09_27_000001_create_funnels_and_stages_tables.php — Refs: US-001, US-002, FR-001, FR-003, AC-001, AC-002, AC-009, AC-010 — Depends: T001, T002, T009, T010
  - [ ] **PREP**: Confirmar RED e banco de teste separado.
  - [ ] **EXECUTE**: funnels(id, project_id FK cascade, name, archived_at null, timestamps; único (project_id, lower(name))); stages(id, funnel_id FK cascade, name, position int, timestamps; único (funnel_id, lower(name))); aplicar em teste e dev com `migrate` (nunca fresh).
  - [ ] **VERIFY**: `php artisan migrate --env=testing` e `migrate:status --env=testing` com Ran.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T016 [CODE] [US-001] Models app/Models/Funnel.php e app/Models/Stage.php e relação funnels() em app/Models/Project.php — Refs: US-001, US-002, FR-001, FR-003, AC-001, AC-009 — Depends: T015
  - [ ] **PREP**: Ler `.agents/skills/specsfy-specialist-laravel`.
  - [ ] **EXECUTE**: Funnel: project(), stages() ordenado por position, scope active(); Stage: funnel(); `$fillable` só name; criação das 4 etapas padrão em função do model dentro de transação.
  - [ ] **VERIFY**: `php artisan test --filter=AC_001` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase 3 — US-003 Autorização

- [ ] T017 [CODE] [US-003] Autorização em app/Policies/FunnelPolicy.php e rotas aninhadas com scopeBindings em routes/web.php — Refs: US-003, FR-005, NFR-001, AC-006, AC-007, AC-008, AC-013 — Depends: T016, T006, T007, T008, T013
  - [ ] **PREP**: Ler ProjectPolicy e rotas atuais.
  - [ ] **EXECUTE**: view: pode ver o projeto (ProjectPolicy::view) e funil ativo ou usuário adm; escrita: adm. Rotas `/projetos/{project}/funis/...` e `/etapas/...` com `scopeBindings()`; escrita no grupo `can:adm`.
  - [ ] **VERIFY**: `php artisan test --filter='AC_00[678]|AC_013'` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase 4 — US-001 e US-002 Casos de uso

- [ ] T018 [CODE] [US-001] FunnelController em app/Http/Controllers/FunnelController.php (store, show, update, archive, unarchive) e funnels em ProjectController::show — Refs: US-001, US-003, FR-001, FR-002, FR-005, FR-006, AC-001, AC-002, AC-003, AC-004, AC-005, AC-006, AC-014 — Depends: T017, T003, T004, T005, T014
  - [ ] **PREP**: Ler ProjectController (validação de nome único case-insensitive já existente).
  - [ ] **EXECUTE**: Validar name obrigatório, ≤ 255, único no projeto sem diferenciar maiúsculas; flash de sucesso; ProjectController::show envia funnels (visíveis ao usuário) com contagem de etapas e can.manage.
  - [ ] **VERIFY**: `php artisan test --filter='AC_00[1-6]|AC_014'` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T019 [CODE] [US-002] StageController em app/Http/Controllers/StageController.php (store, update, move, destroy) — Refs: US-002, FR-003, FR-004, AC-009, AC-010, AC-011, AC-012, AC-013 — Depends: T017, T009, T010, T011, T012
  - [ ] **PREP**: Ler Funnel/Stage.
  - [ ] **EXECUTE**: store no fim (max position + 1); update com nome único no funil; move troca position com a vizinha em transação (no-op nas pontas); destroy recusa a última etapa com erro de validação e renumera positions.
  - [ ] **VERIFY**: `php artisan test --filter=StageTest` verde.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa não altera superfície visual.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase de interface

- [ ] T020 [CODE] [US-001] Seção de funis em resources/js/pages/projetos/show.tsx com resources/js/components/funnel-form-sheet.tsx — Refs: US-001, US-003, FR-006, NFR-002, AC-004, AC-006, AC-014 — Depends: T018
  - [ ] **PREP**: Carregar `$specsfy-specialist-react-ui-components` e `$specsfy-specialist-shadcn-ui`; ler DESIGNSYSTEM.MD e INTERFACE.md; reutilizar PageHeader, FlashMessage, EmptyState, ConfirmDialog, Table e o padrão de painel de project-form-sheet.tsx.
  - [ ] **EXECUTE**: Substituir o EmptyState de funis por tabela (ID, nome, nº de etapas, status Badge) com linha como link para o funil; adm vê "Novo funil", editar e arquivar/desarquivar (ConfirmDialog) e filtro Ativos/Arquivados; gestor só lê.
  - [ ] **VERIFY**: Fluxo como adm e gestor; `npx tsc --noEmit`.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado.
  - [ ] **EVIDENCE**: Registrar arquivos e viewports.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T021 [CODE] [US-002] Página resources/js/pages/funis/show.tsx com resources/js/components/stage-form-sheet.tsx — Refs: US-002, US-003, FR-003, FR-004, FR-006, NFR-002, AC-009, AC-010, AC-011, AC-012, AC-014 — Depends: T019, T020
  - [ ] **PREP**: Carregar `$specsfy-specialist-react-ui-components` e `$specsfy-specialist-shadcn-ui`; ler DESIGNSYSTEM.MD e INTERFACE.md; reutilizar PageHeader, FlashMessage, EmptyState, ConfirmDialog, Table e o padrão de painel de project-form-sheet.tsx.
  - [ ] **EXECUTE**: Lista ordenada das etapas (posição, nome); adm: botões ↑ ↓ com aria-label (desabilitados nas pontas), renomear e "Nova etapa" no painel lateral, apagar com ConfirmDialog (desabilitado quando só há 1 etapa); breadcrumbs Projetos / <projeto> / <funil>; gestor só lê.
  - [ ] **VERIFY**: Criar, renomear, mover e apagar pela UI; foco volta ao botão após mover; `npx tsc --noEmit`.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado.
  - [ ] **EVIDENCE**: Registrar arquivos e viewports.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

#### Fase final — Documentação e qualidade

- [ ] T022 [DOC] [US-002] Atualizar .specsfy/DATABASE.md (funnels, stages) e INTERFACE.md (FunnelFormSheet, StageFormSheet) — Refs: US-001, US-002, FR-001, FR-003, AC-001, AC-009 — Depends: T015, T020, T021
  - [ ] **PREP**: Ler migrations e componentes criados.
  - [ ] **EXECUTE**: Registrar tabelas, índices, relações e componentes com consumidores.
  - [ ] **VERIFY**: `node .agents/skills/specsfy-setup/scripts/monitor_context.mjs --project . --check` CURRENT.
  - [ ] **VISUAL**: Registrar `Não aplicável` porque a tarefa é documentação.
  - [ ] **EVIDENCE**: Registrar arquivos, comandos e saída.
  - [ ] **IMPROVE**: Registrar ou nenhuma.

- [ ] T023 [TEST] [US-003] Executar regressão completa em tests/Feature e checks estáticos — Refs: US-001, US-002, US-003, FR-001, FR-006, NFR-001, NFR-002, AC-001, AC-014 — Depends: T015, T016, T017, T018, T019, T020, T021, T022
  - [ ] **PREP**: Identificar suites e gates.
  - [ ] **EXECUTE**: Rodar `php artisan test`, `npx tsc --noEmit`, `npm run lint`, `npm run build` e `check_traceability.mjs`.
  - [ ] **VERIFY**: Todos verdes; nenhum `RefreshDatabase`; seed de dev com um funil de exemplo no projeto de exemplo.
  - [ ] **VISUAL**: Conferir bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra) em 1440px e 390px, estados vazio/dados/erro/sem permissão, foco e teclado. Conferência final antes da aprovação do responsável.
  - [ ] **EVIDENCE**: Registrar contagens e comandos.
  - [ ] **IMPROVE**: Retrospectiva do processo.

### 15. Ordem de execução

- Caminho crítico: T001–T014 (RED) → T015 → T016 → T017 → T018, T019 → T020 → T021 → T022 → T023.
- Tarefas paralelas: T001–T014 entre si; T018 e T019.
- Estratégia de MVP: fatia inteira.

## Ato III — Entregar e validar

### 16. Dependências, riscos e suposições

#### Dependências

- SPEC-0001 entregue; container `nexuscrm-pgsql` rodando.

#### Riscos

- Troca de `position` concorrente gerar duplicata → sem índice único em position; troca em transação; volume de um único adm torna colisão improvável.

#### Suposições

- Nomes padrão das etapas em português, editáveis.
- Seed de dev cria um funil de exemplo no projeto de exemplo.

### 17. Decisões

- **DEC-001**: Etapas padrão Novo contato → Qualificação → Proposta → Negociação — decisão do responsável (2026-09-27).
- **DEC-002**: Destino obrigatório ao apagar etapa com negócios — decisão do responsável; implementado na fatia 3, quando negócios existirem.
- **DEC-003**: Sem limite de funis — decisão do responsável.
- **DEC-004**: Reordenar por subir/descer, sem biblioteca de arrastar — orquestrador; arrastar reavaliado no kanban.
- **DEC-005**: Motivos de perda e apagar funil vazio fora desta fatia — YAGNI; motivos entram na fatia 3.

### 18. Definition of Done

- [ ] `Definition Gate` está `Passed`.
- [ ] `Plan Gate` está `Passed`.
- [ ] `Delivery Gate` está `Passed`.
- [ ] Todos os cenários `AC` aplicáveis passam.
- [ ] Todos os requisitos possuem evidência de verificação.
- [ ] Todas as tarefas na seção 14 estão concluídas.
- [ ] `php artisan test`, `npx tsc --noEmit` e `npm run lint` passam.
- [ ] Responsável aprovou visualmente em http://localhost:8000.
