<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Código curto gerado automaticamente
    |--------------------------------------------------------------------------
    |
    | Tamanho e alfabeto (base62) do código gerado quando o usuário não
    | informa um código próprio. Diferencia maiúsculas de minúsculas.
    |
    */

    'code_length' => 6,

    'code_alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',

    /*
    |--------------------------------------------------------------------------
    | Código personalizado
    |--------------------------------------------------------------------------
    */

    'custom_code_min' => 3,

    'custom_code_max' => 50,

    'custom_code_pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

    /*
    |--------------------------------------------------------------------------
    | Palavras reservadas
    |--------------------------------------------------------------------------
    |
    | Códigos que não podem ser usados, pois colidem com rotas do sistema
    | ou arquivos públicos.
    |
    */

    'reserved_codes' => [
        'api', 'admin', 'build', 'css', 'js', 'img', 'assets', 'storage',
        'up', 'gerenciar', 'encurtar', 'login', 'logout', 'index',
        'favicon.ico', 'robots.txt',
    ],

    /*
    |--------------------------------------------------------------------------
    | PIN de gerenciamento
    |--------------------------------------------------------------------------
    |
    | Alfabeto sem caracteres ambíguos (0/O, 1/I/L). O PIN é armazenado
    | apenas como HMAC-SHA256 usando a APP_KEY.
    |
    */

    'pin_length' => 8,

    'pin_alphabet' => 'ABCDEFGHJKMNPQRSTUVWXYZ23456789',

    'pin_attempts_per_minute' => 5,

    'shorten_per_minute' => 20,

    'max_url_length' => 2048,

];
