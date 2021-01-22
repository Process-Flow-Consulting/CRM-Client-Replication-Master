<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
//$cmd = "/usr/local/zend/bin/php -f PullBBH.php getLeads > /dev/null 2>&1 & echo $!;";

$process_path = '../upload/process/';
$lock_file = $process_path.'process_lock';
$status_file = $process_path.'import_status';

// Check lock file is exists
if (file_exists($lock_file)) {
    // check pid
    $content = file_get_contents($lock_file);
    $content = explode("_", $content);
    $pid = $content[0];
    $start_time = $content[1];
    $current_time = time();
    // var_dump($pid);
    if(posix_kill($pid, 0)) {
        /**
         * Check process start time, If process take more than 30 min then
         * current process will be killed and start new process.
         */
        if (($current_time - $start_time) > 1800) {
            // Kill the Process.
            posix_kill($pid, 9);
            // Start New Process.
            runCommand($lock_file);
            echo 'start';
        }else{
        	echo 'running';
        }
    } else {
        // Run the command and write pid in a file.
        runCommand($lock_file, $status_file);
        echo 'start';
    }
} else {
    // Run the command and write pid in a file.
    runCommand($lock_file, $status_file);
    echo 'start';
}
function runCommand($lock_file, $status_file)
{
    $pid = PullBBH('getLeads');
    $current_time = time();
    $file_text = $pid . "_" . $current_time;
    $fp = fopen($lock_file, "w");
    fwrite($fp, $file_text);
    fclose($fp);
    $fp1 = fopen($status_file, "w");
    fwrite($fp1, '0|0');
    fclose($fp1);
}
function PullBBH($action){
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
	//$process = $_SERVER['argv'][1];

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
	if($action == 'getLeads'){
		//$s_time = time();
		if(isset($_REQUEST['limit'])){
		$pullObj->limit = $_REQUEST['limit'];
		}
		$pullObj->insertUpdateProjectLeads();
		sugar_cache_reset();
		clearAllJsAndJsLangFilesWithoutOutput();
		return "success";
		//$e_time = time();
		//echo "Leads Time Taken: ".($e_time-$s_time)." Sec.<br>";
	}

	//Insert Update Clients
	if($action == 'getClient'){	
		//$s_time = time();
		$pullObj->insertUpdateClients();
		return "success";
		//$e_time = time();
		//echo "Clients Time Taken: ".($e_time-$s_time)." Sec.<br>";
	}

	//Insert Update Contacts
	if($action == 'getContact'){
		//$s_time = time();
		$pullObj->updateContacts();
		return "success";
		//$e_time = time();
		//echo "Contacts Time Taken: ".($e_time-$s_time)." Sec.<br>";
	}
}
?>
