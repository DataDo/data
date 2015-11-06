<?php

use DataDo\Data\Repository;

require 'vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=test', 'xillio', 'xillio');

class Factuur {
    private $factuur_nr;
    private $prijs;
    private $bestand;
}


$fRepo = new Repository(Factuur::class, $pdo, 'factuur_nr');

print_r($fRepo->findByPrijsMoreThan(3500));


