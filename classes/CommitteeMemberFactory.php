<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/20/17
 * Time: 3:35 PM
 */

namespace UChicago\AdvisoryCouncil;

class CommitteeMemberFactory
{

    private $json_payload;
    private $roles_array;
    private CommitteeMember $member;

    public function member(\stdClass $json_payload)
    {
        if (is_null($json_payload) || !is_object($json_payload)) {
            return false;
        }
        $this->json_payload = $json_payload;
        return $this->set();

    }

    public function set(){
        $this->member = new CommitteeMember();
        $this->member->setIDNumber( $this->json_payload->Id );
        $this->member->setFirstName($this->json_payload->FirstName);
        $this->member->setMiddle($this->json_payload->MiddleName);
        $this->member->setLastName($this->json_payload->LastName);
        $this->member->setFullName($this->json_payload->Name);
        $this->member->setStreet($this->json_payload->MailingAddress->street ?? "");
        $this->member->setCity($this->json_payload->MailingAddress->city ?? "");
        $this->member->setState($this->json_payload->MailingAddress->state ?? "");
        $this->member->setZip($this->json_payload->MailingAddress->postalCode ?? "");
        $this->member->setCountryCode($this->json_payload->MailingAddress->countryCode ?? "");
        $this->member->setEmail($this->json_payload->Email ?? "");
        $this->member->setPhone($this->phone($this->json_payload->HomePhone ?? ""));
        $this->member->setChair( $this->isChair() );
        $this->member->setLifeTimeMember( $this->isLifeMember() );
        $this->member->setEmploymentId($this->json_payload->Preferred_Affiliation__c);
        return $this->member;
    }
    
    private function isChair() {
        return (isset($this->roles_array['chair']) && in_array($this->member->id_number(), $this->roles_array["chair"]));
    }

    private function isLifeMember() {
        return (isset($this->roles_array['chair']) && in_array($this->member->id_number(), $this->roles_array["life-member"]));
    }

    private function phone( $phoneString = "" ){
        $valid_number = "/^\\d+$/";
        $phone = "";
        if(!empty($phoneString) && (strlen($phoneString) == 10) && (preg_match($valid_number, $phoneString) == 1 )){
            return substr($phoneString, 0, 3)."-".substr($phoneString, 3, 3)."-".substr($phoneString, 6, 4);
        }
        return $phone;
    }

    public function idsToString($data=[], $key="", $filter = false): string
    {
        $filtered = $filter ? $this->filterMembers($data) : $data;
        $smush = array_map(function($obj) use ($key) { return trim($obj->{$key}); }, $filtered);
        $smush = array_filter($smush, fn($value) => !is_null($value) && $value !== '');
        return "'".implode("','",$smush)."'";
    }

    public function filterMembers( $data = []): array
    {
        return array_filter($data, array($this , "valid"));
    }

    public function committee_code($records ): string
    {
            return isset($records[0]) ? trim($records[0]->ucinn_ascendv2__Involvement_Code_Description_Formula__c) : "";
    }

    public function setRoles($data = [] ){
        $array = $this->filterMembers($data);
        $this->roles_array = array( "life-member" => [], "chair" => [] );
        foreach ($array as $a ){
            if($a->ucinn_ascendv2__Role__c == "Life Member"){
                $this->roles_array["life-member"] = $this->roles_array["life-member"] ?? [];
                array_push($this->roles_array["life-member"] , $a->ucinn_ascendv2__Contact__c);
            }
            if($a->ucinn_ascendv2__Role__c == "Chair"){
                $this->roles_array["chair"] = $this->roles_array["chair"] ?? [];
                array_push($this->roles_array["chair"] , $a->ucinn_ascendv2__Contact__c);
            }
        }
    }

    private function valid($data): bool
    {
        return
            ($data->Is_Current__c == "true" && $data->ucinn_ascendv2__Status__c == "Current"
                && $data->Is_Expired__c == "false" && $data->ucinn_ascendv2__Role__c != "Ex-Officio");
    }

    private function compare(CommitteeMember $a, CommitteeMember $b)
    {
        return strcmp($a->sort_token(), $b->sort_token());
    }

    public function sortData($data = array())
    {
        usort($data, array($this, "compare"));
        return $data;
    }



}
