<?php
require __DIR__ . "/../vendor/autoload.php";


$app = new UChicago\AdvisoryCouncil\Application;

$auth_err = false;
$soc_auth_err = false;
/**
 * Set committee objects for side nav.
 */

// TODO: Do all of the validation stuff with Guzzle.
use GuzzleHttp\Client;

$client = new Client(['base_uri' => $app->ardUrl() ]);

//$client = new Client(['base_uri' => 'https://ardapi-uat2015.uchicago.edu/api/']); // UAT
$token = new \UChicago\AdvisoryCouncil\BearerToken($client, $app->apiCreds()['username'],  $app->apiCreds()['password']);

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
            $response = $client->request('GET',
                "/report/VC?email_address=" . $_SERVER['mail'],
                [
                    'headers' => ['Authorization' =>  $_SESSION['bearer_token'] ]
                ]
            );
            var_dump($response); exit();
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

$err_msg = "";
if( $auth_err || ( isset($_GET['error']) && $_GET['error'] == 'auth') )
{
    $err_msg = $app->getErrorMessage(0);
}
/*
 * Add soaicl auth error if set.
 */
if( $soc_auth_err )
{
    $err_msg = $app->getErrorMessage(1);
}

echo $template->render([

        "authentication_error" => $err_msg,
        "domain" , $app->domain(),
        "base" , $app->base()
    ]
);

?>

