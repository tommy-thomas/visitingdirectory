<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/30/18
 * Time: 2:34 PM
 */


namespace UChicago\AdvisoryCouncil\Data;
require __DIR__ . "/../../vendor/autoload.php";

define('CLIENT_ID', 'd9b29bf62e5947f38bd8ad48f562d142');
define('CLIENT_SECRET', '6F8534f70E524Dc59C404a4D282e17ae');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use UChicago\AdvisoryCouncil\Application;
use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\CommitteeMemberFactory;
use UChicago\AdvisoryCouncil\CommitteeMemberMembership;
use UChicago\AdvisoryCouncil\Committees;

class Repository
{
    private array $data = [];
    private $headers = [];
    private $header = [];
    private Client $client;
    private string $uri;
    private $memcache;
    private $members = [];
    private Committees $committees;
    private CommitteeMemberFactory $factory;
    private $degreeIDs;

    //stuff for testing
    public $employment_counts = [];
    public $empl_test_obj;

    public function __construct(Client $client, $uri, $environment = "dev")
    {
        $this->committees = new Committees();
        $this->factory = new CommitteeMemberFactory();
        $this->client = $client;
        $this->uri = $uri;
        //for async main_requests
        $this->headers = [
            'headers' => [
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET
            ]
        ];
        //fetch request object
        $this->header = ['client_id' => CLIENT_ID, 'client_secret' => CLIENT_SECRET];
        //$this->setData();
    }

    private function recordsAsObject(Response $response)
    {
        return json_decode($response->getBody()->getContents())->records[0] ?? new \stdClass();
    }
    //TODO: Put the involvement?q=ucinn_ascendv2__Involvement_Code_Description_Formula__c= payload in a roles array to flag lifetime members and chairs
    //TODO: Flag committee chair
    //TODO: Flag lifetime members
    //TODO: Sort by commitee code
    //TODO: Search
    public function setCache()
    {
        $this->setMainData()
            ->setEmploymentData()
            ->setDegreeData()
        ->setDegreeInstitutionData();
    }

    private function setMainData()
    {

        $committee_codes = $this->committees->committeeCodesToArray();

        $_SESSION['committee_data'] = array();

        $main_requests = function () use ($committee_codes) {
            foreach ($committee_codes as $c) {
                $this->emplIDsToString = "";
                $uri = $this->uri . "involvement?q=ucinn_ascendv2__Involvement_Code_Description_Formula__c='" . $c . "'";
                yield new Request('GET', $uri, $this->header);
            }
        };

        //Loop through committee codes
        $degreeIDs = "";
        $main_pool = new Pool($this->client, $main_requests(), [
            'concurrency' => 20,
            'fulfilled' => function (Response $response, $index) {
                //promise, main contact object
                $records = json_decode($response->getBody()->getContents())->records;
                // committee code
                $committee_code = $this->factory->committee_code($records);
                $contactIDsToString = $this->factory->idsToString($records, 'ucinn_ascendv2__Contact__c', true);
                $contacts = $this->client->getAsync($this->uri . "contact?q=Id in (" . $contactIDsToString . ")", $this->headers);
                $contacts->then(
                    function (Response $response) use ($committee_code) {
                        $contact_results = json_decode($response->getBody()->getContents())->records;
                        foreach ($contact_results as $result) {
                            $member = $this->factory->member($result);
                            $this->members[$committee_code][$member->Id] = $member;
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
        foreach ($this->members() as $code => $members_array) {
            foreach ($members_array as $id => $member) {
                $employment = $this->client->getAsync($this->uri . "affiliation?q=Id='" . $member->Preferred_Affiliation__c . "'", $this->headers);
                $employment->then(
                    function (Response $response) use ($code, $id, $member) {
                        $this->members[$code][$id]->employment = json_decode($response->getBody()->getContents())->records;
                    },
                    function (RequestException $exception) {
                        print "Error with employment resquest:\n" . $exception->getMessage();
                    }
                );
                $employment->wait();
            }
        }
        return $this;
    }

    public function setDegreeData()
    {
        foreach ($this->members() as $code => $members_array) {
            foreach ($members_array as $id => $member) {
                //https://itsapi.uchicago.edu/system/ascend/v1/api/query/degree?q=ucinn_ascendv2__Contact__c='0031U00001Q7l0EQAR'
                $degree = $this->client->getAsync($this->uri . "degree?q=ucinn_ascendv2__Contact__c='" . $member->Id . "'", $this->headers);
                $degree->then(
                    function (Response $response) use ($code, $id, $member) {
                        $this->members[$code][$id]->degree = json_decode($response->getBody()->getContents());
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


    public function setDegreeInstitutionData()
    {
        foreach ($this->members() as $code => $members_array) {
            foreach ($members_array as $id => $member) {
                $degrees = $member->degree->records;
                foreach ($degrees as $degree) {
                    if (isset($degree->ucinn_ascendv2__Degree_Institution__c) && !empty($degree->ucinn_ascendv2__Degree_Institution__c)) {
                        $institution_id = $degree->ucinn_ascendv2__Degree_Institution__c;
                        https://itsapi.uchicago.edu/system/ascend/v1/api/query/account?q=Id='0011U00001PTMSKQA5'
                        $degree = $this->client->getAsync($this->uri . "account?q=Id='" . $institution_id . "'", $this->headers);
                        $degree->then(
                            function (Response $response) use ($code, $id, $member, $institution_id) {
                               $institutions = json_decode($response->getBody()->getContents())->records;
                               foreach ($institutions as $institution){
                                   $this->members[$code][$id]->degree_institution = $institutions;
                               }
                            },
                            function (RequestException $exception) use ($member) {
                                print "Error with degree resquest:\n" . $exception->getMessage();
                            }
                        );
                        $degree->wait();
                    }
                }
            }

        }
        return $this;
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

    public function members()
    {
        return $this->members;
    }


}

$app = new Application();
//$client = new Client(['base_uri' => $app->apiUrl()]);
$client = new Client();
$r = new Repository($client, $app->apiUrl());
$r->setCache();
var_dump($r->members());