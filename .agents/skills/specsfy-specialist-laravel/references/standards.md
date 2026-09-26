# Padrões e referências Laravel

## Checklist por superfície

- HTTP: rotas estáveis, validação antes do domínio, autorização explícita e erros coerentes.
- Domínio: invariantes testáveis sem depender do transporte.
- Eloquent: colunas selecionadas, eager loading intencional, casts e transações conscientes.
- Filas: payload serializável, idempotência, unicidade quando necessária e observabilidade.
- Dados: migration expand/contract para mudanças incompatíveis e backup verificado.
- Segurança: mass assignment controlado, escaping, CSRF, rate limit e secrets fora do código.
- Operação: config cacheável, health checks, workers reiniciáveis e deploy reversível.

## Fontes oficiais

- Documentação Laravel: https://laravel.com/docs
- Ciclo de vida da requisição: https://laravel.com/docs/lifecycle
- Autorização: https://laravel.com/docs/authorization
- Eloquent e relacionamentos: https://laravel.com/docs/eloquent-relationships
- Filas: https://laravel.com/docs/queues
- Banco e migrations: https://laravel.com/docs/migrations
- Testes: https://laravel.com/docs/testing
- OWASP Laravel Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Laravel_Cheat_Sheet.html

Confirme a versão instalada antes de usar uma API.
