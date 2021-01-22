<?php
$dictionary['AOS_ProductTemplates']['fields']['manufacturer_id'] = array(
		'name' => 'manufacturer_id',
		'type' => 'id',
		'function' => array('name'=>'getManufacturers', 'returns'=>'html'),
		'required'=>false,
		'reportable'=>false,
		'vname' =>'LBL_LIST_MANUFACTURER_ID',
		'importable' => 'true',
		'comment' => 'Manufacturer of the product'
);
$dictionary['AOS_ProductTemplates']['fields']['manufacturer_name'] =
	array (
		'name' => 'manufacturer_name',
	    'rname'=> 'name',
	    'id_name'=> 'manufacturer_id',
		'type' => 'relate',
		'vname' =>'LBL_MANUFACTURER_NAME',
	    'join_name' => 'manufacturers',
	    'link' => 'manufacturer_link',
	    'table' => 'aos_manufacturers',
	    'isnull' => 'true',
		'source'=>'non-db',
		'module' => 'AOS_Manufacturers',
	    'dbType' => 'varchar',
	    'len' => '255',
		'studio' => 'false'
	);
?>