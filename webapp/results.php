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
$template = $app->template('results.html.cs');
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
$manager = new CommitteeMemberManager();
if( (isset($_POST['search_by_committee']) && empty($_POST['committee'])) )
{
	$app->redirect('./search.php?error=no_select');
}

if( isset($_SESSION['authtoken']) )
{
	if( (isset($_POST['search_by_committee']) && !empty($_POST['committee'])) || isset( $_GET['c']) )
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
		$members_xml = $collection->getMemberData( $code , $_SESSION['authtoken'] );
		$member_list = $manager->load( $code , $members_xml)->getCommiteeMemberList();
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
		if( $count > 0 )
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