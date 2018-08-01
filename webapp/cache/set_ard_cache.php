<?php

require __DIR__ . "/../../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$app = new \UChicago\AdvisoryCouncil\Application(false);

$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($argv[1]);

//// Get base uri from App instance.

$client = new Client(['base_uri' => $app->ardUrl() ]);

$token = new \UChicago\AdvisoryCouncil\BearerToken($client, $app->apiCreds()['username'],  $app->apiCreds()['password']);

$bearer_token = $token->bearer_token();

$committees = new \UChicago\AdvisoryCouncil\Committees();

$factory = new \UChicago\AdvisoryCouncil\CommitteeMemberFactory();
$committee_membership = new \UChicago\AdvisoryCouncil\CommitteeMemberMembership();

$_SESSION['committee_data']=array();

foreach ($committees->committes() as $key=> $committee) {

    $response = $client->request('GET',
        "committee/show/" . $committee['COMMITTEE_CODE'],
        [
            'headers' => ['Authorization' => $bearer_token]
        ]
    );

    $ids_as_query_string = $factory->idNumbersAsQueryString(json_decode($response->getBody())->committees);

    $chairs = $factory->chairsArray(json_decode($response->getBody())->committees);

    $lifetime_member_array = $factory->lifeTimeMembersArray( json_decode($response->getBody())->committees );

    $promise = $client->getAsync(
        "entity/collection?" . $ids_as_query_string,
        [
            'headers' => ['Authorization' => $bearer_token]
        ]
    );

    $promise->then(
        function (\GuzzleHttp\Psr7\Response $resp) use ($factory, $committee, $committee_membership, $chairs, $lifetime_member_array) {

            foreach (json_decode($resp->getBody()) as $object) {

                $chair = $chairs[$committee['COMMITTEE_CODE']] == $object->info->ID_NUMBER ? true : false;

                $lifetime_member = in_array( $object->info->ID_NUMBER , $lifetime_member_array);

                //member is not deceased
                if( isset( $object->info->RECORD_STATUS_CODE ) &&  $object->info->RECORD_STATUS_CODE != "D" ){
                    $_SESSION['committee_data'][$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object, $chair , $lifetime_member);
                }
                $committee_membership->addCommittee($object->info->ID_NUMBER, $committee['COMMITTEE_CODE']);
            }
        },
        function (RequestException $e) {
            print $e->getMessage();
        }
    );

    $promise->wait();
}


if (isset($_SESSION['committee_data']) && is_array($_SESSION['committee_data']) && count($_SESSION['committee_data']) > 0) {
    foreach ($_SESSION['committee_data'] as $key => $committee) {
        $_SESSION['committee_data'][$key] = $factory->sortData($committee);
    }
    $memcache->set('AdvisoryCouncilsMemberData', $_SESSION['committee_data'], MEMCACHE_COMPRESSED, 5);
}
$memcache->set('AdvisoryCouncilsMemberMembershipData', array('committee_membership' => $committee_membership), MEMCACHE_COMPRESSED, 5);
