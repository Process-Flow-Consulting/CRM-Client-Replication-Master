<?php
$dictionary['AOS_Products']['fields']['type_id'] = array(
    'name' => 'type_id',
    'vname' => 'Type ID',
    'type' => 'id',
    'required'=>false,
    'reportable'=>false,
    'function'=>array('name'=>'getProductTypes', 'returns'=>'html', 'include'=>'modules/AOS_ProductTemplates/AOS_ProductTemplates.php'),
    'comment' => 'Product type (ex: hardware, software)',
);
$dictionary['AOS_Products']['fields']['type_name'] = array(
	'name' => 'type_name',
	'rname' => 'name',
	'id_name' => 'type_id',
	'vname' => 'Type',
	'join_name' => 'types',
	'type' => 'relate',
	'link' => 'product_types_link',
	'table' => 'aos_producttypes',
	'isnull' => 'true',
	'module' => 'AOS_ProductTypes',
	'importable' => 'false',
	'dbType' => 'varchar',
	'len' => '255',
	'source' => 'non-db',
);
$dictionary["AOS_Products"]["fields"]["product_types"] = array (
  'name' => 'product_types',
  'type' => 'link',
  'relationship' => 'product_types',
  'source' => 'non-db',
  'vname' => 'Types',
);
$dictionary["AOS_Products"]["relationships"]["product_types"] = array (
  'lhs_module'=> 'ProductTypes', 
  'lhs_table'=> 'product_types', 
  'lhs_key' => 'id',
  'rhs_module'=> 'AOS_Products', 
  'rhs_table'=> 'aos_products', 
  'rhs_key' => 'type_id',
  'relationship_type'=>'one-to-many'
);

?>