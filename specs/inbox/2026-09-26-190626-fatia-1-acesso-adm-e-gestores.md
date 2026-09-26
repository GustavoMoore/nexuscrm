# Inbox: Fatia 1 - Acesso adm e gestores

| Metadado | Valor |
| --- | --- |
| Status | Capturada |
| Capturada em | 2026-09-26T22:06:26Z |
| Slug | fatia-1-acesso-adm-e-gestores |
| Origem | Input do usuário |
| Processamento | Análise inicial sem perguntas |
| Sessão de descoberta | Captura avulsa. |
| Turno da conversa | Não se aplica. |
| Integridade do original | SHA-256 `77a2e7b7dcbfdbf6665d05a2f052c1deed7a60443a408853c49bc1f1ab436a35` |
| Backlog derivado | Nenhum |
| Spec derivada | Nenhuma |

## Texto original

Fatia 1 — Acesso: adm e gestor; o adm cria contas de gestor e projetos e escolhe quais gestores atendem cada projeto; remover o cadastro aberto (register) do starter kit; gestor vê só os projetos atribuídos. Visual: sem marca por enquanto, só o nome Nexus, sem logo; referências visuais virão depois.

## Contexto consultado

Nenhuma fonte contextual consultada.

## Resumo processado

**Inferência:** Primeira fatia do núcleo: papéis adm/gestor, contas criadas pelo adm, projetos e atribuição gestor-projeto.

## Análise inicial

### Problema ou oportunidade

**Declaração ou inferência identificada:** Sem controle de quem acessa quais projetos; o starter kit permite cadastro aberto.

### Pessoas afetadas ou beneficiadas

**Declaração ou inferência identificada:** Adm (1) e gestores do time comercial do cliente.

### Resultado ou valor esperado

**Declaração ou inferência identificada:** Base de acesso segura sobre a qual funis, quadro e agenda são construídos.

### Sinais de escopo, regras ou solução

**Sinais extraídos, não decisões:** Só adm cria contas; sem register; gestor restrito a projetos atribuídos; nome Nexus sem logo.

### Informações que talvez precisem ser guardadas

**Sinais para conversar depois, não confirmação:** Papel do usuário; projetos; vínculo gestor-projeto.

### Riscos e dependências

**Análise preliminar:** Autorização precisa ser no servidor; Supabase Data API deve ficar desligada em produção.

## Possíveis direções futuras

**Hipóteses para backlog ou spec, não requisitos:** Backlog da fatia 1 e spec com telas de usuários e projetos.

## Pontos a revisar no futuro

**A revisar:** Como o gestor recebe a primeira senha; o que o gestor vê ao entrar antes de existirem funis.

## Rastreabilidade

- Formulação original preservada integralmente nesta captura.
- Análises não substituem decisões do usuário.
- Backlogs e specs derivados devem referenciar este arquivo.

## Próximo passo

Manter em `specs/inbox/` ou refinar com `$specsfy-02-backlog`.
