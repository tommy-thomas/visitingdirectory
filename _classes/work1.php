<?php

$nodes = array();
// curl multiple handle
$master_handle = curl_multi_init();

$active = null;
foreach ( $id_numbers as $id )
{		
	$tmp_handle = $curl->getHandle( sprintf( $urls['entity_info'] , $id ) );
	$nodes[] = $tmp_handle;
	curl_multi_add_handle($master_handle, $tmp_handle );
	// print $id.'<br />';
}
	
do {
	$mrc = curl_multi_exec($master_handle, $active);
} while( $mrc == CURLM_CALL_MULTI_PERFORM );

while( $active && $mrc == CURLM_OK )
{
	if( curl_multi_select($master_handle) != -1 )
	{
		do
		{
			$mrc = curl_multi_exec( $master_handle, $active );				
		}while( $mrc == CURLM_CALL_MULTI_PERFORM );
	}
}	


//////////////////////////////////////////////////////////////////////



try {
	$curl = new cURL(null);
} catch (Exception $e) {
	throw new Exception( $e->getMessage() );
}

/*
* Add memcache
*/
$Memcached = new WS_Memcache;
$memcache = $Memcached->getMemcache();
$memcache->delete('VisitingCommiteeMemberIDs');

if( isset( $_POST["login"] ) )
{

	$opts = array(
		'username' => $_POST['username'],
		'password' => $_POST['password']
	);
	
	$curl->setPost($opts);	
	$curl->createCurl('https://grif-uat-soa.uchicago.edu/api/auth/login');
	$t = localtime(time(),true);	
	$report .= "<h1>Report</h1>Start time after getting token: ".$t['tm_hour'].":".$t['tm_min'].":".$t['tm_sec'].'<br />';	
	//print $curl->getHttpStatus().'<br />';
	$tkn = $curl->__toString();
	$token = array(
		'authtoken' => $tkn
	);
 
	$memcache->set('VisitingCommiteeToken',$tkn,0,1800);
	
	$COMMITTEE_IDS = array();
	
	foreach( $committees as $key => $value )
	{		
		$curl->setPost($token);
		$url = sprintf( $urls['affiliations'] , $key );
		$curl->createCurl( $url );
		$xml = $curl->__toString();
		$COMMITTEE_IDS[$key] = $xml;
		$obj = simplexml_load_string($xml);
		$id_numbers = $obj->xpath('//COMMITTEE/ID_NUMBER');
		/*if( !empty($id_numbers) )
		{
			// $COMMITTEE_IDS[$key] = $id_numbers->asXML();
			var_dump( $id_numbers );
		}*/
		
		/***********************************************************/
		/***********************************************************/
		/***********************************************************/
		/***********************************************************/
		/***********************************************************/
		/***********************start*******************************/
//		$nodes = array();
//		// curl multiple handle
//		$master_handle = curl_multi_init();		
//		$active = null;
//		
//		foreach ( $id_numbers as $id )
//		{		
//			$tmp_handle = $curl->getHandle( sprintf( $urls['entity_info'] , $id ) );
//			$nodes[] = $tmp_handle;
//			curl_multi_add_handle($master_handle, $tmp_handle );
//			// print $id.'<br />';
//		}
//			
//		do {
//			$mrc = curl_multi_exec($master_handle, $active);
//		} while( $mrc == CURLM_CALL_MULTI_PERFORM );
//		
//		while( $active && $mrc == CURLM_OK )
//		{
//			if( curl_multi_select($master_handle) != -1 )
//			{
//				do
//				{
//					$mrc = curl_multi_exec( $master_handle, $active );				
//				}while( $mrc == CURLM_CALL_MULTI_PERFORM );
//			}
//		}
//		
//		if(count($nodes) > 0)
//		{
//			foreach( $nodes as $n )
//			{
//				$members[] = curl_multi_getcontent($n);
//			}
//			$COMMITTEE_IDS[$key][1] = $members;
//		}	
//		
//		foreach( $nodes as $n )
//		{
//			curl_multi_remove_handle($master_handle, $n );
//		}
//		curl_multi_close($master_handle);			
		/***********************end*********************************/
		/***********************************************************/
		/***********************************************************/
		/***********************************************************/
		/***********************************************************/		
	}
	$memcache->set('VisitingCommiteeMembers',$COMMITTEE_IDS,0,1800);
}

print '<br /><strong><a href="https://grif-uat-soa.uchicago.edu/api/auth/logoff">logout</a></strong><br />'; 
if( isset($_POST["m"]) )
{
print '<h1>'.$committees[$_POST["m"]]."</h1>";

$start = localtime(time(),true);

$report .= "<h1>Report</h1>Start time after post: ".$start['tm_hour'].":".$start['tm_min'].":".$start['tm_sec'].'<br />';

$visiting_member_ids = $memcache->get('VisitingCommiteeMembers');

$xml = $visiting_member_ids[$_POST["m"]];

// print_r( $xml ); exit();

$obj = simplexml_load_string($xml);

$id_numbers = $obj->xpath('//COMMITTEE/ID_NUMBER');

$report .= "<hr />Total entity count: ".count( $id_numbers )."<hr /><br />";

// $sample = array_slice($id_numbers, 0,30);

set_time_limit(0);

$max_num = 0;

$nodes = array();
// curl multiple handle
$master_handle = curl_multi_init();

$active = null;

$token = array(
			'authtoken' => $memcache->get('VisitingCommiteeToken')
		);
$curl->setPost($token);

$sample = 0;

	foreach ( $id_numbers as $id )
	{		
		$tmp_handle = $curl->getHandle( sprintf( $urls['entity_info'] , $id ) );
		$nodes[] = $tmp_handle;
		curl_multi_add_handle($master_handle, $tmp_handle );
		// print $id.'<br />';
	}
		
	do {
		$mrc = curl_multi_exec($master_handle, $active);
	} while( $mrc == CURLM_CALL_MULTI_PERFORM );
	
	while( $active && $mrc == CURLM_OK )
	{
		if( curl_multi_select($master_handle) != -1 )
		{
			do
			{
				$mrc = curl_multi_exec( $master_handle, $active );				
			}while( $mrc == CURLM_CALL_MULTI_PERFORM );
		}
	}
	
	if(count($nodes) > 0)
	{
		foreach( $nodes as $n )
		{
			//print_r( curl_multi_getcontent($n) ); exit();
			$obj = simplexml_load_string( curl_multi_getcontent($n) );
			foreach ( $obj->children() as $child )
			{	
				$employment = $child->xpath("//JOB[@EMPLOY_RELAT_CODE='PE']");		
				$out .= '<p>'.$child->FIRST_NAME.' '.$child->MIDDLE_NAME.' '.$child->LAST_NAME;
				if( isset($employment[0]) )
				{
					$out .= '<br />'.$employment[0];
					if( strlen($employment[0]['EMPLOYER_NAME1']) > 1 )
					{
						$out .= '<br />'.$employment[0]['EMPLOYER_NAME1'];
					}
					else
					{
						$url = sprintf( $urls['entity_info'] , $employment[0]['EMPLOYER_ID_NUMBER'] );
						$curl->createCurl( $url );
						$employer =  $curl->asSimpleXML();
						$employer_name = $employer->xpath("//NAME[@NAME_TYPE_CODE='00']");
						if( !empty( $employer_name ) )
						{
							$out .= '<br />'.$employer_name[0]->REPORT_NAME;
						}
					}					
				}				
				$url = sprintf( $urls['address_info'] , $child->ID_NUMBER );
				$curl->createCurl( $url );
				$address_entity = $curl->asSimpleXML();
				$address = $address_entity->xpath("//ADDRESS[@Address_Type='H']");
				$emails = $address_entity->xpath('//EMAIL_ADDRESSES/EMAIL_ADDRESS');
				$out .= '<br />'.$address[0]->STREET1.'<br />'.$address[0]->CITY.' , '.$address[0]->STATE_CODE.' '.$address[0]->ZIPCODE;
				$curl->createCurl( sprintf( $urls['degree_info'] , $child->ID_NUMBER ) );							
				$degree_info = $curl->asSimpleXML();	
				$degrees = $degree_info->xpath("//DEGREES/DEGREE");
				foreach ( $degrees as $d )
				{
					if( !empty($d->DEGREE_CODE) && strlen($d->DEGREE_YEAR) > 1 )
					{										
						$out .= '<br />'.$d->DEGREE_CODE." '".date("y", mktime(0, 0, 0, 0, 0, intval($d->DEGREE_YEAR)));
					}					
				}
				if( !empty( $emails[0] ) )
				{
					$out .= '<br />'.$emails[0];
				}
				$out .= '</p>';
			}
		}	
	}	
	
//	foreach( $nodes as $n )
//	{
//		curl_multi_remove_handle($master_handle, $n );
//	}
	curl_multi_close($master_handle);
}
?>