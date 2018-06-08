<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

$app = new \UChicago\AdvisoryCouncil\Application();

//$client = new Client(['base_uri' => 'https://ardapi-uat2015.uchicago.edu/api/']); // UAT
$token = new \UChicago\AdvisoryCouncil\BearerToken($client, $app->apiCreds()['username'], $app->apiCreds()['password']);

$_SESSION['bearer_token'] = $token->bearer_token();

$committees = new \UChicago\AdvisoryCouncil\Committees();

$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($app->environment());

$client = new Client(['base_uri' => $app->ardUrl()]);

$repository = new \UChicago\AdvisoryCouncil\Data\Repository($app->environment(), $memcache, $client, $_SESSION['bearer_token']);

$template = $app->template('./committee.html.twig');

if ($app->isValid() && isset($_GET['c'])) {
    $code = $_GET['c'];
    $members_list = $repository->getCouncilData($code);
    if (!isset($members_list) && count($members_list) > 0) {
        foreach ($members_list as $m) {
            if ($m->chair()) {
                ;
                $TwigTemplateVariables['Chairman'] = $m->full_name() . ', Chair';
            }
        }
        $TwigTemplateVariables['members_list'] = $members_list;
    }
}

echo $template->render($TwigTemplateVariables);
?>