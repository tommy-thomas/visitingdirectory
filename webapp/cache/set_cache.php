<?php
// do everything possible to keep
// script from timing out
ini_set('max_execution_time',1200);
set_time_limit(1200);
ignore_user_abort(1);
require('../_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
/**
 * Start populating the CS template.
 * The Clear Silver template.
 * ws592013 = dc9c6663511c522e5369538a44159693
 * 592013ws  = 036d7426484a9670dcd11e33be785eff
 */
// success or fail message
$message = "";
// codes array
$codes = array();
if( isset( $_GET['key'] ) )
{
	$key = md5($_GET['key']);
	if( $key == 'dc9c6663511c522e5369538a44159693' )
	{	
		try {
			// 1. Create curl instance.
			$curl = new cURL(null);
			// 2. Griffin Collection.
			$collection = GriffinCollection::instance($app , $curl );
			// 3. Get authtoken from Griffin to be used in subsequent api calls.
			$curl->authenticate( $collection->getLoginUrl() );
			$authtoken = array( 'authtoken' => $curl->__toString() );
			// 3a. Exit if service not available...
			if (preg_match("/Authentication failed/i", $curl->__toString()))
			{
			    $message .= "GRIFFIN SERVICE NOT AVAILABLE.";
    			exit();
			}
			// 4. Clear out memcached data.
			$collection->clearGriffinCollection();
			// 5. Set and cache array of Committees.
			$collection->setCommittees();
			$collection->setAllMemberData($authtoken);
			// 6. CommitteeMemberManager object that handles xml parsing.
			$manager = new CommitteeMemberManager();
			// 7. Explode a comma seperated list of codes into an array.
			$all_codes = explode(",",$collection->getActiveCommitteeUrlList());
			// 8. Slice for first round of caching.
			$codes = array_slice( $all_codes , 0, 7);			
		} catch (Exception $e) {
			// 9. Uh oh, add something to the message.
			$message .= "JOB FAILED: ".$e->getMessage()."\r\n";
		}
	}
	elseif( $key == '036d7426484a9670dcd11e33be785eff' )
	{
		try {
			// Same deal as above minus clearing the old cache, and caching committess and big xml payload.
			$curl = new cURL(null);
			$collection = GriffinCollection::instance($app , $curl );
			$curl->authenticate( $collection->getLoginUrl() );
			$authtoken = array( 'authtoken' => $curl->__toString() );
			// Exit if service not available...
			if (preg_match("/Authentication failed/i", $curl->__toString()))
			{
				$message .= "GRIFFIN SERVICE NOT AVAILABLE.";
    			exit();
			}
			$manager = new CommitteeMemberManager();
			$all_codes = explode(",",$collection->getActiveCommitteeUrlList());
			$codes = array_slice( $all_codes , 7, count($all_codes)-1);
		} catch (Exception $e) {
			$message .= "JOB FAILED: ".$e->getMessage()."\r\n";
		}
	}
	if( !empty($codes) )
	{
		foreach ( $codes as $code)
		{
			try {
				// 10. Get xml from big payload based on committee code.
				$member_xml = $collection->getMemberData( $code , $authtoken );
				// 11. Get array of CommiteeMember objects.
				$member_list = $manager->load( $code , $member_xml)->getCommiteeMemberList();
				// 12. Cache the array.
				$collection->setCachedMemberList($code , $member_list );
				// 13. Flush headers.
				ob_flush();
	    		flush();
			} catch (Exception $e) {
				// 14. Uh oh, add something to the message.
				$message .= $code." - JOB FAILED: ".$e->getMessage()."\r\n";
			}
		}	
	}
	else
	{
		$message .= "FAILED";
	}
	// 15. Return success or fail message based on length of $message.
	print strlen( $message ) == 0 ? 'OK' : $message;
}
?>