<?php 
// 'crupright@sflaw.com'
require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('search.html.cs');
$curl = new cURL(null);
$collection = new Collection($app,$curl);
$collection->loadCommitteeTemplateData($template);
$template->add_data("base", $app->base() );
$template->show();
?>