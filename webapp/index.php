<?php
require __DIR__ . "/../vendor/autoload.php";


$app = new UChicago\AdvisoryCouncil\Application;

$app->redirect('./search.php');