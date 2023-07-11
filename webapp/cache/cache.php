<?php

require __DIR__ . "/../../vendor/autoload.php";
use GuzzleHttp\Client;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new Application();
$client = new Client();