<?php

require __DIR__ . "/../vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

// Get base uri from App instance.
$client = new Client(['base_uri' => 'https://ardapi.uchicago.edu/api/']);

// 1. Authentication example
try {
    $reponse = $client->post(
        'account/token',
        [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => [
                'grant_type' => 'password',
                'username' => 'tommyt',
                'password' => 'thom$$$$1967'
            ]

        ]
    );
} catch (\GuzzleHttp\Exception\ClientException $e) {
    print $e->getMessage();
}

$bearer_token = "Bearer " . json_decode($reponse->getBody())->access_token;

// 2. Get committee committee/show/VCLZ
function isActive(stdClass $member = null)
{
    $active = false;
    if (!is_null($member) && !is_null($member->ID_NUMBER)) {
        return $member->TMS_COMMITTEE_STATUS_CODE == "Active" ? true : false;
    }
    return $active;
}

$committees[] = UChicago\AdvisoryCommittee\Committees::$committees[3];

foreach ($committees as $committee) {

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
        // 3. An array of active members + get payload for each member
            function (Response $response) use ($bearer_token, $client) {

                $members = json_decode($response->getBody())->committees;
                foreach ($members as $key => $member) {
                    if (isActive($member)) {
                        $member_path = "entity/show/" . $member->ID_NUMBER;
                        try {
                            $member_reponse = $client->get(
                                $member_path,
                                [
                                    'headers' => ['Authorization' => $bearer_token]
                                ]
                            );
                            //print_r(json_decode($member_reponse->getBody()));
                            $test = new \UChicago\AdvisoryCommittee\CommitteeMemberFactory(json_decode($member_reponse->getBody()));
                        } catch (\GuzzleHttp\Exception\ClientException $e) {
                            print $e->getMessage();
                        }
                    }
                }

            },

            function (RequestException $e) {
                echo $e->getMessage();
            }


        );

        $promise->wait();


    } catch (\GuzzleHttp\Exception\ClientException $e) {
        print $e->getMessage();
    }


}
