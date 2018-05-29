<?php 
require('_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();

$template = $app->template('./data_error.html.twig');
$TwigTemplateVariables = array();


echo $template->render($TwigTemplateVariables);
?>