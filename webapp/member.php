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
if( isset($_SESSION['authtoken']) && isset($_GET['id_number']) )
{
	$member = $collection->getOneMemberData($_GET['id_number'] , $_SESSION['authtoken'] );	
}

$manager = new CommitteeMemberManager();
$member  = $manager->getOneMember($member);

/**
 * Start populating the CS template.
 * The Clear Silver template.
 */
$template = $app->template('member.html.cs');
$id_number = $member->getIdNumber();
$member->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
$template->show();
?>