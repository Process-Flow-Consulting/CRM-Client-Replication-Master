<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
$process = (isset($_REQUEST['process']) && trim($_REQUEST['process']) !='') ? $_REQUEST['process'] : 'getNewOpportunities';
$userId = (isset($_REQUEST['userId']) && trim($_REQUEST['userId']) !='') ? $_REQUEST['userId'] : '1';
$projectOppId = (isset($_REQUEST['projectOppId']) && trim($_REQUEST['projectOppId']) !='') ? $_REQUEST['projectOppId'] : '';
$projectNumber = (isset($_REQUEST['projectNumber']) && trim($_REQUEST['projectNumber']) !='') ? $_REQUEST['projectNumber'] : '';
//$cmd = "/usr/local/zend/bin/php -f PullBBHOpp.php ".$process." ".$userId." ".$projectNumber."  ".$projectOppId." > /dev/null 2>&1 & echo $!;";

$process_path = '../upload/process/';
$lock_file = $process_path.'opp_import_process_lock';
$status_file = $process_path.'opp_import_import_status';
if ($projectOppId != '') {
    $lock_file = $process_path.'opp_import_process_lock_'.$projectOppId;
    $status_file = $process_path.'opp_import_import_status_'.$projectOppId;
}

// Check lock file is exists
if (file_exists($lock_file)) {
    // check pid
    $content = file_get_contents($lock_file);
    $content = explode("_", $content);
    $pid = $content[0];
    $start_time = $content[1];
    $current_time = time();
    // var_dump($pid);
    if (posix_kill($pid, 0)) {
        /**
         * Check process start time, If process take more than 30 min then
         * current process will be killed and start new process.
         */
        if (($current_time - $start_time) > 1800) {
            // Kill the Process.
            posix_kill($pid, 9);
            // Start New Process.
            runCommand($process, $userId, $projectNumber, $projectOppId, $lock_file);
            echo 'start';
        }else{
        	echo 'running';
        }
    } else {
        // Run the command and write pid in a file.
        runCommand($process, $userId, $projectNumber, $projectOppId, $lock_file, $status_file);
        echo 'start';
    }
} else {
    // Run the command and write pid in a file.
    runCommand($process, $userId, $projectNumber, $projectOppId, $lock_file, $status_file);
    echo 'start';
}

//if third parameter is not supplied it is set to null.
function runCommand($process, $userId, $projectNumber, $projectOppId, $lock_file, $status_file=null)
{
    $pid = PullBBHOpp($process,$userId,$projectNumber,$projectOppId);
    $current_time = time();
    $file_text = $pid . "_" . $current_time;
    $fp = fopen($lock_file, "w");
    fwrite($fp, $file_text);
    fclose($fp);
    
    //if status file is not set then do not go to fopen.
    if(trim($status_file) != '') {
		$fp1 = fopen($status_file, "w");
		fwrite($fp1, '0|0');
		fclose($fp1);
	}
}


function PullBBHOpp($process,$userId,$projectNumber,$projectOppId){
	
	
//change directories to where this file is located.
//this is to make sure it can find dce_config.php

chdir('../');
require_once('include/entryPoint.php');


//ini_set('display_errors','1');

// $process = $_SERVER['argv'][1];
// $userId = $_SERVER['argv'][2];
// $projectNumber = $_SERVER['argv'][3];
// $projectOppId = $_SERVER['argv'][4];

require_once 'custom/modules/Opportunities/pull_opportunities/PullBBHOpp.class.php';

require_once 'modules/Administration/Administration.php';
$obAdmin = new Administration ();
$obAdmin->disable_row_level_security = true;
$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
$account_no = $arAdminData->settings['instance_account_name'];

//initiate class
$pullOppObj = new PullBBHOpp($account_no, $userId, $projectOppId);
//Insert Project/Client opportunities
if($process == 'getNewOpportunities'){
	if(isset($_REQUEST['limit'])){
		$pullOppObj->limit = $_REQUEST['limit'];
	}
	$pullOppObj->setProjectClientOpportunities('New', $projectNumber, 0);
	sugar_cache_reset();
	clearAllJsAndJsLangFilesWithoutOutput();
	echo "success";
} else if($process == 'getUpdateOpportunities'){
	if(isset($_REQUEST['limit'])){
		$pullOppObj->limit = $_REQUEST['limit'];
	}
	$pullOppObj->setProjectClientOpportunities('Update', $projectNumber, 0);	
	sugar_cache_reset();
	clearAllJsAndJsLangFilesWithoutOutput();
	
	echo "success";
}
	
	
}

?>
