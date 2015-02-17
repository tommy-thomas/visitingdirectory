<?php
require('../_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();
$template = $app->template('placeholder.html.cs');
$template->add_data( "base" , $app->base() );
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */

$curl = new cURL(null);
$collection = GriffinCollection::instance($app , $curl );
$curl->authenticate( $collection->getLoginUrl() );
$_SESSION['authtoken'] = array( 'authtoken' => $curl->__toString());


$template->show();
?>