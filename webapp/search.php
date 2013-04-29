<?php 
require('_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template = $app->template('search.html.cs');
	$template->add_data('LoggedIn' , true);
	$template->add_data( "base" , $app->base() );
}

$curl = new cURL(null);
$collection = GriffinCollection::instance( $app , $curl , $_SESSION['authtoken'] );
$collection->loadCommitteeTemplateData($template);
//$collection->clearGriffinCollection();

/*if( !isset($_SESSION['async_request_flag']) )
{
	$arr = parse_url ($app->base() );
	$req = new AsyncRequest($arr['host'], $arr['path'].'cache/?authtoken='.$_SESSION['authtoken']['authtoken']);
	$_SESSION['async_request_flag'] = true;
}*/

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