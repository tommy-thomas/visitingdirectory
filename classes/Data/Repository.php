<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/30/18
 * Time: 2:34 PM
 */

namespace UChicago\AdvisoryCouncil\Data;
require __DIR__ . "/../../vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use UChicago\AdvisoryCouncil\Application;
use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\CommitteeMemberFactory;
use UChicago\AdvisoryCouncil\CommitteeMemberMembership;
use UChicago\AdvisoryCouncil\Committees;

class Repository
{
    private $data = array();
    private $bearer_token;
    private $client;
    private $memcache;

    public function __construct(Client $client, $environment = "dev")
    {
        $this->client = $client;

        //$this->setData();
    }


    public function setCache()
    {
        $committees = new Committees();

        $committee_membership = new CommitteeMemberMembership();

        $factory = new CommitteeMemberFactory();

        $_SESSION['committee_data'] = array();

        $response = $this->client->request('GET',
            'involvement?q=ucinn_ascendv2__Involvement_Code_Description_Formula__c in (' . $committees->committeeCodesToString() . ')',
            [
                'headers' => [
                    'client_id' => '',
                    'client_secret' => ''
                ]
            ]

        );

        $records = json_decode($response->getBody()->getContents())->records;
        $filtered_members = $factory->filterMembers($records);
       // var_dump($idsToString);
        //exit();
        foreach ($filtered_members as $fm ){

            $promise = $this->client->getAsync(
                "contact?q=Id='".$fm->ucinn_ascendv2__Contact__c."'",
                [
                    'headers' => [
                        'client_id' => '',
                        'client_secret' => ''
                    ]
                ]
            );

            // start building the member object
            $promise->then(
                function (Response $res){
                    var_dump(json_decode($res->getBody()->getContents())->records); exit();
                    //var_dump(json_decode($res->getBody()->getContents())->records); exit();
                } ,
                function (RequestException $e){
                    print $e->getMessage();
                }
            );

            $promise->wait();
        }

    }

    public function setData()
    {
        //set data array
        $this->data['AdvisoryCouncilsMemberData'] = $this->memcache->get('AdvisoryCouncilsMemberData');
        $this->data['AdvisoryCouncilsMemberMembershipData'] = $this->memcache->get('AdvisoryCouncilsMemberMembershipData');
    }

    public function getCouncilData($code)
    {
        if (isset($this->data['AdvisoryCouncilsMemberData'][$code])) {
            return $this->data['AdvisoryCouncilsMemberData'][$code];
        }
        return array();
    }

    public function findMemberByIdNumber($id_number = "")
    {
        foreach ($this->data['AdvisoryCouncilsMemberData'] as $key => $committee) {
            foreach ($committee as $member) {
                if ($member->id_number() == $id_number) {
                    return $member;
                }
            }
        }
        return;
    }

    public function allCouncilData()
    {
        return $this->data['AdvisoryCouncilsMemberData'];
    }

    public function getCouncilMembershipData()
    {
        if (isset($this->data['AdvisoryCouncilsMemberMembershipData']['committee_membership'])) {
            return $this->data['AdvisoryCouncilsMemberMembershipData']['committee_membership'];
        }
        return new CommitteeMemberMembership();
    }
}

$app = new Application();
$client = new Client(['base_uri' => $app->apiUrl()]);
$r = new Repository($client);
$r->setCache();