<?php 
require('_classes/autoload.php');
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
$collection = Collection::instance($app , $curl , $_SESSION['authtoken'] );
$collection->loadCommitteeTemplateData($template);
if( isset($_GET['error']) && ($_GET['error'] == 'no_select'))
{
	$template->add_data( "authentication_error" , 'Please select a commitee.' );
}
$template->show();
?>