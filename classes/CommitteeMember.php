<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/24/17
 * Time: 2:33 PM
 */

namespace UChicago\AdvisoryCouncil;


class CommitteeMember //extends WS_DynamicGetterSetter
{
    private $id_number;
    private $full_name;
    private $first_name;
    private $middle;
    private $last_name;
    private $street;
    private $city;
    private $state;
    private $zip;
    private $foreign_zip;
    private $country_code;
    private $degrees = [];
    private $employment_job_title;
    private $employment_employer_name;
    private $employment_org_name;
    private $email;
    private $phone;
    private $chair = false;
    private $memberships = array();
    private $membership_display;

    public function setInfo($info=""){
        $this->info = $info;
    }

    public function setName($first, $middle,$last){
        $this->first_name = trim($first);
        $this->middle = trim($middle);
        $this->last_name = trim($last);
        $this->full_name = isset($this->first_name ) && !empty($this->first_name) ? $this->first_name : "";
        $this->full_name .= isset( $this->middle ) && !empty( $this->middle ) ? " ".$this->middle : "";
        $this->full_name.= isset($this->last_name ) && !empty($this->last_name) ? " ".$this->last_name : "";
    }

    public function setIDNumber( $id_number = ""){
        $this->id_number = $id_number;
    }

    public function id_number(){
        return $this->id_number;
    }

    public function full_name(){
        return $this->full_name;
    }

    public function first_name(){
        return $this->first_name;
    }

    public function middle(){
        return $this->middle;
    }

    public function last_name(){
        return $this->last_name;
    }

    public function sort_token(){
        return $this->last_name.$this->first_name.$this->middle;
    }

    public function setAddress( $street="", $city="", $state="", $zip="", $foreignZip="", $countryCode=""){
        $this->street = trim($street);
        $this->city = trim($city);
        $this->state = trim($state);
        $this->zip = trim($zip);
        $this->foreign_zip = trim($foreignZip);
        $this->country_code = trim($countryCode);
    }

    public function street(){
        return $this->street;
    }

    public function city(){
        return $this->city;
    }

    public function state(){
        return $this->state;
    }

    public function zip(){
        return $this->zip;
    }

    public function foreign_zip(){
        return $this->foreign_zip;
    }

    public function country_code(){
        return $this->country_code;
    }

    public function adresses(){
        return $this->addresses;
    }

    public function setDegrees($degrees=[]){
        $this->degrees =  $degrees;
    }

    public function degrees(){
        $degrees_data = [];
        date_default_timezone_set('America/Chicago');
        foreach ($this->degrees as $degree){
            $date = new \DateTime();
            $date->setDate($degree->DEGREE_YEAR, 1, 1);
            $degrees_data[]= $degree->DEGREE_CODE . " '" . $date->format('y');
        }
        return implode(" ,",$degrees_data);
    }

    public function setEmploymentData( $job_title = "", $employer_name="", $org_name=""){
        $this->employment_job_title = $job_title;
        $this->employment_employer_name = $employer_name;
        $this->employment_org_name = $org_name;
    }

    public function employment_job_title(){
        return ucwords($this->employment_job_title);
    }

    public function employment_employer_name(){
        return ucwords($this->employment_employer_name);
    }

    public function employment_org_name(){
        return ucwords($this->employment_org_name);
    }

    public function setEmail($email){
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

    public function setChair( $chair = false ){
        $this->chair = $chair;
    }

    public function chair(){
        return $this->chair;
    }

    public function setMembership($membership = array()){
         array_push( $this->memberships , $membership);
         $display= array();
         foreach ($this->memberships as $key => $array){
             //array_push( $display , $array['SHORT_DESC']);
             print_r( $array );
         }

        $this->membership_display = implode( ", "  , $display);
    }

    public function membership_display(){
        return $this->membership_display;
    }
}