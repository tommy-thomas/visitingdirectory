<?php
require __DIR__ . "/../vendor/autoload.php";
/**
 * The Application object.
 */
use GuzzleHttp\Client;

$app = new \UChicago\AdvisoryCouncil\Application();

$committees = new \UChicago\AdvisoryCouncil\Committees();

$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($app->environment());

$client = new Client(['base_uri' => $app->ardUrl() ]);

$repository = new \UChicago\AdvisoryCouncil\Data\Repository($app->environment(), $memcache, $client, $_SESSION['bearer_token']);

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
if (!$app->isAuthorized() || !isset($_GET['id_number']) ) {
    $app->redirect('./index.php?error=auth');
} else {
    $template = $app->template('./member.html.twig');
    $TwigTemplateVariables = array();

    $TwigTemplateVariables["base"] = $app->base();
    $TwigTemplateVariables['LoggedIn'] = true;
}

$member = $repository->getMemberByIdNumber( $_GET['id_number'] );


if( !is_null($member) )
{
    $TwigTemplateVariables['members'] = array($member);
}


echo $template->render($TwigTemplateVariables);
  
?>