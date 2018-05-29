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
if( !$app->isAuthorized() )
{
	$app->redirect('./index.php?error=auth');
}
else
{
	$template = $app->template('./results.html.twig');
	$TwigTemplateVariables = array();

	$TwigTemplateVariables[ "base" ] = $app->base() ;
	$TwigTemplateVariables['LoggedIn' ] = true;
}
$curl = new cURL(null);
$collection = GriffinCollection::instance( $app , $curl ,  $_SESSION['authtoken']);
$collection->loadCommitteeTemplateData($template);
$manager = new CommitteeMemberManager();
if( (isset($_POST['search_by_committee']) && empty($_POST['committee'])) )
{
	$app->redirect('./search.php?error=no_select');
}
elseif( isset($_POST['search_by_name']) && empty($_POST['f_name']) && empty($_POST['l_name']) )
{
	$app->redirect('./search.php?error=no_name');
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
		$TwigTemplateVariables['Committee' ] = $collection->getCommitteeName($code);
		$members_list = array();
		if( !is_null($collection->getCachedMemberList($code)) )
		{
			$members_list = $collection->getCachedMemberList($code);
		}
		else
		{
			$members_xml = $collection->getMemberData( $code , $_SESSION['authtoken'] );
			$members_list = $manager->load( $code , $members_xml)->getCommiteeMemberList();
			$collection->setCachedMemberList($code , $members_list );
		}
		foreach( $members_list as $m )
		{
			$id_number = $m->getIdNumber();
			if( $m->getCommitteeRoleCode() == 'CH' )
			{
				$name = $m->getFirstName().' ';
				$name .= strlen( $m->getMiddleName() ) > 0 ? $m->getMiddleName().' '.$m->getLastName() : $m->getLastName();
				$name .= ', Chair';
				$TwigTemplateVariables['Chairman'] = $name ;
			}	
			$m->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
		}
		$TwigTemplateVariables['ShowCommiteeResults'] = true ;
	}
	if(  isset($_POST['search_by_name']) )
	{
		$xml  = $manager->searchMembersByName( htmlClean($_POST['f_name']) , htmlClean($_POST['l_name']) );
        $members = $manager->searchCachedMembersByID( $xml );
        if( empty($members) )
        {
            $members = $collection->getMembersAndCommittees($xml, $_SESSION['authtoken']);
        }
		$count = count( $members );
		$total = 0;
		if( $count > 0 )
		{
			foreach( $members as $key => $m )
			{
				$total++;
				$id_number = $m->getIdNumber();				
				$m->addClassDataTemplate( $template , "CommitteeMember.$id_number.");
			}
		}
		$TwigTemplateVariables['count'] = $total ;
		$TwigTemplateVariables['ShowSearchResults'] = true ;		
	}	
}
echo $template->render($TwigTemplateVariables);
?>