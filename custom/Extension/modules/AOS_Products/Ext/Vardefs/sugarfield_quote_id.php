<?php
$dictionary['AOS_Products']['fields']['quote_id'] = array(
   'name' => 'quote_id',
    'type' => 'id',
    'vname' => 'LBL_QUOTE_ID',
    'required'=>false,
    'reportable'=>false,
    'comment' => 'If product created via Quote, this is quote ID',
);
$dictionary['AOS_Products']['fields']['quote_name'] = array(
	'name' => 'quote_name',
    'rname' => 'name',
    'id_name' => 'quote_id',
    'join_name' => 'quotes',
    'type' => 'relate',
    'link' => 'quotes',
    'table' => 'aos_quotes',
    'isnull' => 'true',
    'module' => 'AOS_Quotes',
    'dbType' => 'varchar',
    'len' => '255',
    'vname' => 'Quote Name',
    'source'=>'non-db',
    'comment' => 'Quote Name',
);
$dictionary["AOS_Products"]["fields"]["quote_products"] = array (
  'name' => 'quote_products',
  'type' => 'link',
  'relationship' => 'quote_products',
  'source' => 'non-db',
  'vname' => 'Quotes',
);
$dictionary["AOS_Products"]["relationships"]["quote_products"] = array (
  'lhs_module'=> 'AOS_Quotes', 
  'lhs_table'=> 'aos_quotes', 
  'lhs_key' => 'id',
  'rhs_module'=> 'AOS_Products', 
  'rhs_table'=> 'aos_products', 
  'rhs_key' => 'quote_id',
  'relationship_type'=>'one-to-many'
);

?>