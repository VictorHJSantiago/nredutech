<?php

return [

    'driver' => env('HASH_DRIVER', 'argon2id'), 
    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

    'argon2id' => [
        'memory' => env('ARGON_MEMORY', 131072), 
        'time' => env('ARGON_TIME', 8),         
        'threads' => 1,                        
        'verify' => true,
    ],

    'rehash_on_login' => true,

];