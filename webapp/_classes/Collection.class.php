<?php
class Collection
{
	static private $collection;
	
	private $app;
	
	private $curl;
	
	private $xml;
	
	private $urls = array();
	
	private $xmlreader;
	
	private $affiliations_array = array();
	
	private $active_committees;
	
	public function __construct( Application $app , cURL $curl = null )
	{	
		$this->app = $app;
		$this->curl = $curl;
		$this->xmlreader = new XMLReader();
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
		$this->curl->createCurl( sprintf($this->urls['all_members'], $_SESSION['active_committee_url_list']) );
		$_SESSION['all_member_data'] = $this->curl->asSimpleXML();		
	}
	
	public function setCommittees( $token )
	{
//		libxml_use_internal_errors(true);
//		$this->curl->setPost($token);		
//		$this->curl->createCurl( $this->urls['active_committees'] );
//		$result = $this->curl->asSimpleXML();
//		$list = $result->xpath('//ROW[STATUS_CODE = "A" and contains(SHORT_DESC, "VSC")]');
//		$_SESSION['active_committees'] = array();
//		$arr = array();
//		foreach ( $list as $xml )
//		{
//			$c = new Committee($xml);
//			$arr[] = $c;			
//		}
//		$_SESSION['active_committees'] = $arr;
//		protected $COMMITTEE_CODE;
//		protected $SHORT_DESC;
//		protected $FULL_DESC;
		$committees = array(
			array('COMMITTEE_CODE' => 'VCGS',
				'SHORT_DESC' => 'Council on the Graham School'),					
			array('COMMITTEE_CODE' => 'VCLD',
				'SHORT_DESC' => 'Pub Pol Stds Visit Committee'),			
			array('COMMITTEE_CODE' => 'VCLY',
				'SHORT_DESC' => 'Council on Chicago Booth'),				
			array('COMMITTEE_CODE' => 'VCLZ',
				'SHORT_DESC' => 'Visiting Committee for UCMC'),				
			array('COMMITTEE_CODE' => 'VCPC',
				'SHORT_DESC' => 'Visiting Committee for the Paris Center'),			
			array('COMMITTEE_CODE' => 'VCSA',
				 'SHORT_DESC' => 'Coll and Student Act Vis Committee'),			
			array('COMMITTEE_CODE' => 'VSVC',
				'SHORT_DESC' => 'SSA Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVCL',
				'SHORT_DESC' => 'College Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVHM',
				'SHORT_DESC' => 'HUM Div Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVIP',
				'SHORT_DESC' => 'Stud Prgr/Facs Visit Committee'),			
			array('COMMITTEE_CODE' => 'VVLB',
				'SHORT_DESC' => 'Library Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVLW',
				'SHORT_DESC' => 'Law School Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVME',
				'SHORT_DESC' => 'Far East Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVMS',
				'SHORT_DESC' => 'Music Dept Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVOI',
				'SHORT_DESC' => 'Oriental Inst Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVPS',
				'SHORT_DESC' => 'Phys Scis Div Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVRT',
				'SHORT_DESC' => 'Art History Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVSS',
				'SHORT_DESC' => 'Soc Scis Div Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVTH',
				'SHORT_DESC' => 'DIV Visiting Committee'),			
			array('COMMITTEE_CODE' => 'VVUR',
			 	'SHORT_DESC' => 'Univ-School Relations Visit Committee')
		);
		$_SESSION['active_committees'] = array();
		$root = "<root></root>";
		$xml = new SimpleXMLElement($root);
		foreach ( $committees as $arr )
		{
			$committee = $xml->addChild('COMMITTEE');
			$committee->addChild('COMMITTEE_CODE',$arr['COMMITTEE_CODE'] );
			$committee->addChild('SHORT_DESC',$arr['SHORT_DESC']);
		}
		foreach ( $xml as $com )
		{			
			$c = new Committee($com);
			if( is_a($c, 'Committee'))
			{
				$arr[] = $c;				
			}			
		}
		$_SESSION['active_committees'] = $arr;
		$this->setActiveCommitteeUrlList();
	}
	
	public function setActiveCommitteeUrlList()
	{
		$list = array();		
		if( isset($_SESSION['active_committees']) )
		{
			foreach ( $_SESSION['active_committees'] as $c )
			{
				if( is_a($c, 'Committee'))
				{
					$list[] = $c->getCOMMITTEE_CODE();
				}								
			}
		}
		$_SESSION['active_committee_url_list'] = implode(",", $list);		
	}
	
	public function loadCommitteeTemplateData( $template )
	{		
		foreach( $_SESSION['active_committees'] as $c )
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
		return $_SESSION['active_committees'];
	}
	
	public static function getCommittee($code)
	{
	    foreach ($_SESSION['active_committees'] as $c)
	    {  
	        if( is_a($c, 'Committee') && $c->getCOMMITTEE_CODE() == $code )
	        {
	        	return $c->getSHORT_DESC();
	        } 
	    } 	
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
			if( $key == 'email_validation' && isset($_SESSION['active_committee_url_list']) )
			{
				return sprintf($this->urls[$key], $_SESSION['active_committee_url_list'] , $value);
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
		$list = $_SESSION['all_member_data']->xpath('//COMMITTEES/COMMITTEE/ID_NUMBER[../COMMITTEE_CODE/text()="'.$code.'"]');
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
						$employment = (count($total) > 1) ? $obj->xpath('//EMPLOYMENT/JOB[@JOB_STATUS_CODE="C" and not(@START_DT <= preceding-sibling::JOB/@START_DT) 
									and not(@START_DT <= following-sibling::JOB/@START_DT)]') : $total;
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
			$cm->setCommittees( $arr , $_SESSION['active_committees']);
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
				$member['employment_info'] = (count($total) > 1) ? $member['entity_info']->xpath('//EMPLOYMENT/JOB[@JOB_STATUS_CODE="C" and not(@START_DT <= preceding-sibling::JOB/@START_DT) 
									and not(@START_DT <= following-sibling::JOB/@START_DT)]') : $total;				
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
	
}
?>