# Backlog: Quadro de negócios

| Metainformação | Valor |
| --- | --- |
| ID | BACKLOG-0003 |
| Status | Promoted |
| Produto | A esclarecer |
| Épico | A esclarecer |
| Funcionalidade | A esclarecer |
| Tipo | A esclarecer |
| Prioridade | Não priorizado |
| Milestones | |
| Criado em | 2026-09-26 |
| Spec promovida | specs/defined/0003-quadro-negocios/spec.md |

## Ideia original

Negócios nos funis, vistos em kanban e em lista

## Problema percebido

Não há onde registrar e acompanhar negócios por etapa

## Pessoa afetada ou beneficiada

Gestores (usam) e adm

## Resultado ou valor esperado

Gestor cria, move, ganha e perde negócios em kanban ou lista

## Contexto

Fatia 3 do núcleo; depende das SPEC-0001 e SPEC-0002

## Referências relacionadas

- Nenhuma referência relevante encontrada.

## Comportamento esperado

A esclarecer.

## Regras de negócio

- A esclarecer conforme risco e complexidade.

## Critérios de aceitação

- A esclarecer antes de considerar o item refinado.

## Qualidades e operação

- Segurança: a avaliar.
- Privacidade: a avaliar.
- Desempenho e volume: a avaliar.
- Auditoria e observabilidade: a avaliar.

## Dependências

- Nenhuma registrada.

## Situações de erro

- A esclarecer.

## Escopo

- Dentro: a esclarecer.
- Fora: a esclarecer.

## Dúvidas, decisões e riscos

- Nenhum registrado.

## Pronto para desenvolvimento

- [ ] O problema e a pessoa beneficiada estão claros.
- [ ] O evento inicial e o resultado esperado estão claros.
- [ ] Permissões, regras e exceções relevantes estão claras.
- [ ] O resultado pode ser verificado objetivamente.
- [ ] Segurança, privacidade e desempenho foram avaliados conforme o risco.
- [ ] Fora de escopo, dependências e decisões pendentes estão registrados.

## Próximo passo

Aprofundar nesta etapa até o item ficar pronto para `$specsfy-03-specify`.

## Decisões de produto (discovery fatia 3)

- Já decidido: card = negócio (valor R$, etapa, próximo passo com data obrigatório, anotações com autor/data, motivo de perda); pessoa única por projeto, 1 negócio por funil; Ganho/Perdido são estados com motivo de perda de lista; visão kanban e lista; apagar etapa com negócios exige escolher etapa destino (herdado da SPEC-0002).
- P1 (responsável): negócio tem um ou mais responsáveis (N:N). Quem cria vira responsável automaticamente; o adm adiciona/remove responsáveis (só gestores atribuídos ao projeto; mínimo 1). Minha agenda (fatia 4) mostra os negócios em que o usuário é responsável; o quadro mostra todos os negócios do projeto a quem o atende.
- P2 (Ganho/Perdido): negócio encerrado sai do quadro e da lista padrão; filtro "Ganhos / Perdidos" mostra encerrados; negócio encerrado pode ser reaberto.
- P3 (criar negócio): obrigatórios = nome da pessoa + (telefone OU e-mail) + próximo passo com data; etapa inicial = primeira do funil; valor R$ opcional (preenche depois). Se a pessoa já existe no projeto, o sistema a encontra e reaproveita.
- P4 (pessoa repetida): reconhece por telefone OU e-mail (qualquer um que bata, dentro do projeto; telefone comparado só por dígitos, e-mail sem diferenciar maiúsculas). Se a pessoa já tem negócio ABERTO neste funil, bloqueia a criação e mostra link para ele. Refina "1 negócio por funil" = 1 negócio aberto por funil (encerrado não bloqueia novo).
- P5 (mover): arrastar card no kanban, ou menu de etapa (lista, celular e teclado); muda na hora, sem pedir nada. Próximo passo não é alterado ao mover.
- P6 (motivos de perda): lista por projeto, mantida pelo adm (cadastrar, renomear, desativar; desativado some da escolha mas continua nos negócios antigos). Projeto nasce com: Preço, Sem resposta, Comprou de outro, Sem interesse, Outro.
- P7 (lista): colunas pessoa, etapa, valor, próximo passo + data, responsáveis. Busca por nome, telefone ou e-mail. Ordem fixa pela data do próximo passo; atrasados primeiro e destacados (não só por cor). Sem ordenação por coluna nem filtros extras nesta fatia.
- P8 (card kanban): nome da pessoa, valor, próximo passo + data (marca de atrasado), iniciais dos responsáveis. Ordem na coluna = mesma da lista (data do próximo passo, atrasados em cima); sem ordenação manual. Topo da coluna: contagem + soma dos valores. Busca da lista também filtra o kanban.
- P9 (abrir negócio): painel lateral sobre o quadro (tela cheia no celular). Edita próximo passo, data, valor, etapa e dados da pessoa; anotações só acrescentam (autor/data, sem editar/apagar); botões Ganhar, Perder (exige motivo), Reabrir. Adm gerencia responsáveis no mesmo painel.
- P10 (excluir/reabrir): ninguém exclui negócio nesta fatia (o que não serve vira Perdido). Reabrir volta para a etapa em que estava ao encerrar.
- Consequências derivadas (sem pergunta nova): (a) reabrir é bloqueado se a pessoa já tem outro negócio aberto no mesmo funil (regra P4), com link para ele; (b) apagar etapa (SPEC-0002) também realoca os negócios encerrados que estão nela, para o reabrir sempre ter etapa válida.
