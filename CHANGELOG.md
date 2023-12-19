# CHANGELOG

## [1.0.0] - 2023-11-08

- Versão inicial

## [1.0.1] - 2023-12-19

### Adições

- Adicionada classe `XLSX` para gerar arquivos `.xlsx`
- Adicionada configuração `laravel-base-app.initial_user_callback` que irá receber uma classe com `__invoke` para manipular o usuário criado no comando `devbrlucas:init-user`

### Modificações

- O método `Authenticatable::user()` foi renomeado para `Authenticatable::logged()`