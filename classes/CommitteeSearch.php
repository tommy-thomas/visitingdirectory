<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/9/18
 * Time: 10:13 AM
 */

namespace UChicago\AdvisoryCommittee;


class CommitteeSearch
{
    private $first_name = "";
    private $last_name = "";
    private $committees;
    private $results = array();
    private $factory;

    public function __construct( $committees = array() , CommitteeMemberFactory $factory = null)
    {
        $this->committees = $committees;
        $this->factory = $factory;
    }

    public function searchResults( $search = array("first_name" => "" , "last_name" => "") )
    {
        $this->results = array();
        $this->first_name = trim($search['first_name']);
        $this->last_name = trim($search['last_name']);
        if( empty($this->committees) || (empty($this->first_name) && empty($this->last_name)) ){
           return $this->results;
        }
        foreach ( $this->committees as $key => $committee){
            array_walk( $committee , array($this , "search"));
        }
        return !is_null($this->factory) ? $this->factory->sortData($this->results) : $this->results;
    }

    private function search( CommitteeMember $member ){
        if( !empty($this->first_name) && empty($this->last_name) && (strpos($member->first_name() , $this->first_name) !== false) ){
            array_push($this->results , $member);
        } elseif(!empty($this->last_name) && empty($this->first_name) && (strpos($member->last_name() , $this->last_name) !== false)){
            array_push($this->results , $member);
        } elseif( !empty($this->last_name) && !empty($this->first_name)
        && (strpos($member->first_name() , $this->first_name) !== false)
            || (strpos($member->last_name() , $this->last_name) !== false) ){
            array_push($this->results , $member);
        }
    }
}