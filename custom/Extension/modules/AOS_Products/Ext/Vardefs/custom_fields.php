<?php
$dictionary['AOS_Products']['fields']['product_type']=array(
	'name' => 'product_type',
	'vname' => 'LBL_PRODUCT_TYPE',
	'type' => 'varchar',
	'len' => 100,
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['title_show']=array(
	'name' => 'title_show',
	'vname' => 'LBL_SHOW',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['desc_show']=array(
	'name' => 'desc_show',
	'vname' => 'LBL_SHOW',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['qty_show']=array(
	'name' => 'qty_show',
	'vname' => 'LBL_SHOW',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['price_show']=array(
	'name' => 'price_show',
	'vname' => 'LBL_SHOW',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['total_show']=array(
	'name' => 'total_show',
	'vname' => 'LBL_SHOW',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);


$dictionary['AOS_Products']['fields']['in_hours']=array(
	'name' => 'in_hours',
	'vname' => 'LBL_HOURS',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['in_rates']=array(
	'name' => 'in_rates',
	'vname' => 'LBL_RATES',
	'type' => 'bool',
	'default' => '1',
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['bb_tax']=array(
	'name' => 'bb_tax',
	'vname' => 'LBL_TAX',
	'type' => 'decimal',
	'len' => 11,
	'precision' => 2,
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['bb_tax_per']=array(
	'name' => 'bb_tax_per',
	'vname' => 'LBL_TAX',
	'type' => 'decimal',
	'len' => 11,
	'precision' => 2,
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['bb_shipping']=array(
	'name' => 'bb_shipping',
	'vname' => 'LBL_SHIPPING',
	'type' => 'decimal',
	'len' => 11,
	'precision' => 2,
	'audited'=>true,
);


$dictionary['AOS_Products']['fields']['total']=array(
	'name' => 'total',
	'vname' => 'LBL_TOTAL',
	'type' => 'decimal',
	'len' => 25,
	'precision' => 2,
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['unit_price']=array(
	'name' => 'unit_price',
	'vname' => 'LBL_UNIT_PRICE',
	'type' => 'decimal',
	'len' => 15,
	'precision' => 2,
	'audited'=>true,
);

$dictionary['AOS_Products']['fields']['markup_inper']=array(
		'name' => 'markup_inper',
		'vname' => 'LBL_IN_PERCENT',
		'type' => 'bool',
		'default'=> 0,
		'audited'=>true,
);


$dictionary['AOS_Products']['fields']['account_proview_url'] = array (
		'name' => 'account_proview_url',
		'rname' => 'proview_url',
		'id_name' => 'account_id',
		'vname' => 'LBL_ACCOUNT_NAME',
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
);

/* $dictionary['AOS_Products']['fields']['product_catalog_product'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'product_catalog_product',
    'vname' => 'LBL_PRODUCT_CATALOG_PRODUCT_NAME',
    'type' => 'relate',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => false,
    'reportable' => true,
    'len' => '255',
    'size' => '20',
    'id_name' => 'product_template_id',
    'ext2' => 'ProductTemplates',
    'module' => 'ProductTemplates',
    'rname' => 'name',
    'quicksearch' => 'enabled',
    'studio' => 'visible',
); */
/* $dictionary["AOS_Products"]["fields"]["product_product_template_var"]= array (
	'name' => 'product_product_template_var',
	'type' => 'link',
	'relationship' => 'product_product_template_rel',
	'source' => 'non-db',
	'vname' => 'LBL_PRODUCT_CATALOG_PRODUCT',
); */
/* $dictionary['AOS_Products']['relationships']['product_product_template_rel'] = array(
	'lhs_module'=> 'AOS_Products',
	 'lhs_table'=> 'aos_products',
	 'lhs_key' => 'product_template_id',
	 'rhs_module'=> 'ProductTemplates', 
	 'rhs_table'=> 'product_templates', 
	 'rhs_key' => 'id', 
	 'relationship_type'=>'one-to-many',
); */
$dictionary["AOS_Products"]["fields"]["name"]["audited"] = true;
$dictionary["AOS_Products"]["fields"]["description"]["audited"] = true;
$dictionary["AOS_Products"]["fields"]["product_template_id"]["audited"] = true;

$dictionary['AOS_Products']['fields']['unit_measure'] = array (
        'name' => 'unit_measure',
        'vname' => 'LBL_UNIT_MEASURE',
        'required' => false,
        'type' => 'id',
        'comment' => ''
);
$dictionary['AOS_Products']['fields']['unit_measure_name'] = array (
        'name' => 'unit_measure_name',
        'rname' => 'name',
        'id_name' => 'unit_measure',
        'vname' => 'LBL_UNIT_MEASURE_NAME',
        'type' => 'relate',
        'module' => 'oss_UnitOfMeasure',
        'len' => '255',
        'source'=>'non-db',
        'unified_search' => true,
        'required' => false,
        'importable' => true,
        'reportable' => true,
        'massupdate' => 0,
        'ext2' => 'oss_UnitOfMeasure',
);
$dictionary['AOS_Products']['fields']['quickbooks_id'] = array (
        'required' => false,
        'name' => 'quickbooks_id',
        'vname' => 'LBL_QUICKBOOK_ID',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook Id',
        'help' => 'Quickbook Id',
        'importable' => 'true',
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '40',
        'size' => '40',
);
$dictionary['AOS_Products']['fields']['discount_amount_usdollar'] = array (
    'name' => 'discount_amount_usdollar',
    'vname' => 'LBL_DISCOUNT_RATE_USDOLLAR',
    'type' => 'decimal',
    'len' => '26,6',
  	'studio' => array('editview' => false), 
);
$dictionary['AOS_Products']['fields']['description']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['quantity']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['unit_measure']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['total']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['serial_number']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['tax_class']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['cost_price']['qbimport'] = true;

?>
