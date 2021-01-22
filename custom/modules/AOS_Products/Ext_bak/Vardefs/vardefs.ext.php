<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['AOS_Products']['fields']['team_set_id'] = array(
    'name' => 'team_set_id',
    'vname' => 'LBL_TEAM_SET_ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
    'size' => '20',
);

$dictionary['AOS_Products']['fields']['team_id'] = array(
    'name' => 'team_id',
    'vname' => 'LBL_TEAM_ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
    'size' => '20',
);

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
$dictionary['AOS_Products']['fields']['description']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['quantity']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['unit_measure']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['total']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['serial_number']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['tax_class']['qbimport'] = true;
$dictionary['AOS_Products']['fields']['cost_price']['qbimport'] = true;




$dictionary['AOS_Products']['fields']['weight']=array(
	'name' => 'weight',
    'vname' => 'LBL_WEIGHT',
    'type' => 'decimal',
    'len' => '12,2',
    'precision' => 2,
    'comment' => 'Weight of the product',
);

$dictionary["AOS_Products"]["fields"]["book_value_date"] = array (
	'name' => 'book_value_date',
    'vname' => 'LBL_BOOK_VALUE_DATE',
    'type' => 'date',
    'comment' => 'Date of book value for product in use',
);
$dictionary["AOS_Products"]["fields"]["date_support_starts"] = array (
	'name' => 'date_support_starts',
    'vname' => 'LBL_DATE_SUPPORT_STARTS',
    'type' => 'date',
    'comment' => 'Support start date',
);
$dictionary["AOS_Products"]["fields"]["date_support_expires"] = array (
	'name' => 'date_support_expires',
    'vname' => 'LBL_DATE_SUPPORT_EXPIRES',
    'type' => 'date',
    'comment' => 'Support expiration date',
);

$dictionary["AOS_Products"]["fields"]["date_purchased"] = array (
	'name' => 'date_purchased',
    'vname' => 'LBL_DATE_PURCHASED',
    'type' => 'date',
    'comment' => 'Date product purchased',
);

$dictionary["AOS_Products"]["fields"]["status"] = array(
	'name' => 'status',
    'vname' => 'LBL_STATUS',
    'type' => 'enum',
    'options' => 'product_status_dom',
    'len' => 100,
    'audited'=>true,
    'comment' => 'Product status (ex: Quoted, Ordered, Shipped)',
);

$dictionary["AOS_Products"]["fields"]["tax_class"] = array(
	'name' => 'tax_class',
	'vname' => 'LBL_TAX_CLASS',
	'type' => 'enum',
	'options' => 'tax_class_dom',
	'len' => 100,
	'comment' => 'Tax classification (ex: Taxable, Non-taxable)',
);
$dictionary["AOS_Products"]["fields"]["asset_number"] = array(
	'name' => 'asset_number',
    'vname' => 'LBL_ASSET_NUMBER',
    'type' => 'varchar',
    'len' => '50',
    'comment' => 'Asset tag number of product in use',
);
$dictionary["AOS_Products"]["fields"]["discount_amount"] = array(
	'name' => 'discount_amount',
    'vname' => 'LBL_DISCOUNT_RATE',
    'type' => 'decimal',
    'options' => 'discount_amount_class_dom',
    'len' => '26,6',
    'precision' => 6,
    'comment' => 'Discounted amount',
);
$dictionary["AOS_Products"]["fields"]["mft_part_num"] = array(
	'name' => 'mft_part_num',
    'vname' => 'LBL_MFT_PART_NUM',
    'type' => 'varchar',
    'len' => '50',
    'comment' => 'Manufacturer part number',
);
$dictionary["AOS_Products"]["fields"]["pricing_formula"] = array(
	'name' => 'pricing_formula',
    'vname' => 'LBL_PRICING_FORMULA',
    'type' => 'varchar',
    'len' => 100,
    'comment' => 'Pricing formula (ex: Fixed, Markup over Cost)',
);
$dictionary["AOS_Products"]["fields"]["serial_number"] = array(
	'name' => 'serial_number',
    'vname' => 'LBL_SERIAL_NUMBER',
    'type' => 'varchar',
    'len' => '50',
    'comment' => 'Serial number of product in use',
);
$dictionary["AOS_Products"]["fields"]["support_contact"] = array(
	'name' => 'support_contact',
    'vname' => 'LBL_SUPPORT_CONTACT',
    'type' => 'varchar',
    'len' => '50',
    'comment' => 'Contact for support purposes',
);
$dictionary["AOS_Products"]["fields"]["support_description"] = array(
	'name' => 'support_description',
    'vname' => 'LBL_SUPPORT_DESCRIPTION',
    'type' => 'varchar',
    'len' => '255',
    'comment' => 'Description of product for support purposes',
);
$dictionary["AOS_Products"]["fields"]["support_term"] = array(
	 'name' => 'support_term',
    'vname' => 'LBL_SUPPORT_TERM',
    'type' => 'varchar',
    'len' => 100,
    'function'=>array('name'=>'getSupportTerms', 'returns'=>'html', 'include'=>'modules/ProductTemplates/ProductTemplate.php'),
    'comment' => 'Term (length) of support contract',
);
$dictionary["AOS_Products"]["fields"]["support_name"] = array(
	'name' => 'support_name',
    'vname' => 'LBL_SUPPORT_NAME',
    'type' => 'varchar',
    'len' => '50',
    'comment' => 'Name of product for support purposes',
);
$dictionary["AOS_Products"]["fields"]["website"] = array(
	'name' => 'website',
    'vname' => 'LBL_URL',
    'type' => 'varchar',
    'len' => '255',
    'comment' => 'Product URL',
);
$dictionary["AOS_Products"]["fields"]["vendor_part_num"] = array(
	'name' => 'vendor_part_num',
    'vname' => 'LBL_VENDOR_PART_NUM',
    'type' => 'varchar',
    'len' => '50',
    'comment' => 'Vendor part number',
);
$dictionary['AOS_Products']['fields']['cost_price']=array(
	'name' => 'cost_price',
    'vname' => 'LBL_COST_PRICE',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
    'comment' => 'Product cost ("Cost" in Quote)',
);

$dictionary['AOS_Products']['fields']['discount_price']=array(
	'name' => 'discount_price',
    'vname' => 'LBL_DISCOUNT_PRICE',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
    'comment' => 'Discounted price ("Unit Price" in Quote)',
);

$dictionary['AOS_Products']['fields']['deal_calc']=array(
	'name' => 'deal_calc',
    'vname' => 'LBL_DISCOUNT_TOTAL',
    'type' => 'currency',
    'len' => '26,6',
    'group'=>'deal_calc',
    'comment' => 'deal_calc',
    'customCode' => '{$fields.currency_symbol.value}{$fields.deal_calc.value}&nbsp;',
);

$dictionary['AOS_Products']['fields']['deal_calc_usdollar']=array(
	'name' => 'deal_calc_usdollar',
    'vname' => 'LBL_DISCOUNT_TOTAL_USDOLLAR',
    'type' => 'currency',
    'len' => '26,6',
    'group'=>'deal_calc',
    'comment' => 'deal_calc_usdollar',
  	'studio' => array('editview' => false),
);

$dictionary['AOS_Products']['fields']['list_price']=array(
	'name' => 'list_price',
    'vname' => 'LBL_LIST_PRICE',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
    'comment' => 'List price of product ("List" in Quote)',
);
$dictionary['AOS_Products']['fields']['discount_usdollar']=array(
	'name' => 'discount_usdollar',
    'vname' => 'LBL_DISCOUNT_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'discount_price',
    'type' => 'currency',
    'len' => '26,6',
    'comment' => 'Discount price expressed in USD',
  	'studio' => array('editview' => false),
);
$dictionary['AOS_Products']['fields']['list_usdollar']=array(
	'name' => 'list_usdollar',
    'vname' => 'LBL_LIST_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'currency',
    'group'=>'list_price',
    'len' => '26,6',
    'comment' => 'List price expressed in USD',
  	'studio' => array('editview' => false),
);

$dictionary['AOS_Products']['fields']['book_value']=array(
	 'name' => 'book_value',
    'vname' => 'LBL_BOOK_VALUE',
    'type' => 'currency',
    'len' => '26,6',
    'comment' => 'Book value of product in use',
);
$dictionary['AOS_Products']['fields']['book_value_usdollar']=array(
	'name' => 'book_value_usdollar',
    'vname' => 'LBL_BOOK_VALUE_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'book_value',
    'type' => 'currency',
    'len' => '26,6',
    'comment' => 'Book value expressed in USD',
    'studio' => array('editview' => false),
);

$dictionary['AOS_Products']['fields']['quantity']=array(
	'name' => 'quantity',
    'vname' => 'LBL_QUANTITY',
    'type' => 'int',
    'len'=>5,
    'comment' => 'Quantity in use',
);

$dictionary['AOS_Products']['fields']['pricing_factor']=array(
	'name' => 'pricing_factor',
    'vname' => 'LBL_PRICING_FACTOR',
    'type' => 'int',
    'group'=>'pricing_formula',
    'len' => '4',
    'comment' => 'Variable pricing factor depending on pricing_formula',
);

?>