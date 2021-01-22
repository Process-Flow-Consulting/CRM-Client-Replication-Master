<?php
require_once 'custom/include/common_functions.php';

if($_REQUEST['subpanel_module_name']=='oss_classification_leads'){
	global $db, $current_user;
 	if(isset($_REQUEST['record'])){
		$lead_id = $_REQUEST['record'];
	}else{		
		$lead_id = $_REQUEST['return_id'];
	}
	
	$sql = "SELECT IFNULL(MAX(after_value_string),UTC_TIMESTAMP()) before_date FROM `leads_audit` WHERE `parent_id`='" . $lead_id . "' AND `field_name`='Project Class Modification'";
	$query = $db->query ( $sql );
	$result = $db->fetchByAssoc ( $query );
	$insertSQL = "INSERT INTO leads_audit (`id`,`parent_id`,`date_created`,`created_by`,`field_name`,`data_type`,`before_value_string`,`after_value_string`) VALUES (UUID(),'" . $lead_id . "',UTC_TIMESTAMP(),'" . $current_user->id . "','Project Class Modification','datetimecombo','" . $result ['before_date'] . "',UTC_TIMESTAMP());";
	$db->query ( $insertSQL );
	//$insert_sql = "UPDATE leads SET change_log_flag=1 WHERE id='".$lead_id."' AND deleted=0";
	//$db->query($insert_sql);
	changeLogFlag($lead_id, $db);	
}
require_once 'include/generic/Save2.php';