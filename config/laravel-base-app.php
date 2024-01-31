<?php

declare(strict_types=1);

return [
    /*
    |----------------------------------------------------------------------------------------
    |
    | initial_user_fields           Campos requisitados ao criar um usuário com o comando
    |                               `laravel-base-app:init-user`
    | initial_user_callback         Classe com __invoke() que é chamada quando o usuário
    |                               é criado com o comando `laravel-base-app:init-user`,
    |                               tendo o `model` informado (\App\Models\User por exemplo)
    |                               como parâmetro
    | create_states_cities_tables   Se `true`, executa as migrations que cria as tabelas de
    |                               cidades e estados brasileiros
    |
    |----------------------------------------------------------------------------------------
    |
    */

    'initial_user_fields' => [
        'name',
        'email',
    ],
    'initial_user_callback' => null,
    'create_states_cities_tables' => false,
    
];