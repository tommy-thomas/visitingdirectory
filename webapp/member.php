<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */

use GuzzleHttp\Client;
use UChicago\AdvisoryCouncil\Committees;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new \UChicago\AdvisoryCouncil\Application();

$committees = new Committees();

$client = new Client();

$repository = new Repository();


$template = $app->template('./member.html.twig');
$TwigTemplateVariables = array();

$TwigTemplateVariables["base"] = $app->base();
$TwigTemplateVariables['LoggedIn'] = true;

$member = $repository->findMemberByIdNumber($_GET['id_number']);

if (!is_null($member)) {

    $membership_data = $repository->councilMembershipData();

    $memberships = $membership_data->getCommittees($member->id_number());

    $committees = $committees->getCommitteeMemberships($memberships);

    $TwigTemplateVariables['members'] = array($member);

    $TwigTemplateVariables['committees'] = $committees;
}

echo $template->render($TwigTemplateVariables);

?>