<?php 

require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();

$authentication_error = null;
$social_auth_errror = null;
if( $app->isShibbAuth() )
{
	if( isset($_SESSION['email']) )
	{
		$app->redirect('./search.php');
	}
	elseif( $app->isValidService()  )
	{		
		$curl = new cURL(null);
		$collection = Collection::instance($app , $curl );	
		$curl->authenticate( $collection->getLoginUrl() );
		$_SESSION['authtoken'] = array( 'authtoken' => $curl->__toString());		
		if( !is_null( $collection->getServiceUrl('email_validation' , $_SERVER['mail'] ) ) )
		{			
			$curl->setPost($_SESSION['authtoken']);	
				
			$collection->setCommittees($_SESSION['authtoken']);
			$curl->createCurl( $collection->getServiceUrl('email_validation', 'crupright@sflaw.com' ) );			
			if( !$curl->xmlChildExists($curl->asSimpleXML(), '//ID_NUMBER'))
			{
				$authentication_error = "You are not authorized to log into this site.";
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
		$social_auth_errror = "You have tried to aunthenticate from an unauthorized service.";
	}
}

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('index.html.cs');

// print $_SESSION['cnetid'];
$template->add_data( "base" , $app->base() );
/*
 * Add authentication error if set.
 */
if( !is_null($authentication_error) )
{
	$template->add_data( "authentication_error" , $authentication_error );
}
/*
 * Add soaicl auth error if set.
 */
if( !is_null($social_auth_errror) )
{
	$template->add_data( "social_auth_errror" , $social_auth_errror );
}
$template->show();
	
?>