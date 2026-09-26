# Backlog: Acesso adm e gestores

| Metainformação | Valor |
| --- | --- |
| ID | BACKLOG-0001 |
| Status | Promoted |
| Produto | NexusCRM |
| Épico | Núcleo (MVP) |
| Funcionalidade | Acesso e projetos |
| Tipo | História |
| Prioridade | Alta — fatia 1, desbloqueia todo o núcleo |
| Milestones | |
| Criado em | 2026-09-26 |
| Spec promovida | specs/defined/0001-acesso-adm-gestores/spec.md |

## Ideia original

Adm cria contas de gestor e projetos e escolhe quais gestores atendem cada projeto; sem cadastro aberto; gestor vê só os projetos atribuídos.

## Problema percebido

Starter kit permite cadastro aberto e não tem papéis nem projetos.

## Pessoa afetada ou beneficiada

Adm e gestores do time comercial do cliente.

## Resultado ou valor esperado

Acesso seguro por papel, base para funis, quadro e agenda.

## Contexto

Fatia 1 do núcleo NexusCRM; origem specs/inbox/2026-09-26-190626-fatia-1-acesso-adm-e-gestores.md

## Referências relacionadas

- specs/inbox/2026-09-26-190626-fatia-1-acesso-adm-e-gestores.md
- PROJECT.md, .specsfy/RULES.md, DESIGNSYSTEM.MD (direção visual vigente)

## Comportamento esperado

- Não existe cadastro aberto: a tela e a rota de registro deixam de existir.
- O primeiro adm é criado por comando/seed na instalação.
- O adm cria a conta do gestor com nome, e-mail e uma senha provisória, que ele
  repassa ao gestor por fora (ex.: WhatsApp). No primeiro login o gestor é
  obrigado a trocar a senha antes de usar qualquer tela.
- O adm cria projetos (nome) e marca quais gestores atendem cada projeto.
- O gestor vê e acessa somente os projetos atribuídos a ele; o adm vê todos.
- O produto aparece como "Nexus" em texto, sem logo.

## Regras de negócio

- Papéis: `adm` (1) e `gestor` (N).
- Só o adm cria contas, projetos e atribuições.
- Autorização no servidor: acesso a projeto não atribuído retorna 403/404, não só some da tela.
- Senha provisória expira no primeiro login (troca obrigatória).
- Gestor que sai é desativado, nunca apagado: não entra mais, nome permanece no histórico, pode ser reativado.
- Projeto não é apagado; pode ser arquivado (some das listas do gestor, adm ainda vê).
- Sem envio de e-mail nesta fatia (convite por e-mail fica para quando houver serviço de e-mail).

## Critérios de aceitação

- `/register` não existe (404) e a tela de login não mostra link de cadastro.
- Adm cria gestor com senha provisória; gestor entra e é levado à troca de senha antes de qualquer outra tela.
- Gestor desativado não consegue entrar; reativado volta a entrar.
- Gestor vê só projetos atribuídos; acessar URL de projeto não atribuído é negado pelo servidor.
- Gestor não vê o menu Usuários nem consegue acessar suas rotas.
- Adm cria, renomeia e arquiva projeto e marca/desmarca gestores atendentes.
- Nome "Nexus" no lugar de "Laravel Starter Kit"; `tsc --noEmit` sem erros.

## Qualidades e operação

- Segurança: autorização por papel e por projeto no servidor (Policy); senha provisória com troca obrigatória.
- Privacidade: gestor não enxerga dados de projeto não atribuído.
- Desempenho e volume: baixo (1–10 usuários, dezenas de projetos).
- Auditoria e observabilidade: fora desta fatia.

## Dependências

- Nenhuma registrada.

## Situações de erro

- E-mail já usado ao criar gestor → erro no campo.
- Gestor desativado tenta entrar → mensagem de conta desativada.
- Adm tenta desativar a si mesmo → bloqueado.

## Escopo

- Dentro: papéis, criação/edição/desativação de gestor, projetos (criar/renomear/arquivar), atribuição gestor↔projeto, menu, troca obrigatória de senha, remoção do register, nome Nexus, correção dos 2 erros de `tsc` herdados.
- Fora: funis, negócios, agenda com conteúdo, convite por e-mail, múltiplos adms, Supabase de produção.

## Dúvidas, decisões e riscos

- Decidido (2026-09-26): senha provisória definida pelo adm + troca obrigatória no primeiro login.
- Decidido: visual neutro, nome "Nexus" em texto, sem logo.
- Decidido (2026-09-26): criar/editar abre em painel lateral (gaveta à direita), lista continua visível. Padrão para todo o produto.
- Decidido (2026-09-26): gestor que sai é desativado (nunca apagado).
- Decidido (padrão assumido, sem objeção): menu lateral Minha agenda / Projetos / Usuários (só adm).
- Risco: "esqueci minha senha" depende de e-mail; sem e-mail configurado, o adm redefine a senha provisória.

## Pronto para desenvolvimento

- [x] O problema e a pessoa beneficiada estão claros.
- [x] O evento inicial e o resultado esperado estão claros.
- [x] Permissões, regras e exceções relevantes estão claras.
- [x] O resultado pode ser verificado objetivamente.
- [x] Segurança, privacidade e desempenho foram avaliados conforme o risco.
- [x] Fora de escopo, dependências e decisões pendentes estão registrados.

## Próximo passo

Aprofundar nesta etapa até o item ficar pronto para `$specsfy-03-specify`.
