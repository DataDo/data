<?php
/**
 * //TODO PHPDoc
 * @author Thomas Biesaart
 */
require_once 'vendor/autoload.php';

use DataDo\Data\Repository;

class User {
    public $id;
    public $username;
}

$pdo = new \PDO('mysql:host=localhost;dbname=test', 'xillio', 'xillio');
$repo = new Repository(User::class, 'user', $pdo);

$repo->findAll();
$repo->getAll();
$repo->getAllIdByUsername('bob');