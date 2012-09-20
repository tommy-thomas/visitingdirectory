<?php 
// 'crupright@sflaw.com'
require('_classes/autoload.php');
//apc_delete('active_committees');
/**
 * The Application object.
 */
$app = Application::app();
$template = $app->template('search.html.cs');
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template->add_data('LoggedIn' , true);
}

$curl = new cURL(null);
$collection = new Collection($app , $curl , $_SESSION['authtoken'] );
$collection->loadCommitteeTemplateData($template);
if( isset($_GET['error']) && ($_GET['error'] == 'no_select'))
{
	$template->add_data( "authentication_error" , 'Please select a commitee.' );
}
$template->show();
?>