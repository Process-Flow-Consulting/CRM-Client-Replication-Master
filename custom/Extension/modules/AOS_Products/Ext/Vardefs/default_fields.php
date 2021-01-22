<?php
$dictionary['AOS_Products']['fields']['weight']=array(
	'name' => 'weight',
    'vname' => 'LBL_WEIGHT',
    'type' => 'decimal',
    'len' => '12,2',
    'precision' => 2,
    'comment' => 'Weight of the product',
);
$dictionary['AOS_Products']['fields']['product_template_id']=array(
	 'name' => 'product_template_id',
    'type' => 'id',
    'vname' => 'LBL_PRODUCT_TEMPLATE_ID',
    'required'=>false,
    'reportable'=>false,
    'comment' => 'Product (in Admin Products) from which this product is derived (in user Products)',
);

$dictionary['AOS_Products']['fields']['aos_product_category_id']=array(
	'name' => 'aos_product_category_id',
    'vname' => 'LBL_CATEGORY',
    'type' => 'id',
    'group'=>'aos_product_category_name',
    'required'=>false,
    'reportable'=>true,
    'function'=>array('name'=>'getCategories', 'returns'=>'html', 'include'=>'modules/AOS_ProductTemplates/AOS_ProductTemplates.php'),
    'comment' => 'Product category',
);

$dictionary['AOS_Products']['fields']['manufacturer_id']=array(
	'name' => 'manufacturer_id',
    'vname' => 'LBL_MANUFACTURER',
    'type' => 'id',
    'required'=>false,
    'reportable'=>false,
    'function'=>array('name'=>'getManufacturers', 'returns'=>'html', 'include'=>'modules/AOS_ProductTemplates/AOS_ProductTemplates.php'),
    'comment' => 'Manufacturer of product',
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
    'function'=>array('name'=>'getSupportTerms', 'returns'=>'html', 'include'=>'modules/AOS_ProductTemplates/AOS_ProductTemplates.php'),
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
$dictionary["AOS_Products"]["fields"]["discount_select"] = array(
	'name' => 'discount_select',
    'vname' => 'LBL_SELECT_DISCOUNT',
    'type' => 'bool',
    'reportable'=>false,
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
$dictionary['AOS_Products']['fields']['indexs']=array(
	'name' => 'indexs',
    'vname' => 'LBL_INDEXS',
    'type' => 'int',
	'len'=>5,
);