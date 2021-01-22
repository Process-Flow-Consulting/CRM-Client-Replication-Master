<?php 

global $timedate;
//check if id for online plan exists in the URL
if(isset($_REQUEST['record']))
{
	$obOnlinePlans = new oss_OnlinePlans();
	$obOnlinePlans->retrieve($_REQUEST['record']);
	
	if (preg_match('/^[^:\/]*:\/\/.*/', $obOnlinePlans->description)) {
		$stUrl = $obOnlinePlans->description;
	} else {
		$stUrl = 'http://' . $obOnlinePlans->description;
	}
	
	//mark this has been viewed on
	$obOnlinePlans->last_reviewed_date =$timedate->to_db( $timedate->now());
	
	$obOnlinePlans->save();
	ob_start();
	header("Location:".html_entity_decode($stUrl));
}

?>