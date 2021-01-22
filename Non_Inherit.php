<?php
define("sugarEntry", true);
require_once('include/entryPoint.php');
global $db;
$sel_user = "SELECT * FROM users WHERE deleted=0";
$res_user = $db->query($sel_user);
while($row_user = $db->fetchByAssoc($res_user)){
	$select = "SELECT * FROM securitygroups_users WHERE user_id='".$row_user['id']."' AND user_id != '1' AND deleted=0 AND (primary_group != 1 OR primary_group IS NULL)";
	$res = $db->query($select);
	while($row = $db->fetchByAssoc($res)){
		if(!empty($row)){
			$update = "UPDATE securitygroups_users SET noninheritable=1 WHERE securitygroup_id='".$row['securitygroup_id']."' AND user_id='".$row['user_id']."'";
			$db->query($update);
		}
	}
}

?>