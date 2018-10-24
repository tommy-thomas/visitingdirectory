<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */

use GuzzleHttp\Client;

$app = new \UChicago\AdvisoryCouncil\Application();
if (!$app->isAuthorized()) {
    $app->redirect('./index.php?error=auth');
}

$committees = new \UChicago\AdvisoryCouncil\Committees();

$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($app->environment());

$client = new Client(['base_uri' => $app->ardUrl()]);

$repository = new \UChicago\AdvisoryCouncil\Data\Repository($app->environment(), $memcache, $client, $_SESSION['bearer_token']);


$template = $app->template('./results.html.twig');
$TwigTemplateVariables = array();

$TwigTemplateVariables["base"] = $app->base();
$TwigTemplateVariables['LoggedIn'] = true;


if ((isset($_POST['search_by_committee']) && empty($_POST['committee']))) {
    $app->redirect('./search.php?error=no_select');
} elseif (isset($_POST['search_by_name']) && empty($_POST['f_name']) && empty($_POST['l_name'])) {
    $app->redirect('./search.php?error=no_name');
}

/**
 * Search by council
 */
if ((isset($_POST['search_by_committee']) && !empty($_POST['committee'])) || isset($_GET['c'])) {
    if (isset($_POST['committee'])) {
        $code = $_POST['committee'];
    } elseif ($_GET['c']) {
        $code = $_GET['c'];
    }
    $TwigTemplateVariables['Committee'] = $committees->getCommitteeName($code);
    $TwigTemplateVariables['ShowCommiteeResults'] = true;
    $members_list = $repository->getCouncilData($code);
    $chairs_array = array();

    foreach ($members_list as $m) {
        if ($m->chair()) {
           array_push( $chairs_array , $m->full_name() );
        }
    }
    if( !empty($chairs_array )){
        $TwigTemplateVariables['Chairman'] = count($chairs_array) < 2 ? $chairs_array[0] . ', Chair' : implode(" and " , $chairs_array) . ", Co-Chairs";
    }
    $TwigTemplateVariables['members'] = $members_list;
}

/**
 * Search by first_name or last_name
 */
if (isset($_POST['search_by_name'])) {
    $search = new \UChicago\AdvisoryCouncil\CommitteeSearch($repository->allCouncilData(),
        new \UChicago\AdvisoryCouncil\CommitteeMemberFactory(),
        $repository->getCouncilMembershipData());

    $results = $search->searchResults(array("first_name" => htmlClean($_POST['f_name']), "last_name" => htmlClean($_POST['l_name'])),
        $committees);

    if ($search->total() > 0) {
        $TwigTemplateVariables['members'] = $results;
    }
    $TwigTemplateVariables['total'] = $search->total();
    $TwigTemplateVariables['ShowSearchResults'] = true;
}

echo $template->render($TwigTemplateVariables);
?>
