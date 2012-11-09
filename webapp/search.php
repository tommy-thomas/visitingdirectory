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
/*
 * Error messages
 */
$error_messages = array(
	'no_select' => 'Please select a commitee.',
	'no_name' => 'Please enter a first or last name.'
);
if( isset($_GET['error']) &&  isset($error_messages[$_GET['error']]) )
{
		$template->add_data( "authentication_error" , $error_messages[$_GET['error']] );
}
$template->show();
?>