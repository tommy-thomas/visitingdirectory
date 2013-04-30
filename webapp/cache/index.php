<?php
require('../_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$authtoken = $_GET['authtoken'];
$curl = new cURL(null);
$collection = GriffinCollection::instance( $app , $curl ,  $authtoken);
$manager = new CommitteeMemberManager();
$committees = array_reverse($collection->getCommittees());
foreach ( $committees as $c )
{	
	$code = $c->getCOMMITTEE_CODE();
	if( is_null( $collection->getCachedMemberList( $code ) ) )
	{
		try {
			$members_xml = $collection->getMemberData( $code , $authtoken , true);
			$member_list = $manager->load( $code , $members_xml)->getCommiteeMemberList();
			$collection->setCachedMemberList($code , $member_list );
			sleep(1);
		} catch (Exception $e) {
			Application::handleExceptions($e);
		}
	}
}
?>