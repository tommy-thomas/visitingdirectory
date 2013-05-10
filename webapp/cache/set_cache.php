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
	/*
	 * Create the objects used in all of the caching chunks.
	 */
	$_SESSION['authtoken'] = array( 'authtoken' => $_GET['authtoken'] );
	$curl = new cURL(null);
	$collection = GriffinCollection::instance($app , $curl );
	$manager = new CommitteeMemberManager();
	/*
	 * Set big payload with all of the memebers from every committee and cache as xml blob.
	 */
	if( $_GET['payload'] == 'alldata' )
	{		
		$collection->clearGriffinCollection();
		$collection->setCommittees();
		$collection->setAllMemberData($_SESSION['authtoken']);
	}
	/*
	 * Load chunks of committees by groups of five and cache as arrays of CommitteeMember objects.
	 */
	$codes_array = array(
		'one' => array('VCLY','VVRT','VCLZ','VCSA','VVTH')
		'two' =>  array('VCGS','VVHM','VVLW','VVLB','VVMS')
		'three' => array('VVOI','VVPS','VCLD','VVSS','VSVC')
		);
	if( isset( $codes_array($_GET['payload']) ) )
	{
		$codes = $codes_array($_GET['payload']);
		foreach ( $codes as $code)
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