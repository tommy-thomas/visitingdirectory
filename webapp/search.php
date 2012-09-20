<?php 
// 'crupright@sflaw.com'
require('_classes/autoload.php');
//apc_delete('active_committees');
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
$collection = new Collection($app , $curl , $_SESSION['authtoken'] );
$collection->loadCommitteeTemplateData($template);
$template->show();
?>