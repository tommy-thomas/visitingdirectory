<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/30/18
 * Time: 2:34 PM
 */

namespace UChicago\AdvisoryCouncil\Data;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\CommitteeMemberMembership;

class Repository
{
    private $data = array();
    private $bearer_token;
    private $client;
    private $memcache;

    public function __construct($environment = "dev", \UChicago\AdvisoryCouncil\CLIMemcache $memcache, Client $client, $bearer_token = "")
    {
        $this->bearer_token = $bearer_token;
        $this->client = $client;
        $this->memcache = $memcache;

        if (!$this->memcache->get('AdvisoryCouncilsMemberData') || !$this->memcache->get('AdvisoryCouncilsMemberMembershipData')) {
            $this->setCache();
        }
        $this->setData();
    }


    private function setCache()
    {
        $committees = new \UChicago\AdvisoryCouncil\Committees();

        $committee_membership = new \UChicago\AdvisoryCouncil\CommitteeMemberMembership();

        $factory = new \UChicago\AdvisoryCouncil\CommitteeMemberFactory();

        foreach ($committees->committes() as $key => $committee) {

            $response = $this->client->request('GET',
                "committee/show/" . $committee['COMMITTEE_CODE'],
                [
                    'headers' => ['Authorization' => $this->bearer_token]
                ]
            );

            $ids_as_query_string = $factory->idNumbersAsQueryString(json_decode($response->getBody())->committees);

            $chairs = $factory->chairsArray(json_decode($response->getBody())->committees);

            $promise = $this->client->getAsync(
                "entity/collection?" . $ids_as_query_string,
                [
                    'headers' => ['Authorization' => $this->bearer_token]
                ]
            );

            $promise->then(
                function (\GuzzleHttp\Psr7\Response $resp) use ($factory, $committee, $committee_membership, $chairs) {

                    foreach (json_decode($resp->getBody()) as $object) {

                        $chair = $chairs[$committee['COMMITTEE_CODE']] == $object->info->ID_NUMBER ? true : false;

                        $_SESSION['committees'][$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object, $chair);

                        $committee_membership->addCommittee($object->info->ID_NUMBER, $committee['COMMITTEE_CODE']);
                    }
                },
                function (RequestException $e) {
                    print $e->getMessage();
                }
            );

            $promise->wait();
        }


        if (isset($_SESSION['committees']) && is_array($_SESSION['committees']) && count($_SESSION['committees']) > 0) {
            foreach ($_SESSION['committees'] as $key => $committee) {
                $_SESSION['committees'][$key] = $factory->sortData($committee);
            }
            $this->memcache->set('AdvisoryCouncilsMemberData', $_SESSION['committees'], MEMCACHE_COMPRESSED, 0);
        }
        $this->memcache->set('AdvisoryCouncilsMemberMembershipData', array("committee_membership", $committee_membership), MEMCACHE_COMPRESSED, 0);

        return;
    }

    public function setData()
    {
        //set data array
        $this->data['AdvisoryCouncilsMemberData'] = $this->memcache->get('AdvisoryCouncilsMemberData');
        $this->data['AdvisoryCouncilsMemberMembershipData'] = $this->memcache->get('AdvisoryCouncilsMemberMembershipData');
    }

    public function getCouncilData( $code )
    {
        if(isset($this->data['AdvisoryCouncilsMemberData'][$code])){
            return $this->data['AdvisoryCouncilsMemberData'][$code];
        }
        return array();
    }

    public function allCouncilData(){
        return $this->data['AdvisoryCouncilsMemberData'];
    }

    public function getCouncilMembershipData()
    {
        if( isset($this->data['AdvisoryCouncilsMemberMembershipData'])
            && $this->data['AdvisoryCouncilsMemberMembershipData'] instanceof CommitteeMemberMembership){
            return $this->data['AdvisoryCouncilsMemberMembershipData'];
        }
        return new CommitteeMemberMembership();
    }
}