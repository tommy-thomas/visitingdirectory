<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/20/17
 * Time: 3:35 PM
 */

namespace UChicago\AdvisoryCommittee;


use GuzzleHttp\Promise\Promise;

class CommitteeMemberFactory
{

    private $json_payload;
    private $member;

    public function member($json_payload){
        if (is_null($json_payload)) {
            return false;
        }
        $this->json_payload = $json_payload;
        $this->member = new CommitteeMember();
        $this
            ->info()
            ->addresses()
            ->degrees()
            ->employment()
            ->email()
            ->phone();
        return $this->member;
    }

    public static function isActive(\stdClass $member = null)
    {
        if (!is_null($member) && !is_null($member->ID_NUMBER)) {
            return $member->TMS_COMMITTEE_STATUS_CODE == "Active" ? true : false;
        }
        return false;
    }

    public function idNumbers($members=[]){
        $id_numbers = array_map( function($ar){
            if(\UChicago\AdvisoryCommittee\CommitteeMemberFactory::isActive($ar)){
                return $ar->ID_NUMBER;
            } else {
                return null;
            }
        }, $members);
        $id_numbers = array_filter($id_numbers, function ($value){
            if(!is_null($value) && !empty($value)){
                return $value;
            }
        });
        return $this->idsAsQueryString($id_numbers);
}


    private function idsAsQueryString($id_numbers=[]){
        $ids_as_array_string = "";
        foreach ($id_numbers as $id){
            $ids_as_array_string .= "keys[]=".$id."&";
        }
        return substr($ids_as_array_string , 0 , (strlen($ids_as_array_string) - 1));
    }

    private function info(){
        if (!isset($this->json_payload->info)) {
            return $this;
        }
        $this->member->setInfo($this->json_payload->info);
        $this->member->setName();
        return $this;
    }

    private function addresses()
    {
        if (!isset($this->json_payload->addresses)) {
            return $this;
        }
        $this->member->setAddresses($this->addressesFilter($this->json_payload->addresses));
        return $this;
    }

    private function addressesFilter($addresses)
    {
        foreach ($addresses as $key => $address) {
            if (isset($address->ADDR_PREF_IND) && $address->ADDR_PREF_IND != "Y") {
                unset($addresses[$key]);
            }
        }
        return $addresses;
    }

    private function degrees()
    {
        if (!isset($this->json_payload->degree)) {
            return $this;
        }
        $this->member->setDegrees($this->degreesFilter($this->json_payload->degree));
        return $this;
    }

    private function degreesFilter($degrees)
    {
        foreach ($degrees as $key => $degree) {
            if (isset($degree->LOCAL_IND) && $degree->LOCAL_IND != "Y") {
                unset($degrees[$key]);
            }
        }
        return $degrees;
    }

    private function employment()
    {
        if (!isset($this->json_payload->employment)) {
            return $this;
        }
        $this->member->setEmployment($this->employmentFilter($this->json_payload->employment));
        return $this;
    }

    private function employmentFilter($employments)
    {
        foreach ($employments as $key => $employment) {
            if (isset($employment->PRIMARY_EMP_IND) && $employment->PRIMARY_EMP_IND != "Y") {
                unset($employments[$key]);
            }
        }
        return $employments;
    }

    private function email()
    {
        if (!isset($this->json_payload->email)) {
            return $this;
        }
        $this->member->setEmail($this->emailFilter($this->json_payload->email));
        return $this;
    }

    private function emailFilter($emails)
    {
        foreach ($emails as $key => $email) {
            if (isset($email->PREFERRED_IND) && isset($email->EMAIL_TYPE_CODE) 
                && ($email->PREFERRED_IND != "Y" || $email->EMAIL_TYPE_CODE != "H")){
                unset($emails[$key]);
            }
        }
        return $emails;
    }

    private function phone()
    {
        if (!isset($this->json_payload->telephone)) {
            return false;
        }
        $this->member->setPhone($this->phoneFilter($this->json_payload->telephone));
        return $this;
    }

    private function phoneFilter($telephone)
    {
        foreach ($telephone as $key => $phone) {
            if (isset($phone->PREFERRED_IND) && $phone->PREFERRED_IND != "Y") {
                unset($telephone[$key]);
            }
        }
        return $telephone;
    }

}