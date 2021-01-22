<?php
// created: 2013-10-15 13:13:30
$dictionary["AOS_Products"]["fields"]["product_notes"] = array (
  'name' => 'product_notes',
  'type' => 'link',
  'relationship' => 'product_notes',
  'source' => 'non-db',
  'vname' => 'LBL_PRODUCTS_NOTES',
);
$dictionary['AOS_Products']['relationships']['product_notes'] = array(
    'lhs_module'=> 'AOS_Products', 'lhs_table'=> 'aos_products', 'lhs_key' => 'id',
    'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'parent_id',
    'relationship_type'=>'one-to-many'
);

