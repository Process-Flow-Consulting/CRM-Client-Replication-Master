<?php


/**
 * for quotes if a document is removed from subpanel
 * proposal will be marked as not verified.
 */

if($_REQUEST['subpanel_module_name'] == 'documents'){
	global $db;
	//cancel scheduled proposal
	require_once 'custom/modules/Quotes/schedule_quotes/class.easylinkmessage.php';
	
	$obEasyLink = new easyLinkMessage();
	$obProposal = new Quote();
	$obProposal->retrieve($_REQUEST['record']);
	$obEasyLink->cancelScheduledProposal($obProposal);
	
	$updateQuery = "UPDATE quotes SET verify_email_sent=0, proposal_verified='2' WHERE id='"
	. $_REQUEST['record']. "'";
	$db->query ( $updateQuery );

	$_REQUEST['return_url']= $_REQUEST['return_url'].urlencode('&verify_message=1');


}
require_once 'include/generic/Save2.php';