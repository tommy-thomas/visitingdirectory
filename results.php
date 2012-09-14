<?php
require('_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
$curl = new cURL(null);
$collection = Collection::instance( $app , $curl );
if( isset($_SESSION['authtoken'])  && !isset($_SESSION['all_member_data']) )
{
	$collection->setAllMemberData($_SESSION['authtoken']);
}
$manager = new CommitteeMemberManager( $_SESSION['all_member_data'] );
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('results.html.cs');

if( isset($_SESSION['authtoken']) )
{
	if( isset($_POST['search_by_committee']) || isset( $_GET['c']) )
	{
		if( isset($_POST['committee']) )
		{
			$code = $_POST['committee'];
		}
		elseif( $_GET['c'] )
		{
			$code = $_GET['c'];
		}
		$template->add_data('Committee' , Collection::getCommittee($code) );
		$members = $collection->getMemberData( $code , $_SESSION['authtoken'] );
		$member_list = $manager->load( $code , $members)->getCommiteeMemberList();
		foreach( $member_list as $m )
		{
			$id_number = $m->getIdNumber();		
			if( $m->getCommitteeRoleCode() == 'CH' )
			{
				$name = $m->getFirstName().' ';
				$name .= strlen( $m->getMiddleName() ) > 0 ? $m->getMiddleName().' '.$m->getLastName() : $m->getLastName();
				$name .= ', Chair';
				$template->add_data('Chairman', $name );
			}	
			$m->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
		}
		$template->add_data('ShowCommiteeResults', true );
	}
	if(  isset($_POST['search_by_name']) )
	{
		$xml  = $manager->searchMembersByName( htmlClean($_POST['f_name']) , htmlClean($_POST['l_name']) );
		$members = $collection->getMembersAndCommittees($xml, $_SESSION['authtoken']);
		$count = count( $members );
		if( count($members) > 0 )
		{
			foreach( $members as $m )
			{
				$id_number = $m->getIdNumber();
				$m->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
			}
		}
		$template->add_data('count', $count );
		$template->add_data('ShowSearchResults', true );		
	}	
}
$template->show();
?>