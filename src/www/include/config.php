<?php

$cfg = [
    // База данных: адрес узла
    'db_host' => 'localhost',

    // База данных: имя БД
    'db_name' => 'sqlinj',

    // База данных: префикс таблиц
    'db_prefix' => '',

    // База данных: данные для входа
    'db_credentials' => [
        // Пользователь только для чтения (SELECT)
        'anon' => [
            'username' => 'sqlinj_anon',
            'password' => 'PWD_ANON'
        ],

        // Пользователь для чтения и записи (SELECT, INSERT)
        'admin' => [
            'username' => 'sqlinj_admin',
            'password' => 'PWD_ADMIN'
        ]
    ]
];

?>
