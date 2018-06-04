<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * The Application object.
 */

use GuzzleHttp\Client;

$app = new \UChicago\AdvisoryCouncil\Application();

$committees = new \UChicago\AdvisoryCouncil\Committees();

//public function __construct($environment = "dev", CLIMemcache $memcache, $ard_api_url = "", Client $client, $bearer_token = "")
$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($app->environment());

$client = new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/']);

$token = new \UChicago\AdvisoryCouncil\BearerToken($client);

$bearer_token = $token->bearer_token();

$repository = new \UChicago\AdvisoryCouncil\Data\Repository($app->environment(), $memcache, $client, $bearer_token);

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
if (!$app->isAuthorized()) {
    $app->redirect('./index.php?error=auth');
} else {
    $template = $app->template('./results.html.twig');
    $TwigTemplateVariables = array();

    $TwigTemplateVariables["base"] = $app->base();
    $TwigTemplateVariables['LoggedIn'] = true;
}


if ((isset($_POST['search_by_committee']) && empty($_POST['committee']))) {
    $app->redirect('./search.php?error=no_select');
} elseif (isset($_POST['search_by_name']) && empty($_POST['f_name']) && empty($_POST['l_name'])) {
    $app->redirect('./search.php?error=no_name');
}

if ((isset($_POST['search_by_committee']) && !empty($_POST['committee'])) || isset($_GET['c'])) {
    if (isset($_POST['committee'])) {
        $code = $_POST['committee'];
    } elseif ($_GET['c']) {
        $code = $_GET['c'];
    }
    $TwigTemplateVariables['Committee'] = $committees->getCommitteeName($code);
    $TwigTemplateVariables['ShowCommiteeResults'] = true;
    $members_list = $repository->getCouncilData($code);

    foreach ($members_list as $m) {
        if ($m->chair()) {
            $name = $m->first_name() . ' ';
            $name .= strlen($m->middle()) > 0 ? $m->middle() . ' ' . $m->last_name() : $m->last_name();
            $name .= ', Chair';
            $TwigTemplateVariables['Chairman'] = $name;
        }
    }
    $TwigTemplateVariables['members'] = $members_list;
}
if (isset($_POST['search_by_name'])) {
    $search = new \UChicago\AdvisoryCouncil\CommitteeSearch( $repository->allCouncilData() , new \UChicago\AdvisoryCouncil\CommitteeMemberFactory());

    $results = $search->searchResults( array("first_nme" => htmlClean($_POST['f_name']) , "last_name" => htmlClean($_POST['l_name'])) );

    var_dump($results); exit();
    $members = $manager->searchCachedMembersByID($xml);
    if (empty($members)) {
        $members = $collection->getMembersAndCommittees($xml, $_SESSION['authtoken']);
    }
    $count = count($members);
    $total = 0;
    if ($count > 0) {
        foreach ($members as $key => $m) {
            $total++;
            $id_number = $m->getIdNumber();
            $m->addClassDataTemplate($template, "CommitteeMember.$id_number.");
        }
    }
    $TwigTemplateVariables['count'] = $total;
    $TwigTemplateVariables['ShowSearchResults'] = true;
}

echo $template->render($TwigTemplateVariables);
?>
