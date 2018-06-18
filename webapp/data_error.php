<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */
$app = new \UChicago\AdvisoryCouncil\Application();

$template = $app->template('data_error.html.twig');
$TwigTemplateVariables = array();


echo $template->render($TwigTemplateVariables);
?>