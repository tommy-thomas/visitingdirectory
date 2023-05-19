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
    private $member;

    public function member($json_payload, $chair = false, $lifetime_member = false)
    {

        $reflection = new \ReflectionClass($json_payload);
        print_r($reflection->getAttributes());

        if (is_null($json_payload) || !is_object($json_payload)) {
            return false;
        }
        $this->member = new CommitteeMember();
        $this->json_payload = $json_payload;

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
//        return $this->member;
    }



    public function filterMembers($json_payload){
        return array_filter($json_payload, array($this , "valid"));
    }

    private function valid($data){
        return
            ($data->Is_Current__c == "true" && $data->ucinn_ascendv2__Status__c == "Current"
                && $data->Is_Expired__c == "false" && $data->ucinn_ascendv2__Role__c != "Ex-Officio");
    }
}
