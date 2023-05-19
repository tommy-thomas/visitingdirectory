<?php

require __DIR__ . "/../../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use UChicago\AdvisoryCouncil\CommitteeMemberFactory;
use UChicago\AdvisoryCouncil\CommitteeMemberMembership;
use UChicago\AdvisoryCouncil\Committees;

$app = new \UChicago\AdvisoryCouncil\Application(false);

$memcache_instance = new \UChicago\AdvisoryCouncil\CLIMemcache();

$memcache = $memcache_instance->getMemcacheForCLI($argv[1]);

//// Get base uri from App instance.

$client = new Client(['base_uri' => $app->apiUrl() ]);

$committees = new Committees();

$factory = new CommitteeMemberFactory();
$committee_membership = new CommitteeMemberMembership();

$_SESSION['committee_data']=array();

foreach ($committees->committees() as $key=> $committee) {

    $response = $client->getAsync('GET',
        'involvement?q=ucinn_ascendv2__Involvement_Code_Description_Formula__c in ('.$committees->committeeCodesToString().')',
        [
            'headers' => [
                'client_id' => $client_id,
                'client_secret' => $client_secret
            ]
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
        function (Response $resp) use ($factory, $committee, $committee_membership, $chairs, $lifetime_member_array) {

            foreach (json_decode($resp->getBody()) as $object) {

                $chair = (($chairs[$committee['COMMITTEE_CODE']] == $object->info->ID_NUMBER) || (is_array($chairs[$committee['COMMITTEE_CODE']] ) && in_array($object->info->ID_NUMBER,$chairs[$committee['COMMITTEE_CODE']] )) ) ? true : false;

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
    $memcache->set('AdvisoryCouncilsMemberData', $_SESSION['committee_data'], MEMCACHE_COMPRESSED, 604800 );
}
$memcache->set('AdvisoryCouncilsMemberMembershipData', array('committee_membership' => $committee_membership), MEMCACHE_COMPRESSED, 604800 );
