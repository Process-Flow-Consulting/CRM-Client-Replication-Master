<?php
require_once 'custom/include/common_functions.php';

class OnlinPlanHooks {
	
	/**
	 * Method to update online links count for project lead
	 * 
	 * @param unknown_type $bean
	 */
	function updateLookupCounts(&$bean) {
		global $db, $current_user;
		updateOnlineCount ( $bean->lead_id );
		$is_pl_update = false;
		if (! (($_REQUEST ['xml_source'] == 'reed') || ($_REQUEST ['xml_source'] == 'dodge') || ($_REQUEST ['xml_source'] == 'onvia'))) {
			
			if (empty ( $_REQUEST ['record'] )) {
				$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='" . $bean->lead_id . "' AND `field_name`='Online Plan Modification'";
				$query = $db->query ( $sql );
				$result = $db->fetchByAssoc ( $query );
				$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'" . $bean->lead_id . "',UTC_TIMESTAMP(),'" . $current_user->id . "','Online Plan Modification','datetimecombo','" . $result ['before_date'] . "',UTC_TIMESTAMP());";
				$db->query ( $insertSQL );
				$is_pl_update = true;
								
			}
		}
		
		//Add Chagne log entry into leads_audit table if Bid Status changed
		if(!empty($_REQUEST['record'])){		
			$sql = "SELECT before_value_text, after_value_text FROM oss_onlineplans_audit WHERE parent_id='".$bean->id."' AND field_name='description' ORDER BY date_created desc";			
			$query = $db->query($sql);
			$result = $db->fetchByAssoc($query);
			if($result['before_value_text'] != $result['after_value_text']){
				$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='".$bean->lead_id."' AND `field_name`='Online Plan Modification'";
				$query = $db->query($sql);
				$result = $db->fetchByAssoc($query);
				$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'".$bean->lead_id."',UTC_TIMESTAMP(),'".$current_user->id."','Online Plan Modification','datetimecombo','".$result['before_date']."',UTC_TIMESTAMP());";
				$db->query($insertSQL);
				$is_pl_update = true;
			}			 
		}
		
		if($is_pl_update){
			//$insert_sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$bean->lead_id."' AND deleted=0";
			//$db->query($insert_sql);
			changeLogFlag($bean->lead_id,$db);
		}
	}
	
	
	/**
	 * Update Type and Source of the Online Plans if saved by User.
	 * 
	 * @param object $bean
	 */
	function updateTypeAndSource(&$bean){
		
		global $current_user;
		
		if(empty($_REQUEST['record'])){
			
			if(empty($bean->plan_type)){
				$bean->plan_type = 'Other';
			}
			
			if(empty($bean->plan_source)){
				$bean->plan_source = $current_user->name;
			}
			
		}
		
	}
	
	/**
	 * Do not delete data from BB source.
	 * 
	 * @ 
	 */
	function restrictBBData(&$bean){
		$mi_oss_onlineplans_id = trim($bean->mi_oss_onlineplans_id);
		
		if(!empty($mi_oss_onlineplans_id)){
			sugar_die('You are not authorized to delete this record.');
		}
	}
	
}
?>