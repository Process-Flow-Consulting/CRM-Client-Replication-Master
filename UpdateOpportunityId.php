<?php
// ini_set('display_errors', 1);
global $db;
define("sugarEntry", true);
require_once('include/entryPoint.php');

$select_quotes = "select * from aos_quotes where deleted = 0";
$res_quotes = $db->query($select_quotes);
while($row_quotes = $db->fetchByAssoc($res_quotes)){
	$select = "select * from quotes_accounts where quote_id = '".$row_quotes['id']."' and deleted=0";
	$res = $db->query($select);
	$row = $db->fetchByAssoc($res);
	if(!empty($row)){
		$update = "update aos_quotes set billing_account_id = '".$row['account_id']."' where id = '".$row_quotes['id']."'";
		$db->query($update);
	}
	// $update = "update aos_quotes set opportunity_id = '".$row['opportunity_id']."' where id = '".$row_quotes['id']."'";
	//$db->query($update);
}
echo "done";
?>



 