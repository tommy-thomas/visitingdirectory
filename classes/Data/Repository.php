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

        $committee_data=array();

        foreach ($committees->committes() as $key => $committee) {

            $response = $this->client->request('GET',
                "committee/show/" . $committee['COMMITTEE_CODE'],
                [
                    'headers' => ['Authorization' => $this->bearer_token]
                ]
            );

            $ids_as_query_string = $factory->idNumbersAsQueryString(json_decode($response->getBody())->committees);

            $chairs = $factory->chairsArray(json_decode($response->getBody())->committees);

            $lifetime_member_array = $factory->lifeTimeMembersArray( json_decode($response->getBody())->committees );

            $promise = $this->client->getAsync(
                "entity/collection?" . $ids_as_query_string,
                [
                    'headers' => ['Authorization' => $this->bearer_token]
                ]
            );

            $promise->then(
                function (\GuzzleHttp\Psr7\Response $resp) use ($factory, $committee_data, $committee, $committee_membership, $chairs, $lifetime_member_array) {

                    foreach (json_decode($resp->getBody()) as $object) {

                        $chair = $chairs[$committee['COMMITTEE_CODE']] == $object->info->ID_NUMBER ? true : false;

                        $lifetime_member = in_array( $object->info->ID_NUMBER , $lifetime_member_array);

                        $committee_data[$committee['COMMITTEE_CODE']][$object->info->ID_NUMBER] = $factory->member($object, $chair, $lifetime_member);

                        $committee_membership->addCommittee($object->info->ID_NUMBER, $committee['COMMITTEE_CODE']);
                    }
                },
                function (RequestException $e) {
                    print $e->getMessage();
                }
            );

            $promise->wait();
        }


        if (isset($committee_data) && is_array($committee_data) && count($committee_data) > 0) {
            foreach ($committee_data as $key => $committee) {
                $committee_data[$key] = $factory->sortData($committee);
            }
            $this->memcache->set('AdvisoryCouncilsMemberData', $committee_data, MEMCACHE_COMPRESSED, 0);
        }
        $this->memcache->set('AdvisoryCouncilsMemberMembershipData', array('committee_membership' => $committee_membership), MEMCACHE_COMPRESSED, 0);

        return;
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

    public function findMemberByIdNumber($id_number = "" ){
        foreach ( $this->data['AdvisoryCouncilsMemberData'] as $key => $committee){
           foreach ( $committee as $member ){
               if( $member->id_number() == $id_number ){
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