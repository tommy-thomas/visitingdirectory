<?php
/**
 * 
 * Manager class for raw simple xml objects , apc cache , and search.
 * @author tommyt
 *
 */
class CommitteeMemberManager extends WS_DynamicGetterSetter
{
	/*
	 * All member data only including first name, last name, and id number
	 */
	private $all_member_data;		
	/*
	 * Payload holds first name , last name , employement info
	 */
	private $entity_info;
	/*
	 * Payload holds address by committee member id_number
	 */
	private $address_info;
	/*
	 * Payload holds degree info by committee member id_number
	 */
	private $degree_info;
	/*
	 * Payload holds employer info by committee member employee number
	 */
	private $employment_info;
	/*
	 * Xpath search results.
	 */
	private $search_results;
	/*
	 * Array of CommitteeMember objects.
	 */
	private $committee_members_list = array();
	/*
	 * all_member_data loaded from apc_cache
	 */
	public function __construct()
	{
		{
			try
			{
				$this->all_member_data = simplexml_load_string( apc_fetch('vc_all_member_data') );
			} catch (Exception $e) {
				Application::handleExceptions($e);
			}
		}
	}
	/**
	 * Fluent load method based on four character committee code and assoc array of simple xml.
	 */
	public function load( $code="" , $members)
	{				
		if( !empty($code) )
		{	
			$this->entity_info = $this->all_member_data->xpath('//COMMITTEE/COMMITTEE_CODE[. ="'.$code.'"]/parent::*');				
			$this->address_info = isset($members['address_info']) ? $members['address_info'] : array();
			$this->degree_info = isset($members['degree_info']) ? $members['degree_info'] : array() ;
			$this->employment_info = isset($members['employment_info']) ? $members['employment_info'] : array() ;			
		}
		foreach( $this->entity_info as $key => $obj )
		{
			$member = new CommitteeMember();
			$member->setIdNumber( htmlClean((int)$obj->ID_NUMBER) );
			$member->setCommitteeRoleCode( $this->setValue($obj->COMMITTEE_ROLE_CODE) );
			$member->setFirstName( $this->setValue($obj->FIRST_NAME) );
			$member->setMiddleName( $this->setValue($obj->MIDDLE_NAME) );		
			$last_name = (  isset($obj->COMMITTEE_ROLE_CODE ) && ((string)$obj->COMMITTEE_ROLE_CODE ) ==  'LM' )
				? $this->setValue($obj->LAST_NAME)."*"
				: $this->setValue($obj->LAST_NAME);
			$member->setLastName( $last_name );				
			$this->setMemberAddressData( $member,$this->setValue($obj->ID_NUMBER) );
			$this->setMemberDegreeData( $member, $this->setValue($obj->ID_NUMBER) );
			$this->setMemberEmploymentData( $member, $this->setValue($obj->ID_NUMBER) );
			$this->committee_members_list[] = $member;
		}
		return $this;
	}

	/**
	 * Set CommitteeMember object address info.
	 */
	public function setMemberAddressData( $member , $id )
	{
		if( isset($this->address_info[$id]) )
		{
			$a_xml = $this->address_info[$id];
			$address = $a_xml->xpath("//ADDRESS/ADDR_PREF_IND[. = 'Y']/parent::*");			
			$phone = $a_xml->xpath("//PHONE_NUMBER[@Address_Type='H']");
			$email = $a_xml->xpath("//EMAIL_ADDRESSES/EMAIL_ADDRESS[@Address_Type='E']");			
			if( isset($address[0]) )
			{
				$member->setStreetOne( $this->setValue((string)$address[0]->STREET1));
				$member->setStreetTwo( $this->setValue((string)$address[0]->STREET2));
				$member->setStreetThree( $this->setValue((string)$address[0]->STREET3) );
				$member->setCity( $this->setValue((string)$address[0]->CITY) );
				$member->setState( $this->setValue((string)$address[0]->STATE_CODE) );
				$member->setZip( $this->setValue((string)$address[0]->ZIPCODE) );
				$member->setForeignCityZip( $this->setValue((string)$address[0]->FOREIGN_CITYZIP) );
				$member->setCountryCode( $this->setValue((string)$address[0]->COUNTRY_CODE) );					
			}
			if( isset($phone[0]) )
			{
				$member->setPhoneAreaCode( $this->setValue($phone[0]->PHONE_AREA_CODE) );
				$member->setPhoneNumber( $this->setValue($phone[0]->PHONE_NUMBER) );
			}
			if( isset($email[0]) )
			{
				$member->setEmail( $this->setValue((string)$email[0]) );
			}						
		}
	}
	/**
	 * Set CommitteeMember object degree info.
	 */
	public function setMemberDegreeData( $member , $id = null)
	{
		if( isset($this->degree_info[$id]) )
		{
			$d_xml = $this->degree_info[$id];
			$degrees = $d_xml->xpath("//ENTITY/DEGREES/DEGREE/LOCAL_IND[. = 'Y']/parent::*");
			$degree_info = array();
			foreach ( $degrees as $d )
			{	
				if( !empty($d->DEGREE_CODE) && strlen($d->DEGREE_YEAR) > 1 )
				{
					$degree_info[] =$this->setValue($d->DEGREE_CODE)." '".date("y", mktime(0, 0, 0, 1, 1, intval($d->DEGREE_YEAR)));
				}
			}
			$member->setDegreeInfo( $degree_info );
		}
	}
	/**
	 * Set CommitteeMember object employment info.
	 */
	public function setMemberEmploymentData( $member , $id )
	{
		if( isset($this->employment_info) && isset($this->employment_info[$id]) )
		{			
			$employment = $this->employment_info[$id];
			if( count($employment) > 0 && isset($employment[0]) )
			{
				$member->setJobTitle( $this->setValue((string)$employment[0]->JOB) );
				$member->setEmployerName( $this->setValue((string)$employment[0]->EMPLOYER) );
			}			
		}
	}
	/**
	 * @return fully loaded CommitteeMember.
	 */
	public function getOneMember( $xml )
	{
		$member = null;
		if( isset($xml['entity_info']) && is_a($xml['entity_info'], 'SimpleXMLElement') && $xml['entity_info']->count() > 0 )
		{
			$member = new CommitteeMember();
			$id = $xml['entity_info']->xpath('//ENTITY/ID_NUMBER') ;
			$fname = $xml['entity_info']->xpath('//ENTITY/FIRST_NAME') ;
			$lname = $xml['entity_info']->xpath('//ENTITY/LAST_NAME') ;
			$middle = $xml['entity_info']->xpath('//ENTITY/MIDDLE_NAME') ;
			$member->setIdNumber( (int)$id[0]  );		
			$member->setFirstName( (string)$fname[0]);
			if( is_array($middle) )
			{
				$member->setMiddleName( (string)$middle[0] );	
			}		
			$member->setLastName( (string)$lname[0] );
			$address = $xml['address_info']->xpath("//ADDRESS/ADDR_PREF_IND[. = 'Y']/parent::*");
			$phone = $xml['address_info']->xpath("//PHONE_NUMBER[@Address_Type='H']");
			$email = $xml['address_info']->xpath("//EMAIL_ADDRESSES/EMAIL_ADDRESS[@Address_Type='E']");				
			if( isset( $address[0] ) )
			{
				$member->setStreetOne( $this->setValue((string)$address[0]->STREET1));
				$member->setStreetTwo( $this->setValue((string)$address[0]->STREET2));
				$member->setStreetThree( $this->setValue((string)$address[0]->STREET3) );
				$member->setCity( $this->setValue((string)$address[0]->CITY) );
				$member->setState( $this->setValue((string)$address[0]->STATE_CODE) );
				$member->setZip( $this->setValue((string)$address[0]->ZIPCODE) );
				$member->setForeignCityZip( $this->setValue((string)$address[0]->FOREIGN_CITYZIP) );
				$member->setCountryCode( $this->setValue((string)$address[0]->COUNTRY_CODE) );
			}
			if( isset($phone[0]) )
			{
				$member->setPhoneAreaCode( $this->setValue((string)$phone[0]->PHONE_AREA_CODE) );
				$member->setPhoneNumber( $this->setValue((string)$phone[0]->PHONE_NUMBER) );
			}
			if( isset($email[0]) )
			{
				$member->setEmail( $this->setValue((string)$email[0]) );
			}
			$degree_info = $xml['degree_info']->xpath("//ENTITY/DEGREES/DEGREE/LOCAL_IND[. = 'Y']/parent::*");		
			$degrees = array();
			foreach ( $degree_info as $d )
			{				
				if( !empty($d->DEGREE_CODE) && strlen($d->DEGREE_YEAR) > 1 )
				{
					$degrees[] = $this->setValue((string)$d->DEGREE_CODE)." '".date("y", mktime(0, 0, 0, 1, 1, intval($d->DEGREE_YEAR)));
				}
			}
			$member->setDegreeInfo( $degrees );
			
			$member->setCommitteesFromXML( $xml['committee_info'] ,   apc_fetch('vc_active_committees'));
			$employment = $xml['employment_info'];
			if( isset($employment[0]) )
			{
				$member->setJobTitle( $this->setValue((string)$employment[0]->JOB) );
				$member->setEmployerName( $this->setValue((string)$employment[0]->EMPLOYER) );
			}		
		}
		return $member;

	}
	/**
	 * @return array of CommiteeMember objects.
	 */
	public function getCommiteeMemberList()
	{
		return $this->committee_members_list;
	}
	/**
	 * Search simple xml object of all member data by first name and/or last name and return search results
	 * as simple xml.
	 * @param $firstname
	 * @param $lastname
	 */
	public function searchMembersByName( $firstname = "" , $lastname = "" )
	{
		$this->search_results = array();
		
		if( !empty($lastname) && !empty($firstname) )
		{			
			$this->search_results =  $this->all_member_data->xpath('//COMMITTEE[contains(LAST_NAME , "'.ucfirst($this->restoreString($lastname)).'") and contains(FIRST_NAME , "'.ucfirst($this->restoreString($firstname)).'")]');
		}
		elseif( !empty($firstname) )
		{			
			$this->search_results = $this->all_member_data->xpath('//COMMITTEE/FIRST_NAME[contains(., "'.ucfirst($this->restoreString($firstname)).'")]/parent::*');				
		}
		elseif( !empty($lastname) )
		{
			$this->search_results = $this->all_member_data->xpath('//COMMITTEE/LAST_NAME[contains(., "'.ucfirst($this->restoreString($lastname)).'")]/parent::*');
		}
		$this->xsort($this->search_results, 'LAST_NAME' , 'FIRST_NAME');
		return $this->search_results;
	}
	/**
	 * Search simple xml search results by first name and/or last name.
	 * @param $nodes
	 * @param $child_name
	 * @param $second_child
	 * @param $order
	 */
	public function xsort(&$nodes, $child_name, $second_child =null , $order = SORT_ASC)
	{
	    $sort_proxy = array();
	    foreach ($nodes as $k => $node)
	    {
	    	$value = !is_null( $second_child ) ? $this->setValue($node->$child_name) .', '.$this->setValue($node->$second_child) : $this->setValue($node->$child_name);
	        $sort_proxy[$k] = $value;
	    }
	    array_multisort($sort_proxy, $order, $nodes);
	}
	/**
	 * Cast value to string and htmlClean it.
	 */
	private function setValue($string)
	{
		$str = htmlClean(trim((string)$string));
		if( !empty($str))
		{
			return $str;
		}
		else
		{
			return null;
		}
	}
	/**
	 * Replace entities in cleaned strings with correct characters for search.
	 */
	private function restoreString($string)
	{
		return str_replace("&#39;", "'", $string);
	}
}
?>