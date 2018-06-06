<?php
require __DIR__ . "/../vendor/autoload.php";
/**
 * The Application object.
 */
$app = new \UChicago\AdvisoryCouncil\Application();
/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template = $app->template('member.html.cs');
	$template->add_data('LoggedIn' , true);
}

$curl = new cURL(null);
$collection = GriffinCollection::instance( $app , $curl , $_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
$manager = new CommitteeMemberManager();
if( isset($_SESSION['authtoken']) && isset($_GET['id_number']) )
{
    $result = $manager->searchCachedMembersByID( array($_GET['id_number']) );
    if( !empty($result) )
    {
        $member = $result[0];
    }
    else
    {
        $member_xml = $collection->getOneMemberData($_GET['id_number'] , $_SESSION['authtoken'] );
        $member  = $manager->getOneMember($member_xml);
    }

}
if( !is_null($member) )
{
	$id_number = $member->getIdNumber();
	$member->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
	
	$committees = $member->getCommitteesLong();
	$committee_list = array();
	foreach( $committees as $key=>$value)
	{
		$committee_list[] = array("key" => $key , "value" => $value);
		
	}
	$template->add_data('committee_list', $committee_list , false );
}
$template->show();
  
?>