<?php

require __DIR__ . "/../../vendor/autoload.php";
use GuzzleHttp\Client;
use use UChicago\AdvisoryCouncil\Application;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new Application();
$client = new Client();
$repo = new Repository($client, $app->apiUrl());
$repo->cache();