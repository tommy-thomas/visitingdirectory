<?php
require __DIR__ . "/../vendor/autoload.php";


$app = new UChicago\AdvisoryCouncil\Application;

$auth_err = false;
$valid_social_auth = null;


if ( $app->isAuthorized() ) {
        $app->redirect('./search.php');
}

//Adding to test security scan header
if( $app->isAppSecScan() ){
    $_SESSION['email'] = 'oregonian@alumni.uchicago.edu';
    $app->redirect('./search.php');
}

if (  $app->isValidGroup() ) {
    $_SESSION['email'] = $_SERVER['mail'];
    $app->redirect('./search.php');
}

if ( !$app->isValidGroup() ) {
    $auth_err = true;
}

if ($app->userIsFromSocialAuth() && isset($_SERVER['mail'])) {
    $valid_social_auth = $app->isValidSocialAuth($_SERVER['mail']);
    if( $valid_social_auth ){
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
 * Add social auth error if set.
 */
if ( isset($valid_social_auth) && !is_null($valid_social_auth) && !$valid_social_auth) {
    $err_msg = $app->getErrorMessage(1);
}

echo $template->render([

        "authentication_error" => $err_msg,
        "domain" => $app->domain(),
    ]
);

?>

