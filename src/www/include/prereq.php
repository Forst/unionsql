<?php

// Если HEAD-запрос, то вообще ничего не отправлять
if ($_SERVER['REQUEST_METHOD'] === 'HEAD')
    die();

// Загрузка зависимостей
$dependencies = [
    'vendor/autoload.php',
    'config.php',
    'db.php'
];

foreach ($dependencies as $dependency)
    require_once($dependency);


// Отображение всех ошибок (TODO убрать в продакшене)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Шаблонизатор Twig
$twig_loader = new Twig_Loader_Filesystem('template');
$twig = new Twig_Environment($twig_loader, array(
    'cache' => 'cache',
    'auto_reload' => true
));


// Таблица конфигурации
$cfg_tmp = query_all("SELECT `ckey`, `cvalue`, `ctype` FROM `{$cfg['db_prefix']}config`");

foreach($cfg_tmp as $item)
    if ($item['ctype'] === 'string')
        $cfg[$item['ckey']] = $item['cvalue'];
    elseif ($item['ctype'] === 'int')
        $cfg[$item['ckey']] = (int)$item['cvalue'];
    elseif ($item['ctype'] === 'bool')
        $cfg[$item['ckey']] = ($item['cvalue'] != '0');
    elseif ($item['ctype'] === 'json')
        $cfg[$item['ckey']] = json_decode($item['cvalue'], true);

unset($cfg_tmp);


// Аутентификация пользователя
require_once('auth.php');


// Информационные сообщения для показа пользователю
$msgs = [];

?>
