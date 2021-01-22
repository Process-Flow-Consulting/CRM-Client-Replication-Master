<?php
$dictionary['AOS_Quotes']['fields']['taxrate_id'] = array(
    'name' => 'taxrate_id',
    'vname' => 'LBL_TAXRATE_ID',
    'type' => 'id',
    'required'=>false,
    'do_report'=>false,
    'reportable'=>false,
);
$dictionary['AOS_Quotes']['fields']['subtotal'] = array(
    'name' => 'subtotal',
    'vname' => 'LBL_SUBTOTAL',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar'] = array(
    'name' => 'subtotal_usdollar',
    'group'=>'subtotal',
    'vname' => 'LBL_SUBTOTAL_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
);
$dictionary['AOS_Quotes']['fields']['tax'] = array(
    'name' => 'tax',
    'vname' => 'LBL_TAX',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['tax_usdollar'] = array(
    'name' => 'tax_usdollar',
    'vname' => 'LBL_TAX_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'tax',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
);
$dictionary['AOS_Quotes']['fields']['shipping'] = array(
     'name' => 'shipping',
    'vname' => 'LBL_SHIPPING',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['shipping_usdollar'] = array(
   'name' => 'shipping_usdollar',
    'vname' => 'LBL_SHIPPING_USDOLLAR',
    'group'=>'shipping',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['total'] = array(
    'name' => 'total',
    'vname' => 'LBL_TOTAL',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['total_usdollar'] = array(
    'name' => 'total_usdollar',
    'vname' => 'LBL_TOTAL_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'total',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
  	'enable_range_search' => true,
  	'options' => 'numeric_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['show_line_nums'] = array(
    'name' => 'show_line_nums',
    'vname' => 'LBL_SHOW_LINE_NUMS',
    'type' => 'bool',
    'default'=>true,
    'reportable'=>false,
    'massupdate'=>false,
	
);
$dictionary['AOS_Quotes']['fields']['calc_grand_total'] = array(
    'name' => 'calc_grand_total',
    'vname' => 'LBL_CALC_GRAND',
    'type' => 'bool',
    'reportable'=>false,
    'default'=>true,
    'massupdate' => false,
);
$dictionary['AOS_Quotes']['fields']['quote_type'] = array(
	'name' => 'quote_type',
	'vname' => 'LBL_QUOTE_TYPE',
	'type' => 'varchar',
	'len' => 100,
);
$dictionary['AOS_Quotes']['fields']['date_quote_expected_closed'] = array(
	'name' => 'date_quote_expected_closed',
    'vname' => 'LBL_DATE_QUOTE_EXPECTED_CLOSED',
    'type' => 'date',
    'audited'=>true,
    'reportable'=>true,
    'importable' => 'required',
    'required'=>true,
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['original_po_date'] = array(
	'name' => 'original_po_date',
    'vname' => 'LBL_ORIGINAL_PO_DATE',
    'type' => 'date',
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['payment_terms'] = array(
	'name' => 'payment_terms',
    'vname' => 'LBL_PAYMENT_TERMS',
    'type' => 'enum',
    'options' => 'payment_terms',
    'len' => '128',
);
$dictionary['AOS_Quotes']['fields']['date_quote_closed'] = array(
	'name' => 'date_quote_closed',
    'massupdate' => false,
    'vname' => 'LBL_DATE_QUOTE_CLOSED',
    'type' => 'date',
    'reportable'=>false,
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['date_order_shipped'] = array(
	'name' => 'date_order_shipped',
    'massupdate' => false,
    'vname' => 'LBL_LIST_DATE_QUOTE_CLOSED',
    'type' => 'date',
    'reportable' => false,
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['order_stage'] = array(
	'name' => 'order_stage',
    'vname' => 'LBL_ORDER_STAGE',
    'type' => 'enum',
    'options' => 'order_stage_dom',
    'massupdate'=>false,
    'len' => 100,
);
$dictionary['AOS_Quotes']['fields']['quote_stage'] = array(
	'name' => 'quote_stage',
    'vname' => 'LBL_QUOTE_STAGE',
    'type' => 'enum',
    'options' => 'quote_stage_dom',
    'len' => 100,
    'audited'=>true,
    'importable' => 'required',
    'required'=>true,
);
$dictionary['AOS_Quotes']['fields']['quote_num'] = array(
	'name' => 'quote_num',
    'vname' => 'LBL_QUOTE_NUM',
    'type' => 'int',
    'required'=>true,
    'unified_search' => true,
    'full_text_search' => array('boost' => 3),
    'options' => 'numeric_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar'] = array(
	'name' => 'subtotal_usdollar',
    'group'=>'subtotal',
    'vname' => 'LBL_SUBTOTAL_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
);
$dictionary['AOS_Quotes']['fields']['discount'] = array(
	'name' => 'discount',
    'vname' => 'LBL_DISCOUNT_TOTAL',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['deal_tot_usdollar'] = array(
	'name' => 'deal_tot_usdollar',
    'vname' => 'LBL_DEAL_TOT_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'decimal',
    'len' => '26,2',
);
$dictionary['AOS_Quotes']['fields']['deal_tot'] = array(
	'name' => 'deal_tot',
    'vname' => 'LBL_DEAL_TOT',
    'dbType' => 'decimal',
    'type' => 'decimal',
    'len' => '26,2',
);
$dictionary['AOS_Quotes']['fields']['new_sub'] = array(
	'name' => 'new_sub',
    'vname' => 'LBL_NEW_SUB',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['new_sub_usdollar'] = array(
	'name' => 'new_sub_usdollar',
    'vname' => 'LBL_NEW_SUB',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['system_id'] = array(
	'name' => 'system_id',
    'vname' => 'LBL_SYSTEM_ID',
    'type' => 'int',
); 

