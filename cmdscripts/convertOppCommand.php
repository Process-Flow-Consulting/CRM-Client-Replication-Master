<?php
//ini_set('display_errors',1);
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
/**
 * handle the ajax call for lead conversion to opportunity and updating status to top of the page
 * @author Mohit Kumar Gupta
 * @date 06/11/2014
 */
set_time_limit ( 0 );
$requestData = array();
$type = (isset($_REQUEST['type']))?$_REQUEST['type']:'new';
$process = (isset($_REQUEST['process']))?$_REQUEST['process']:'convertOpp';
//current user id issue identified in BodScope integration and fixed
//Mohit Kumar Gupta 25-09-2015
$userId = (isset($_REQUEST['userId']) && trim($_REQUEST['userId']) !='') ? $_REQUEST['userId'] : '1';
$jsonArray = json_decode(urldecode($_REQUEST['jsonString']),true);
foreach ($jsonArray as $key => $dataArray){
    if ($dataArray['name'] == 'bid[]') {
    	$requestData['bid'][]= $dataArray['value'];
    } else {
        $requestData[$dataArray['name']] = $dataArray['value'];
    }
    
}
//echo "<pre>";print_r($requestData);die;
$timeStamp = time();
$formData = base64_encode(json_encode($requestData));

$cmd = "/usr/local/zend/bin/php -f convertOpp.php ".$process." ".$userId." ".$type." ".$timeStamp.' > /dev/null 2>&1 & echo $!;';

$process_path = '../upload/process/';
$lock_file = $process_path.'opp_process_lock_'.$timeStamp;
$status_file = $process_path.'opp_convert_status_'.$timeStamp;
$oppDataFile = $process_path.'opp_data_'.$timeStamp;

//write post data to file
$fpData = fopen($oppDataFile, "w");
fwrite($fpData, $formData);
fclose($fpData);

// Check lock file is exists
if (file_exists($lock_file)) {
    // check pid
    $pid = file_get_contents($lock_file);

    // if the process is running
    if (posix_kill($pid, 0)) {

        echo 'running_'.$timeStamp;

    } else {
        // Run the command and write pid in a file.
        runCommand($process, $userId, $type ,$timeStamp, $lock_file, $status_file);
        echo 'start_'.$timeStamp;
    }
} else {
    // Run the command and write pid in a file.
    runCommand($process, $userId, $type, $timeStamp, $lock_file, $status_file);
    echo 'start_'.$timeStamp;
}

function runCommand($process ,$userId, $type, $timeStamp, $lock_file, $status_file=null)
{
    $pid = convertOpp($process,$userId,$type,$timeStamp);
    $fp = fopen($lock_file, "w");
    fwrite($fp, $pid);
    fclose($fp);

    if(trim($status_file) != '') {
        $fp1 = fopen($status_file, "w");
        fwrite($fp1, '0');
        fclose($fp1);
    }
}

function convertOpp($process,$userId,$type,$timeStamp){
	chdir('../');
	set_time_limit ( 0 );
	require_once('include/entryPoint.php');

	$process_path = 'upload/process/';
	$lock_file = $process_path.'opp_process_lock_'.$timeStamp;
	$status_file = $process_path.'opp_convert_status_'.$timeStamp;
	$oppDataFile = $process_path.'opp_data_'.$timeStamp;
	$data = file_get_contents($oppDataFile);

	$formData = json_decode(base64_decode($data),true);
	$formData['lockFilePath'] = $lock_file;
	$formData['statusFilePath'] = $status_file;

	//Insert Update Project Lead
	if($process == 'convertOpp'){
			
		//create new opportunity on lead conversion
		if ($type == 'new') {
			require_once 'custom/modules/Leads/views/view.save_opportunity.php';
			$pullObj = new ViewSave_opportunity($formData, $userId);
			$pullObj->display();
		} else {//update existing opportunity on lead conversion
			require_once 'custom/modules/Leads/views/view.save_new_opportunity.php';
			$pullObj = new LeadsViewSave_new_opportunity($formData, $userId);
			$pullObj->display();
		}
			
		unlink($lock_file);
		unlink($status_file);
		unlink($oppDataFile);
		sugar_cache_reset();
		clearAllJsAndJsLangFilesWithoutOutput();
		return "success_".$timeStamp;	
	}
}

?>