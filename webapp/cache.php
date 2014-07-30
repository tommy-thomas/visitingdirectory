<?php
include __DIR__ . "/vendor/autoload.php";
/**
 * The Application object.
 */
$app = Application::app();
$curl = new cURL(null);
if( isset($_SESSION['authtoken']) && !isset($_GET['err']))
{
	set_time_limit(0);
	/*
	 * Double check big pay load is cached.
	 */
	$collection = GriffinCollection::instance( $app , $curl ,  $_SESSION['authtoken']);
	$collection->checkCache();
}
?>