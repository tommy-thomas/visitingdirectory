<?php

require __DIR__ . "/../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

date_default_timezone_set('America/Chicago');
$date = new DateTime();
print $date->format('H:i:s') . "\n";

//// Get base uri from App instance.

$client = new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/']);
//$client = new Client(['base_uri' => 'https://ardapi-uat2015.uchicago.edu/api/']); // UAT

$token = new \UChicago\AdvisoryCouncil\BearerToken($client, "tommyt" , "thom$$$$1967");

$bearer_token = $token->bearer_token();

$committees = new \UChicago\AdvisoryCouncil\Committees();

$committee_membership = new \UChicago\AdvisoryCouncil\CommitteeMemberMembership();

$factory = new \UChicago\AdvisoryCouncil\CommitteeMemberFactory();

foreach ($committees->committes() as $committee) {

    $response = $client->request('GET',
        "committee/show/" . $committee['COMMITTEE_CODE'],
        [
            'headers' => ['Authorization' => $bearer_token]
        ]
    );

    $ids_as_query_string = $factory->idNumbersAsQueryString(json_decode($response->getBody())->committees);

    $chairs = $factory->chairsArray( json_decode($response->getBody())->committees );

    $promise = $client->getAsync(
        "entity/collection?" . $ids_as_query_string,
        [
            'headers' => ['Authorization' => $bearer_token]
        ]
    );

    $promise->then(
        function (\GuzzleHttp\Psr7\Response $resp) use ($factory, $committee, $committee_membership , $chairs) {
            $test = 0;
            foreach (json_decode($resp->getBody()) as $object) {

                $chair = $chairs[$committee['COMMITTEE_CODE']]== $object->info->ID_NUMBER ? true : false;

                $_SESSION['committees'][$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object , $chair);

                $committee_membership->addCommittee( $object->info->ID_NUMBER , $committee['COMMITTEE_CODE']);

//                if ($test < 30) {
//                    $test_member = $factory->member($object);
//                    print $test_member->id_number() . "\n";
//                    print $test_member->full_name() . "\n";
//                    print $test_member->degrees() . "\n";
//                    print $test_member->email() . "\n";
//                    print $test_member->phone() . "\n";
//                    print $test_member->employment_job_title() . "\n";
//                    print $test_member->employment_employer_name() . "\n";
//                    print $test_member->employment_org_name() . "\n";
//                    print $test_member->street() . "\n";
//                    print $test_member->city() . ", " . $test_member->state() . ", " . $test_member->zip() . "\n";
//                    print "=========================================================\n";
//                    $test++;
//                } else {
//                    exit();
//                }
            }
        },
        function (RequestException $e) {
            print $e->getMessage();
        }
    );

    $promise->wait();
}


//foreach ($_SESSION['committees'] as $key => $committee){
//    $_SESSION['committees'][$key] = $factory->sortData($committee);
//}
//
//$search = new \UChicago\AdvisoryCouncile\CommitteeSearch( $_SESSION['committees'] , $factory);
//
//$results = $search->searchResults(array("first_name" => "John" , "last_name" => ""));
//
//foreach ( $results as $result){
//    print $result->full_name()."\n";
//}


$_SESSION['committee_membership'] = $committee_membership;

$end_date = new DateTime();
print "\n\n=====================================\n\n".$end_date->format('H:i:s') . "\n";

// TODO: Verify email report end point, what else is in the payload?