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
$token = new \UChicago\AdvisoryCommittee\BearerToken($client);

$bearer_token = $token->bearer_token();

$committees = new \UChicago\AdvisoryCommittee\Committees();

$factory = new \UChicago\AdvisoryCommittee\CommitteeMemberFactory();

foreach ($committees->committes() as $committee) {

    $response = $client->request('GET',
        "committee/show/" . $committee['COMMITTEE_CODE'],
        [
            'headers' => ['Authorization' => $bearer_token]
        ]
    );

    $ids_as_query_string = $factory->idNumbers(json_decode($response->getBody())->committees);

    $promise = $client->getAsync(
        "entity/collection?" . $ids_as_query_string,
        [
            'headers' => ['Authorization' => $bearer_token]
        ]
    );

    $promise->then(
        function (\GuzzleHttp\Psr7\Response $resp) use ($factory, $committee) {
            $test = 0;
            foreach (json_decode($resp->getBody()) as $object) {

                $_SESSION['committees'][$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object);
                // TODO Maybe figure out a way to sort here...
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
$end_date = new DateTime();
print $end_date->format('H:i:s') . "\n";

foreach ($_SESSION['committees'] as $key => $committee){
    $_SESSION['committees'][$key] = $factory->sortCommittee($committee);
}

//print( $_SESSION['committees']['VCLZ'][0]->full_name() . "\n");
//print( $_SESSION['committees']['VCLZ'][1]->full_name() . "\n");
//print( $_SESSION['committees']['VCLZ'][2]->full_name() . "\n");
//print( $_SESSION['committees']['VCLZ'][3]->full_name() . "\n");
//print( $_SESSION['committees']['VCLZ'][4]->full_name() . "\n");

// TODO: Add sorting.
// TODO: Add search.