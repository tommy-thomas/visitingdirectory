<?php
/**
 * 
 * A class used as a catch all for several cURL queries, urls to Griffin api, and getters/setters for Memcached payloads.
 * @author tommyt
 *
 */
class GriffinCollection
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
	 * Container for raw xml or simple exml.
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
	/*
	 * Memcache object
	 */
	private $memcache;
	/**
     * Public constructor.
	 */
	public function __construct( Application $app , cURL $curl = null  , $token = null)
	{	
		$this->app = $app;
		$this->curl = $curl;
		$this->token = $token;
		$cache = new WS_Memcache();
		$this->memcache = $cache->getMemcache();
		if( $this->app->isDev() || $this->app->isStage() )
		{
			$this->urls = array(
			'active_committees' => 'https://grif-uat-soa.uchicago.edu/api/griffin/metadata/committee_code',
			'address_info' => 'https://grif-uat-soa.uchicago.edu/api/griffin/entities/%s/addresses',
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
			if( !$this->memcache->get('VisDirectoryActiveCommittees'))
			{				
				$this->setCommittees($token);
			}
			if( !$this->memcache->get('VisCommitteeAllMemberData'))
			{
				$this->setAllMemberData($token);
				$this->all_member_data = simplexml_load_string( $this->memcache->get('VisCommitteeAllMemberData') );
			}
		}
		if( $this->memcache->get('VisCommitteeAllMemberData') )
		{			
			$this->all_member_data = simplexml_load_string( $this->memcache->get('VisCommitteeAllMemberData') );
		}
	}
	/**
	 * GriffinCollection instance.
	 * @param $app
	 * @param $curl
	 * @param $token
	 */
	public static function instance($app,$curl=null,$token=null)
	{
		if(!self::$collection)
		{
			self::$collection = new GriffinCollection($app,$curl,$token);
		}
		return self::$collection;
	}
	/**
	 * Set all of the member data from the big Griffin api payload that includes 
	 * fist name, last name, middle, committee tittle and id number. 
	 * @param $token
	 */
	public function setAllMemberData($token)
	{	
		libxml_use_internal_errors(true);
		if( !$this->memcache->get('VisCommitteeAllMemberData') )
		{
			$this->curl->setPost($token);
			$this->curl->createCurl( sprintf($this->urls['all_members'], $this->memcache->get('VisDirectoryActiveCommitteeCodes') ));			
			$this->memcache->set('VisCommitteeAllMemberData' , $this->curl->__toString() , 0, 86400 );
			if( !is_a(simplexml_load_string( $this->memcache->get('VisCommitteeAllMemberData') ),'SimpleXMLElement') )
			{	
				$this->app->redirect('./data_error.php');
			}
		}
	}
	/**
	 * Cache list of active committees as array of Committee objects.
	 */
	public function setCommittees()
	{
		$committees = array(
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
			foreach ( $committees as $c )
			{
				$tmp = new Committee($c);				
				$arr[$c['COMMITTEE_CODE']] = $tmp;
			}
			$this->memcache->set('VisDirectoryActiveCommittees' , $arr, 0 , 86400 );
		
		$this->setActiveCommitteeUrlList();
	}
	/**
	 * Create and cache comma seperated list of active committee codes needed to pass in some of the api urls.
	 */
	public function setActiveCommitteeUrlList()
	{
		$list = array();
		if( $this->memcache->get('VisDirectoryActiveCommittees') 
		&& !$this->memcache->get('VisDirectoryActiveCommitteeCodes'))
		{
			$active_committees = $this->memcache->get('VisDirectoryActiveCommittees');
			foreach ( $active_committees as $c )
			{
				if( is_a($c, 'Committee'))
				{
					$list[] = $c->getCOMMITTEE_CODE();
				}								
			}
			$this->memcache->set('VisDirectoryActiveCommitteeCodes', implode(",", $list) , 0 , 86400);
		}
		
	}
	/**
	 * Return comma seperated active code list.
	 */
	public function getActiveCommitteeUrlList()
	{
		if( $this->memcache->get('VisDirectoryActiveCommitteeCodes') )
		{
			return $this->memcache->get('VisDirectoryActiveCommitteeCodes');
		}
	}
	/**
	 * Load a template with active committees.
	 * @param $template
	 */
	public function loadCommitteeTemplateData( $template )
	{	
		$this->checkCache();
		foreach( $this->memcache->get('VisDirectoryActiveCommittees') as $c )
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
		return $this->memcache->get('VisDirectoryActiveCommittees');		
	}
	/**
	 * Get cached array of CommitteeMembers if set
	 * @param Committee $code
	 */
	public function getCachedMemberList( $code=null )
	{
		if( !is_null($code) )
		{
			$key = "VisDirectory_".$code."_List";
			return ($this->memcache->get($key)) ?  $this->memcache->get($key) : null; 
		}
	}
	/**
	 * Set cached list of CommitteeMembers if set
	 * @param array of CommitteeMembers
	 */
	public function setCachedMemberList($code=null , $member_list=null )
	{
		$key = "VisDirectory_".$code."_List";
		if( !is_null($code) && !is_null($member_list) 
		&&  !$this->memcache->get($key) )
		{
			$this->memcache->set($key , $member_list , 0, 86400);
		}
	}	
	/**
	 * Return committee code key from vc_active_committees cache.
	 */
	public function getCommitteeName($code)
	{
		$desc = "";
		if( $this->memcache->get('VisDirectoryActiveCommittees') )
		{
			$committees = $this->memcache->get('VisDirectoryActiveCommittees');
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
			if( $key == 'email_validation' && $this->memcache->get('VisDirectoryActiveCommitteeCodes') )
			{
				return sprintf($this->urls[$key], $this->memcache->get('VisDirectoryActiveCommitteeCodes') , $value);
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
	public function getMemberData( $code=null , $token=null , $async = false )
	{
		if( !isset($this->all_member_data) )
		{	
			sleep(5);
			$this->all_member_data = simplexml_load_string( $this->memcache->get('VisCommitteeAllMemberData') );
			// If still no data, there's a problem with the service, go to data error page.
		}
		if( !is_a($this->all_member_data,'SimpleXMLElement') )
		{	
			$this->app->redirect('./data_error.php');
		}
		$info = array('address_info' , 'degree_info' , 'entity_info');
		$list = $this->all_member_data->xpath('//COMMITTEES/COMMITTEE/ID_NUMBER[../COMMITTEE_CODE/text()="'.$code.'" and ../RECORD_STATUS_CODE="A" and ../COMMITTEE_ROLE_CODE != "EO"]');
		$members = array();
		$data = array();
		if( !is_null($list) && !is_null($token) )
		{
			$token = $async ? array( 'authtoken'=>$token ) : $token;
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
						$employment = $obj->xpath('//EMPLOYMENT/JOB[@PRIMARY_EMP_IND="Y"]');
						if( isset($employment[0]))
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
		/*
		 * array holding list of id numbers for members, passed in curl multiple call
		 */
		$list = array();
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
				$arr[(string)$obj->ENTITY->ID_NUMBER] = $obj->xpath('//COMMITTEE[COMMITTEE_STATUS_CODE = "A" and RECORD_STATUS_CODE = "A"]');				
			}
		}
		foreach ( $xml as $m )
		{
			if( $this->isValidMember($m))
			{
				$cm = new CommitteeMember();
				$cm->setIdNumber( (string)$m->ID_NUMBER );
				$cm->setFirstName( (string)$m->FIRST_NAME );
				$cm->setLastName( (string)$m->LAST_NAME  );	
				$cm->setCommittees( $arr , $this->memcache->get('VisDirectoryActiveCommittees'));
				$members[(string)$m->ID_NUMBER] = $cm;
			}
			
		}
		return $members;
	}
	/**
	 * Filter out non-active ex oficio members.
	 */
	public function isValidMember( $member = null )
	{
		if( !is_null($member) && is_a($member,'SimpleXMLElement') )
		{
			return ( $member->COMMITTEE_STATUS_CODE == "A" && $member->RECORD_STATUS_CODE == "A" && $member->COMMITTEE_ROLE_CODE != "EO");
		}
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
				$member['employment_info']= $member['entity_info']->xpath('//EMPLOYMENT/JOB[@PRIMARY_EMP_IND="Y"]');	
				if( !empty($member['employment_info']))
				{
					$attributes = $member['employment_info'][0]->attributes();			
					$member['employment_info'][0]->addChild('JOB' , $this->xmlEscape($member['employment_info'][0]) );
					$employer_id = trim($attributes['EMPLOYER_ID_NUMBER']);
					$employer_name = trim($attributes['EMPLOYER_NAME1']);
					if( !empty($employer_id) && empty($employer_name) )
					{	
						$member['employment_info'] = $this->getEmployerDataByID($member['employment_info'], $employer_id, $token);
					}
					else
					{
						$member['employment_info'][0]->addChild('EMPLOYER' , $this->xmlEscape( $employer_name ) );
					}
				}																		
			}				
			$url = sprintf(  $this->urls['all_affiliations'] , $id_number );
			$this->curl->createCurl( $url );
			$xml = $this->curl->asSimpleXML();
			$member['committee_info'] = $xml->xpath('//COMMITTEE[COMMITTEE_STATUS_CODE = "A" and RECORD_STATUS_CODE = "A" and COMMITTEE_ROLE_CODE != "EO"]');
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
			if( isset($member[0]) )
			{
				libxml_use_internal_errors(true);
				$this->curl->setPost($token);
				$url = sprintf( $this->urls['entity_info'] , $employer_id );
				$this->curl->createCurl( $url );
				$xml = $this->curl->asSimpleXML();
				$employer_element = $xml->xpath('//ENTITY/NAMES/NAME[@NAME_TYPE_CODE="00"]');				
				if( isset($employer_element[0]) )
				{
					$member[0]->EMPLOYER = (string)$employer_element[0]->REPORT_NAME;
				}
			}					
		}
		return $member;
	}
	/**
	 * Escape reserved characters.
	 */
	public function xmlEscape($string)
	{
    	return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), (string)$string);
	}
	/**
	 * Check existence of simple xml child node.
	 * @param $xml
	 * @param $childpath
	 */
	public function xmlChildExists( SimpleXMLElement $xml , $childpath )
	{
		$result = $xml->xpath($childpath);
		return (bool)(count($result));
	}
	/**
	 * Check that vc_active_committees is cached , if not set and cache it.
	 */
	public function checkCache()
	{
		if( !$this->memcache->get('VisDirectoryActiveCommittees') )
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
	public function clearGriffinCollection()
	{	
		$this->memcache->delete('VisDirectoryActiveCommittees');
		$this->memcache->delete('VisCommitteeAllMemberData');
		$codes = array();
		$codes = explode("," , $this->getActiveCommitteeUrlList());
		if( !empty($codes) )
		{			
			foreach( $codes as $c )
			{
				$key = "VisDirectory_".$c."_List";
				$this->memcache->delete($key);
			}
		}
		$this->memcache->delete('VisDirectoryActiveCommitteeCodes');
	}
	/**
	 * Return curl HTTP status code.
	 */
	public function getHTTPStatus()
	{
		return $this->curl->getStatus();
	}
}
?>