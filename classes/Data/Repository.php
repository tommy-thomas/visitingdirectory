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
    public CommitteeMemberMembership $committee_membership;
    private CommitteeMemberFactory $factory;


    public function __construct(Client $client, $uri, $environment = "dev")
    {
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

        $this->setCache();

        //SQLite Backup
        $db = new Database();
        $db->set('member_data',$this->data['AdvisoryCouncilsMemberData']);
        $db->set('membership_data', $this->data['AdvisoryCouncilsMemberMembershipData']);

    }

    public function setCache()
    {

        $this->setMainData()
            ->setEmploymentData()
            ->setDegreeData()
            ->sortData()
            ->setData();

        return $this;

    }

    public function setData(){
        $db = new Database();
        $db->set('member_data', $this->members());
        $db->set('membership_data', array('committee_membership' => $this->committee_membership() ));
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
        $degreeIDs = "";
        $test = 3;
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
                if( !empty(  $member->employment_id() )){
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
                //https://itsapi.uchicago.edu/system/ascend/v1/api/query/degree?q=ucinn_ascendv2__Contact__c='0031U00001Q7l0EQAR'
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


//    public function setDegreeInstitutionData()
//    {
//        foreach ($this->members() as $committee_code => $members_array) {
//            foreach ($members_array as $id => $member) {
//                $degrees = $member->degree->records;
//                foreach ($degrees as $degree) {
//                    if (isset($degree->ucinn_ascendv2__Degree_Institution__c) && !empty($degree->ucinn_ascendv2__Degree_Institution__c)) {
//                        $institution_id = $degree->ucinn_ascendv2__Degree_Institution__c;
//                        https://itsapi.uchicago.edu/system/ascend/v1/api/query/account?q=Id='0011U00001PTMSKQA5'
//                        $degree = $this->client->getAsync($this->uri . "account?q=Id='" . $institution_id . "'", $this->headers_array);
//                        $degree->then(
//                            function (Response $response) use ($committee_code, $id, $member, $institution_id) {
//                                $this->members[$committee_code][$id]->degree_institution = json_decode($response->getBody()->getContents())->records;
//                            },
//                            function (RequestException $exception) use ($member) {
//                                print "Error with degree resquest:\n" . $exception->getMessage();
//                            }
//                        );
//                        $degree->wait();
//                    }
//                }
//            }
//
//        }
//        return $this;
//    }

    public function sortData(){
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
        foreach ($this->data['AdvisoryCouncilsMemberData'] as $key => $committee) {
            foreach ($committee as $member) {
                if ($member->Id == $id_number) {
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

    public function committee_membership(){
        return $this->committee_membership;
    }
}
$app = new Application();
//$client = new Client(['base_uri' => $app->apiUrl()]);
$client = new Client();
$r = new Repository($client, $app->apiUrl());
$r->setCache();
var_dump($r->members());