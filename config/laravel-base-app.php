<?php

declare(strict_types=1);

return [
    /*
    |----------------------------------------------------------------------------------------
    | Here are the settings for the `devbrlucas:init-user`
    | command
    |
    | initial_user_fields - Fields requested in the terminal for registration
    | initial_user_callback - Class with an __invoke method to handle the created user
    |
    |----------------------------------------------------------------------------------------
    |
    */

    'initial_user_fields' => [
        'name',
        'email',
    ],
    'initial_user_callback' => null,
    
];