<?php

$authdata = [
    'needs_authentication' => false,
    'db_user' => 'anon'
];
require_once('include/prereq.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        !empty($_POST['username']) &&
        !empty($_POST['password'])
    ) {
        $u = sql_escape($_POST['username']);
        $p = md5($_POST['password']);

        $r = query_one("SELECT `password` FROM `{$cfg['db_prefix']}uzwers` WHERE `username` = '{$u}';");

        if (empty($r) || $r['password'] !== $p) {
            array_push($msgs, [
                'level' => 'danger',
                'text' => 'Неверное имя пользователя или пароль.'
            ]);
        } else {
            write_cookie($_POST['username']);

            header('Location: /');
            exit();
        }
    } else {
        array_push($msgs, [
            'level' => 'danger',
            'text' => 'Вы не ввели имя пользователя или пароль.'
        ]);
    }
}



echo $twig->render('login.twig', [
    'cfg' => $cfg,
    'msgs' => $msgs,
    'auth' => $authdata,

    'post' => $_POST,

    'page' => 'login',
    'title' => 'Вход в систему'
]);

?>
