<?php

require __DIR__ . "/../../vendor/autoload.php";


use GuzzleHttp\Client;
use UChicago\AdvisoryCouncil\BearerToken;
use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\Committees;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new \UChicago\AdvisoryCouncil\Application();

$committees = new Committees();

$memcache_instance = new CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($app->environment());

$client = new Client(['base_uri' => $app->ardUrl()]);

$token = new BearerToken($client, $app->apiCreds()['username'], $app->apiCreds()['password']);

$_SESSION['bearer_token'] = $token->bearer_token();

$repository = new Repository($memcache, $client, $_SESSION['bearer_token'], $app->environment());

$template = $app->template('./committee.html.twig');

$TwigTemplateVariables = array();

if ($app->isValid() && isset($_GET['c'])) {
    $code = $_GET['c'];
    $members_list = $repository->getCouncilData($code);
    if (isset($members_list) && count($members_list) > 0) {
        $chairs_array = array();

        foreach ($members_list as $m) {
            if ($m->chair()) {
                array_push( $chairs_array , $m->full_name() );
            }
        }
        if( !empty($chairs_array )){
            asort($chairs_array);
            $TwigTemplateVariables['Chairman'] = count($chairs_array) < 2 ? $chairs_array[0] . ', Chair' : implode(" and " , $chairs_array) . ", Co-Chairs";
        }
        $TwigTemplateVariables['members_list'] = $members_list;
    }
}
$TwigTemplateVariables['Committee'] = $committees->getCommitteeName($code);
$TwigTemplateVariables['loggedIn'] = $app->isLoggedIn() ? true : false;
$TwigTemplateVariables['committees'] =$committees->committes();

echo $template->render($TwigTemplateVariables);
?>
