<?php
//ini_set('display_errors','1');
if(isset($_REQUEST['process']) && !empty($_REQUEST['process'])){
	$process = $_REQUEST['process'];
}else{
	sugar_die('No process defined.');
}

require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';



if(isset($_REQUEST['account_no']) && !empty($_REQUEST['account_no'])){
	$account_no = $_REQUEST['account_no'];
}else{
	require_once 'modules/Administration/Administration.php';
	$obAdmin = new Administration ();
	$obAdmin->disable_row_level_security = true;
	$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
	$account_no = $arAdminData->settings['instance_account_name'];
}

//initiate class
$pullObj = new PullBBH($account_no);

//Insert Update Project Lead
if($process == 'getLeads'){
	//$s_time = time();
	if(isset($_REQUEST['limit'])){
	$pullObj->limit = $_REQUEST['limit'];
	}
	$pullObj->insertUpdateProjectLeads();
	echo "success";
	//$e_time = time();
	//echo "Leads Time Taken: ".($e_time-$s_time)." Sec.<br>";
}
 
//Insert Update Clients
if($process == 'getClient'){	
	//$s_time = time();
	$pullObj->insertUpdateClients();
	echo "success";
	//$e_time = time();
	//echo "Clients Time Taken: ".($e_time-$s_time)." Sec.<br>";
}

//Insert Update Contacts
if($process == 'getContact'){
	//$s_time = time();
	$pullObj->updateContacts();
	echo "success";
	//$e_time = time();
	//echo "Contacts Time Taken: ".($e_time-$s_time)." Sec.<br>";
}

?>
