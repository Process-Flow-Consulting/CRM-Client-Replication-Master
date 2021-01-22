<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'custom/include/common_functions.php';
/**
 * Description of SaveName
 *
 * @author satish
 */
class LeadClientHooks {

    function saveAccountName(&$focus) {
        if (isset($_REQUEST['account_name'])) {
            $focus->name = $_REQUEST['account_name'];
        }
        
        //set update prev bid to flag
        if(empty($focus->fetched_row['id'])){
        	setPreviousBidToUpdate();
        }
    }
	/** 
	 * @method : setViewFlag
	 * @added by : Ashutosh
	 * @purpose : Hook to modify the is_viewed flag
	 *
	 */
    function setViewFlag(&$bean){
    	   	
    	//set image instead of 0 1
    	//for pop in leads detail view do not change the flag
    	if($_REQUEST['action'] != 'getallbidders' && isset($bean->is_viewed) && $bean->is_viewed == '0'){
    		
    		//this record viewed mark is as viewed
    		
    		//we need to update only one column hence inline SQL is written    		
    		$GLOBALS['db']->query('UPDATE oss_leadclientdetail set is_viewed="1" where id= "'.$bean->id.'"');
			//$bean->is_viewed = "<div class='star' title='New Bidder'><div class='off' >&nbsp;</div></div>";
    		//change the image
    		//$bean->is_viewed = '<img src="custom/themes/default/images/new_icon.jpg">';
    		$bean->is_viewed = '<span style="color:#00CC00; font-family: Helvetica, sans-serif;"><i>New!!!</i></span>';
			
			//move new counter to -1 for new bidders in project_lead_lookup
			$obLead = new Lead();
			$obLead->retrieve($bean->lead_id);
			//echo '<pre>';print_r($obLead);echo '</pre>';
			$stParentLeadId = (trim($obLead->parent_lead_id) == '')?$obLead->id:$obLead->parent_lead_id;
			 $stUpdateCount = "UPDATE project_lead_lookup lookup			
			SET lookup.new_bidder = IF(lookup.new_bidder <= 0,0,(lookup.new_bidder-1))
			 WHERE lookup.project_lead_id= '".$stParentLeadId."'";
			$GLOBALS['db']->query($stUpdateCount);
			
		
		}elseif(isset($bean->is_viewed)) {	
				
		 	$bean->is_viewed = '';
		}
    	
    }
    /**
     * @method : updateBidderCounts
     * @added by : Ashutosh
     * @purpose : To update the bidders count for parent 
     * 			  project lead 
     * 
     */
    function updateBidderCounts(&$bean){
    	
    	global $db, $current_user;    	
    	if(trim($bean->lead_id) != ''){
    		   		
    		$obLead = new Lead();
    		$obLead->disable_row_level_security = true;
    		$obLead->retrieve($bean->lead_id);    		
    		$stParentLeadId = ($obLead->parent_lead_id == '')?$obLead->id:$obLead->parent_lead_id;    		    		
    		updateNewTotalBidderCount($stParentLeadId);
    		
    		//$sqlLeadClient
    		
    		//Add Change log entry into leads_audit table if new bidders added into system.
    		//Modified by Basudeba, Date : 23/Oct/2012.
    		$is_pl_update = false;
    		
    		if(!(($_REQUEST['xml_source'] == 'reed') || ($_REQUEST['xml_source'] == 'dodge') || ($_REQUEST['xml_source'] == 'onvia'))){
    			
    			if(empty($_REQUEST['record'])){
	    			$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='".$bean->lead_id."' AND `field_name`='Bidders List Modification'";
	    			$query = $db->query($sql);
	    			$result = $db->fetchByAssoc($query);
	    			$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'".$bean->lead_id."',UTC_TIMESTAMP(),'".$current_user->id."','Bidders List Modification','datetimecombo','".$result['before_date']."',UTC_TIMESTAMP());";
	    			$db->query($insertSQL);
	    			$is_pl_update = true;
	    		}
    		}
    		//Add Chagne log entry into leads_audit table if Bid Status changed
    		if(!empty($_REQUEST['record'])){
    			$sql = "SELECT before_value_string, after_value_string FROM oss_leadclientdetail_audit WHERE parent_id='".$bean->id."' AND field_name='bid_status' ORDER BY date_created desc";
    			$query = $db->query($sql);
    			$result = $db->fetchByAssoc($query);
    			if($result['before_value_string'] != $result['after_value_string']){
    				$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='".$bean->lead_id."' AND `field_name`='Bidders List Modification'";
    				$query = $db->query($sql);
    				$result = $db->fetchByAssoc($query);
    				$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'".$bean->lead_id."',UTC_TIMESTAMP(),'".$current_user->id."','Bidders List Modification','datetimecombo','".$result['before_date']."',UTC_TIMESTAMP());";
    				$db->query($insertSQL);
    				$is_pl_update = true;
    			}
    			
    		}
    		
    		if($is_pl_update==true){
    			//$insert_sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$bean->lead_id."' AND deleted=0";
    			//$db->query($insert_sql);
    			changeLogFlag($bean->lead_id,$db);
    		}
    		
    	}
    	   	
    }
    
   /**
    * Update modified date of project lead when new bidder comes or modified previous bidders
    */
    
    function updateLeadModifiedDate(&$bean){
    	global $timedate, $db;
    	$now_db = $timedate->nowDb();
    	$updateSql = "UPDATE leads SET date_modified='".$now_db."' WHERE id='".$bean->lead_id."'";
    	$db->query($updateSql);
    }

    function setAccountProviewLink(&$focus){
    	
    	if($_REQUEST['action'] == 'EditView'){
    		return;
    	}
    	 
//     	if($focus->account_proview_url != '')
//     	{
//     		$focus->account_proview_url = $focus->account_proview_url;
//     		if (preg_match('/^[^:\/]*:\/\/.*/', $focus->account_proview_url)) {
//     			$focus->account_proview_url= $focus->account_proview_url;
//     		} else {
//     			$focus->account_proview_url = 'http://' . $focus->account_proview_url;
//     		}
    
//     		$focus->account_proview_url = '<a href="javascript:void(0)" onclick="window.open(\''.$focus->account_proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//     	}
//     	else{
//     		$focus->account_proview_url = '';
//     		//$focus->account_proview_url = '<a href="javascript:void(0)" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//     	}

    	$focus->account_proview_url = proview_url(array('url'=>$focus->account_proview_url));
    	 
    }
    
    function setModified(&$focus){
    	if(isset($_REQUEST['record']) && !empty($_REQUEST['record'])){
    		$focus->is_modified = 1;
    	}
    }
    
    

}

?>
