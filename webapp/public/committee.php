<?php
require __DIR__ . "/../../vendor/autoload.php";

// https://voices.uchicago.edu/advisorycouncils/about-the-councils/ links to this page

use GuzzleHttp\Client;
use UChicago\AdvisoryCouncil\BearerToken;
use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\Committees;
use UChicago\AdvisoryCouncil\Data\Repository;

$app = new \UChicago\AdvisoryCouncil\Application();

$committees = new Committees();
$client = new Client();
$repository = new Repository();

$template = $app->template('./committee.html.twig');

$TwigTemplateVariables = array();

if ( isset($_GET['c']) ) {
    $code = $_GET['c'];
    $members_list = $repository->getCouncilData($code);

    if (count($members_list) == 0)
    {
        $app->redirect('https://voices.uchicago.edu/advisorycouncils/about-the-councils/');
    }
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
$TwigTemplateVariables['committees'] =$committees->committees();

echo $template->render($TwigTemplateVariables);
?>
