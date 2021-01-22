<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

//change directories to where this file is located.
//this is to make sure it can find dce_config.php

/*
if(file_exists('pull_lock')){
	die('script already running'.PHP_EOL);
}

$fp=fopen('pull_lock',"w");

function shutdown($test)
{    
	fwrite($test);
	fclose($fp);
	//unlink('pull_lock');	
	echo 'Script executed with success', PHP_EOL;
	exit();
}
$test = 1;
register_shutdown_function('shutdown',&$test);
*/
chdir('../');
//chdir(dirname(__FILE__));
require_once('include/entryPoint.php');


//ini_set('display_errors','1');

//$_REQUEST['process'] = $_SERVER['argv'][1];	
$process = $_SERVER['argv'][1];

//if(isset($_REQUEST['process']) && !empty($_REQUEST['process'])){
//	$process = $_REQUEST['process'];
//}else{
//	sugar_die('No process defined.');
//}

require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';


//$_REQUEST['account_no'] = $_SERVER['argv'][2];


//if(isset($_REQUEST['account_no']) && !empty($_REQUEST['account_no'])){
//	$account_no = $_REQUEST['account_no'];
//}else{
	require_once 'modules/Administration/Administration.php';
	$obAdmin = new Administration ();
	$obAdmin->disable_row_level_security = true;
	$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
	$account_no = $arAdminData->settings['instance_account_name'];
//}

//$account_no = "8765";
//initiate class
$pullObj = new PullBBH($account_no);

//Insert Update Project Lead
if($process == 'getLeads'){
	//$s_time = time();
	if(isset($_REQUEST['limit'])){
	$pullObj->limit = $_REQUEST['limit'];
	}
	$pullObj->insertUpdateProjectLeads();
	sugar_cache_reset();
	clearAllJsAndJsLangFilesWithoutOutput();
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
