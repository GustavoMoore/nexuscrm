# Interface do projeto

<!-- markdownlint-disable MD013 -->

Este arquivo é a fonte canônica para construir e reaproveitar a interface.
Atualize-o antes e depois de cada tarefa que criar ou mudar uma tela React.
Leia `DESIGNSYSTEM.MD` antes de escolher a composição macro. Este arquivo
registra componentes, blocos e telas locais; as regras globais de SaaS vivem em
`DESIGNSYSTEM.MD`.

## Base observada

- Stack: Laravel
- Política: toda interface React é composta por componentes React.
- Primitives: shadcn/ui.
- Composições gratuitas: ReUI.

Para Laravel, descreva módulos de domínio, fronteiras HTTP/console e use `database/migrations` como primeira evidência do mapa de dados.

## Design system

| Item | Localização ou valor | Uso no projeto |
| --- | --- | --- |
| Tokens e tema | A mapear | Cores, tipografia, espaçamento, raio e tema |
| Configuração shadcn/ui | A mapear | `components.json`, aliases e registry |
| Registry ReUI | A mapear | Itens gratuitos `@reui/c-*` |
| PageHeader compartilhado | `DESIGNSYSTEM.MD` | Um componente reutilizável para lista, detalhe, criação e edição |
| Padrão de dashboard | `DESIGNSYSTEM.MD` | `PageHeader`, filtros, `KPI`, visualização principal e investigação detalhada |
| Padrão de linha | `DESIGNSYSTEM.MD` | `DataGrid` em largura total, coluna `ID`, detalhe clicável por linha e ações de editar e apagar |
| Padrão de formulário | `DESIGNSYSTEM.MD` | Seções, coluna de contexto e painel em duas colunas responsivas |
| Padrão de contexto | `DESIGNSYSTEM.MD` | `Breadcrumb` em todas as telas, com equipe, módulo e tela atual |
| Primitives compartilhadas | A mapear | Componentes em `ui/` ou diretório equivalente |
| Composições de domínio | A mapear | Componentes em `features/` ou diretório equivalente |

## Blocos criados e reaproveitáveis

Registre todos os blocos criados no projeto, inclusive os internos de uma
feature. Um bloco é um componente React com responsabilidade própria, como
grade, formulário, filtro, cabeçalho, cartão, diálogo, painel lateral, estado
vazio, upload ou ação em lote.

| Bloco | Tipo | Arquivo | Origem | Finalidade e API pública | Estados e acessibilidade | Consumidores | Reaproveitar ou estender |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A mapear | Primitive, composição ou domínio | A mapear | shadcn/ui, ReUI ou próprio | Props, eventos e dados esperados | Foco, teclado, loading, vazio, erro e sucesso | Telas e outros blocos | Quando usar; qual bloco estender antes de criar outro |

## Telas e composição

| Tela ou rota | Arquivo | Componentes React usados | Dados e ações | Estados |
| --- | --- | --- | --- | --- |
| A mapear | A mapear | A mapear | A mapear | Carregando, vazio, erro e sucesso |

## Regras de composição

1. Páginas e rotas coordenam dados e compõem componentes; não concentram a
   grade, formulário, filtros, overlays ou cartões reutilizáveis.
2. Antes de criar um componente, consulte esta tabela e reaproveite o item
   existente quando ele atender à mesma intenção.
3. Todo item instalado de shadcn/ui ou ReUI entra na tabela com seu arquivo,
   origem, explicação, API, estados e consumidores reais.
4. ReUI usa somente itens gratuitos `@reui/c-*`; use shadcn/ui para
  primitives e ReUI para composições de produto.
5. Para dashboards, registre a pergunta operacional, escopo, filtros,
   indicadores, visualizações, tabela de investigação e estados. Prefira
   blocos existentes de ReUI e primitives shadcn/ui antes de criar uma nova
   composição.
6. Em CRUD, reutilize o mesmo `PageHeader` nas telas de lista, detalhe,
   criação e edição. Registre uma única implementação, suas props, variações e
   consumidores; não duplique markup de cabeçalho por página.
7. Linhas de `DataGrid` com detalhe usam link acessível em toda a área; a
   listagem ocupa a largura disponível, exibe a coluna `ID` e oferece ações de
   editar e apagar. Ações internas usam `TableRowAction` ou equivalente e não
   propagam a navegação.
8. Formulários de criar e editar usam seções com coluna de contexto e painel
   em duas colunas nos breakpoints largos, refluindo para uma no mobile.
9. Toda tela renderiza `Breadcrumb` com o nome da equipe ativa, o módulo e o
   título atual. Em Laravel, reaproveite o `Breadcrumb` ou `Breadcrumbs` do
   layout existente e registre o componente real em vez de criar outro.
10. Ao criar um bloco, registre-o nesta tabela na mesma tarefa. Ao alterar ou
  remover um bloco, atualize seus consumidores e a orientação de reuso.

<!-- markdownlint-enable MD013 -->

## SPEC-0001 — blocos e telas implementados

| Bloco | Arquivo | Origem | Consumidores / contrato |
| --- | --- | --- | --- |
| PageHeader | `resources/js/components/page-header.tsx` | próprio | Agenda, Usuários, Projetos e detalhe; título, descrição e ação |
| EmptyState | `resources/js/components/empty-state.tsx` | próprio | Agenda, listas e detalhe; título, descrição e ação opcional |
| FlashMessage | `resources/js/components/flash-message.tsx` | shadcn Alert | Telas internas; `flash.success` compartilhado pelo Inertia |
| UserFormSheet | `resources/js/components/user-form-sheet.tsx` | shadcn Sheet/Input | Usuários; criar, editar e redefinir com foco inicial e erros associados |
| ProjectFormSheet | `resources/js/components/project-form-sheet.tsx` | shadcn Sheet/Checkbox | Projetos; nome e gestores, inativos atribuídos desabilitados |
| ConfirmDialog | `resources/js/components/confirm-dialog.tsx` | shadcn Dialog | Usuários e Projetos; confirmar desativar/arquivar |
| Table | `resources/js/components/ui/table.tsx` | fallback shadcn local | Usuários e Projetos; coluna ID e ações acessíveis |
| AppSidebar, AppHeader, NavMain | `resources/js/components/{app-sidebar,app-header,nav-main}.tsx` | starter kit adaptado | Shell; menu por papel, item ativo e `aria-current` |

Telas: `/agenda` (`pages/agenda.tsx`), `/usuarios` (`pages/usuarios/index.tsx`), `/projetos` (`pages/projetos/index.tsx`), `/projetos/{id}` (`pages/projetos/show.tsx`), `/trocar-senha` (`pages/auth/trocar-senha.tsx`). As listas mostram vazio ou dados; erros de formulário ficam junto ao campo. O registry ReUI e `shadcn add table` não responderam em 60 s/20 s neste ambiente; a tabela usa código local equivalente à primitive shadcn sem pacote novo.

## SPEC-0002 — funis e etapas

| Bloco | Arquivo | Origem | Consumidores / contrato |
| --- | --- | --- | --- |
| FunnelFormSheet | `resources/js/components/funnel-form-sheet.tsx` | shadcn Sheet/Input/Label | `/projetos/{id}`; criar e renomear funil, foco inicial no nome, Esc, erro associado ao campo e retorno de foco pelo Radix |
| StageFormSheet | `resources/js/components/stage-form-sheet.tsx` | shadcn Sheet/Input/Label | `/projetos/{id}/funis/{id}`; criar e renomear etapa, foco inicial no nome, Esc, erro associado ao campo |
| Table | `resources/js/components/ui/table.tsx` | SPEC-0001 | Listas de funis e etapas; largura total com rolagem horizontal no mobile, coluna ID visível |
| ConfirmDialog | `resources/js/components/confirm-dialog.tsx` | SPEC-0001 | Arquivar/desarquivar funil e apagar etapa, com confirmação por teclado |
| PageHeader, FlashMessage, EmptyState | `resources/js/components/` | SPEC-0001 | Cabeçalho, sucesso e estado vazio nas duas telas |

| Tela ou rota | Arquivo | Componentes React usados | Dados e ações | Estados |
| --- | --- | --- | --- | --- |
| `/projetos/{id}` | `resources/js/pages/projetos/show.tsx` | PageHeader, FlashMessage, EmptyState, Table, Badge, FunnelFormSheet, ConfirmDialog | Funis ativos; adm alterna Ativos/Arquivados e cria, edita ou arquiva; gestor consulta ativos | vazio, dados, erro no painel e sem ações de gestão |
| `/projetos/{id}/funis/{id}` | `resources/js/pages/funis/show.tsx` | PageHeader, FlashMessage, EmptyState, Table, Badge, StageFormSheet, ConfirmDialog | Etapas ordenadas; adm adiciona, edita, sobe, desce e apaga; gestor consulta | dados, erro no painel, limites de movimento desabilitados, última etapa protegida |

A navegação principal continua em Projetos. A linha do funil e seu link abrem o detalhe. O shell existente apresenta os breadcrumbs `Projetos / projeto / funil`. Os botões de mover usam `ArrowUp`/`ArrowDown`, têm nome acessível com a etapa e ficam desabilitados nas pontas. Os painéis laterais usam o foco preso e o comportamento de Esc da primitive Radix.

## SPEC-0003 — quadro de negócios

| Bloco | Arquivo | Origem | Consumidores / contrato |
| --- | --- | --- | --- |
| DealFormSheet | `resources/js/components/deal-form-sheet.tsx` | shadcn Sheet/Input/Checkbox | Quadro; cria negócio e pessoa, valida campos, mostra link de duplicidade |
| DealSheet | `resources/js/components/deal-sheet.tsx` | shadcn Sheet/Dialog/Select/Checkbox | Quadro; edita, anota, ganha, perde, reabre e gerencia responsáveis conforme permissão |
| LossReasonFormSheet | `resources/js/components/loss-reason-form-sheet.tsx` | shadcn Sheet/Input | Projeto; cria e renomeia motivo de perda |
| ConfirmDialog | `resources/js/components/confirm-dialog.tsx` | SPEC-0001, shadcn Dialog | Etapas recebem Select de destino e erro no próprio diálogo; motivos usam desativação |
| PageHeader, FlashMessage, EmptyState | `resources/js/components/` | SPEC-0001 | Quadro e projeto; título, feedback, pessoa reutilizada e estados vazios |

| Tela ou rota | Arquivo | Componentes React usados | Dados e ações | Estados |
| --- | --- | --- | --- | --- |
| `/projetos/{p}/funis/{f}/negocios` | `resources/js/pages/negocios/index.tsx` | PageHeader, ToggleGroup, Input, Table, Badge, Avatar, Select, DealFormSheet, DealSheet | Kanban ou lista; busca; encerrados; mover por arrastar nativo ou Select; painel por `?negocio=` | vazio, busca sem resultado, atraso por texto e ícone, formulário com erro, duplicidade e leitura de arquivados |
| `/projetos/{p}` | `resources/js/pages/projetos/show.tsx` | PageHeader, Table, LossReasonFormSheet, ConfirmDialog | Adm cria, renomeia e desativa motivos; linha do funil segue para sua página | ativos, inativos e erro de validação |
| `/projetos/{p}/funis/{f}` | `resources/js/pages/funis/show.tsx` | PageHeader, Table, ConfirmDialog, Select | Botão Abrir quadro; apagar etapa com escolha de destino e erro | etapa vazia ou com negócios; permissão de adm |

O acesso ao quadro parte de Projetos → projeto → funil → Abrir quadro. Breadcrumbs no layout: Projetos / projeto / funil / Quadro. O painel usa o foco preso e Esc do Radix; em celular ocupa toda a largura. A lista mantém ID visível e a ação de mover por teclado; o kanban tem rolagem horizontal com snap em telas estreitas. Não há item novo no menu principal.
