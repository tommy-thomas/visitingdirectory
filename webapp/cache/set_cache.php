<?php
ini_set('max_execution_time', 60000);
set_time_limit(0);
ignore_user_abort(1);
require('../_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
if( isset($_GET['authtoken']) && isset($_GET['payload']) )
{
	$_SESSION['authtoken'] = array( 'authtoken' => $_GET['authtoken'] );
	$curl = new cURL(null);
	$collection = GriffinCollection::instance($app , $curl );
	$manager = new CommitteeMemberManager();	
	if( $_GET['payload'] == 'alldata' )
	{		
		$collection->clearGriffinCollection();
		$collection->setCommittees();
		$collection->setAllMemberData($_SESSION['authtoken']);		
		
	}
	elseif( $_GET['payload'] == 'one' )
	{
		$first = array('VCLY','VVRT','VCLZ','VCSA','VVTH');		
		foreach ( $first as $key => $code)
		{
			try {					
					$members_xml = $collection->getMemberData( $code , $_SESSION['authtoken'] );
					$member_list = $manager->load( $code , $members_xml)->getCommiteeMemberList();
					$collection->setCachedMemberList($code , $member_list );
					ob_flush();
		    		flush();
				} catch (Exception $e) {
					Application::handleExceptions($e);
				}
		
		}
		
	}
	elseif( $_GET['payload'] == 'two' )
	{
		$second = array('VCGS','VVHM','VVLW','VVLB','VVMS');	
		foreach ( $second as $key => $code)
		{
			try {
					$members_xml = $collection->getMemberData( $code , $_SESSION['authtoken'] );
					$member_list = $manager->load( $code , $members_xml)->getCommiteeMemberList();
					$collection->setCachedMemberList($code , $member_list );
					ob_flush();
		    		flush();
				} catch (Exception $e) {
					Application::handleExceptions($e);
				}
		
		}
	}
	elseif( $_GET['payload'] == 'three' )
	{
		$third = array('VVOI','VVPS','VCLD','VVSS','VSVC');	
		foreach ( $third as $key => $code)
		{
			try {
					$members_xml = $collection->getMemberData( $code , $_SESSION['authtoken'] );
					$member_list = $manager->load( $code , $members_xml)->getCommiteeMemberList();
					$collection->setCachedMemberList($code , $member_list );
					ob_flush();
		    		flush();
				} catch (Exception $e) {
					Application::handleExceptions($e);
				}
		
		}
	}		
}
?>