<?php
require __DIR__ . "/../vendor/autoload.php";
/**
 * The Application object.
 */

$app = new \UChicago\AdvisoryCouncil\Application();

$auth_err = false;
$soc_auth_err = false;
/**
 * Set committee objects for side nav.
 */

// TODO: Do all of the validation stuff with Guzzle.
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

$client = new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/']);

//$client = new Client(['base_uri' => 'https://ardapi-uat2015.uchicago.edu/api/']); // UAT
$token = new \UChicago\AdvisoryCouncil\BearerToken($client);

$_SESSION['bearer_token'] = $token->bearer_token();

if( $app->isShibbAuth() )
{
	if( $app->isAuthorized() )
	{
		$app->redirect('./search.php');
	}
	elseif( $app->isValidService()  )
	{
		if( $app->userIsFromSocialAuth() && isset($_SERVER['mail']) )
		{
//		try {
//			$curl->setPost($_SESSION['authtoken']);
//			$curl->createCurl( $collection->getServiceUrl('email_validation', $_SERVER['mail'] ) );
//			if( !is_a($curl->asSimpleXML() , 'SimpleXMLElement' ) || !$collection->xmlChildExists($curl->asSimpleXML(), '//ID_NUMBER'))
//			{
//				$soc_auth_err = true;
//			}
//			else
//			{
//				$_SESSION['email'] =  $_SERVER['mail'];
//				$app->redirect('./search.php');
//			}
//			} catch (Exception $e) {
//				Application::handleExceptions($e);
//			}
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
 * Start Twig
 */
$template = $app->template("index.html.twig");

if( $auth_err || ( isset($_GET['error']) && $_GET['error'] == 'auth') )
{
    $err_msg = $app->get_error_message(0);
}
/*
 * Add soaicl auth error if set.
 */
if( $soc_auth_err )
{
    $err_msg = $app->get_error_message(1);
}

echo $template->render([

        "authentication_error" => $err_msg,
        "domain" , $app->domain(),
        "base" , $app->base()
    ]
);

?>

