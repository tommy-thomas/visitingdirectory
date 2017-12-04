<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/15/17
 * Time: 3:05 PM
 */

namespace UChicago\AdvisoryCommittee;

class Committees
{
    private $committees = array(
        array('COMMITTEE_CODE' => 'VCLZ',
            'SHORT_DESC' => 'Biological Sciences and Pritzker',
            'FULL_DESC' => 'The Division of the Biological Sciences and the Pritzker School of Medicine Council'),
        array('COMMITTEE_CODE' => 'VCLY',
            'SHORT_DESC' => 'Chicago Booth',
            'FULL_DESC' => 'The University of Chicago Booth School of Business Council'),
        array('COMMITTEE_CODE' => 'VCSA',
            'SHORT_DESC' => 'College and Student Activities',
            'FULL_DESC' => 'The College Advisory Council'),
        array('COMMITTEE_CODE' => 'VVTH',
            'SHORT_DESC' => 'Divinity',
            'FULL_DESC' => 'The Divinity School Council'),
        array('COMMITTEE_CODE' => 'VCGS',
            'SHORT_DESC' => 'Graham School',
            'FULL_DESC' => 'The University of Chicago Graham School of Continuing Liberal and Professional Studies Council'),
        array('COMMITTEE_CODE' => 'VVHM',
            'SHORT_DESC' => 'Humanities',
            'FULL_DESC' => 'The Division of the Humanities Council'),
        array('COMMITTEE_CODE' => 'VVLW',
            'SHORT_DESC' => 'Law School',
            'FULL_DESC' => 'The Law School Council'),
        array('COMMITTEE_CODE' => 'VVLB',
            'SHORT_DESC' => 'Library',
            'FULL_DESC' => 'The Library Council'),
        array('COMMITTEE_CODE' => 'VVIM',
            'SHORT_DESC' => 'Molecular Engineering',
            'FULL_DESC' => 'The Institute for Molecular Engineering Council'),
        array('COMMITTEE_CODE' => 'VVOI',
            'SHORT_DESC' => 'Oriental Institute',
            'FULL_DESC' => 'The Oriental Institute Council'),
        array('COMMITTEE_CODE' => 'VVPS',
            'SHORT_DESC' => 'Physical Sciences',
            'FULL_DESC' => 'The Division of the Physical Sciences Council'),
        array('COMMITTEE_CODE' => 'VCLD',
            'SHORT_DESC' => 'Public Policy',
            'FULL_DESC' => 'The University of Chicago Harris School of Public Policy Council'),
        array('COMMITTEE_CODE' => 'VVSS',
            'SHORT_DESC' => 'Social Sciences',
            'FULL_DESC' => 'The Division of the Social Sciences Council'),
        array('COMMITTEE_CODE' => 'VSVC',
            'SHORT_DESC' => 'Social Service Administration',
            'FULL_DESC' => 'The School of Social Service Administration Council')
    );

    public function committes(){
        return $this->committees;
    }

}
