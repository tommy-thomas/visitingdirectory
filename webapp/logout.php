<?php
require __DIR__ . "/../vendor/autoload.php";
/**
 * The Application object.
 */
$app = new \UChicago\AdvisoryCouncil\Application();
$app->endSession();
$app->redirect('https://shibboleth2.uchicago.edu/idp/logout.html');
?>