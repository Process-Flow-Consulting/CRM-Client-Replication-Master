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

chdir('../');
require_once('include/entryPoint.php');


//ini_set('display_errors','1');

$process = $_SERVER['argv'][1];
$userId = $_SERVER['argv'][2];
$projectNumber = $_SERVER['argv'][3];
$projectOppId = $_SERVER['argv'][4];

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
?>
