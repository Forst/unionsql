<?php

$db = new mysqli(
    $cfg['db_host'],
    $cfg['db_credentials'][$authdata['db_user']]['username'],
    $cfg['db_credentials'][$authdata['db_user']]['password'],
    $cfg['db_name']
);

if ($db->connect_errno)
    die("Не удалось подключиться к базе данных.");

$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
$db->set_charset('utf8mb4');


// Запрос к базе данных (без обработки ответа)
function query($query) {
    global $db;

    $res = $db->query($query);
    $id = $db->insert_id;

    if ($res instanceof mysqli_result)
        $res->free();
    elseif(!$res) {
        // trigger_error('Ошибка выполнения запроса к БД.', E_USER_ERROR);
        $id = -1;
    }

    return $id;
}


// Запрос к базе данных, получение всех строк
function query_all($query) {
    global $db;

    $res = $db->query($query);

    if ($res instanceof mysqli_result) {
        $out = $res->fetch_all(MYSQLI_ASSOC);
        $res->free();
    } else {
        // trigger_error('Ошибка выполнения запроса к БД.', E_USER_ERROR);
        $out = null;
    }

    return $out;
}


// Запрос к базе данных, получение одной строки
function query_one($query) {
    global $db;

    $res = $db->query($query);

    if ($res instanceof mysqli_result) {
        $out = $res->fetch_assoc();
        $res->free();
    } else {
        // trigger_error('Ошибка выполнения запроса к БД.', E_USER_ERROR);
        $out = null;
    }

    return $out;
}


// Экранирование символов для вставки в SQL-запрос
function sql_escape($text) {
    global $db;

    return $db->real_escape_string($text);
}


// Экранирует элементы массива для передачи в SQL-запрос
function sql_prepare_array($input) {
    global $db;

    $output = [];

    foreach ($input as $key => $value) {
        if      (is_string($value)) $output[$key] = sql_escape($value);
        elseif  (is_bool($value))   $output[$key] = ($value ? 1 : 0);
        else                        $output[$key] = $value;
    }

    return $output;
}

?>
