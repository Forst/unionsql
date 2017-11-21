<?php

const HMAC_ALG = 'sha256';

// Чтение информации из куки
function read_cookie() {
    global $cfg, $authdata;

    $authdata['is_authenticated'] = false;

    if (!empty($_COOKIE['auth'])) {
        try {
            $jdata = json_decode($_COOKIE['auth'], true);

            if (
                !array_key_exists('username', $jdata) ||
                !array_key_exists('sig', $jdata)
            ) return null;

            if (hash_hmac(HMAC_ALG, $jdata['username'], $cfg['hmac_key']) !== $jdata['sig'])
                throw new Exception('Неверный HMAC.');
        } catch (Exception $e) {
            return null;
        }

        $u = sql_escape($jdata['username']);
        $r = query_one("SELECT `displayname` FROM `{$cfg['db_prefix']}uzwers` WHERE `username` = '{$u}';");

        if ($r) {
            $authdata['is_authenticated'] = true;
            $authdata['username'] = $jdata['username'];
        } else {
            delete_cookie();
        }
    } else {
        return null;
    }
}

// Запись информации в куки
function write_cookie($data) {
    global $cfg;

    $hmac = hash_hmac(HMAC_ALG, $data, $cfg['hmac_key']);

    $jdata = json_encode([
        'username' => $data,
        'sig' => $hmac
    ]);

    setcookie('auth', $jdata, time() + $cfg['session_duration'], '/');
}

// Удаление куки
function delete_cookie() {
    global $cfg;

    header("Set-Cookie: auth=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0; path=/");
}


read_cookie();

if (!$authdata['is_authenticated'] && $authdata['needs_authentication']) {
    header('Location: /login.php');
    exit();
}

?>
