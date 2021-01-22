<?php
define("sugarEntry", true);
require_once('include/entryPoint.php');
global $db; 
$term= $_REQUEST['term'];
$selQuery="select id, name, description, discount_price, markup, markup_inper, quantity, unit_measure, tax_class from aos_producttemplates where name like '".$term."%' and deleted = 0 order by name asc limit 0,10";
$res=$db->query($selQuery);
$data = array();
while($row = $db->fetchByAssoc($res))
{
	$unit_measure_name = '';
	if(!empty($row['unit_measure'])){
		$UnitOfMeasure = new oss_UnitOfMeasure();
		$UnitOfMeasure->retrieve($row['unit_measure']);
		$unit_measure_name = $UnitOfMeasure->name;
	}
	
     $data[] = array(
            'value' => $row['name'],
            'id' => $row['id'],
            'description' => $row['description'],
            'cost_price' => $row['cost_price'],
            'discount_price' => $row['discount_price'],
            'markup' => $row['markup'],
            'markup_inper' => $row['markup_inper'],
            'quantity' => $row['quantity'],
            'unit_measure' => $row['unit_measure'],
            'unit_measure_name' => $unit_measure_name,
            'tax_class' => $row['tax_class'],
        );
}

echo json_encode($data);
flush(); 
?>