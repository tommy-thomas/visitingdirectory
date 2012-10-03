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
	 * Authentication token array passed by curl.
	 */
	private $token;
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
		$this->token = $token;
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
			'active_committees' => 'https://soa.griffin.uchicago.edu/api/griffin/metadata/committee_code',
			'address_info' => 'https://soa.griffin.uchicago.edu/api/griffin/entities/%s/addresses',
			'affiliations' => 'https://soa.griffin.uchicago.edu/api/griffin/membershipaffiliation/%s',
			'all_affiliations' => 'https://soa.griffin.uchicago.edu/api/griffin/entities/%s/membershipaffiliation',
			'all_members' => 'https://soa.griffin.uchicago.edu/api/griffin/membershipaffiliation/%s',							
			'degree_info' => 'https://soa.griffin.uchicago.edu/api/griffin/entities/%s/degrees',		
			'entity_info' => 'https://soa.griffin.uchicago.edu/api/griffin/entities/%s',	
			'email_validation' => 'https://soa.griffin.uchicago.edu/api/griffin/membershipaffiliation/%s?emailaddress=%s'			
			);	
		}
		if( !is_null($token) )
		{
			$this->checkCache($token);
		}		
		if( apc_exists('vc_all_member_data') )
		{
			$this->all_member_data = simplexml_load_string( apc_fetch('vc_all_member_data') );
		}
		else
		{
			if( !is_null($token))
			{
				$this->setCommittees($token);
				$this->all_member_data = simplexml_load_string( apc_fetch('vc_all_member_data') );
			}
			
		}
		self::$collection = $this;
	}
	/**
	 * Collection instance.
	 * @param $app
	 * @param $curl
	 * @param $token
	 */
	public static function instance($app,$curl=null,$token=null)
	{
		if(!self::$collection)
		{
			self::$collection = new Collection($app,$curl,$token);
		}
		return self::$collection;
	}
	/**
	 * Set all of the member data from the big Griffin api payload the includes 
	 * fist name, last name, middle, committee tittle and id number. 
	 * @param $token
	 */
	public function setAllMemberData($token)
	{	
		libxml_use_internal_errors(true);
		$this->curl->setPost($token);		
		$this->curl->createCurl( sprintf($this->urls['all_members'], apc_fetch('vc_active_committee_code_list') ));
		if( !apc_exists('vc_all_member_data') )
		{
			apc_add('vc_all_member_data', $this->curl->__toString() , 172800);
		}
	}
	/**
	 * Cache list of active committees as array of Committee objects.
	 */
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
		if( !apc_exists('vc_active_committees') )
		{
			foreach ( $committees as $c )
			{
				$tmp = new Committee($c);				
				$arr[$c['COMMITTEE_CODE']] = $tmp;
			}
			apc_add('vc_active_committees', $arr , 172800);
		}
		$this->setActiveCommitteeUrlList();
	}
	/**
	 * Create and cache comma seperated list of active committee codes needed to pass in some of the api urls.
	 */
	public function setActiveCommitteeUrlList()
	{
		$list = array();		
		if( apc_exists('vc_active_committees') && !apc_exists('vc_active_committee_code_list'))
		{
			$active_committees = apc_fetch('vc_active_committees');
			foreach ( $active_committees as $c )
			{
				if( is_a($c, 'Committee'))
				{
					$list[] = $c->getCOMMITTEE_CODE();
				}								
			}
			apc_add('vc_active_committee_code_list' , implode(",", $list) , 172800);
		}
	}
	/**
	 * Return comma seperated active code list.
	 */
	public function getActiveCommitteeUrlList()
	{
		if( apc_exists('vc_active_committee_code_list') )
		{
			return apc_fetch('vc_active_committee_code_list');
		}
	}
	/**
	 * Load a template with active committees.
	 * @param $template
	 */
	public function loadCommitteeTemplateData( $template )
	{	
		$this->checkCache();
		foreach( apc_fetch('vc_active_committees') as $c )
		{	
			if( is_a($c,'Committee') )
			{			
				$code = $c->getCOMMITTEE_CODE();	
				$c->addClassDataTemplate( $template , "Committee.$code.");
			}					
		}
	}
	/**
	 * Return the array of Committee objects.
	 */
	public function getCommittees()
	{		
		$this->checkCache();
		return apc_fetch('vc_active_committees');		
	}
	/**
	 * Return committee code key from vc_active_committees cache.
	 */
	public static function getCommitteeName($code)
	{
		$desc = "";
		if( apc_exists('vc_active_committees') )
		{
			$committees = apc_fetch('vc_active_committees');
			if( is_a($committees[$code], 'Committee'))
			{
				$desc = $committees[$code]->getFULL_DESC();
			}	
		}
		return $desc;
	}
	/**
	 * Return griffin login url.
	 */
	public function getLoginUrl()
	{
		if( $this->app->isDev() || $this->app->isStage() )
		{
			return 'https://grif-uat-soa.uchicago.edu/api/auth/login';
		}
		elseif( $this->app->isProd() )
		{
			return 'https://soa.griffin.uchicago.edu/api/auth/login';
		}
	}
	/**
	 * Return a member api url.
	 * @param $key
	 * @param $value
	 */
	public function getServiceUrl( $key=null , $value = null)
	{
		if( !is_null($key) && !is_null($this->urls[$key]) )
		{
			if( $key == 'email_validation' && apc_exists('vc_active_committee_code_list') )
			{
				return sprintf($this->urls[$key], apc_fetch('vc_active_committee_code_list') , $value);
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
	/**
	 * Return simple xml obj of committee members info.
	 * @param $id_number
	 * @param $token
	 */
	public function getInfo( $id_number = null , $token = null , $key=null )
	{
		if( !is_null($token) && !is_null($id_number) && !is_null($key))
		{
			/*
			 * Suppress xml warnings. 
			 */
			libxml_use_internal_errors(true);
			$this->curl->setPost($token);
			$which = $key.'_info';
			$url = sprintf( $this->urls[$which] , $id_number );
			$this->curl->createCurl( $url );
			return $this->curl->asSimpleXML();			
		}
	}
	/**
	 * Get all members as simple xml obj array based on commitee code loaded with address, degree, and employment info.
	 * @param $code
	 * @param $token
	 */
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
	/**
	 * Get list of members and committee affiliations.
	 * @param $xml
	 * @param $token
	 */
	public function getMembersAndCommittees( $xml , $token )
	{
		$this->checkCache();
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
			$cm->setIdNumber( htmlClean((string)$m->ID_NUMBER) );
			$cm->setFirstName( htmlClean((string)$m->FIRST_NAME) );
			$cm->setLastName( htmlClean((string)$m->LAST_NAME)  );	
			$cm->setCommittees( $arr , apc_fetch('vc_active_committees'));
			$members[(string)$m->ID_NUMBER] = $cm;
		}
		return $members;
	}
	/**
	 * Return single member info as simple xml object. 
	 * @param $id_number
	 * @param $token
	 */
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
			$member['address_info'] = $this->getInfo( $id_number , $token , 'address');
			$member['degree_info'] = $this->getInfo( $id_number , $token , 'degree');
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
	/**
	 * Get employer information based on employee id.
	 * @param $member
	 * @param $employer_id
	 * @param $token
	 */
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
	/**
	 * Check that vc_active_committees is cached , if not set and cache it.
	 */
	public function checkCache()
	{
		if( !apc_exists('vc_active_committees') )
		{
			$this->setCommittees();			
		}
		if( isset($this->token) )
		{
			$this->setAllMemberData($this->token);
		}
	}
	/**
	 * Delete the cached items.
	 */
	public function clearCollection()
	{
		apc_delete('vc_active_committees');
		apc_delete('vc_all_member_data');
		apc_delete('vc_active_committee_code_list');
	}
}
?>