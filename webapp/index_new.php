<?php

require __DIR__ . "/../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

//date_default_timezone_set('America/Chicago');
//$date = new DateTime();
//print $date->format('H:i:s') . "\n";

//// Get base uri from App instance.

$client = new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/']);
$token = new \UChicago\AdvisoryCommittee\BearerToken( $client );

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
            foreach (json_decode($resp->getBody()) as $object) {
                $_SESSION['committees'][$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object);
                // TODO Maybe figure out a way to sort here...
            }
        },
        function (RequestException $e) {
            print $e->getMessage();
        }
    );

    $promise->wait();
}

var_dump($_SESSION['committees']);
//$end_date = new DateTime();
//print $end_date->format('H:i:s');

// TODO: Add sorting.
// TODO: Add search.