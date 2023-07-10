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
    private $employment_id;
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
    private $lifetime_member = false;
    private $memberships = array();
    private $membership_display;

    /**
     * @param mixed $employment_id
     */
    public function setEmploymentId($employment_id): void
    {
        $this->employment_id = $employment_id;
    }

    /**
     * @param mixed $country_code
     */
    public function setCountryCode($country_code): void
    {
        $this->country_code = $country_code;
    }

    /**
     * @param mixed $employment_job_title
     */
    public function setEmploymentJobTitle($employment_job_title): void
    {
        $this->employment_job_title = $employment_job_title;
    }

    /**
     * @param mixed $employment_employer_name
     */
    public function setEmploymentEmployerName($employment_employer_name): void
    {
        $this->employment_employer_name = $employment_employer_name;
    }

    /**
     * @param mixed $employment_org_name
     */
    public function setEmploymentOrgName($employment_org_name): void
    {
        $this->employment_org_name = $employment_org_name;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @param mixed $middle
     */
    public function setMiddle($middle): void
    {
        $this->middle = $middle;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name): void
    {
        $this->last_name = $last_name;
    }



    public function setInfo($info=""){
        $this->info = $info;
    }

    public function setName($first, $middle,$last){
        $this->first_name = trim($first);
        $this->middle = trim($middle);
        $this->last_name = trim($last);
    }

    public function setIDNumber( $id_number = ""){
        $this->id_number = $id_number;
    }

    public function id_number(){
        return $this->id_number;
    }

    public function employment_id(){
        return $this->employment_id;
    }

    public function setFullName( $FullName ){
        $this->full_name = $FullName;
    }

    public function first_name(){
        return $this->first_name;
    }

    public function middle(){
        return $this->middle;
    }

    public function last_name(){
        return $this->lifetime_member() ? $this->last_name."*" : $this->last_name;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->full_name;
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
        if( !empty($degrees )){
            foreach ($degrees as $degree ){
                $d = new \stdClass();
                $d->year = $degree->ucinn_ascendv2__Conferred_Degree_Year__c;
                $d->type = $degree->ucinn_ascendv2__Degree__c;
                array_push($degree, $this->degrees);
            }
        }
    }

    public function degrees(){
        $degrees_data = [];
        date_default_timezone_set('America/Chicago');
        foreach ($this->degrees as $degree){
            $date = new \DateTime();
            $date->setDate((int)$degree->year, 1, 1);
            $degrees_data[]= $degree->type . " '" . $date->format('y');
        }
        return implode(", ",$degrees_data);
    }

    public function setEmploymentData( $data = [] ){
        if( isset($data) && is_array($data) && isset($data[0])){
            $this->employment_job_title = $data[0]->ucinn_ascendv2__Job_Title__c;
            $this->employment_employer_name = $data[0]->ucinn_ascendv2__Related_Account_Name_Formula__c;
            $this->employment_org_name = "";
        }

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

    public function setLifeTimeMember( $lifetime_member = false){
        $this->lifetime_member = $lifetime_member;
    }

    public function lifetime_member(){
        return $this->lifetime_member;
    }

    public function setMembership($memberships = array()){
         array_push( $this->memberships , $memberships);
         $display= array();
         $data = $this->memberships[0];
         for ($i=0; $i<count($data); $i++){
             isset( $data[$i]['SHORT_DESC'] ) && !in_array($data[$i]['SHORT_DESC'] , $display) ? array_push( $display , $data[$i]['SHORT_DESC']) : "";
         }
        $this->membership_display = implode( ", "  , $display);
    }

    public function membership_display(){
        return $this->membership_display;
    }
}