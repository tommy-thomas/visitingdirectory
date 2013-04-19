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
$template = $app->template('member.html.cs');
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template->add_data('LoggedIn' , true);
}

$curl = new cURL(null);
$collection = Collection::instance( $app , $curl , $_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
if( isset($_SESSION['authtoken']) && isset($_GET['id_number']) )
{
	$member_xml = $collection->getOneMemberData($_GET['id_number'] , $_SESSION['authtoken'] );
}

$manager = new CommitteeMemberManager();
$member  = $manager->getOneMember($member_xml);
if( !is_null($member) )
{
	$id_number = $member->getIdNumber();
	$member->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
	
	$committees = $member->getCommittees();
	$committee_list = array();
	foreach( $committees as $key=>$value)
	{
		$committee_list[] = array("key" => $key , "value" => $value);
		
	}
	$template->add_data('committee_list', $committee_list , false );
}
$template->show();
  
?>