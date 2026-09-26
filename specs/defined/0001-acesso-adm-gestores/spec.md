# Especificação integrada: Acesso adm e gestores

| Campo | Valor |
| --- | --- |
| Formato | Specsfy/2.0 |
| ID | SPEC-0001 |
| Slug | 0001-acesso-adm-gestores |
| Status | Defined |
| Effort | 5 |
| Effort updated at | 2026-09-26 |
| Effort rationale | Autenticação e autorização por papel e por projeto, 3 telas com painel lateral, remoção de rotas do starter kit; sem integração externa. |
| ClickUp Task | |
| Milestones | Núcleo (MVP) — fatia 1 |
| Definition Gate | Passed |
| Plan Gate | Pending |
| Delivery Gate | Pending |
| Evidence Contract | 1 |
| Interface para pessoas | Sim — adm e gestores usam login, Minha agenda, Projetos e Usuários |
| Atualizada em | 2026-09-26 |

## Ato I — Definir

### 1. Problema e resultado

#### Problema

O starter kit permite que qualquer pessoa crie conta e não tem papéis nem
projetos. O cliente precisa que só o adm crie contas e que cada gestor enxergue
somente os projetos que atende.

#### Resultado desejado

O adm cria gestores e projetos e define quem atende cada projeto. O gestor
entra, troca a senha provisória e vê apenas os seus projetos. É a base sobre a
qual funis, quadro e agenda serão construídos.

#### Métricas de sucesso

- 100% das rotas de projeto negam acesso a gestor não atribuído (testes HTTP).
- 0 caminhos de cadastro público ou autoexclusão acessíveis.
- Adm cria um gestor e um projeto atribuído em menos de 1 minuto, sem sair da lista (painel lateral).

### 2. Research e esclarecimentos

#### Researchs executados

- **R-001**: ReUI oferece DataGrid para shadcn? → Sim, `@reui/data-grid` (TanStack Table) instalado via `shadcn add`; há build Radix, compatível com o starter kit.

#### Fontes e contexto consultados

- Código do starter kit: `routes/auth.php`, `routes/settings.php`, `routes/web.php`, `app/Models/User.php`, `resources/js/components/app-sidebar.tsx`, `app-logo.tsx`, `resources/js/pages/*`.
- `PROJECT.md`, `.specsfy/RULES.md`, `DESIGNSYSTEM.MD`, `specs/backlog/0001-acesso-adm-gestores.md`, `specs/inbox/2026-09-26-190626-fatia-1-acesso-adm-e-gestores.md`.

#### Documentação consultada

- ReUI Data Grid, https://reui.io/docs/data-grid, acesso 2026-09-26: instalação e dependência TanStack Table.

#### Artefatos de pesquisa armazenados

- Nenhum artefato externo; a única consulta externa (ReUI) é documentação pública de instalação, resumida em R-001.

#### Dúvidas respondidas

- **Q**: Como o gestor recebe a primeira senha? → **A**: Adm define senha provisória, repassa por fora; troca obrigatória no 1º login; sem e-mail nesta fatia.
- **Q**: Onde abre o formulário de criar/editar? → **A**: Painel lateral à direita, lista visível atrás; padrão do produto inteiro.
- **Q**: O que acontece quando o gestor sai? → **A**: Desativar, nunca apagar; histórico preservado; pode ser reativado.
- **Q**: Visual? → **A**: Neutro, nome "Nexus" em texto, sem logo; referências virão depois.

#### Dúvidas abertas

- Nenhuma.

#### Revisão independente (lane review, Claude, read-only, 2026-09-26)

- Veredito: APROVAR COM AJUSTES; 0 P1. FIND-SEC-001..005, FIND-ARCH-001..004, FIND-PROD-001..006 incorporados nesta versão (sessões encerradas na redefinição, `attemptWhen`, campos fora de `$fillable`, e-mail minúsculo, validação de `user_ids`, inativos preservados, arquivos do starter kit a ajustar, flash com `Alert`, `aria-current`, ACs de UI reescritos como props Inertia + VISUAL manual, sheet único). FIND-ARCH-005 registrado em DEC-004.

### 3. Escopo e atores

#### Incluído

- Papéis `adm` e `gestor`; primeiro adm criado por comando Artisan.
- Remoção de cadastro público, "esqueci minha senha" por e-mail, verificação de e-mail e autoexclusão de conta.
- Tela Usuários (só adm): listar, criar, editar, redefinir senha provisória, desativar e reativar gestor.
- Troca obrigatória de senha no primeiro acesso e após redefinição.
- Tela Projetos: adm cria, renomeia, arquiva, desarquiva e atribui gestores; gestor vê só os atribuídos e ativos.
- Detalhe do projeto: nome, gestores atendentes e estado vazio de funis.
- Shell: nome "Nexus", menu Minha agenda / Projetos / Usuários; `/` leva à Minha agenda (estado vazio).
- Correção dos erros de `tsc` herdados do starter kit (os arquivos afetados são removidos por esta fatia).

#### Fora de escopo

- Funis, etapas, negócios e conteúdo real da Minha agenda (fatias 2–4).
- Convite ou recuperação de senha por e-mail.
- Mais de um adm, auditoria de ações, Supabase de produção, Laravel Octane.

#### Atores

- **Adm**: único; administra contas, projetos e atribuições; vê tudo.
- **Gestor**: usa o CRM nos projetos atribuídos; edita só o próprio perfil e senha.

### 4. Princípios e restrições do projeto

- **PR-001**: Autorização sempre no servidor; esconder item de menu nunca é a proteção.
- **PR-002**: Usuário e projeto não são apagados: desativar/arquivar preserva histórico.
- **PR-003**: Menor código que funciona; reutilizar componentes do starter kit (`Sheet`, `Button`, `Input`, `Label`, `InputError`, `AppSidebar`).
- **PR-004**: Testes em Postgres `nexuscrm_test` com `DatabaseTransactions`; nunca no banco de dev.

### 5. Histórias de usuário

#### US-001 — Adm gerencia gestores (P1)

Como adm, quero criar, editar, redefinir a senha e desativar gestores, para
controlar quem acessa o CRM sem perder o histórico de quem saiu.

**Por que P1**: sem contas não há uso.
**Teste independente**: adm cria gestor; gestor entra com a senha provisória.
**Requisitos**: FR-002, FR-003, FR-004

#### US-002 — Adm gerencia projetos e atribuições (P1)

Como adm, quero criar e arquivar projetos e escolher os gestores de cada um,
para distribuir a carteira do time comercial.

**Por que P1**: funis e negócios pertencem a projetos.
**Teste independente**: adm cria projeto com um gestor; esse gestor o vê na lista.
**Requisitos**: FR-005

#### US-003 — Gestor acessa só o que lhe cabe (P1)

Como gestor, quero entrar, trocar minha senha provisória e ver somente meus
projetos, para trabalhar sem ruído e sem acessar carteira de outro gestor.

**Por que P1**: isolamento entre gestores é regra do cliente.
**Teste independente**: gestor A não abre projeto atribuído só ao gestor B.
**Requisitos**: FR-001, FR-003, FR-006, FR-007

### 6. Cenários BDD de aceite

#### AC-001 — Não existe cadastro público

**Cobre**: US-003, FR-001, NFR-001

```gherkin
@US-003 @FR-001 @NFR-001 @AC-001
Feature: Sem cadastro aberto

  Scenario: Visitante tenta se cadastrar
    Given um visitante não autenticado
    When ele acessa GET /register ou envia POST /register
    Then recebe 404
    And as rotas nomeadas register, password.request e password.reset não existem (Route::has falso)
```

#### AC-002 — Não existe autoexclusão de conta

**Cobre**: FR-001, NFR-001

```gherkin
@FR-001 @NFR-001 @AC-002
Feature: Conta só é desativada pelo adm

  Scenario: Gestor tenta apagar a própria conta
    Given um gestor autenticado
    When ele envia DELETE /settings/profile
    Then a requisição é rejeitada com 404 ou 405
    And a conta continua existindo
    And a rota nomeada profile.destroy não existe
```

#### AC-003 — Não existe recuperação de senha por e-mail

**Cobre**: FR-001

```gherkin
@FR-001 @AC-003
Feature: Recuperação de senha pelo adm

  Scenario: Visitante tenta recuperar senha
    Given um visitante não autenticado
    When ele acessa /forgot-password ou /reset-password/{token}
    Then recebe 404
```

#### AC-004 — Adm cria gestor com senha provisória

**Cobre**: US-001, FR-002

```gherkin
@US-001 @FR-002 @AC-004
Feature: Criar gestor

  Scenario: Adm cadastra gestor
    Given o adm autenticado na tela Usuários
    When ele abre o painel "Novo gestor" e envia nome, e-mail e senha provisória com 8+ caracteres
    Then o gestor aparece na lista como Ativo
    And o gestor tem papel gestor e troca de senha pendente
    And o e-mail é gravado em minúsculas
```

#### AC-005 — E-mail duplicado é recusado no campo

**Cobre**: US-001, FR-002, NFR-002

```gherkin
@US-001 @FR-002 @NFR-002 @AC-005
Feature: Validação do cadastro de gestor

  Scenario: E-mail já usado
    Given já existe um usuário com e-mail ana@cliente.com
    When o adm tenta criar outro gestor com ana@cliente.com
    Then nenhum usuário é criado
    And a resposta traz erro de validação no campo email

  Scenario: E-mail igual com maiúsculas diferentes
    Given já existe ana@cliente.com
    When o adm tenta criar gestor com Ana@Cliente.com
    Then nenhum usuário é criado e há erro no campo email
```

#### AC-006 — Adm redefine senha provisória

**Cobre**: US-001, FR-002, FR-003

```gherkin
@US-001 @FR-002 @FR-003 @AC-006
Feature: Redefinir senha

  Scenario: Gestor esqueceu a senha
    Given um gestor ativo que já trocou a senha
    When o adm define uma nova senha provisória para ele
    Then o gestor entra com a nova senha
    And é obrigado a trocá-la antes de usar qualquer tela
    And sessões abertas do gestor feitas antes da redefinição são encerradas
```

#### AC-007 — Troca obrigatória bloqueia o resto do sistema

**Cobre**: US-003, FR-003, NFR-001

```gherkin
@US-003 @FR-003 @NFR-001 @AC-007
Feature: Primeiro acesso

  Scenario: Gestor com senha provisória navega
    Given um gestor autenticado com troca de senha pendente
    When ele acessa cada rota GET registrada com middleware auth
    Then é redirecionado para /trocar-senha
    And apenas /trocar-senha e logout respondem normalmente
```

#### AC-008 — Troca concluída libera o acesso

**Cobre**: US-003, FR-003

```gherkin
@US-003 @FR-003 @AC-008
Feature: Concluir troca de senha

  Scenario: Gestor define a senha definitiva
    Given um gestor em /trocar-senha
    When ele envia nova senha com confirmação, diferente da provisória
    Then a troca pendente é removida
    And ele é levado para Minha agenda

  Scenario: Nova senha igual à provisória
    Given um gestor em /trocar-senha com a senha provisória "provisoria1"
    When ele envia "provisoria1" como nova senha
    Then a troca é recusada com erro no campo
```

#### AC-009 — Gestor desativado não entra

**Cobre**: US-001, FR-004

```gherkin
@US-001 @FR-004 @AC-009
Feature: Desativar gestor

  Scenario: Login de conta desativada
    Given o adm desativou o gestor Bruno
    When Bruno tenta entrar com e-mail e senha corretos
    Then o login falha com a mensagem "Conta desativada. Fale com o administrador."
    And nenhuma sessão autenticada nem cookie "lembrar-me" é criado
    And após o adm reativar Bruno, ele volta a entrar
```

#### AC-010 — Sessão aberta de gestor desativado é encerrada

**Cobre**: FR-004, NFR-001

```gherkin
@FR-004 @NFR-001 @AC-010
Feature: Desativação imediata

  Scenario: Gestor logado é desativado
    Given Bruno está autenticado
    When o adm o desativa e Bruno faz a próxima requisição
    Then Bruno é deslogado e levado ao login
```

#### AC-011 — Adm não desativa a si mesmo

**Cobre**: FR-004

```gherkin
@FR-004 @AC-011
Feature: Proteção do adm

  Scenario: Adm tenta se desativar
    Given o adm autenticado
    When ele envia a desativação da própria conta
    Then a ação é recusada com 403
    And a linha do próprio adm chega à tela com can.deactivate falso
```

#### AC-012 — Adm cria projeto e atribui gestores

**Cobre**: US-002, FR-005

```gherkin
@US-002 @FR-005 @AC-012
Feature: Criar projeto

  Scenario: Novo projeto com gestores
    Given o adm na tela Projetos e os gestores Ana e Bruno ativos
    When ele abre o painel "Novo projeto", informa "Mentoria X" e marca Ana
    Then "Mentoria X" aparece na lista com Ana como gestora
    And o adm pode editar depois para marcar Bruno e desmarcar Ana
```

#### AC-013 — Projeto arquivado some para o gestor

**Cobre**: US-002, FR-005, FR-006

```gherkin
@US-002 @FR-005 @FR-006 @AC-013
Feature: Arquivar projeto

  Scenario: Adm arquiva projeto
    Given "Mentoria X" atribuído a Ana
    When o adm arquiva "Mentoria X"
    Then Ana não vê mais o projeto nem abre sua URL (403)
    And o adm o vê no filtro "Arquivados" e pode desarquivar
```

#### AC-014 — Nome de projeto obrigatório e único

**Cobre**: US-002, FR-005

```gherkin
@US-002 @FR-005 @AC-014
Feature: Validação de projeto

  Scenario: Nome vazio ou repetido
    Given já existe o projeto "Mentoria X"
    When o adm salva projeto sem nome ou com "mentoria x"
    Then nada é salvo e o erro aparece no campo nome
```

#### AC-015 — Gestor vê só os projetos atribuídos

**Cobre**: US-003, FR-006

```gherkin
@US-003 @FR-006 @AC-015
Feature: Lista de projetos do gestor

  Scenario: Isolamento na listagem
    Given Ana atende "Mentoria X" e Bruno atende "Imersão Y"
    When Ana abre Projetos
    Then vê somente "Mentoria X"
    And a página recebe can.create, can.update e can.archive falsos
```

#### AC-016 — URL de projeto alheio é negada

**Cobre**: US-003, FR-006, NFR-001

```gherkin
@US-003 @FR-006 @NFR-001 @AC-016
Feature: Isolamento por URL

  Scenario: Gestor digita a URL de outro projeto
    Given Ana não atende "Imersão Y"
    When Ana acessa /projetos/{id de Imersão Y}
    Then recebe 403
```

#### AC-017 — Área de usuários é só do adm

**Cobre**: FR-006, FR-007, NFR-001

```gherkin
@FR-006 @FR-007 @NFR-001 @AC-017
Feature: Menu por papel

  Scenario: Gestor tenta administrar usuários
    Given Ana autenticada como gestora
    When ela olha o menu lateral e acessa /usuarios diretamente
    Then as props compartilhadas trazem auth.user.is_adm falso (o menu esconde "Usuários")
    And /usuarios responde 403
```

#### AC-018 — Shell Nexus e Minha agenda vazia

**Cobre**: FR-007, NFR-002

```gherkin
@FR-007 @NFR-002 @AC-018
Feature: Navegação principal

  Scenario: Usuário entra no sistema
    Given um usuário autenticado sem troca pendente
    When ele acessa /
    Then é levado para /agenda
    And a página Inertia renderizada é "agenda"
    And as props compartilhadas trazem name "Nexus" e auth.user.is_adm conforme o papel
    And a verificação visual confirma o menu com aria-current="page" no item atual
```

#### AC-019 — Painel lateral acessível por teclado

**Cobre**: US-001, FR-007, NFR-002

```gherkin
@US-001 @FR-007 @NFR-002 @AC-019
Feature: Painel lateral

  Scenario: Adm usa só o teclado
    Given o adm na tela Usuários
    When ele ativa "Novo gestor" pelo teclado
    Then o painel abre com foco no campo nome e o foco fica preso no painel
    And Esc fecha o painel e devolve o foco ao botão "Novo gestor"
    And campos com erro têm aria-invalid e aria-describedby apontando para a mensagem
    # Verificação: manual por teclado, registrada no item VISUAL (sem teste de navegador nesta fatia)
```

#### AC-020 — Gestor não cria nem altera projeto por requisição direta

**Cobre**: US-002, FR-005, NFR-001

```gherkin
@US-002 @FR-005 @NFR-001 @AC-020
Feature: Escrita de projeto só pelo adm

  Scenario: Gestor forja requisição
    Given Ana autenticada como gestora e atribuída a "Mentoria X"
    When ela envia POST /projetos ou PATCH/arquivar em "Mentoria X"
    Then recebe 403 e nada muda
```

### 7. Requisitos

#### Funcionais

- **FR-001**: O sistema não deve expor cadastro público, recuperação de senha por e-mail, verificação de e-mail nem autoexclusão de conta; as rotas e telas correspondentes são removidas.
- **FR-002**: O adm deve criar e editar gestores (nome, e-mail único em minúsculas, senha provisória ≥ 8) e redefinir a senha provisória; cada gestor criado ou redefinido fica com troca de senha pendente e perde as sessões abertas. `role`, `deactivated_at` e `must_change_password` ficam fora de `$fillable` e só são gravados com `forceFill` nos controllers do adm. O gestor edita no próprio perfil apenas o nome; e-mail de login só o adm muda.
- **FR-003**: Usuário com troca pendente deve ser redirecionado a `/trocar-senha` em toda rota autenticada, exceto essa e logout; a nova senha precisa de confirmação e ser diferente da atual.
- **FR-004**: O adm deve desativar e reativar gestores; conta desativada não autentica (`Auth::attemptWhen`, sem criar sessão nem cookie), sessão aberta é encerrada na próxima requisição, e o adm não desativa a si mesmo.
- **FR-005**: O adm deve criar, renomear, arquivar e desarquivar projetos (nome obrigatório, único sem diferenciar maiúsculas) e definir os gestores atendentes (`user_ids` validados como gestores existentes; gestores inativos já atribuídos são preservados ao salvar); gestores não escrevem em projetos.
- **FR-006**: O gestor deve listar e abrir somente projetos ativos atribuídos a ele; o adm lista todos, com filtro Ativos/Arquivados; a área Usuários é exclusiva do adm.
- **FR-007**: O shell deve exibir "Nexus" em texto e o menu Minha agenda (`/agenda`), Projetos (`/projetos`) e Usuários (`/usuarios`, só adm); `/` redireciona para `/agenda` autenticado ou `/login` visitante; criar/editar abre em painel lateral.

#### Não funcionais

- **NFR-001**: Toda autorização é aplicada no servidor (Policy/Gate/middleware), independente do que a tela mostra. **Verificação**: testes HTTP Pest de 403/404/redirect para cada rota protegida (AC-001, 002, 007, 010, 016, 017, 020).
- **NFR-002**: Telas e painel lateral operáveis por teclado, com labels, erros ligados aos campos e foco gerenciado. **Verificação**: inspeção manual por teclado registrada no item VISUAL + assertivas de atributos (AC-005, 018, 019).

#### Erros e casos-limite

- E-mail duplicado ao criar/editar gestor → erro no campo, painel permanece aberto.
- Senha provisória < 8 caracteres → erro no campo.
- Gestor desativado com sessão aberta → deslogado na próxima requisição.
- Adm tenta se desativar → 403.
- Projeto sem gestores → permitido (só o adm o vê até atribuir).
- Gestor desativado continua atendente do projeto, aparece marcado "Inativo" e desabilitado no painel, e não é removido ao salvar.

## Ato II — Projetar e provar

### 8. Plano técnico

#### Contexto existente

- Laravel 12 + Inertia 2 + React 19 + TypeScript + Tailwind 4 + shadcn/ui (Radix) do starter kit; auth por controllers em `app/Http/Controllers/Auth`; layout `AppSidebarLayout`; Pest; Postgres dev/test.

#### Arquitetura e módulos

- Papel em `users.role` (`adm`|`gestor`); helper `User::isAdm()`.
- `Gate::define('adm', fn (User $u) => $u->isAdm())` em `AppServiceProvider`; `ProjectPolicy` (`view`: adm ou atribuído e não arquivado; escrita: adm).
- Middleware `EnsurePasswordChanged` (redireciona para `/trocar-senha`) e checagem de desativado no mesmo middleware (logout + redirect login), registrados no grupo `web` em `bootstrap/app.php`.
- Login: `LoginRequest::authenticate` usa `Auth::attemptWhen($cred, fn ($u) => $u->isActive(), $remember)`; se as credenciais forem válidas mas a conta estiver desativada, lança a mensagem do AC-009.
- Sessões: `SESSION_DRIVER=database`; ao redefinir senha ou desativar, apagar as linhas do usuário em `sessions` e girar `remember_token`.
- E-mail normalizado com regra `lowercase` + `unique` no store/update do adm.
- Primeiro adm: comando Artisan `nexus:criar-adm` em `routes/console.php` (pergunta nome, e-mail, senha). `DatabaseSeeder` cria `adm@nexus.test` e `gestor@nexus.test` (senha `password`) só em `APP_ENV=local`.

#### Migrations

- `add_role_and_status_to_users_table`: `role` string default `gestor`, `deactivated_at` timestamp nulo, `must_change_password` boolean default false.
- `create_projects_table`: `id`, `name` string, `archived_at` timestamp nulo, timestamps; índice único em `lower(name)`.
- `create_project_user_table`: `project_id`, `user_id` (FK com cascade), PK composta.
- Todas aditivas; rollback por `down()` padrão. Aplicadas em `nexuscrm` e `nexuscrm_test` com `migrate` (nunca `fresh`).

#### Models

- `User`: casts `deactivated_at` datetime, `must_change_password` bool; `projects()` belongsToMany; `isAdm()`, `isActive()`.
- `Project` (`app/Models/Project.php`): `users()` belongsToMany; scope `active()`; scope `visibleTo(User)`.

#### Controllers e casos de uso

- `UserController` (`app/Http/Controllers/UserController.php`, `can:adm`): `index`, `store`, `update`, `deactivate`, `activate`, `resetPassword`.
- `ProjectController` (`app/Http/Controllers/ProjectController.php`): `index`, `show` (Policy view), `store`, `update` (nome + gestores), `archive`, `unarchive` (Policy/adm).
- `PasswordChangeController`: `edit`, `update` em `/trocar-senha`.
- `/agenda`: `Inertia::render('agenda')` com estado vazio.
- Remover `RegisteredUserController`, `PasswordResetLinkController`, `NewPasswordController`, `EmailVerification*`, `VerifyEmailController`, `ConfirmablePasswordController` e suas rotas; remover `destroy` de `ProfileController` e a rota; `ProfileUpdateRequest`/`ProfileController` passam a aceitar só `name` (remover a linha que zera `email_verified_at`).
- Trocar referências a `dashboard`/`/dashboard` por `agenda`: `AuthenticatedSessionController.php` (redirect pós-login), `app-header.tsx`, `app-sidebar.tsx`. Manter a rota nomeada `home` em `/` (usada pelos layouts de auth) redirecionando para `/agenda` ou `/login`.
- `HandleInertiaRequests::share`: `name` = config('app.name') ("Nexus"), `auth.user.is_adm`, `flash.success`.

#### Views e experiência

- Páginas: `pages/agenda.tsx`, `pages/projetos/index.tsx`, `pages/projetos/show.tsx`, `pages/usuarios/index.tsx`, `pages/auth/trocar-senha.tsx`.
- Remover: `pages/welcome.tsx`, `pages/dashboard.tsx`, `pages/auth/register.tsx`, `forgot-password.tsx`, `reset-password.tsx`, `verify-email.tsx`, `confirm-password.tsx`, `components/delete-user.tsx`.
- Ajustar: `pages/auth/login.tsx` (tirar links de cadastro e recuperação), `pages/settings/profile.tsx` (tirar e-mail editável, reenvio de verificação e "Apagar conta"), `components/nav-main.tsx` (`aria-current="page"` e ativo por prefixo de URL), `components/input-error.tsx` (aceitar `id` para `aria-describedby`).

#### Queries e repositórios

- Feedback de sucesso: `flash.success` compartilhado pelo Inertia e exibido com `Alert` do shadcn no topo do conteúdo (sem biblioteca de toast nova).
- `Project::visibleTo($user)`: adm → todos (filtro por `archived_at`); gestor → `whereNull('archived_at')->whereHas('users', id)`. Volume baixo; sem paginação nesta fatia.

#### Jobs e processamento assíncrono

- Não aplicável.

#### Estrutura de arquivos

```text
specs/defined/0001-acesso-adm-gestores/spec.md
app/Http/Controllers/{UserController,ProjectController,PasswordChangeController}.php
app/Http/Middleware/EnsurePasswordChanged.php
app/Models/Project.php
app/Policies/ProjectPolicy.php
database/migrations/*_add_role_and_status_to_users_table.php
database/migrations/*_create_projects_table.php
database/migrations/*_create_project_user_table.php
resources/js/pages/{agenda,projetos/index,projetos/show,usuarios/index,auth/trocar-senha}.tsx
resources/js/components/{page-header,user-form-sheet,project-form-sheet,confirm-dialog,empty-state,flash-message}.tsx
resources/js/components/reui/data-grid*.tsx
tests/Feature/{AccessTest,UserManagementTest,ProjectTest}.php
```

### 9. Modelo de dados

#### Entidades

| Entidade | Identidade | Atributos e regras | Relações |
| --- | --- | --- | --- |
| User | id | name obrigatório; email único; role `adm`/`gestor`; deactivated_at nulo = ativo; must_change_password | N:N Project |
| Project | id | name obrigatório, único case-insensitive; archived_at nulo = ativo | N:N User (gestores) |
| project_user | (project_id, user_id) | sem atributos | FK cascade |

#### Estados e transições

| Entidade | Estado atual | Evento | Próximo estado | Invariantes |
| --- | --- | --- | --- | --- |
| User | Ativo | adm desativa | Desativado | adm não desativa a si |
| User | Desativado | adm reativa | Ativo | — |
| User | Senha provisória | usuário troca senha | Senha definitiva | nova ≠ atual |
| User | Senha definitiva | adm redefine | Senha provisória | — |
| Project | Ativo | adm arquiva | Arquivado | gestores perdem acesso |
| Project | Arquivado | adm desarquiva | Ativo | — |

#### Migração e retenção

- Migrations aditivas. Usuários e projetos não são apagados (PR-002); retenção indefinida até política LGPD da fatia 7.

### 10. Interfaces e contratos

#### Interface para pessoas

- **Há interface para pessoas**: Sim — adm e gestores usam todas as telas desta fatia.

#### Stack e convenções de interface

- React 19 + TypeScript via Inertia; rotas nomeadas do Laravel; formulários com `useForm` do Inertia; primitives shadcn/ui (Radix) já em `resources/js/components/ui`; ícones lucide; layout `AppSidebarLayout`. Preservar login, perfil, senha e aparência do starter kit (sem a seção "Apagar conta").

#### Telas e responsabilidades

- **Login** (todos): entrar; sem links de cadastro/recuperação.
- **Trocar senha** (usuário com troca pendente): definir senha definitiva.
- **Minha agenda** (todos): nesta fatia, estado vazio.
- **Projetos** (adm: todos + filtro; gestor: atribuídos): listar, abrir; adm cria/edita/arquiva.
- **Detalhe do projeto** (adm e gestores atribuídos): nome, gestores, estado vazio "Funis chegam na próxima etapa".
- **Usuários** (só adm): listar, criar, editar, redefinir senha, desativar/reativar.

#### Fluxo de informação e navegação

- Login → (troca pendente? Trocar senha) → Minha agenda.
- Projetos → linha → Detalhe; "Novo projeto"/"Editar" → painel lateral → salvar → lista atualizada com toast.
- Usuários → "Novo gestor"/"Editar"/"Redefinir senha" → painel lateral → salvar → lista.
- Breadcrumbs: `Nexus / Projetos`, `Nexus / Projetos / <nome>`, `Nexus / Usuários`, `Nexus / Minha agenda`.

#### Menus e navegação principal

- Menu lateral (`AppSidebar`), cada item com seu destino e rota: "Minha agenda" → `/agenda` (todos); "Projetos" → `/projetos` (todos); "Usuários" → `/usuarios` (só adm). Rodapé: menu do usuário (Configurações, Sair). Remover links "Repository" e "Documentation" do starter kit.
- Mobile: o sidebar colapsa no `Sheet` já existente do starter kit.

#### Formulários e ações

- **Gestor** (painel lateral): Nome (obrigatório), E-mail (obrigatório, único), Senha provisória (obrigatória ao criar, ≥ 8; oculta ao editar). Ação "Salvar".
- **Redefinir senha** (mesmo painel do gestor, modo redefinir): Nova senha provisória (≥ 8). Ação "Redefinir".
- **Desativar/Reativar**: ação direta da linha com modal de confirmação ("Bruno não poderá mais entrar.").
- **Projeto** (painel lateral): Nome (obrigatório, único), Gestores (checkboxes com gestores ativos; inativos já atribuídos aparecem marcados, desabilitados e com rótulo "Inativo"). Ação "Salvar".
- **Arquivar/Desarquivar**: ação da linha com modal de confirmação.
- **Trocar senha** (página): Nova senha, Confirmar senha.
- Erros sempre no campo (`InputError`), painel permanece aberto.

#### Composição e disposição

- Shell do starter kit: sidebar à esquerda, `Breadcrumb` no header, conteúdo com `PageHeader` (título, descrição, ação primária à direita) e `DataGrid` em largura total.
- Densidade média; em mobile a tabela rola horizontalmente.
- Laravel Octane com Open Swoole (`laravel/octane`, `--server=swoole`, healthcheck `/up`, reload de workers) é obrigatório pelo contrato Specsfy, mas fica numa spec própria de runtime antes do deploy (DEC-004); esta fatia roda em `php artisan serve`.

#### Blocos React e componentes selecionados

| Tela | Bloco React | Responsabilidade | Arquivo previsto | Componente ou composição | Origem | Reuso ou extensão |
| --- | --- | --- | --- | --- | --- | --- |
| Todas | PageHeader | título, descrição, ação primária | `components/page-header.tsx` | `Heading` + `Button` | próprio | novo, único para todas as telas |
| Projetos, Usuários | DataGrid | tabela em largura total | `components/reui/data-grid*.tsx` | `@reui/data-grid` | ReUI | novo, instalado via shadcn |
| Usuários | UserFormSheet | criar/editar gestor e redefinir senha (modo) | `components/user-form-sheet.tsx` | `Sheet`, `Input`, `Label`, `InputError` | shadcn/ui | novo |
| Todas | FlashMessage | feedback de sucesso | `components/flash-message.tsx` | `Alert` | shadcn/ui | novo |
| Projetos | ProjectFormSheet | criar/editar projeto e gestores | `components/project-form-sheet.tsx` | `Sheet`, `Input`, `Checkbox` | shadcn/ui | novo |
| Projetos, Usuários | ConfirmDialog | confirmar desativar/arquivar | `components/confirm-dialog.tsx` | `Dialog` | shadcn/ui | novo |
| Todas | AppSidebar | menu por papel | `components/app-sidebar.tsx` | `Sidebar` | starter kit | extensão |
| Todas | AppLogo | texto "Nexus" | `components/app-logo.tsx` | — | starter kit | extensão |
| Agenda, Projeto | EmptyState | estado vazio | `components/empty-state.tsx` | `PlaceholderPattern` | starter kit | novo |

- shadcn/ui: `Sheet`, `Dialog`, `Input`, `Label`, `Checkbox`, `Button`, `Badge`, `Breadcrumb`, `Sidebar`. ReUI: `@reui/data-grid`.

#### Estados e acessibilidade

- Loading: botão "Salvar" desabilitado com `processing` do `useForm`.
- Vazio: Projetos sem itens → "Nenhum projeto ainda" (adm vê botão "Novo projeto"; gestor vê "Nenhum projeto atribuído a você"). Agenda → "Nenhum negócio ainda".
- Erro: mensagens no campo com `aria-describedby`; foco no primeiro campo com erro.
- Sucesso: mensagem `Alert` via flash "Gestor criado", "Projeto arquivado" etc.
- Permissão insuficiente: página 403 padrão com link para Minha agenda.
- Teclado: painel com foco preso, Esc fecha e devolve foco; linha da tabela é link focável; status Ativo/Inativo em `Badge` com texto (não só cor).
- O `Breadcrumb` usa links nos itens anteriores e `aria-current="page"` no atual.

#### Contrato CRUD

- Todas as telas usam o mesmo `PageHeader`. As listas usam `DataGrid` em largura total com a coluna `ID` visível e a linha inteira como link para o detalhe (Projetos) ou abertura do painel de edição (Usuários).
- Cada linha tem ações independentes de editar e de "apagar". Por PR-002, "apagar" é materializado como **Desativar** (usuário) e **Arquivar** (projeto), ambos reversíveis, respeitando permissão e com confirmação da consequência.
- Componentes novos e reaproveitados registrados em `INTERFACE.md` com consumidores.

#### Revisão visual durante o desenvolvimento

- Durante a implementação e no Delivery Gate, conferir em 1440px e 390px, nos estados vazio, com dados, erro de validação e sem permissão: bordas, espaçamentos, margens, padding e tipografia (família, peso, tamanho, altura de linha, quebra de texto), alinhamento, overflow, foco, conteúdo curto e longo (nome de projeto com 80 caracteres).
- Registrar a forma de conferência, viewport, estados, achados e ajustes em cada tarefa com interface; tarefas sem interface registram `Não aplicável` com motivo.

#### APIs expostas

- Rotas web Inertia (sessão + CSRF), sem API pública: `GET /agenda`; `GET|POST /projetos`; `GET|PATCH /projetos/{project}`; `POST /projetos/{project}/arquivar|desarquivar`; `GET|POST /usuarios`; `PATCH /usuarios/{user}`; `POST /usuarios/{user}/desativar|reativar|redefinir-senha`; `GET|PUT /trocar-senha`. Erros: 403 sem permissão, 422 validação.

#### APIs externas utilizadas

- Nenhuma.

#### Documentação das APIs consultadas

- ReUI Data Grid (R-001): instalação via `shadcn add @reui/data-grid`, variante Radix.

#### Eventos e outros contratos

- Não aplicável.

### 11. Estratégia TDD

- **Unidade**: `Project::visibleTo` (adm, gestor atribuído, gestor não atribuído, projeto arquivado).
- **Integração/contrato**: testes HTTP Pest por rota e papel (403/404/redirect/422) e asserções de props Inertia (`assertInertia`: componente, `can.*`, `auth.user.is_adm`, `name`).
- **BDD/aceite**: Gherkin da seção 6 orienta os casos TDD; sem arquivos `.feature`.
- **Runner TDD**: Pest em `nexuscrm_test`.
- **E2E**: Não aplicável nesta fatia (sem teste de navegador); foco, Esc, aria-current e aria-describedby são verificados manualmente no item VISUAL, e a jornada é aprovada pelo responsável em localhost.
- **Verificação manual**: navegação por teclado no painel (NFR-002) e aprovação visual do responsável.

#### Evidência RED-GREEN-REFACTOR

- Preenchida por `$specsfy-05-tasks` e `$specsfy-06-tdd-bdd` no Ato II.

### 12. Plano de testes e rastreabilidade

- Preenchido por `$specsfy-05-tasks` a partir dos AC-001 a AC-020.

### 13. Validações

#### Gate do Ato I — Definição

- **Resultado**: Passed (2026-09-26)
- **Comando**: `node .agents/skills/specsfy-04-validate/scripts/validate_spec.mjs specs/defined/0001-acesso-adm-gestores/spec.md`
- **Achados**: validação estrutural sem erros; revisão semântica independente (lane review) sem P1, 15 achados P2/P3 incorporados (ver seção 2); aprovação do responsável em 2026-09-26.

#### Gate do Ato II — Plano

- **Resultado**: Pending
- **Comando**: `node .agents/skills/specsfy-05-tasks/scripts/validate_tasks.mjs specs/defined/0001-acesso-adm-gestores/spec.md`
- **Achados**: Pending.

#### Gate do Ato III — Entrega

- **Resultado**: Pending
- **Comando**: `node .agents/skills/specsfy-06-tdd-bdd/scripts/check_traceability.mjs specs/defined/0001-acesso-adm-gestores/spec.md .`
- **Achados**: Pending.

### 14. Tarefas

- Geradas por `$specsfy-05-tasks` após o Definition Gate.

### 15. Ordem de execução

- Definida por `$specsfy-05-tasks`.

## Ato III — Entregar e validar

### 16. Dependências, riscos e suposições

#### Dependências

- Container `nexuscrm-pgsql` rodando (dev e test).
- Instalação de `@reui/data-grid` (acrescenta TanStack Table).

#### Riscos

- Remover rotas de auth quebra testes do starter kit → apagar/ajustar os testes das rotas removidas na mesma tarefa.
- ReUI (TanStack v9) incompatível com a versão do React/Radix → verificar na instalação; se falhar, registrar e usar `Table` shadcn mantendo a API do `DataGrid` (atualizar spec via `$specsfy-update-spec`).

#### Suposições

- Menu lateral Minha agenda / Projetos / Usuários (assumido, sem objeção do responsável).
- Um único adm; o papel não é editável pela tela.
- Senha provisória mínima de 8 caracteres.
- Gestor não altera o próprio e-mail (DEC-006).
- URLs em português.

### 17. Decisões

- **DEC-001**: Papel como coluna `role` em `users` — 2 papéis fixos; pacote de permissões seria excesso. Trocar se surgirem papéis configuráveis.
- **DEC-002**: Desativar/arquivar em vez de apagar — preserva autoria e histórico; decisão do responsável (2026-09-26).
- **DEC-003**: Senha provisória + troca obrigatória, sem e-mail — decisão do responsável; convite por e-mail entra quando houver serviço de envio.
- **DEC-004**: Octane/Open Swoole fora desta fatia — contrato Specsfy exige, mas é runtime de produção e depende de compatibilidade com PHP 8.5; spec própria antes do deploy. Aceito pelo responsável ao aprovar a spec (2026-09-26).
- **DEC-006**: Gestor edita no perfil só o nome; e-mail de login é alterado pelo adm — default assumido pela revisão (FIND-PROD-005), o login é a identidade controlada pelo adm.
- **DEC-005**: Painel lateral para criar/editar no produto inteiro — decisão do responsável (2026-09-26).

### 18. Definition of Done

- [ ] `Definition Gate` está `Passed`.
- [ ] `Plan Gate` está `Passed`.
- [ ] `Delivery Gate` está `Passed`.
- [ ] Todos os cenários `AC` aplicáveis passam.
- [ ] Todos os requisitos possuem evidência de verificação.
- [ ] Todas as tarefas na seção 14 estão concluídas.
- [ ] `php artisan test`, `npx tsc --noEmit` e `npm run lint` passam.
- [ ] Responsável aprovou visualmente em http://localhost:8000.
