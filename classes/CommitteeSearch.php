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
    private $committees = array();
    private $results = array();
    private $factory;
    private $membership;

    public function __construct( $committees = array() , CommitteeMemberFactory $factory = null, CommitteeMemberMembership $membership = null)
    {
        $this->committees = $committees;
        $this->factory = $factory;
        $this->membership = $membership;

    }

    public function searchResults( $search = array("first_name" => "" , "last_name" => "") )
    {
        $this->results = array();
        $this->first_name = trim(strtolower($search['first_name']));
        $this->last_name = trim(strtolower($search['last_name']));
        if( empty($this->committees) || (empty($this->first_name) && empty($this->last_name)) ){
           return $this->results;
        }
        foreach ( $this->committees as $key => $committee){
            array_walk( $committee , array($this , "search"));
        }
        return !is_null($this->factory) ? $this->factory->sortData($this->results) : $this->results;
    }

    private function search( CommitteeMember $member ){
        if( !empty($this->first_name) && empty($this->last_name) && (strpos(strtolower($member->first_name()) , $this->first_name) !== false) ){
            $membership_display = $this->membership->getCommitteesDisplay( $member->id_number() , $this->committees);
            $member->setMembershipDisplay( $membership_display );
            array_push($this->results , $member);
        } elseif(!empty($this->last_name) && empty($this->first_name) && (strpos(strtolower($member->last_name()) , $this->last_name) !== false)){
            $membership_display = $this->membership->getCommitteesDisplay( $member->id_number() , $this->committees);
            $member->setMembershipDisplay( $membership_display );
            array_push($this->results , $member);
        } elseif( (!empty($this->last_name) && strpos(strtolower($member->last_name()) , $this->last_name) !== false)
            || (!empty($this->first_name) && strpos(strtolower($member->first_name()) , $this->first_name) !== false)
        ){
            $membership_display = $this->membership->getCommitteesDisplay( $member->id_number() , $this->committees);
            $member->setMembershipDisplay( $membership_display );
            array_push($this->results , $member);
        }
    }

    public function total(){
        return count( $this->results );
    }
}