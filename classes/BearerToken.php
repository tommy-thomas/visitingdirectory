<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/27/17
 * Time: 2:19 PM
 */

namespace UChicago\AdvisoryCommittee;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BearerToken
{
    private $bearer_token;

    public function __construct(Client $client)
    {
        try {
            $reponse = $client->post(
                'account/token',
                [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => '',
                        'password' => ''
                        //
                    ]

                ]
            );
        } catch (ClientException $e) {
            print $e->getMessage();
        }

        $this->bearer_token = "Bearer " . json_decode($reponse->getBody())->access_token;
    }

    public function bearer_token(){
        return $this->bearer_token;
    }
}