<?php
require __DIR__ . "/../vendor/autoload.php";


$app = new UChicago\AdvisoryCouncil\Application;

$auth_err = false;
$soc_auth_err = false;

/**
 * Set committee objects for side nav.
 */

use GuzzleHttp\Client;

$client = new Client(['base_uri' => $app->ardUrl()]);

//$client = new Client(['base_uri' => 'https://ardapi-uat2015.uchicago.edu/api/']); // UAT
$token = new \UChicago\AdvisoryCouncil\BearerToken($client, $app->apiCreds()['username'], $app->apiCreds()['password']);

$_SESSION['bearer_token'] = $token->bearer_token();

if ( $app->isAuthorized() ) {
        $app->redirect('./search.php');
}

if (  $app->userIsFromShibb() && $app->isValidGroup() ) {
    $_SESSION['email'] = $_SERVER['email'];
    $app->redirect('./search.php');
}

$valid_social_auth = null;
if ($app->isValidService() && isset($_SERVER['mail'])) {
    $valid_social_auth = $app->isValidSocialAuth($client, $_SERVER['mail'], $_SESSION['bearer_token']);
}

if ($app->userIsFromShibb()) {
    if (!$app->isValidGroup()) {
        $auth_err = true;
    } else {
        $_SESSION['email'] = $_SERVER['mail'];
        $app->redirect('./search.php');
    }
}

/**
 * Start Twig
 */
$template = $app->template("index.html.twig");

$err_msg = "";
if ($auth_err || (isset($_GET['error']) && $_GET['error'] == 'auth')) {
    $err_msg = $app->getErrorMessage(0);
}
/*
 * Add soaicl auth error if set.
 */
if (isset($valid_social_auth) && !$valid_social_auth) {
    $err_msg = $app->getErrorMessage(1);
}

echo $template->render([

        "authentication_error" => $err_msg,
        "domain" => $app->domain(),
    ]
);

?>

