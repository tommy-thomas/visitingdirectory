<?php
require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
$curl = new cURL(null);
if( $app->isAuthorized() && isset($_SESSION['authtoken']) && !isset($_GET['err']))
{
	$collection = Collection::instance( $app , $curl ,  $_SESSION['authtoken']);
	$collection->checkCache();
}
?>