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
//Below committees array keys maps each listed item to md5 hash
//ws592013
//592013ws
//ws100000
//ws200000
//ws300000
//ws400000
//ws500000
//ws600000
//ws700000
//ws800000
//ws900000
//ws110000
//ws120000
$committees = array(
'dc9c6663511c522e5369538a44159693' => 'VCLZ',
'036d7426484a9670dcd11e33be785eff' => 'VCLY',
'123f6ed23246ed87b004eb29e46563a6' => 'VCSA',
'eaa328c81afedc87abe7fff05939e3d4' => 'VVTH',
'd89f847938a6cf3ea748d02cce8ca5e5' => 'VCGS',
'92a760250c71c456ddacf10cd587aac5' => 'VVHM',
'a14aa3d989ef628913d5b3698149b32c' => 'VVLW',
'4508dea6ececd5a1e92bd5e0c859df3a' => 'VVLB',
'5e449fe7f4826e9d5e83f973b3708587' => 'VVOI',
'b81e18ce04724c5bf24d57b5aede8545' => 'VVPS',
'c1d8003dee0a03b79e3e081881c23196' => 'VCLD',
'c358905d59da55952d7b9141e3c4926d' => 'VVSS',
'da38dbd539a4f0d2c4fd80ac9d2d4b50' => 'VSVC'
);
// success or fail message
$message = "";
if( isset($_GET['key']) 
&& isset($committees[md5($_GET['key'])]) )
{
	try {
		$key = md5($_GET['key']);
		// 1. Committee Code
		$code = $committees[$key];
		// 2. Create curl instance.
		$curl = new cURL(null);
		// 3. Griffin Collection.
		$collection = GriffinCollection::instance($app , $curl );
		// 4. Get authtoken from Griffin to be used in subsequent api calls.
		$curl->authenticate( $collection->getLoginUrl() );
		$authtoken = array( 'authtoken' => $curl->__toString() );
		// 5. Exit if service not available...
		if (preg_match("/Authentication failed/i", $curl->__toString()))
		{
		    $message .= "GRIFFIN SERVICE NOT AVAILABLE.";
	    	exit();
		}
		// 6. Clear out memcached data once for first round 
		// to make sure we're getting a new cache.
		if( $key == 'dc9c6663511c522e5369538a44159693' )
		{
			$collection->clearGriffinCollection();
			// 5. Set and cache array of Committees.
			$collection->setCommittees();
			$collection->setAllMemberData($authtoken);	
		}
		// 7. CommitteeMemberManager object that handles xml parsing.
		$manager = new CommitteeMemberManager();
		// 8. Get xml from big payload based on committee code.
		$member_xml = $collection->getMemberData( $code , $authtoken );
		// 9. Get array of CommiteeMember objects.
		$member_list = $manager->load( $code , $member_xml)->getCommiteeMemberList();
		// 10. Cache the array.
		$collection->setCachedMemberList($code , $member_list );
		// 11. Flush headers.
		ob_flush();
    	flush();
	} catch (Exception $e) {
		// 12. Uh oh, add something to the message.
		$message .= "JOB FAILED FOR: ".$code." :".$e->getMessage()."\r\n";
	}
	// 13. Return success or fail message based on length of $message.
	print strlen( $message ) == 0 ? 'OK' : $message;
}
?>