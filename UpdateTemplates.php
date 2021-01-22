<?php
global $db;
define("sugarEntry", true);
require_once('include/entryPoint.php');
$id = $_GET['temp_id'];
$select = "SELECT * FROM aos_producttemplates WHERE id = '".$id."' and deleted =0";
$result = $db->query($select);
$row = $db->fetchByAssoc($result);
$return_data['id'] = $row['id'];
$return_data['name'] = $row['name'];
$return_data['cost'] = $row['cost_price'];
$return_data['discount'] = $row['discount_price'];
$return_data['measure'] = $row['unit_measure'];
$return_data['type'] = $row['type_id'];
$return_data['price'] = $row['list_price'];
$return_data['description'] = $row['description'];
$return_data['category'] = $row['category_id'];
$return_data['term'] = $row['support_term'];
$return_data['support_name'] = $row['support_name'];
$return_data['support_contact'] = $row['support_contact'];
$return_data['support_desc'] = $row['support_description'];
$return_data['mft_num'] = $row['mft_part_num'];
$return_data['vendor_num'] = $row['vendor_part_num'];
$return_data['tax'] = $row['tax_class'];

echo json_encode($return_data);	
?>