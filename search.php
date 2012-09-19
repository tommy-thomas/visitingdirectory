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
if( isset($_SESSION['authtoken'])  && !apc_exists('all_member_data') )
{
	$collection->setAllMemberData($_SESSION['authtoken']);
}
$collection->loadCommitteeTemplateData($template);
$template->show();
?>