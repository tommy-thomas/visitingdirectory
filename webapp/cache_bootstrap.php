<?php
require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
$curl = new cURL(null);
if( $app->isAuthorized() && isset($_SESSION['authtoken']) && !isset($_GET['err']))
{
	ignore_user_abort(true);
	set_time_limit(0);
	/*
	 * Try to lazy cache 1 large committee list results if not already in cache
	 */
	$collection = Collection::instance( $app , $curl ,  $_SESSION['authtoken']);
	$manager = new CommitteeMemberManager();
	$count = 0;
	$max = 1;
	$codes = explode("," , $collection->getActiveCommitteeUrlList());
	if( !empty($codes) && isset($_SESSION['current_search']) )
	{
		foreach( $codes as $c )
		{
			$key = "vc_".$c."_list";
			if( (!apc_exists($key)) && ( $count < $max ) && ($c != $_SESSION['current_search']) )
			{
				$members_xml = $collection->getMemberData( $c , $_SESSION['authtoken'] );
				$member_list = $manager->load( $c , $members_xml)->getCommiteeMemberList();
				$collection->setCachedMemberList($c , $member_list );
				$count++;
			}
		}
	}
}
?>