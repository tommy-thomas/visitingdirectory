<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/15/17
 * Time: 3:05 PM
 */

namespace UChicago\AdvisoryCouncil;

class Committees
{
    private $committees = array(
        'VCLZ' => array('COMMITTEE_CODE' => 'VCLZ',
            'SHORT_DESC' => 'Biological Sciences and Pritzker',
            'FULL_DESC' => 'The Division of the Biological Sciences and the Pritzker School of Medicine Council'),
        'VCLY' => array('COMMITTEE_CODE' => 'VCLY',
            'SHORT_DESC' => 'Chicago Booth',
            'FULL_DESC' => 'The University of Chicago Booth School of Business Council'),
        'VCSA' => array('COMMITTEE_CODE' => 'VCSA',
            'SHORT_DESC' => 'College and Student Activities',
            'FULL_DESC' => 'The College Advisory Council'),
        'VVTH' => array('COMMITTEE_CODE' => 'VVTH',
            'SHORT_DESC' => 'Divinity',
            'FULL_DESC' => 'The Divinity School Council'),
        'VCGS' => array('COMMITTEE_CODE' => 'VCGS',
            'SHORT_DESC' => 'Graham School',
            'FULL_DESC' => 'The University of Chicago Graham School of Continuing Liberal and Professional Studies Council'),
        'VVHM' => array('COMMITTEE_CODE' => 'VVHM',
            'SHORT_DESC' => 'Humanities',
            'FULL_DESC' => 'The Division of the Humanities Council'),
        'VVLW' => array('COMMITTEE_CODE' => 'VVLW',
            'SHORT_DESC' => 'Law School',
            'FULL_DESC' => 'The Law School Council'),
        'VVLB' => array('COMMITTEE_CODE' => 'VVLB',
            'SHORT_DESC' => 'Library',
            'FULL_DESC' => 'The Library Council'),
        'VVIM' => array('COMMITTEE_CODE' => 'VVIM',
            'SHORT_DESC' => 'Molecular Engineering',
            'FULL_DESC' => 'The Institute for Molecular Engineering Council'),
        'VVOI' => array('COMMITTEE_CODE' => 'VVOI',
            'SHORT_DESC' => 'Oriental Institute',
            'FULL_DESC' => 'The Oriental Institute Council'),
        'VVPS' => array('COMMITTEE_CODE' => 'VVPS',
            'SHORT_DESC' => 'Physical Sciences',
            'FULL_DESC' => 'The Division of the Physical Sciences Council'),
        'VCLD' => array('COMMITTEE_CODE' => 'VCLD',
            'SHORT_DESC' => 'Public Policy',
            'FULL_DESC' => 'The University of Chicago Harris School of Public Policy Council'),
        'VVSS' => array('COMMITTEE_CODE' => 'VVSS',
            'SHORT_DESC' => 'Social Sciences',
            'FULL_DESC' => 'The Division of the Social Sciences Council'),
        'VSVC' => array('COMMITTEE_CODE' => 'VSVC',
            'SHORT_DESC' => 'Social Service Administration',
            'FULL_DESC' => 'The School of Social Service Administration Council')
    );

    public function committes()
    {
        return $this->committees;
    }

    public function getCommitteeName($committee_code = "")
    {
        if (!empty($committee_code) && array_key_exists($committee_code , $this->committees)) {
                return $this->committees[$committee_code]['FULL_DESC'];
        }
        return "";
    }

    public function getCommitteesForDisplay( $memberships = array() ){
        $display = array();
        foreach ($memberships as $key => $committee_cocde ){
            array_push( $display , $this->committees[$committee_cocde]['FULL_DESC']);
        }
        return implode( " ," , $display );
    }

}
