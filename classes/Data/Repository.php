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
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use UChicago\AdvisoryCouncil\CommitteeMemberFactory;
use UChicago\AdvisoryCouncil\CommitteeMemberMembership;
use UChicago\AdvisoryCouncil\Committees;
use UChicago\AdvisoryCouncil\Data\Database as Database;

class Repository
{
    private array $data = [];
    private $headers_array;
    private $header;
    private Client $client;
    private string $uri;
    private $members = [];
    private Committees $committees;
    private CommitteeMemberMembership $committee_membership;
    private CommitteeMemberFactory $factory;
    private Database $database;


    public function __construct(Client $client = null, $uri = null, $environment = "dev")
    {
        $this->database = new Database();
        // If no $client or api uri then instance is in read only status...
        if (!is_null($client) && !is_null($uri)) {
            $this->committees = new Committees();
            $this->factory = new CommitteeMemberFactory();
            $this->committee_membership = new CommitteeMemberMembership();
            $this->client = $client;
            $this->uri = $uri;
            //for async main_requests
            $this->headers_array = [
                'headers' => [
                    'client_id' => CLIENT_ID,
                    'client_secret' => CLIENT_SECRET
                ]
            ];
            //fetch request object
            $this->header = ['client_id' => CLIENT_ID, 'client_secret' => CLIENT_SECRET];
        }
        $this->setData();
    }

    public function cache()
    {

        $this->setMainData()
            ->setEmploymentData()
            ->setDegreeData()
            ->sortData()
            ->setDBData();

    }

    public function setDBData()
    {
        if( !empty($this->members()) && !empty($this->committee_membership())){
            $this->database->set('member_data', $this->members());
            $this->database->set('membership_data', array('committee_membership' => $this->committee_membership()));
            return true;
        }
        return false;
    }

    public function setData()
    {
        //set data array
        $this->data['AdvisoryCouncilsMemberData'] = $this->database->get('member_data');
        $this->data['AdvisoryCouncilsMemberMembershipData'] = $this->database->get('membership_data');
    }

    private function setMainData()
    {

        $committee_codes = $this->committees->committeeCodesToArray();

        $main_requests = function () use ($committee_codes) {
            foreach ($committee_codes as $c) {
                $this->emplIDsToString = "";
                $uri = $this->uri . "involvement?q=ucinn_ascendv2__Involvement_Code_Description_Formula__c='" . $c . "'";
                yield new Request('GET', $uri, $this->header);
            }
        };

        //Loop through committee codes
        $main_pool = new Pool($this->client, $main_requests(), [
            'concurrency' => 20,
            'fulfilled' => function (Response $response, $index) {
                //promise, main contact object
                $records = json_decode($response->getBody()->getContents())->records;
                // committee code
                $committee_code = $this->factory->committee_code($records);
                // committee role
                $this->factory->setRoles($records);
                $contactIDsToString = $this->factory->idsToString($records, 'ucinn_ascendv2__Contact__c', true);
                $contacts = $this->client->getAsync($this->uri . "contact?q=Id in (" . $contactIDsToString . ")", $this->headers_array);
                $contacts->then(
                    function (Response $response) use ($committee_code) {
                        $contact_results = json_decode($response->getBody()->getContents())->records;
                        foreach ($contact_results as $result) {
                            $member = $this->factory->member($result);
                            $this->members[$committee_code][$member->id_number()] = $member;
                            $this->committee_membership->addCommittee($member->id_number(), $committee_code);
                        }
                        return true;
                    },
                    function (RequestException $exception) {
                        print "Error with contact main_requests:\n" . $exception->getMessage();
                    }
                );
            }, 'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
                print  $reason->getMessage();
            }
        ]); // end main_pool

        // Initiate the transfers and create a promise
        $promise = $main_pool->promise();

        // Force the main_pool of main_requests to complete.
        $promise->wait();

        return $this;
    }

    public function setEmploymentData()
    {
        foreach ($this->members() as $committee_code => $members_array) {
            foreach ($members_array as $id => $member) {
                if (!empty($member->employment_id())) {
                    $employment = $this->client->getAsync($this->uri . "affiliation?q=Id='" . $member->employment_id() . "'", $this->headers_array);
                    $employment->then(
                        function (Response $response) use ($committee_code, $id, $member) {
                            $this->members[$committee_code][$id]->setEmploymentData(json_decode($response->getBody()->getContents())->records);
                        },
                        function (RequestException $exception) {
                            print "Error with employment resquest:\n" . $exception->getMessage();
                        }
                    );
                    $employment->wait();
                }

            }
        }
        return $this;
    }

    public function setDegreeData()
    {
        foreach ($this->members() as $committee_code => $members_array) {
            foreach ($members_array as $id => $member) {
                $degree = $this->client->getAsync($this->uri . "degree?q=ucinn_ascendv2__Contact__c='" . $member->id_number() . "'", $this->headers_array);
                $degree->then(
                    function (Response $response) use ($committee_code, $id, $member) {
                        $this->members[$committee_code][$id]->setDegrees(json_decode($response->getBody()->getContents())->records);
                    },
                    function (RequestException $exception) use ($member) {
                        print "Error with degree resquest:\n" . $exception->getMessage();
                    }
                );
                $degree->wait();
            }
        }
        return $this;
    }

    public function sortData()
    {
        foreach ($this->members() as $key => $committee) {
            $this->members[$key] = $this->factory->sortData($committee);
        }
        return $this;
    }

    public function getCouncilData($committee_code)
    {
        if (isset($this->data['AdvisoryCouncilsMemberData'][$committee_code])) {
            return $this->data['AdvisoryCouncilsMemberData'][$committee_code];
        }
        return array();
    }

    public function findMemberByIdNumber($id_number = "")
    {
        foreach ($this->data['AdvisoryCouncilsMemberData'] as $committee) {
            foreach ($committee as $member) {
                if ($member->id_number() == $id_number) {
                    return $member;
                }
            }
        }
    }

    public function allCouncilData()
    {
        return $this->data['AdvisoryCouncilsMemberData'];
    }

    public function councilMembershipData()
    {
        if (isset($this->data['AdvisoryCouncilsMemberMembershipData']['committee_membership'])) {
            return $this->data['AdvisoryCouncilsMemberMembershipData']['committee_membership'];
        }
        return new CommitteeMemberMembership();
    }

    public function members()
    {
        return $this->members;
    }

    public function committee_membership()
    {
        return $this->committee_membership;
    }

}