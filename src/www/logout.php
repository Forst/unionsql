<?php

$authdata = [
    'needs_authentication' => false,
    'db_user' => 'anon'
];
require_once('include/prereq.php');

delete_cookie();

header('Location: /');
exit();

?>
