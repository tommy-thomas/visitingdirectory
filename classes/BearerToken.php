<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/27/17
 * Time: 2:19 PM
 */

namespace UChicago\AdvisoryCommittee;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class BearerToken
{
    private $bearer_token;

    public function __construct(\GuzzleHttp\Client $client)
    {
        try {
            $reponse = $client->post(
                'account/token',
                [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => 'tommyt',
                        'password' => ''
                    ]

                ]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            print $e->getMessage();
        }

        $this->bearer_token = "Bearer " . json_decode($reponse->getBody())->access_token;
    }

    public function bearer_token(){
        return $this->bearer_token;
    }
}