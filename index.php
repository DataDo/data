<?php

require_once 'vendor/autoload.php';

use DataDo\Data\Repository;

class User {
    private $id;
    public $username;
}

$pdo = new \PDO('mysql:host=localhost;dbname=test', 'xillio', 'xillio');
$repo = new Repository(User::class, 'user', $pdo);

$repo->get();

function show($message) {
    print_r(PHP_EOL . json_encode($message, JSON_PRETTY_PRINT));
}