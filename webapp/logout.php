<?php
require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
$app->endSession();
$app->redirect('https://shibboleth2.uchicago.edu/idp/logout.html');
?>