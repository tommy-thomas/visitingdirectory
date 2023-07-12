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
        'VSC: BSD/PSOM Council' => array('COMMITTEE_CODE' => 'VSC: BSD/PSOM Council',
            'SHORT_DESC' => 'Biological Sciences and Pritzker',
            'FULL_DESC' => 'The Division of the Biological Sciences and the Pritzker School of Medicine Council'),
        'VSC: Chicago Booth Council' => array('COMMITTEE_CODE' => 'VSC: Chicago Booth Council',
            'SHORT_DESC' => 'Chicago Booth',
            'FULL_DESC' => 'The University of Chicago Booth School of Business Council'),
        'VSC: College Advisory Council' => array('COMMITTEE_CODE' => 'VSC: College Advisory Council',
            'SHORT_DESC' => 'College and Student Activities',
            'FULL_DESC' => 'The College Advisory Council'),
        'VSC: Divinity School Council' => array('COMMITTEE_CODE' => 'VSC: Divinity School Council',
            'SHORT_DESC' => 'Divinity',
            'FULL_DESC' => 'The Divinity School Council'),
        'VSC: Graham School Council' => array('COMMITTEE_CODE' => 'VSC: Graham School Council',
            'SHORT_DESC' => 'Graham School',
            'FULL_DESC' => 'The University of Chicago Graham School of Continuing Liberal and Professional Studies Council'),
        'VSC: Humanities Division Council' => array('COMMITTEE_CODE' => 'VSC: Humanities Division Council',
            'SHORT_DESC' => 'Humanities',
            'FULL_DESC' => 'The Division of the Humanities Council'),
        'VSC: Law School Council' => array('COMMITTEE_CODE' => 'VSC: Law School Council',
            'SHORT_DESC' => 'Law School',
            'FULL_DESC' => 'The Law School Council'),
        'VSC: Library Council' => array('COMMITTEE_CODE' => 'VSC: Library Council',
            'SHORT_DESC' => 'Library',
            'FULL_DESC' => 'The Library Council'),
        'VSC: Pritzker School of Mol Eng Council' => array('COMMITTEE_CODE' => 'VSC: Pritzker School of Mol Eng Council',
            'SHORT_DESC' => 'Molecular Engineering',
            'FULL_DESC' => 'The Pritzker School of Molecular Engineering Council'),
        'VSC: Oriental Institute Council' => array('COMMITTEE_CODE' => 'VSC: Oriental Institute Council',
            'SHORT_DESC' => 'ISAC',
            'FULL_DESC' => ' Institute for the Study of Ancient Cultures'),
        'VSC: Physical Sciences Division Council' => array('COMMITTEE_CODE' => 'VSC: Physical Sciences Division Council',
            'SHORT_DESC' => 'Physical Sciences',
            'FULL_DESC' => 'The Division of the Physical Sciences Council'),
        'VSC: Harris School Council' => array('COMMITTEE_CODE' => 'VSC: Harris School Council',
            'SHORT_DESC' => 'Public Policy',
            'FULL_DESC' => 'The University of Chicago Harris School of Public Policy Council'),
        'VSC: Social Sciences Division Council' => array('COMMITTEE_CODE' => 'VSC: Social Sciences Division Council',
            'SHORT_DESC' => 'Social Sciences',
            'FULL_DESC' => 'The Division of the Social Sciences Council'),
        'VSC: CFS Council' => array('COMMITTEE_CODE' => 'VSC: CFS Council',
            'SHORT_DESC' => 'Social Service Administration',
            'FULL_DESC' => 'The University of Chicago Crown Family School of Social Work, Policy, and Practice Council')
    );

    public function committeeCodesToString(){
        return "'".implode( "','",(array_keys($this->committees())))."'";
    }

    public function committeeCodesToArray(){
        return array_keys($this->committees());
    }

    public function committees()
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

    private function sortByDesc($a, $b)
    {
        $a = $a['FULL_DESC'];
        $b = $b['FULL_DESC'];

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }


    public function getCommitteeMemberships( $memberships = array() ){
        $return = array();
        foreach ($memberships as $key => $committee_code ){
            array_push( $return , $this->committees[$committee_code]);
        }
        usort($return, array($this,'sortByDesc'));
        return $return;
    }

}