<?php
require('_classes/autoload.php');

/**
 * The Application object.
 */
$app = Application::app();
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('committee.html.cs');
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template->add_data('LoggedIn' , true);
}
$curl = new cURL(null);
$collection = Collection::instance( $app , $curl ,  $_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
$manager = new CommitteeMemberManager();

if( isset($_SESSION['authtoken']) && isset($_GET['c']) )
{
	$code = $_GET['c'];
	$template->add_data('Committee' , Collection::getCommitteeName($code) );
	$member_list = array();
	$collection->clearCollection();
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