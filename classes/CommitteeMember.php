<?php
/**
 * 
 * Value object for Committee Member
 * @author tommyt
 *
 */
	class CommitteeMember extends WS_DynamicGetterSetter
	{
		protected $IdNumber;
		protected $CommitteeRoleCode;
		protected $FirstName;
		protected $MiddleName;
		protected $LastName;
		protected $FullName;
		protected $StreetOne;
		protected $StreetTwo;
		protected $StreetThree;
		protected $City;
		protected $State;
		protected $Zip;
		protected $ForeignCityZip;
		protected $CountryCode;
		protected $PhoneAreaCode;
		protected $PhoneNumber;
		protected $Email;
		protected $DegreeInfo;
		protected $JobTitle;
		protected $EmployerName;
		protected $IsLifeTimeMember;
		protected $CommitteesShort = array();
        protected $CommitteesLong = array();
		protected $CommitteesDisplay;
		
		public function getPhoneNumber()
		{			
			if( isset($this->PhoneNumber) && strlen($this->PhoneNumber) == 7)
			{
				$tx = preg_replace('~(\d{3})[^\d]*(\d{4})$~', '$1-$2', $this->PhoneNumber);
				if( isset($this->PhoneAreaCode) && strlen($this->PhoneAreaCode) == 3)
				{
					return $this->PhoneAreaCode.'-'.$tx;
				}
				else
				{
					return $tx;
				}
			}
			else
			{
				return "";
			}
		}
		
		public function getDegreeInfo()
		{
			if( is_array($this->DegreeInfo) && !empty($this->DegreeInfo) )
			{
				return implode(", " , $this->DegreeInfo);
			}
		}
		
		public function getFullName()
		{			
			if( !is_null($this->FirstName) && strlen($this->FirstName) )
			{
				$this->FullName .= $this->FirstName." ";
			}
			if( !is_null($this->MiddleName) && strlen($this->MiddleName) )
			{
				$this->FullName .= $this->MiddleName." ";
			}
			if( !is_null($this->LastName) && strlen($this->LastName) )
			{
				$this->FullName .= $this->LastName;
			}
			return $this->FullName;
		}
		
		public function setMemberCommitteesShort($ids=array(), $committees=array())
		{			
			if( !empty($ids) && !empty($committees) )
			{
			foreach ( $ids as $key=>$xml )
			{							
			foreach ( $committees as $c )
			{								
				if( $key == $this->IdNumber && is_array($xml) )
				{	
					foreach ( $xml as $value)
					{
						if( is_a($c, 'Committee') && (string)$value->COMMITTEE_CODE == (string)$c->getCOMMITTEE_CODE())
						{									
							$this->CommitteesShort[(string)$c->getCOMMITTEE_CODE()] = (string)$c->getSHORT_DESC();
						}								
					}							
				}
			}
			}
			}				
		}
		
		public function setMemberCommittees( $xml , $committees )
		{	
			if( is_array($xml) )
			{
				foreach ( $xml as $x )
				{
					foreach ( $committees as $c )
					{
						if( is_a($c,'Committee') && (string)$x->COMMITTEE_CODE == (string)$c->getCOMMITTEE_CODE())
						{
							$this->CommitteesLong[(string)$x->COMMITTEE_CODE] = (string)$c->getFULL_DESC();
                            $this->CommitteesShort[(string)$x->COMMITTEE_CODE] = (string)$c->getSHORT_DESC();
						}
					}
				}
			}
		}
		
		public function getCommitteesDisplay()
		{
			if( !empty($this->CommitteesShort) )
			{
				asort($this->CommitteesShort);
				$this->CommitteesDisplay = implode(", ",$this->CommitteesShort);
				return $this->CommitteesDisplay;
			}
		}
		
		/**
		 * Add a object to the ClearSilver Template -
		 * Template variable names have to match class property names exactly for this to work
		 * @param object $template - WS_Template object
		 * @param string $prefix - Prefix for template variable array -- Default - ''
		 */
		public function addClassDataTemplate($template, $prefix = '')
		{
			$propertyarray = $this->getpropertyarray();
			foreach ($propertyarray as $propertyname)	{
				$functionname = 'get' . $propertyname;
				$template->add_data($prefix . $propertyname, $this->$functionname() , false);	
			}
			return;
		}
	}
?>