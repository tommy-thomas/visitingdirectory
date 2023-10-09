<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/23/18
 * Time: 11:56 AM
 */

namespace UChicago\AdvisoryCouncil;


class CommitteeMemberMembership
{
    private $committee_members = array();

    public function addCommittee( $id_number = "" , $committee_code = "")
    {
        if( empty($id_number) || empty($committee_code) )
        {
            return false;
        }

        if( array_key_exists($id_number , $this->committee_members))
        {
            $committees = $this->committee_members[$id_number];
            if( !in_array($committee_code, $this->committee_members[$id_number] )){
                array_push( $committees , $committee_code);
                $this->committee_members[$id_number] = $committees;
            }
        } else {
            $this->committee_members[$id_number]= array( $committee_code );
        }
    }

    public function getCommittees($id_number = "" ){

        if( !isset($this->committee_members[$id_number])){
            return array();
        }
        return $this->committee_members[$id_number];
    }
}