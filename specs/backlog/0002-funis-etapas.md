# Backlog: Funis e etapas

| Metainformação | Valor |
| --- | --- |
| ID | BACKLOG-0002 |
| Status | Promoted |
| Produto | NexusCRM |
| Épico | Núcleo (MVP) |
| Funcionalidade | Funis e etapas |
| Tipo | Feature |
| Prioridade | P1 |
| Milestones | Núcleo (MVP) — fatia 2 |
| Criado em | 2026-09-26 |
| Spec promovida | specs/defined/0002-funis-etapas/spec.md |

## Ideia original

Adm cria funis (2 a 4 por projeto) com etapas próprias; gestor usa

## Problema percebido

Cada projeto roda vários funis de marketing com etapas diferentes e hoje não há onde organizá-los

## Pessoa afetada ou beneficiada

Adm (configura) e gestores (usam)

## Resultado ou valor esperado

Cada projeto tem seus funis com etapas prontas para receber negócios na fatia 3

## Contexto

Fatia 2 do núcleo; depende da SPEC-0001

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

## Decisões de produto (discovery fatia 2)

- Já decidido: 2 a 4 funis por projeto; só adm cria/renomeia/arquiva funis e etapas; gestor só usa; funil com negócios não é apagado, só arquivado; Ganho/Perdido são estados, não colunas.
- P1 (etapas iniciais): funil novo nasce com etapas padrão editáveis: Novo contato → Qualificação → Proposta → Negociação; adm renomeia, reordena e remove.
- P2 (apagar etapa com negócios): o sistema exige que o adm escolha a etapa de destino antes de apagar; nenhum negócio fica sem etapa. Todo funil mantém ao menos 1 etapa.
- P3 (limite de funis): sem trava; "2 a 4" é uso típico, não regra do sistema.
- Decidido pelo orquestrador: reordenar etapas com botões ↑↓ (sem lib de arrastar; arrastar entra com o kanban na fatia 3).
- Decidido pelo orquestrador: motivos de perda ficam para a fatia 3 (onde "Perdido" é usado); destino ao apagar etapa com negócios é implementado na fatia 3 (não há negócios ainda) — aqui só a regra de manter ao menos 1 etapa. Funil só é arquivado (apagar funil vazio fica fora, YAGNI).
