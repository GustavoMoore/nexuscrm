# Projeto

## História e motivação

Descreva a origem do projeto, o problema que motivou sua criação e sua evolução.

## Finalidade

Explique para que o sistema serve e qual resultado entrega.

## Pessoas e contexto de uso

Registre quem usa o sistema e em quais situações.

## Capacidades principais

Liste as capacidades estáveis sem transformar esta descrição em inventário de
rotas, schemas ou tarefas.

## Limites

Explique o que o sistema deliberadamente não faz.

## Contexto técnico

Modelo inicial sugerido a partir de: **Laravel**. Detalhes verificáveis
ficam em `.specsfy/STACK.md` e `.specsfy/DATABASE.md`.

Para Laravel, descreva módulos de domínio, fronteiras HTTP/console e use `database/migrations` como primeira evidência do mapa de dados.

## Evolução da primeira fatia

A SPEC-0001 define o acesso inicial: o administrador cria e desativa gestores, administra projetos e atribuições, e gestores acessam apenas projetos ativos atribuídos. A interface inclui login, troca de senha provisória, Minha agenda vazia, Usuários, Projetos e detalhe com estado vazio de funis. Cadastro público, recuperação de senha por e-mail e autoexclusão foram removidos do código. A efetivação no banco e a verificação funcional dependem de restabelecer a conexão Postgres local; consulte a evidência de execução da entrega.
