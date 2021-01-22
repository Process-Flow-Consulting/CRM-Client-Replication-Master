<?php
require_once 'custom/include/common_functions.php';
if (!defined('sugarEntry') || !sugarEntry)
die('Not A Valid Entry Point');
/* * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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
* ****************************************************************************** */

class LeadHooks {

	function SaveData(&$focus) {
		if (isset ( $_REQUEST ['action'] )) {
			if ('Save' == $_REQUEST ['action']) {
				$focus->union_c = isset ( $_REQUEST ['union_c'] ) ? $_REQUEST ['union_c'] : '0';
				$focus->non_union = isset ( $_REQUEST ['non_union'] ) ? $_REQUEST ['non_union'] : '0';
				$focus->prevailing_wage = isset ( $_REQUEST ['prevailing_wage'] ) ? $_REQUEST ['prevailing_wage'] : '0';
			}
			
			// in case of data comming from other sources ie outlook, Google
			// Contacts, Webex, gotomeeting
			if (trim ( $focus->project_title ) == '' && (trim ( $focus->last_name ) != '' || trim ( $focus->first_name ) != '')) {
				$focus->project_title = trim ( $focus->first_name ) . ' ' . trim ( $focus->last_name );
			} else {
				$focus->last_name = $focus->project_title;
			}
		}
		
		//if new lead add own id to parent_lead_id
		if(empty($focus->fetched_row['id'])){
		    $lead_id = create_guid();
		    $focus->id = $lead_id;
		    $focus->parent_lead_id = $lead_id;
		    $focus->new_with_id = true;
		}
	}
	function convertDueDateToTimeZone(&$focus) {
		if (isset ( $_REQUEST ['action'] )) {
			if ('Save' == $_REQUEST ['action']) {
				require_once 'custom/include/OssTimeDate.php';
				$oss_timedate = new OssTimeDate ();
				$focus->bids_due = $oss_timedate->convertDateForDB ( $_REQUEST ['bids_due'], $focus->bid_due_timezone );
				
				/*
				 * global $timedate; $due_date_arr = explode("
				 * ",$_REQUEST['bids_due']); $db_date_time_arr =
				 * $timedate->to_db_date_time($due_date_arr[0],
				 * $due_date_arr[1]); $db_date_time = strtotime(implode("
				 * ",$db_date_time_arr)); $time_zone = $focus->bid_due_timezone;
				 * switch($time_zone){ case 'Eastern'; $gmt_time = date('Y-m-d
				 * H:i:s',strtotime('+5 hour',$db_date_time)); break; case
				 * 'Central'; $gmt_time = date('Y-m-d H:i:s',strtotime('+6
				 * hour',$db_date_time)); break; case 'Mountain'; $gmt_time =
				 * date('Y-m-d H:i:s',strtotime('+7 hour',$db_date_time));
				 * break; case 'Pacific'; $gmt_time = date('Y-m-d
				 * H:i:s',strtotime('+8 hour',$db_date_time)); break; default:
				 * $gmt_time = date('Y-m-d H:i:s',$db_date_time); }
				 * $focus->bids_due = $gmt_time;
				 */
			}
		}
	}
	
	function updateLookupCounts(&$bean){
		//update counts 
		updateLeadVersionBidDueDate($bean->id);
		updateNewTotalBidderCount($bean->id);
		
		//should be called at oss_onlineplan
		//updateOnlineCount($bean->id);
		
		
	}
	
	function changeLogFlag(&$bean){
		global $db;
		$sql = "SELECT count(1) cnt FROM leads_audit WHERE parent_id='".$bean->id."'";
		$query = $db->query($sql);
		$result = $db->fetchByAssoc($query);
		if($result['cnt'] > 0){			
			$sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$bean->id."'";
			$db->query($sql);
			//Check for De-duped Project Lead
			$pl_sql = "SELECT parent_lead_id FROM leads WHERE id='".$bean->id."' AND deleted = 0";
			$pl_query = $db->query($pl_sql);
			$pl_result = $db->fetchByAssoc($pl_query);
			if(!empty($pl_result['parent_lead_id'])){
				$sql="SELECT change_log_flag FROM leads WHERE id='".$pl_result['parent_lead_id']."' AND deleted = 0";
				$query = $db->query($sql);
				$result = $db->fetchByAssoc($query);
				if($result['change_log_flag']== 0){
					$sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$pl_result['parent_lead_id']."' AND deleted = 0";
					$db->query($sql);
				}
			}
			/* $lookUp = checkLookupLeadExists($bean->id);			
			if(!empty($lookUp['id'])){
				$update_sql = "UPDATE project_lead_lookup SET change_log_flag=1 WHERE project_lead_id='".$bean->id."' AND deleted = 0";
				$db->query($update_sql);
			}else{
				$insert_sql = "INSERT INTO project_lead_lookup(id,project_lead_id,change_log_flag) VALUES(UUID(),'".$bean->id."',1)";
				$db->query($insert_sql);
			} */
		}
		
		if($result['cnt'] == 0){
			//Checking Lead Version			
			$sql = "SELECT lead_version FROM project_lead_lookup WHERE project_lead_id='".$bean->id."'";
			$query = $db->query($sql);
			$result = $db->fetchByAssoc($query);
			if($result['lead_version'] == 1 && $bean->change_log_flag == 1){
				$update_sql = "UPDATE leads SET change_log_flag=0 WHERE id='".$bean->id."'";
				$db->query($update_sql);
			}
		}
	}

	function setModified(&$focus){
		if(isset($_REQUEST['record']) && !empty($_REQUEST['record'])){
			$focus->is_modified = 1;
		}
		
		if(trim($focus->bids_due) == ''){
			$focus->bids_due = '0000-00-00 00:00:00';
		}
	}
	
	/**
	 * Delete All related data to this Lead
	 */
	function deleteRelatedData(&$focus){
	    
	    global $db;
	    
	    $leadId = $focus->id;
	    
	    //delete related Tasks
	    $deleteRelatedTasks = " UPDATE tasks SET deleted = '1' WHERE parent_id = '".$leadId."'  ";
	    $db->query($deleteRelatedTasks);
	    
	    //delete related Calls
	    $deleteRelatedCalls = " UPDATE calls SET deleted = '1' WHERE parent_id = '".$leadId."' ";
	    $db->query($deleteRelatedCalls);
	    
	    //delete related Meetings
	    $deleteRelatedMeetings = " UPDATE meetings SET deleted = '1' WHERE parent_id = '".$leadId."' ";
	    $db->query($deleteRelatedMeetings);
	    
	    //delete related Notes
	    $deleteRelatedNotes = " UPDATE notes SET deleted = '1' WHERE parent_id = '".$leadId."'  ";
	    $db->query($deleteRelatedNotes);
	    
	    //delete related Emails
	    $deleteRelatedEmails = " UPDATE emails_beans SET deleted = '1' WHERE bean_id = '".$leadId."' ";
	    $db->query($deleteRelatedEmails);
	    
	    //delete related Online Plans
	    $deleteRelatedOnlinePlans = " UPDATE oss_onlineplans SET deleted = '1' WHERE lead_id = '".$leadId."' ";
	    $db->query($deleteRelatedOnlinePlans);
	    
	    //delete classification reslationship
	    $deleteRelatedClassifications = " UPDATE oss_classifcation_leads_c SET deleted = '1' WHERE oss_classi7103dsleads_idb = '".$leadId."' ";
	    $db->query($deleteRelatedClassifications);
	    
	    //delete Related Bidders
	    $deleteRelatedBidders = "UPDATE oss_leadclientdetail SET deleted = '1' WHERE lead_id ='".$leadId."' ";
	    $db->query($deleteRelatedBidders);  
	    
	}
	
	/**
	 * update CRM data to corresponding outlook data
	 * @author Mohit KUmar Gupta
	 * @date 06-03-2014
	 */
	function updateOutlookData(&$focus){
	    global $db;
	    $projectTitle = trim ( $focus->project_title );
	    $lastName = trim ( $focus->last_name );
	    $firstName = trim ( $focus->first_name );
	    // in case of data comming from other sources ie outlook, Google
	    // Contacts, Webex, gotomeeting
	    if ($projectTitle == '' && ($lastName != '' || $firstName != '')) {
	        $projectTitle = trim($firstName . ' ' . $lastName);
	        $query = "UPDATE leads SET project_title='".$projectTitle."' WHERE id='".$focus->id."'";	        
	        $db->query($query);
	    } 	    	        
	}
	
	/**
	 * update project lead status to "Converted" for all related linked leads if any one out of them is updated to converted
	 * @author Mohit KUmar Gupta
	 * @date 02-09-2015
	 */
	function updateProjectStatus(&$focus){
	    global $db;
	    if (!empty($focus->parent_lead_id)) {
	        $sql = "SELECT count(id) as total FROM leads WHERE parent_lead_id='".$focus->parent_lead_id."' AND status='Converted' AND deleted='0'";
	        $query = $db->query($sql);
	        $result = $db->fetchByAssoc($query);
	        if ($result['total'] > 0) {
	        	 $lookupSql = "SELECT lead_version FROM project_lead_lookup WHERE project_lead_id='".$focus->parent_lead_id."' and deleted='0'";
			     $lookupQuery = $db->query($lookupSql);
			     $lookupResult = $db->fetchByAssoc($lookupQuery);
			     if ($lookupResult['lead_version'] != $result['total']) {
			     	$query = "UPDATE leads SET status='Converted' WHERE parent_lead_id='".$focus->parent_lead_id."' AND deleted='0'";
	                $db->query($query);
			     }
	        }
	        
	    }	    
	}

}

?>
