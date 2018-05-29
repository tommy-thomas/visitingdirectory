<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */
$app = new \UChicago\AdvisoryCouncil\Application();

if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template = $app->template('./search.html.twig');
	$TwigTemplateVariables = array();

	$TwigTemplateVariables['LoggedIn' ] = true;
	$TwigTemplateVariables[ "base" ] = $app->base() ;
}

//$curl = new cURL(null);
//$collection = GriffinCollection::instance( $app , $curl , $_SESSION['authtoken'] );
//$collection->loadCommitteeTemplateData($template);

/*
 * Error messages
 */
$error_messages = array(
	'no_select' => 'Please select a commitee.',
	'no_name' => 'Please enter a first or last name.'
);
if( isset($_GET['error']) &&  isset($error_messages[$_GET['error']]) )
{
		$TwigTemplateVariables[ "authentication_error" ] = $error_messages[$_GET['error']] ;
}
echo $template->render($TwigTemplateVariables);
?>