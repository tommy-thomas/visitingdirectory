<?php
class CommitteeMemberManager extends WS_DynamicGetterSetter
{
	private $all_member_data;
	
	private $data = array();			
	
	private $entity_info;
	
	private $address_info;
	
	private $degree_info;
	
	private $employment_info;
	
	private $search_results;
	
	private $committee_members_list = array();
	
	public function __construct( $all_member_data = array()  )
	{
		if( !empty($all_member_data) )
		{
			$this->all_member_data = $all_member_data;
		}
	}
	
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
			$member->setIdNumber( (int)$obj->ID_NUMBER );
			$member->setCommitteeRoleCode( (string)$obj->COMMITTEE_ROLE_CODE );
			$member->setFirstName( (string)$obj->FIRST_NAME );
			$member->setMiddleName( (string)$obj->MIDDLE_NAME );
			$member->setLastName( (string)$obj->LAST_NAME );				
			$this->setMemberAddressData( $member, (string)$obj->ID_NUMBER );				
			$this->setMemberDegreeData( $member, (string)$obj->ID_NUMBER );
			$this->setEmploymentData( $member, (string)$obj->ID_NUMBER );
			$this->committee_members_list[] = $member;
		}
		return $this;		
	}

	public function setMemberAddressData( $member , $id )
	{
		if( isset($this->address_info) )
		{
			$a_xml = $this->address_info[$id];
			$address = $a_xml->xpath("//ADDRESS[@Address_Type='H']");
			$phone = $a_xml->xpath("//PHONE_NUMBER[@Address_Type='H']");
			$email = $a_xml->xpath("//EMAIL_ADDRESSES/EMAIL_ADDRESS[@Address_Type='E']");			
			if( isset($address[0]) )
			{
				$member->setStreetOne( (string)$address[0]->STREET1 );
				$member->setStreetTwo( (string)$address[0]->STREET2 );
				$member->setStreetThree( (string)$address[0]->STREET3 );
				$member->setCity( (string)$address[0]->CITY );
				$member->setState( (string)$address[0]->STATE_CODE );
				$member->setZip( (string)$address[0]->ZIPCODE );
				$member->setForeignCityZip( (string)$address[0]->FOREIGN_CITYZIP );
				$member->setCountryCode( (string)$address[0]->COUNTRY_CODE );					
			}
			if( isset($phone[0]) )
			{
				$member->setPhoneAreaCode( (string)$phone[0]->PHONE_AREA_CODE );
				$member->setPhoneNumber( (string)$phone[0]->PHONE_NUMBER );
			}
			if( isset($email[0]) )
			{
				$member->setEmail( (string)$email[0] );
			}						
		}
	}
	
	public function setMemberDegreeData( $member , $id = null)
	{
		if( isset($this->degree_info) && !is_null($id) )
		{
			$d_xml = $this->degree_info[$id];
			$degrees = $d_xml->xpath("//DEGREE");
			$degree_info = array();
			foreach ( $degrees as $d )
			{				
				if( !empty($d->DEGREE_CODE) && strlen($d->DEGREE_YEAR) > 1 )
				{
					$degree_info[] = (string)$d->DEGREE_CODE." '".date("y", mktime(0, 0, 0, 0, 0, intval($d->DEGREE_YEAR)));
				}
			}
			$member->setDegreeInfo( $degree_info );
		}
	}
	
	public function setEmploymentData( $member , $id )
	{
		if( isset($this->employment_info) && isset($this->employment_info[$id]) )
		{			
			$e_xml = $this->employment_info[$id];			
			if( is_a($e_xml , 'SimpleXMLElement') )
			{
				$employment = $e_xml->xpath("//JOB[@JOB_STATUS_CODE='C']");		
			}						
		}
	}
	
	public function getOneMember( $xml )
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
		$address = $xml['address_info']->xpath("//ADDRESS[@Address_Type='H']") ;
		$phone = $xml['address_info']->xpath("//PHONE_NUMBER[@Address_Type='H']");
		$email = $xml['address_info']->xpath("//EMAIL_ADDRESSES/EMAIL_ADDRESS[@Address_Type='E']");				
		if( is_array( $address ) )
		{
			$member->setStreetOne( (string)$address[0]->STREET1 );
			$member->setStreetTwo( (string)$address[0]->STREET2 );
			$member->setStreetThree( (string)$address[0]->STREET3 );
			$member->setCity( (string)$address[0]->CITY );
			$member->setState( (string)$address[0]->STATE_CODE );
			$member->setZip( (string)$address[0]->ZIPCODE );
			$member->setForeignCityZip( (string)$address[0]->FOREIGN_CITYZIP );
			$member->setCountryCode( (string)$address[0]->COUNTRY_CODE );
		}
		if( is_array($phone) )
		{
			$member->setPhoneAreaCode( (string)$phone[0]->PHONE_AREA_CODE );
			$member->setPhoneNumber( (string)$phone[0]->PHONE_NUMBER );
		}
		if( is_array($email) )
			{
				$member->setEmail( (string)$email[0] );
			}		
		$degree_info = $xml['degree_info']->xpath("//ENTITY/DEGREES/DEGREE");				
		$degrees = array();
		foreach ( $degree_info as $d )
		{				
			if( !empty($d->DEGREE_CODE) && strlen($d->DEGREE_YEAR) > 1 )
			{
				$degrees[] = (string)$d->DEGREE_CODE." '".date("y", mktime(0, 0, 0, 0, 0, intval($d->DEGREE_YEAR)));
			}
		}
		
		$member->setDegreeInfo( $degrees );
//		$member->setCommittees( $id , $_SESSION['active_committees']);
//		$this->setEmploymentData( $member, (string)$obj->ID_NUMBER );
//		var_dump( $member );
		return $member;		
	}
	
	public function getCommiteeMemberList()
	{		
		return $this->committee_members_list;
	}
	
	public function searchMembersByName( $firstname = "" , $lastname = "" )
	{
		$this->search_results = array();
		
		if( !empty($lastname) && !empty($firstname) )
		{			
			$this->search_results =  $this->all_member_data->xpath('//COMMITTEE[contains(LAST_NAME , "'.ucfirst($lastname).'") and contains(FIRST_NAME , "'.ucfirst($firstname).'")]');
		}
		elseif( !empty($firstname) )
		{			
			$this->search_results = $this->all_member_data->xpath('//COMMITTEE/FIRST_NAME[contains(., "'.ucfirst($firstname).'")]/parent::*');				
		}
		elseif( !empty($lastname) )
		{
			$this->search_results = $this->all_member_data->xpath('//COMMITTEE/LAST_NAME[contains(., "'.ucfirst($lastname).'")]/parent::*');
		}
		return $this->search_results;
	}
}
?>