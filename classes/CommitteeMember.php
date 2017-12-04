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
    private $info;
    private $name;
    private $addresses;
    private $degrees;
    private $employment;
    private $email;
    private $phone;

    public function setInfo($info=""){
        $this->info = $info;
    }

    public function setName(){
        $this->name = isset($this->info->FIRST_NAME ) && !empty($this->info->FIRST_NAME) ? $this->info->FIRST_NAME : "";
        $this->name .= isset( $this->info->MIDDLE_NAME ) && !empty( $this->info->MIDDLE_NAME) ? " ".$this->info->MIDDLE_NAME : "";
        $this->name .= isset($this->info->LAST_NAME ) && !empty($this->info->LAST_NAME) ? " ".$this->info->LAST_NAME : "";
    }

    public function name(){
        return $this->name;
    }

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

    public function setEmail($email=""){
        $this->email = $email;
    }

    public function email(){
        return $this->email;
    }

    public function setPhone($phone=""){
        $this->phone = $phone;
    }

    public function phone(){
        return $this->phone;
    }
}