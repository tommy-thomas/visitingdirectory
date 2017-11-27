<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/24/17
 * Time: 2:33 PM
 */

namespace UChicago\AdvisoryCommittee;


class CommitteeMember //extends WS_DynamicGetterSetter
{
    private $addresses;
    private $degrees;
    private $employment;
    private $email;
    private $phone;

    public function setAddresses($addresses=[]){
        $this->addresses = $addresses;
    }

    public function adresses(){
        return $this->employment;
    }

    public function setDegrees($degrees=[]){
        $this->degrees =  $degrees;
    }

    public function degrees(){
        return $this->degrees;
    }

    public function setEmployment($employment=[]){
        $this->employment =  $employment;
    }

    public function employment(){
        return $this->employment;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function email(){
        return $this->email;
    }

    public function setPhone($phone){
        $this->phone = $phone;
    }

    public function phone(){
        return $this->phone;
    }
}