<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/30/18
 * Time: 2:34 PM
 */

namespace UChicago\AdvisoryCouncil\Data;

use UChicago\AdvisoryCouncil\CLIMemcache;
use UChicago\AdvisoryCouncil\CommitteeMemberMembership;
use UChicago\AdvisoryCouncil\Data\Database as Database;

class StaticRepository
{
    private $data = array();
    private $memcache;

    public function __construct(CLIMemcache $memcache, $environment = "dev")
    {
        $this->memcache = $memcache;

        $this->setData();
    }

    public function setData()
    {
        //set data array
        $this->data['AdvisoryCouncilsMemberData'] = $this->memcache->get('AdvisoryCouncilsMemberData');
        $this->data['AdvisoryCouncilsMemberMembershipData'] = $this->memcache->get('AdvisoryCouncilsMemberMembershipData');

        $this->memcache->set('AdvisoryCouncilsMemberData', $this->data['AdvisoryCouncilsMemberData'], MEMCACHE_COMPRESSED, 0);
        $this->memcache->set('AdvisoryCouncilsMemberMembershipData', $this->data['AdvisoryCouncilsMemberMembershipData'] , MEMCACHE_COMPRESSED, 0 );

        //SQLite Backup
        $db = new Database();
        $db->set('member_data',$this->data['AdvisoryCouncilsMemberData']);
        $db->set('membership_data', $this->data['AdvisoryCouncilsMemberMembershipData']);
    }

    public function getCouncilData($code)
    {
        if (isset($this->data['AdvisoryCouncilsMemberData'][$code])) {
            return $this->data['AdvisoryCouncilsMemberData'][$code];
        }
        return array();
    }

    public function findMemberByIdNumber($id_number = "" ){
        foreach ( $this->data['AdvisoryCouncilsMemberData'] as $key => $committee){
           foreach ( $committee as $member ){
               if( $member->id_number() == $id_number ){
                   return $member;
               }
           }
        }
        return;
    }

    public function allCouncilData()
    {
        return $this->data['AdvisoryCouncilsMemberData'];
    }

    public function getCouncilMembershipData()
    {
        if (isset($this->data['AdvisoryCouncilsMemberMembershipData']['committee_membership'])) {
            return $this->data['AdvisoryCouncilsMemberMembershipData']['committee_membership'];
        }
        return new CommitteeMemberMembership();
    }
}