<?php
define("sugarEntry", true);
require_once('include/entryPoint.php');
global $db;


$select = "SELECT id,quote_num FROM aos_quotes";
$result = $db->query($select);
while($row = $db->fetchByAssoc($result)){
	$update_quote = "UPDATE aos_quotes SET number='".$row['quote_num']."' WHERE id='".$row['id']."'";
	$db->query($update_quote);
}
echo "Done";
?>