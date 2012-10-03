<?php
require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();

$auth_err = false;
$soc_auth_err = false;
if( $app->isShibbAuth() )
{
	if( $app->isAuthorized() )
	{
		$app->redirect('./search.php');
	}
	elseif( $app->isValidService()  )
	{		
		$curl = new cURL(null);
		$collection = Collection::instance($app , $curl );	
		$curl->authenticate( $collection->getLoginUrl() );
		$_SESSION['authtoken'] = array( 'authtoken' => $curl->__toString());
		if( $app->userIsFromSocialAuth() && isset($_SERVER['mail']) )
		{
			$curl->setPost($_SESSION['authtoken']);	
			$curl->createCurl( $collection->getServiceUrl('email_validation', $_SERVER['mail'] ) );	
			if( !$curl->xmlChildExists($curl->asSimpleXML(), '//ID_NUMBER'))
			{
				$soc_auth_err = true;
			}
			else
			{		
				$_SESSION['email'] =  $_SERVER['mail'];
				$app->redirect('./search.php');
			}	
		}
		elseif( $app->userIsFromShibb() )
		{	
			if( !$app->isValidGroup() )
			{				
				$auth_err = true;
			}
			else
			{
				$_SESSION['email'] =  $_SERVER['mail'];
				$app->redirect('./search.php');
			}
		}
	}
	else
	{
		$soc_auth_err = true;
	}
}

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('index.html.cs');
$template->add_data( "domain" , $app->domain() );
$template->add_data( "base" , $app->base() );
/*
 * Add authentication error if set.
 */
if( $auth_err || ( isset($_GET['error']) && $_GET['error'] == 'auth') )
{
	$template->add_data( "authentication_error" , $app->get_error_message(0) );
}
/*
 * Add soaicl auth error if set.
 */
if( $soc_auth_err )
{
	$template->add_data( "authentication_error" , $app->get_error_message(1) );
}
$template->show();
?>