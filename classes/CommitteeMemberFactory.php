<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/20/17
 * Time: 3:35 PM
 */

namespace UChicago\AdvisoryCouncil;

use UChicago\AdvisoryCouncil\CommitteeMember;

class CommitteeMemberFactory
{

    private $json_payload;
    private $member;

    public function member(\stdClass $json_payload, $chair = false, $lifetime_member = false)
    {
        if (is_null($json_payload) || !is_object($json_payload)) {
            return false;
        }

        $this->member = $this->set($json_payload);

        /**
         * Is_Current__c = true
         * ucinn_ascendv2__Status__c = Current
         * Is_Expired__c = false
         * ucinn_ascendv2__Role__c != Ex-Officio
         */
//        $this
//            ->info()
//            ->chair($chair)
//            ->lifetime_member($lifetime_member)
//            ->addresses()
//            ->degrees()
//            ->employment()
//            ->email()
//            ->phone();
        return $this->member;
    }

    public function set(\stdClass $json_payload){
        $member = new CommitteeMember();
        if( isset($json_payload) ){
            $ref = new \ReflectionClass('UChicago\AdvisoryCouncil\CommitteeMember');
            $props = $ref->getProperties();
            foreach ($props as $prop) {
                $member->{$prop->getName()} = $json_payload->{$prop->getName()} ?? "";
            }
        }
        return $member;
    }

    public function idsToString($data=[], $key="", $filter = false): string
    {
        $filtered = $filter ? $this->filterMembers($data) : $data;
        $smush = array_map(function($obj) use ($key) { return trim($obj->{$key}); }, $filtered);
        $smush = array_filter($smush, fn($value) => !is_null($value) && $value !== '');
        return "'".implode("','",$smush)."'";
    }

    public function filterMembers($json_payload): array
    {
        return array_filter($json_payload, array($this , "valid"));
    }

    public function membership( $records ): string
    {
            return isset($records[0]) ? trim($records[0]->ucinn_ascendv2__Involvement_Code_Description_Formula__c) : "";
    }

    private function valid($data): bool
    {
        return
            ($data->Is_Current__c == "true" && $data->ucinn_ascendv2__Status__c == "Current"
                && $data->Is_Expired__c == "false" && $data->ucinn_ascendv2__Role__c != "Ex-Officio");
    }

}
