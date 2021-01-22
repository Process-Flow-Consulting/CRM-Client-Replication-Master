<?php
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
class userRelatedtoRoleAndSecurityGroup
{
	function addsecurityGroupAndRole(&$bean, $event, $arguments) 
	{	
		global $db;
		if(empty($bean->fetched_row['id'])){
			$id = create_guid();
			$insertRoleUser = "INSERT INTO acl_roles_users(id, role_id, user_id, date_modified, deleted)VALUES ('".$id."','b84b0480-03d7-c0e4-da01-5e53b7db1794','".$bean->id."',now(),0)"; 
			$db->query($insertRoleUser);
			
			$name = $bean->first_name." ".$bean->last_name;
			$security_id = create_guid();
			$insertSecurity = "INSERT INTO securitygroups(id, name, date_modified, modified_user_id, created_by, deleted) VALUES ('".$security_id."','".$name."',now(),'NULL','NULL',0)"; 
			$db->query($insertSecurity);
			
			$sec_id = create_guid();
			$insertSecurityUser = "INSERT INTO securitygroups_users(id, date_modified, deleted, securitygroup_id, user_id)VALUES ('".$sec_id."',now(),0,'".$security_id."','".$bean->id."')"; 
			$db->query($insertSecurityUser);
		}	
	
	}
}	