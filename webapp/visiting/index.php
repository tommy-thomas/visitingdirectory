<?php
require __DIR__ . "/../../vendor/autoload.php";

/**
 * The Application object.
 */
$app = new \UChicago\AdvisoryCouncil\Application();

$template = $app->template('./placeholder.html.twig');


echo $template->render([]);
?>