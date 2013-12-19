<?php 
require('_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();

$template = $app->template('data_error.html.cs');

$template->show();
?>