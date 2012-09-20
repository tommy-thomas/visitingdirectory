<?php
/**
 * 
 * A class used as a catch all for several cURL queries, urls to Griffin api, and setters for
 * PHP's  Alternative PHP Cache.
 * @author tommyt
 *
 */
class Collection
{
	/*
	 * Class instance.
	 */
	static private $collection;
	/*
	 * Application app.
	 */
	private $app;
	/*
	 * cURL curl.
	 */
	private $curl;
	/*
	 * Holder for raw xml or simple exml.
	 */
	private $xml;
	/*
	 * Array of sevice urls.
	 */
	private $urls = array();
	/*
	 * All member data as array of simple xml.
	 */
	private $all_member_data;
	/**
     * Public constructor.
	 */
	public function __construct( Application $app , cURL $curl = null  , $token = null)
	{	
		$this->app = $app;
		$this->curl = $curl;
		if( $this->app->isDev() || $this->app->isStage() )
		{
			$this->urls = array(
			'active_committees' => 'https://grif-uat-soa.uchicago.edu/api/griffin/metadata/committee_code',
			'address_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/addresses',
			'affiliations' => 'https://grif-uat-soa.uchicago.edu/api/griffin/membershipaffiliation/%s',
			'all_affiliations' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/membershipaffiliation',
			'all_members' => 'https://grif-uat-soa.uchicago.edu/api/griffin/membershipaffiliation/%s',					
			'degree_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/degrees',		
			'entity_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s',	
			'email_validation' => 'https://grif-uat-soa.uchicago.edu/api/griffin/membershipaffiliation/%s?emailaddress=%s'		
			);	
		}
		elseif( $this->app->isProd() )
		{
			$this->urls = array(
			'active_committees' => 'https://grif-uat-soa.uchicago.edu/api/griffin/metadata/committee_code',
			'address_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/addresses',
			'affiliations' => 'https://grif-uat-soa.uchicago.edu/api/griffin/membershipaffiliation/%s',
			'all_affiliations' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/membershipaffiliation',
			'all_members' => 'https://grif-uat-soa.uchicago.edu/api/griffin/membershipaffiliation/%s',							
			'degree_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/degrees',		
			'entity_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s',	
			'email_validation' => 'https://grif-uat-soa.uchicago.edu/api/griffin/membershipaffiliation/%s?emailaddress=%s'			
			);	
		}
		if( !is_null($token) )
		{
			$this->setCache($token);
		}		
		if( apc_exists('all_member_data') )
		{
			$this->all_member_data = simplexml_load_string( apc_fetch('all_member_data') );
		}
		else
		{
			if( !is_null($token))
			{
				$this->setCommittees($token);
				$this->all_member_data = simplexml_load_string( apc_fetch('all_member_data') );
			}
			
		}
		
		self::$collection = $this;
	}
	
	public static function instance($app,$curl=null)
	{
		if(!self::$collection)
		{
			self::$collection = new Collection($app,$curl);
		}
		return self::$collection;
	}
	
	public function setAllMemberData($token)
	{
		libxml_use_internal_errors(true);
		$this->curl->setPost($token);		
		$this->curl->createCurl( sprintf($this->urls['all_members'], apc_fetch('active_committee_url_list') ));
		if( !apc_exists('all_member_data') )
		{
			apc_add('all_member_data', $this->curl->__toString() , 172800);
		}		
	}
	
	public function setCommittees()
	{
		$committees = array(
			array('COMMITTEE_CODE' => 'VVRT',
				'SHORT_DESC' => 'Art History',
				'FULL_DESC' => 'Visiting Committee to the Department of Art History'),
			array('COMMITTEE_CODE' => 'VCLZ',
				'SHORT_DESC' => 'Biological Sciences and Pritzker',
				'FULL_DESC' => 'Visiting Committee to the Division of the Biological Sciences and the Pritzker School of Medicine '),
			array('COMMITTEE_CODE' => 'VCLY',
				'SHORT_DESC' => 'Chicago Booth',
				'FULL_DESC' => 'Council on the University of Chicago Booth School of Business'),
			array('COMMITTEE_CODE' => 'VCSA',
				'SHORT_DESC' => 'College and Student Activities',
				'FULL_DESC' => 'Visiting Committee on the College and Student Activities'),
			array('COMMITTEE_CODE' => 'VVTH',
				'SHORT_DESC' => 'Divinity',
				'FULL_DESC' => 'Visiting Committee to the Divinity School '),
			array('COMMITTEE_CODE' => 'VCGS',
				'SHORT_DESC' => 'Graham School',
				'FULL_DESC' => 'Council on the Graham School'),
			array('COMMITTEE_CODE' => 'VVHM',
				'SHORT_DESC' => 'Humanities',
				'FULL_DESC' => 'Visiting Committee to the Division of the Humanities'),
			array('COMMITTEE_CODE' => 'VVLW',
				'SHORT_DESC' => 'Law School',
				'FULL_DESC' => 'Visiting Committee to the Law School'),
			array('COMMITTEE_CODE' => 'VVLB',
				'SHORT_DESC' => 'Library',
				'FULL_DESC' => 'Visiting Committee to the Library'),
			array('COMMITTEE_CODE' => 'VVMS',
				'SHORT_DESC' => 'Music',
				'FULL_DESC' => 'Visiting Committee to the Department of Music'),
			array('COMMITTEE_CODE' => 'VVOI',
				'SHORT_DESC' => 'Oriental Institute',
				'FULL_DESC' => 'Visiting Committee to the Oriental Institute'),
			array('COMMITTEE_CODE' => 'VVPS',
				'SHORT_DESC' => 'Physical Sciences',
				'FULL_DESC' => 'Visiting Committee to the Division of the Physical Sciences'),
			array('COMMITTEE_CODE' => 'VCLD',
				'SHORT_DESC' => 'Public Policy',
				'FULL_DESC' => 'Visiting Committee to the Irving B. Harris Graduate School of Public Policy Studies'),
			array('COMMITTEE_CODE' => 'VVSS',
				'SHORT_DESC' => 'Social Sciences',
				'FULL_DESC' => 'Visiting Committee to the Division of the Social Sciences'),
			array('COMMITTEE_CODE' => 'VSVC',
				'SHORT_DESC' => 'Social Service Administration',
				'FULL_DESC' => 'Visiting Committee to the School of Social Service Administration')
		);
		$arr = array();	
		if( !apc_exists('active_committees') )
		{
			foreach ( $committees as $c )
			{
				$tmp = new Committee($c);				
				$arr[$c['COMMITTEE_CODE']] = $tmp;
			}
			apc_add('active_committees', $arr , 172800);
		}
		$this->setActiveCommitteeUrlList();
	}
	
	public function setActiveCommitteeUrlList()
	{
		$list = array();		
		if( apc_exists('active_committees') && !apc_exists('active_committee_url_list'))
		{
			$active_committees = apc_fetch('active_committees');
			foreach ( $active_committees as $c )
			{
				if( is_a($c, 'Committee'))
				{
					$list[] = $c->getCOMMITTEE_CODE();
				}								
			}
			apc_add('active_committee_url_list' , implode(",", $list) , 172800);
		}
	}
	
	public function loadCommitteeTemplateData( $template )
	{	
		foreach( apc_fetch('active_committees') as $c )
		{	
			if( is_a($c,'Committee') )
			{			
				$code = $c->getCOMMITTEE_CODE();	
				$c->addClassDataTemplate( $template , "Committee.$code.");
			}					
		}
	}
	
	public function getCommittees()
	{
		return apc_fetch('active_committees');
	}
	
	public static function getCommittee($code)
	{	
		$desc = "";
		if( apc_exists('active_committees') )
		{
			$committees = apc_fetch('active_committees');
			if( is_a($committees[$code], 'Committee'))
			{
				$desc = $committees[$code]->getFULL_DESC();
			}	
		}
		return $desc;
	}
		
	public function getLoginUrl()
	{
		if( $this->app->isDev() || $this->app->isStage() )
		{
			return 'https://grif-uat-soa.uchicago.edu/api/auth/login';
		}
		elseif( $this->app->isProd() )
		{
			return 'https://grif-uat-soa.uchicago.edu/api/auth/login';
		}
	}
	
	public function getServiceUrl( $key=null , $value = null)
	{
		if( !is_null($key) && !is_null($this->urls[$key]) )
		{
			if( $key == 'email_validation' && apc_exists('active_committee_url_list') )
			{
				return sprintf($this->urls[$key], apc_fetch('active_committee_url_list') , $value);
			}
			else
			{
				return sprintf($this->urls[$key],  $value);
			}
			
		}
		else
		{
			return null;
		}		
	}
	
	public function getAddressInfo( $id_number = null , $token = null)
	{
		if( !is_null($token) && !is_null($id_number) )
		{
			/*
			 * Suppress xml warnings. 
			 */
			libxml_use_internal_errors(true);
			$this->curl->setPost($token);
			$url = sprintf( $this->urls['address_info'] , $id_number );
			$this->curl->createCurl( $url );
			return $this->curl->asSimpleXML();				
		}
		
	}
	
	public function getDegreeInfo( $id_number = null , $token = null)
	{
		if( !is_null($token) && !is_null($id_number) )
		{
			/*
			 * Suppress xml warnings. 
			 */
			libxml_use_internal_errors(true);
			$this->curl->setPost($token);
			$url = sprintf( $this->urls['degree_info'] , $id_number );
			$this->curl->createCurl( $url );
			return $this->curl->asSimpleXML();	
		}
		
	}
	
	public function getEmplymentInfo( $id_number = null , $token = null )
	{
		if( !is_null($token) && !is_null($id_number) )
		{
			/*
			 * Suppress xml warnings. 
			 */
			libxml_use_internal_errors(true);
			$this->curl->setPost($token);
			$url = sprintf( $this->urls['entity_info'] , $id_number );
			$this->curl->createCurl( $url );
			return $this->curl->asSimpleXML();			
		}
	}
	
	public function getMemberData( $code=null , $token=null )
	{
		$info = array('address_info' , 'degree_info' , 'entity_info');	
		$list = $this->all_member_data->xpath('//COMMITTEES/COMMITTEE/ID_NUMBER[../COMMITTEE_CODE/text()="'.$code.'"]');
		$members = array();
		$data = array();
		if( !is_null($list) && !is_null($token) )
		{
			foreach ( $info as $key )
			{
				/*
				 * Suppress xml warnings.
				 */
				libxml_use_internal_errors(true);
				$this->curl->setPost($token);
				$this->curl->createCurlMultiple( $this->urls[$key] , $list );
				$data[$key] = $this->curl->getNodes();					
			}			
			foreach ( $info as $key )
			{
				foreach ( $data[$key] as $obj )
				{			
					$obj = simplexml_load_string( curl_multi_getcontent($obj) );
					if( $key == 'entity_info' && is_a($obj, 'SimpleXMLElement') )
					{	
						$total = $obj->xpath('//EMPLOYMENT/JOB[@JOB_STATUS_CODE="C"]');
						$employment = (count($total) > 1) ? $obj->xpath('//EMPLOYMENT/JOB[@JOB_STATUS_CODE="C" and not(@START_DT <= preceding-sibling::JOB/@START_DT) and not(@START_DT <= following-sibling::JOB/@START_DT)]')
														  : $total;
						if( $total > 0 && !empty($employment))
						{							
							$employment[0]->addChild('JOB' , (string)$employment[0] );
							$attributes = $employment[0]->attributes();
							$employer_id = trim($attributes['EMPLOYER_ID_NUMBER']);
							$employer_name = trim($attributes['EMPLOYER_NAME1']);
							if( !empty($employer_id) && empty($employer_name) )
							{
								$employment = $this->getEmployerDataByID($employment, $employer_id, $token);
							}
							else
							{
								$employment[0]->addChild('EMPLOYER' , $employer_name );
							}
						}																							
						$members['employment_info'][(string)$obj->ENTITY->ID_NUMBER] = $employment;				
					}
					elseif( is_a($obj, 'SimpleXMLElement') )
					{
						$members[$key][(string)$obj->ENTITY->ID_NUMBER] = $obj;
					}																		
				}
			}			
			$this->curl->clear();
		}		
		return $members;
	}
	
	public function getMembersAndCommittees( $xml , $token )
	{
		$list = array();
		$committee_codes = array();
		$members  = array();
		foreach ( $xml as $m)
		{
			if( is_a($m, 'SimpleXMLElement'))
			{
				$list[] = $m->ID_NUMBER;
			}
		}
		libxml_use_internal_errors(true);
		$this->curl->setPost($token);
		$this->curl->createCurlMultiple( $this->urls['all_affiliations'] , $list );
		$committee_list = $this->curl->getNodes();
		$arr = array();
		$obj = null;
		foreach ( $committee_list as $c )
		{
			$obj = simplexml_load_string( curl_multi_getcontent($c) );
			if( is_a($obj, 'SimpleXMLElement'))
			{
				$arr[(string)$obj->ID_NUMBER] = $obj->xpath('//COMMITTEE[COMMITTEE_STATUS_CODE = "A" and contains(COMMITTEE_SRC_CODE, "VSC")]');				
			}											
		}
		foreach ( $xml as $m )
		{
			$cm = new CommitteeMember();
			$cm->setIdNumber( (string)$m->ID_NUMBER );
			$cm->setFirstName( (string)$m->FIRST_NAME );
			$cm->setLastName( (string)$m->LAST_NAME  );	
			$cm->setCommittees( $arr , apc_fetch('active_committees'));
			$members[] = $cm;
		}
		return $members;
	}
	
	public function getOneMemberData( $id_number , $token )
	{
	if( !is_null($token) && !is_null($id_number) )
		{
			/*
			 * Suppress xml warnings. 
			 */			
			libxml_use_internal_errors(true);
			$member = array();
			$this->curl->setPost($token);
			$url = sprintf( $this->urls['entity_info'] , $id_number );
			$this->curl->createCurl( $url );					
			$member['entity_info'] = $this->curl->asSimpleXML();			
			$member['address_info'] = $this->getAddressInfo( $id_number , $token );
			$member['degree_info'] = $this->getDegreeInfo( $id_number , $token );
			if( is_a($member['entity_info'], 'SimpleXMLElement') )
			{
				$total = $member['entity_info']->xpath('//EMPLOYMENT/JOB[@JOB_STATUS_CODE="C"]');
				$member['employment_info'] = (count($total) > 1) 
												? $member['entity_info']->xpath('//EMPLOYMENT/JOB[@JOB_STATUS_CODE="C" and not(@START_DT <= preceding-sibling::JOB/@START_DT) and not(@START_DT <= following-sibling::JOB/@START_DT)]')
												: $total;				
				if( $total > 0 && !empty($member['employment_info']))
				{
					$attributes = $member['employment_info'][0]->attributes();							
					$member['employment_info'][0]->addChild('JOB' , (string)$member['employment_info'][0] );
					$attributes =$member['employment_info'][0]->attributes();
					$employer_id = trim($attributes['EMPLOYER_ID_NUMBER']);
					$employer_name = trim($attributes['EMPLOYER_NAME1']);
					if( !empty($employer_id) && empty($employer_name) )
					{
						$member['employment_info'] = $this->getEmployerDataByID($member['employment_info'], $employer_id, $token);			
					}
				}																		
			}				
			$url = sprintf(  $this->urls['all_affiliations'] , $id_number );
			$this->curl->createCurl( $url );
			$xml = $this->curl->asSimpleXML();			
			$member['committee_info'] = $xml->xpath('//COMMITTEE[COMMITTEE_STATUS_CODE = "A" and contains(COMMITTEE_SRC_CODE, "VSC")]');					
			return $member;
		}
	}
	
	public function getEmployerDataByID( $member , $employer_id , $token )
	{
		if( !is_null($token) && !is_null($employer_id) )
		{
			/*
			 * Suppress xml warnings. 
			 */				
			libxml_use_internal_errors(true);
			$this->curl->setPost($token);
			$url = sprintf( $this->urls['entity_info'] , $employer_id );
			$this->curl->createCurl( $url );					
			$xml = $this->curl->asSimpleXML();
			$employer_element = $xml->xpath('//ENTITY/NAMES/NAME[@NAME_TYPE_CODE="00"]');
			$member[0]->addChild('EMPLOYER' , (string)$employer_element[0]->REPORT_NAME );		
		}
		return $member;
	}
	
	private function setCache($token)
	{
		if( !apc_exists('active_committees') )
		{
			$this->setCommittees();
		}
		if( !apc_exists('all_member_data') )
		{			
			$this->setAllMemberData($token);
		}
	}
	
	public function clearCollection()
	{
		apc_delete('active_committees');
		apc_delete('all_member_data');
		apc_delete('active_committee_url_list');
	}
}
?>