<?php

require __DIR__ . "/../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

//date_default_timezone_set('America/Chicago');
//$date = new DateTime();
//print $date->format('H:i:s') . "\n";

//// Get base uri from App instance.
$token = new \UChicago\AdvisoryCommittee\BearerToken(
    new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/'])
);

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
                $ID_NUMBER = $object->info->ID_NUMBER;
                $_SESSION['committees'][$committee['COMMITTEE_CODE']][$ID_NUMBER] = $factory->member($object);

            }
        },
        function (RequestException $e) {
            print $e->getMessage();
        }
    );

    $promise->wait();
}

var_dump( $_SESSION['committees']);


//$end_date = new DateTime();
//print $end_date->format('H:i:s');