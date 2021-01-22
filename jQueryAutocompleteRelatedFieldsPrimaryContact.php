<?php
define("sugarEntry", true);
require_once('include/entryPoint.php');
global $db; 

$account_id = $_REQUEST['acc'];

$sql = "select * from accounts_contacts where account_id = '".$account_id."'";
$result=$db->query($sql);
while($row = $db->fetchByAssoc($result)){

	$con= $row['contact_id'];

	$sql_con="select * from contacts where id = '".$con."' "; 
	$res_con=$db->query($sql_con);
	while($row_con = $db->fetchByAssoc($res_con)){

		$name = $row_con['first_name']." ".$row_con['last_name'];
		$data[] = array(
				
				'value' => $name,
				'id' => $row_con['id']
			);
	}	
}


echo json_encode($data);
flush(); 
?>