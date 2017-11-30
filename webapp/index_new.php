<?php

require __DIR__ . "/../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

//
//// Get base uri from App instance.
$client = new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/']);


$token = new \UChicago\AdvisoryCommittee\BearerToken($client);

$bearer_token = $token->bearer_token();

//$committees[] = UChicago\AdvisoryCommittee\Committees::$committees[3];

foreach (\UChicago\AdvisoryCommittee\Committees::$committees as $committee) {

    $committee_code = $committee['COMMITTEE_CODE'];
    $commitee_path = "committee/show/" . $committee_code;

    try {
        $promise = $client->requestAsync(
            'GET',
            $commitee_path,
            [
                'headers' => ['Authorization' => $bearer_token]
            ]
        );

        $promise->then(
        // 2. An array of active members + get payload for each member

            function (Response $response) use ($bearer_token, $client) {

                // Get all active id numbers for a committee
                $id_numbers = \UChicago\AdvisoryCommittee\CommitteeMemberFactory::idNumbers(json_decode($response->getBody())->committees);

                foreach ($id_numbers as $id){
                try {
                    $member_path = "entity/show/" . $id;
                    $member_reponse = $client->get(
                        $member_path,
                        [
                            'headers' => ['Authorization' => $bearer_token],
                        ]
                    );
                    $test = new \UChicago\AdvisoryCommittee\CommitteeMemberFactory(json_decode($member_reponse->getBody()));
                    var_dump($test);
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    print $e->getMessage();
                }
            }
            }
            ,

            function (RequestException $e) {
                echo $e->getMessage();
            }


        );

        if (!empty($promise)) {
            $promise->wait();
        }


    } catch (\GuzzleHttp\Exception\ClientException $e) {
        print $e->getMessage();
    }


}
