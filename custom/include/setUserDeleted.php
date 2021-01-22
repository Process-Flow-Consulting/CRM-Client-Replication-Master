<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
* by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/
/**
 * delete user of client instance using master crm
 * @author Mohit Kumar Gupta
 * @date 06-dec-2013
 */
global $db, $sugar_config;

if(isset($_REQUEST['key']) && !empty($_REQUEST['key'])){
	//Create Key for Validation
	$key = md5($sugar_config['validation_key']);
	if($key != $_REQUEST['key']){
		sugar_die('Un-Authorised Access');
	}
}else{
	sugar_die('Un-Authorised Access');
}

$uid = isset($_REQUEST['uid'])?$_REQUEST['uid']:'';
if (!empty($uid)) {
	$stGetInstanceClssSQL = "UPDATE users SET deleted='1' WHERE md5(id)='".base64_decode($uid)."'";
	$rsGetInstanceClssSQL = $db->query ( $stGetInstanceClssSQL );
	sugar_die("success");
} else{
	sugar_die('Un-Authorised Access');
}
?>
