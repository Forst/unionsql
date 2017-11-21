<?php

$authdata = [
    'needs_authentication' => false,
    'db_user' => 'anon'
];
require_once('include/prereq.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['query'])) {
    $q = $_POST['query'];
    $news = query_all("
        SELECT
            `{$cfg['db_prefix']}nyoows`.`id` AS 'id',
            `{$cfg['db_prefix']}nyoows`.`title` AS 'title',
            `{$cfg['db_prefix']}nyoows`.`text` AS 'text',
            `{$cfg['db_prefix']}nyoows`.`created` AS 'created',
            `{$cfg['db_prefix']}uzwers`.`displayname` AS 'name'
        FROM `{$cfg['db_prefix']}nyoows` INNER JOIN `{$cfg['db_prefix']}uzwers`
        ON `{$cfg['db_prefix']}nyoows`.`author` = `{$cfg['db_prefix']}uzwers`.`username`
        WHERE `{$cfg['db_prefix']}nyoows`.`text` LIKE '%{$q}%'
        ORDER BY `id` ASC
    ");
} else {
    $news = query_all("
        SELECT
            `{$cfg['db_prefix']}nyoows`.`id` AS 'id',
            `{$cfg['db_prefix']}nyoows`.`title` AS 'title',
            `{$cfg['db_prefix']}nyoows`.`text` AS 'text',
            `{$cfg['db_prefix']}nyoows`.`created` AS 'created',
            `{$cfg['db_prefix']}uzwers`.`displayname` AS 'name'
        FROM `{$cfg['db_prefix']}nyoows` INNER JOIN `{$cfg['db_prefix']}uzwers`
        ON `{$cfg['db_prefix']}nyoows`.`author` = `{$cfg['db_prefix']}uzwers`.`username`
        ORDER BY `{$cfg['db_prefix']}nyoows`.`id` ASC
    ");
}

echo $twig->render('index.twig', [
    'cfg' => $cfg,
    'msgs' => $msgs,
    'authdata' => $authdata,

    'post' => $_POST,

    'page' => 'index',
    'title' => 'Новости',

    'news' => $news
]);

?>
