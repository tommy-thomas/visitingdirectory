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
$curl = new cURL(null);
$collection = Collection::instance($app , $curl );
$curl->authenticate( $collection->getLoginUrl() );
$_SESSION['authtoken'] = array( 'authtoken' => $curl->__toString());
$collection = Collection::instance( $app , $curl ,  $_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
$template->show();
?>