<?php

return [

    'driver' => env('HASH_DRIVER', 'argon'),

    'bcrypt' => [
        'rounds' => 10,
        'verify' => true,
    ],

    'argon' => [
        'memory' => 1024,
        'threads' => 2,
        'time' => 2,
    ],

];
