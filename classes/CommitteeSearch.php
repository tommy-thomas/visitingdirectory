<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/9/18
 * Time: 10:13 AM
 */

namespace UChicago\AdvisoryCouncil;


class CommitteeSearch
{
    private $first_name = "";
    private $last_name = "";
    private $data = array();
    private $results = array();
    private $factory;
    private $committees;
    private $membership;

    public function __construct( $data = array() , CommitteeMemberFactory $factory = null , $membership = null)
    {
        $this->data = $data;
        $this->factory = $factory;
        $this->membership = $membership;
    }

    public function searchResults( $search = array("first_name" => "" , "last_name" => "") , Committees $committees )
    {
        $this->results = array();
        $this->first_name = trim(strtolower($search['first_name']));
        $this->last_name = trim(strtolower($search['last_name']));
        $this->committees = $committees;

        if( empty($this->data) || (empty($this->first_name) && empty($this->last_name)) ){
           return $this->results;
        }
        foreach ( $this->data as $key => $data){
            array_walk( $data , array($this , "search"));
        }
        return !is_null($this->factory) ? $this->factory->sortData($this->results) : $this->results;
    }

    private function search( CommitteeMember $member ){
        if( !empty($this->first_name) && empty($this->last_name) && (strpos(strtolower($member->first_name()) , $this->first_name) !== false) ){

            $memberships = $this->membership->getCommittees( $member->id_number() );
            $member->setMembership( $memberships );
            array_push($this->results , $member);
        } elseif(!empty($this->last_name) && empty($this->first_name) && (strpos(strtolower($member->last_name()) , $this->last_name) !== false)){
            $memberships = $this->membership->getCommittees( $member->id_number() );
            $member->setMembership($memberships );
            array_push($this->results , $member);
        } elseif( (!empty($this->last_name) && strpos(strtolower($member->last_name()) , $this->last_name) !== false)
            || (!empty($this->first_name) && strpos(strtolower($member->first_name()) , $this->first_name) !== false)
        ){
            $memberships = $this->membership->getCommittees( $member->id_number() );
            $member->setMembership( $memberships );
            array_push($this->results , $member);
        }
    }

    public function total(){
        return count( $this->results );
    }
}