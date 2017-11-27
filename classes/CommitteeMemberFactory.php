<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/20/17
 * Time: 3:35 PM
 */

namespace UChicago\AdvisoryCommittee;


class CommitteeMemberFactory
{

    private $json_payload;
    private $member;


    public function __construct($json_payload = null)
    {
        if (is_null($json_payload)) {
            return false;
        }
        $this->json_payload = $json_payload;
        $this->member = new CommitteeMember();
        $this->addresses()
            ->degrees()
            ->employment()
            ->email()
            ->phone();
        var_dump($this->member->phone());
    }


    private function addresses()
    {
        if (!isset($this->json_payload->addresses)) {
            return false;
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
            return false;
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
            return false;
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
            return false;
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