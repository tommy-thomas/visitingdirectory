<?php

require __DIR__ . "/../../vendor/autoload.php";
error_reporting(E_ERROR | E_PARSE);
use GuzzleHttp\Client;
use UChicago\AdvisoryCouncil\Application;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new Application();
$client = new Client();
$repo = new Repository($client, $app->apiUrl());

error_log("New cache build started: ".date("g:i a") );
$repo->cache();
error_log("New cache build completed: ".date("g:i a") );