<?php
require('../_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('../_templates/visiting.html.cs');
$template->add_data( "base" , $app->base() );
$curl = new cURL(null);
$collection = GriffinCollection::instance($app , $curl );
$curl->authenticate( $collection->getLoginUrl() );
$_SESSION['authtoken'] = array( 'authtoken' => $curl->__toString());
$collection = GriffinCollection::instance( $app , $curl ,  $_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
$template->show();
?>