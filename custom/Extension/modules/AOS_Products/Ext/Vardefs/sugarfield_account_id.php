<?php
$dictionary['AOS_Products']['fields']['account_id'] = array(
    'required' => false,
    'name' => 'account_id',
    'vname' => 'Client ID',
    'type' => 'id',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => 0,
    'audited' => false,
    'reportable' => true,
    'len' => 36,
    'size' => '20',
);
$dictionary['AOS_Products']['fields']['account_name'] = array(
    'name' => 'account_name',
	'rname' => 'name',
	'id_name' => 'account_id',
	'vname' => 'Client Name',
	'join_name'=>'accounts',
	'type' => 'relate',
	'link' => 'account_link',
	'table' => 'accounts',
	'isnull' => 'true',
	'module' => 'Accounts',
	'dbType' => 'varchar',
	'len' => '255',
	'source' => 'non-db',
	'unified_search' => true,
	'full_text_search' => array('boost' => 1),
);
$dictionary["AOS_Products"]["fields"]["products_accounts"] = array (
  'name' => 'products_accounts',
  'type' => 'link',
  'relationship' => 'products_accounts',
  'source' => 'non-db',
  'vname' => 'Client',
);
$dictionary["AOS_Products"]["relationships"]["products_accounts"] = array (
  'name' => 'products_accounts',
  'lhs_module'=> 'Accounts', 
  'lhs_table'=> 'accounts', 
  'lhs_key' => 'id',
  'rhs_module'=> 'AOS_Products', 
  'rhs_table'=> 'aos_products', 
  'rhs_key' => 'account_id',
  'relationship_type'=>'one-to-many'
);
?>