<?php

require __DIR__ . "/../../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$app = new \UChicago\AdvisoryCouncil\Application();

$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($argv[1]);

//// Get base uri from App instance.

$client = new Client(['base_uri' => $app->ardUrl() ] );

$token = new \UChicago\AdvisoryCouncil\BearerToken($client, "tommyt" , "thom$$$$1967");

$bearer_token = $token->bearer_token();

$committees = new \UChicago\AdvisoryCouncil\Committees();

$committee_membership = new \UChicago\AdvisoryCouncil\CommitteeMemberMembership();

$factory = new \UChicago\AdvisoryCouncil\CommitteeMemberFactory();

print print $memcache->get('AdvisoryCouncilsMemberData'); exit();

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

            foreach (json_decode($resp->getBody()) as $object) {

                $chair = $chairs[$committee['COMMITTEE_CODE']]== $object->info->ID_NUMBER ? true : false;

                $_SESSION['committees'][$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object , $chair);

                $committee_membership->addCommittee( $object->info->ID_NUMBER , $committee['COMMITTEE_CODE']);
            }
        },
        function (RequestException $e) {
            print $e->getMessage();
        }
    );

    $promise->wait();
}


if( isset( $_SESSION['committees'] ) && is_array($_SESSION['committees'])  && count($_SESSION['committees']) > 0 ){
    foreach ($_SESSION['committees'] as $key => $committee){
        $_SESSION['committees'][$key] = $factory->sortData($committee);
    }

    $memcache->set('AdvisoryCouncilsMemberData',  $_SESSION['committees'] , MEMCACHE_COMPRESSED , 0);
}

$_SESSION['committee_membership'] = $committee_membership;

// Example usage for search, returns array of committee members.
//$search = new \UChicago\AdvisoryCouncile\CommitteeSearch( $_SESSION['committees'] , $factory);
//
//$results = $search->searchResults(array("first_name" => "John" , "last_name" => ""));

// TODO: Verify email report end point, what else is in the payload?