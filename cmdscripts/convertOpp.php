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
/**
 * handle the command line script for project lead converion to new opprtunity or update to existing opportunity
 * @author Mohit Kumar Gupta
 * @date 06/11/2014
 */

chdir('../');
set_time_limit ( 0 );
require_once('include/entryPoint.php');

$process = $_SERVER['argv'][1];
//current user id issue identified in BodScope integration and fixed
//Mohit Kumar Gupta 25-09-2015
$userId = $_SERVER['argv'][2];
$type = $_SERVER['argv'][3];
$timeStamp = $_SERVER['argv'][4];

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
	echo "success_".$timeStamp;	
}
?>