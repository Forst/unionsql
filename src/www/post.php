<?php

$authdata = [
    'needs_authentication' => true,
    'db_user' => 'admin'
];
require_once('include/prereq.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        !empty($_POST['title']) &&
        !empty($_POST['text']) &&
        strlen($_POST['title']) <= 64 &&
        strlen($_POST['text']) <= 255
    ) {
        $title = sql_escape($_POST['title']);
        $text = sql_escape($_POST['text']);
        $user = sql_escape($authdata['username']);

        $id = query("INSERT INTO `{$cfg['db_prefix']}nyoows` (`author`, `title`, `text`) VALUES ('{$user}', '{$title}', '{$text}');");

        header("Location: /#{$id}");
        exit();
    }
    else {
        array_push($msgs, [
            'level' => 'danger',
            'text' => 'Вы не ввели заголовок или текст новости, либо они недопустимой длины.'
        ]);
    }
}

echo $twig->render('post.twig', [
    'cfg' => $cfg,
    'msgs' => $msgs,
    'authdata' => $authdata,

    'post' => $_POST,

    'page' => 'post',
    'title' => 'Разместить новость',
]);

?>
