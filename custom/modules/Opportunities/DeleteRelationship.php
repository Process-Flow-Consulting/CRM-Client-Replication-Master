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
* by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/
/**
* use for remove leads record from history subpanel of opportunity
* @author Mohit Kumar Gupta
* @date 30-06-2014
*/
$linkedFieldArray = array('tasks','meetings','calls','emails','notes');
if(in_array($_REQUEST['linked_field'], $linkedFieldArray)){
	global $db;
	$tableName = $_REQUEST['linked_field'];
	if (!empty($tableName) && !empty($_REQUEST['linked_id'])) {
	    $selectOppQuery = "SELECT parent_opportunity_id, project_lead_id FROM opportunities WHERE deleted='0' AND project_lead_id IS NOT NULL AND id=" . $db->quoted($_REQUEST['record']);
	    $selectOppResult = $db->query ( $selectOppQuery );
	    $selectOppData = $db->fetchByAssoc($selectOppResult);
	    
	    //if records belongs to project opportunity
	    if (empty($selectOppData['parent_opportunity_id'])) {
    	    //get related module of related record
    	    $selectQuery = "SELECT parent_type, parent_id FROM ".$tableName." WHERE deleted='0' AND id=" . $db->quoted($_REQUEST['linked_id']);
        	$selectResult = $db->query ( $selectQuery );
        	$selectData = $db->fetchByAssoc($selectResult);
        	
        	//if records belongs to different module then change the bean module and bean id
        	if ($_REQUEST['module'] != $selectData['parent_type'] && $_REQUEST['record'] != $selectData['parent_id']) {
        	    if ($selectData['parent_type'] != 'Leads') {    	        
        	    	echo $_REQUEST['module'] = 'Leads';
        		    echo $_REQUEST['record'] = $selectOppData['project_lead_id'];
        	    } else {
        	        echo $_REQUEST['module'] = $selectData['parent_type'];
        	        echo $_REQUEST['record'] = $selectData['parent_id'];
        	    }    		
        	}
	   }
	}    	
}

require_once 'include/generic/DeleteRelationship.php';