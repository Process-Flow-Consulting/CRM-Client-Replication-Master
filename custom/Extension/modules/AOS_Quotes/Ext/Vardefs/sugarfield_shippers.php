<?php
$dictionary['AOS_Quotes']['fields']['shipper_id'] = array(
    'name' => 'shipper_id',
    'vname' => 'LBL_SHIPPER_ID',
    'type' => 'id',
    'required'=>false,
    'do_report'=>false,
    'reportable'=>false,
);
$dictionary['AOS_Quotes']['fields']['shipper_name'] = array(
     'name' => 'shipper_name',
    'rname' => 'name',
    'id_name' => 'shipper_id',
    'join_name' => 'AOS_Shippers',
    'type' => 'relate',
    'link' => 'AOS_Shippers',
    'table' => 'aos_shippers',
    'isnull' => 'true',
    'module' => 'AOS_Shippers',
    'dbType' => 'varchar',
    'len' => '255',
    'vname' => 'LBL_SHIPPING_PROVIDER',
    'source'=>'non-db',
    'comment' => 'Shipper Name'
);
$dictionary["AOS_Quotes"]["fields"]["shippers"] = array (
	'name' => 'shippers',
	'type' => 'link',
	'relationship' => 'shipper_quotes',
	'vname' => 'LBL_SHIPPING_PROVIDER',
	'source'=>'non-db',
);

?>