<?php
require('../_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();
$template = $app->template('committee.html.cs');
$template->add_data( "base" , $app->base() );
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */

$curl = new cURL(null);
$collection = GriffinCollection::instance($app , $curl );
$curl->authenticate( $collection->getLoginUrl() );
$_SESSION['authtoken'] = array( 'authtoken' => $curl->__toString());
$collection->checkCache($_SESSION['authtoken']);
$collection->setAllMemberData($_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
$manager = new CommitteeMemberManager();

if( isset($_SESSION['authtoken']) && isset($_GET['c']) )
{
	$code = $_GET['c'];
	$template->add_data('Committee' , GriffinCollection::getCommitteeName($code) );
	$member_list = array();
	if( !is_null($collection->getCachedMemberList($code)) )
	{			
		$member_list = $collection->getCachedMemberList($code);
	}
	else
	{
		$members_xml = $collection->getMemberData( $code , $_SESSION['authtoken'] );
		$member_list = $manager->load( $code , $members_xml , true)->getCommiteeMemberList();
		$collection->setCachedMemberList($code , $member_list );
	}	
	$chair_id = -1;
	foreach( $member_list as $m )
	{
		$id_number = $m->getIdNumber();
		if( $m->getCommitteeRoleCode() == 'CH' )
		{
			$name = $m->getFirstName().' ';
			$name .= strlen( $m->getMiddleName() ) > 0 ? $m->getMiddleName().' '.$m->getLastName() : $m->getLastName();
			$name .= ', Chair';				
			$template->add_data('Chairman', $name );
			$chair_id = $id_number;
		}elseif( $id_number != $chair_id )
		{
			$m->addClassDataTemplate( $template , "CommitteeMember.$id_number.");	
		}						
	}
}
$template->show();
?>