<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */

use GuzzleHttp\Client;
use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\Committees;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new \UChicago\AdvisoryCouncil\Application();

$committees = new Committees();

$memcache_instance = new CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($app->environment());

$client = new Client(['base_uri' => $app->ardUrl()]);

$repository = new Repository($memcache, $client, $_SESSION['bearer_token'], $app->environment());

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
if (!$app->isAuthorized() || !isset($_GET['id_number'])) {
    $app->redirect('./index.php?error=auth');
}

$template = $app->template('./member.html.twig');
$TwigTemplateVariables = array();

$TwigTemplateVariables["base"] = $app->base();
$TwigTemplateVariables['LoggedIn'] = true;

$member = $repository->findMemberByIdNumber($_GET['id_number']);

if (!is_null($member)) {

    $membership_data = $repository->getCouncilMembershipData();

    $memberships = $membership_data->getCommittees($member->id_number());

    $committees = $committees->getCommitteeMemberships($memberships);

    $TwigTemplateVariables['members'] = array($member);

    $TwigTemplateVariables['committees'] = $committees;
}

echo $template->render($TwigTemplateVariables);

?>