<?php
// BB  -- start
$dictionary['AOS_ProductTemplates']['fields']['markup'] = array (
// BB  -- End
		'name' => 'markup',
		'vname' => 'LBL_MARKUP',
		'required' => false,
		'type' => 'currency',
		'len' => '26,6',
		'importable' => 'required',		
		'comment' => 'Mark Up of product'
);

$dictionary['AOS_ProductTemplates']['fields']['markup_inper']=array(
		'name' => 'markup_inper',
		'vname' => 'LBL_IN_PERCENT',
		'type' => 'bool',
		'default'=> 0,
		'audited'=>true,
);

$dictionary['AOS_ProductTemplates']['fields']['quantity'] = array (
		'name' => 'quantity',
		'vname' => 'LBL_QUANTITY_PROPOSAL',
		'required' => false,
		'type' => 'int',
		'len' => '11',
		'importable' => 'required',
		'comment' => 'Quantity of product'
);

$dictionary['AOS_ProductTemplates']['fields']['total_cost'] = array (
		'name' => 'total_cost',
		'vname' => 'LBL_TOTAL_COST_PRODUCT',
		'required' => false,
		'type' => 'currency',
		'len' => '26,6',
		'importable' => 'required',
		'comment' => ''
);
$dictionary['AOS_ProductTemplates']['fields']['unit_measure'] = array (
        'name' => 'unit_measure',
        'vname' => 'LBL_UNIT_MEASURE',
        'required' => false,
        'importable' => true,
        'type' => 'id',
        'comment' => ''
);
$dictionary['AOS_ProductTemplates']['fields']['editsequence'] = array (
        'required' => false,
        'name' => 'editsequence',
        'vname' => 'LBL_QUICKBOOK_EDITSEQUENCE',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook EditSequence',
        'help' => 'Quickbook EditSequence',
        'importable' => 'true',
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
        'len' => '10',
        'size' => '10',
);
$dictionary['AOS_ProductTemplates']['fields']['quickbooks_id'] = array (
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
$dictionary['AOS_ProductTemplates']['fields']['push_to_qb'] = array (
        'required' => false,
        'name' => 'push_to_qb',
        'vname' => 'LBL_PUSH_TOQUICKBOOK',
        'type' => 'bool',
        'massupdate' => 1,
        'comments' => 'Push to Quickbook flag',
        'help' => 'Push to Quickbook flag',
        'importable' => 'false',
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
         
);
$dictionary['AOS_ProductTemplates']['indices'][] = array(
        'name' =>'idx_quickbooks_id0',
        'type'=>'unique',
        'fields'=>array('quickbooks_id')
);
$dictionary['AOS_ProductTemplates']['indices'][] = array(
        'name' =>'idx_quickbooks_id1',
        'type'=>'index',
        'fields'=>array('quickbooks_id'),
        'source'=>'non-db'
);
$dictionary['AOS_ProductTemplates']['fields']['unit_measure_name'] = array (
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
        'importable' => false,
        'reportable' => true,
        'massupdate' => 0,
        'ext2' => 'oss_UnitOfMeasure',
);
$dictionary['AOS_ProductTemplates']['fields']['quickbook_type'] = array (
        'required' => false,
        'name' => 'quickbook_type',
        'vname' => 'LBL_QUICKBOOK_TYPE',
        'type' => 'enum',
        'massupdate' => 0,
        'comments' => 'Quickbook Type',
        'help' => 'Quickbook Type',
        'importable' => true,
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '50',
		'options' => 'quickbook_type_dom',
);
$dictionary['AOS_ProductTemplates']['fields']['qb_sale_purchse_type'] = array (
        'required' => false,
        'name' => 'qb_sale_purchse_type',
        'vname' => 'LBL_QB_SALE_PURCHASE_TYPE',
        'type' => 'bool',
        'massupdate' => 1,
        'comments' => 'Sale Purchase type for non inventory of qb: 0 for sales and purchase, 1 for sales or purchase',
        'help' => 'Sale Purchase type for non inventory of qb',
        'importable' => false,
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
);
$dictionary['AOS_ProductTemplates']['fields']['quickbook_assets_account'] = array (
		'required' => true	,
		'name' => 'quickbook_assets_account',
		'vname' => 'LBL_QUICKBOOK_ASSESTS_ACCOUNT',
		'type' => 'enum',
		'massupdate' => 0,
		'comments' => 'Quickbook Assest Account',
		'help' => 'Quickbook Assest Account',
		'importable' => true,
		'duplicate_merge' => 'disabled',
		'audited' => true,
		'reportable' => false,
		'len' => '255',
		'function' => 'getQBAccount'
);
$dictionary['AOS_ProductTemplates']['fields']['quickbook_cogs_account'] = array (
		'required' => true	,
		'name' => 'quickbook_cogs_account',
		'vname' => 'LBL_QUICKBOOK_COGS_ACCOUNT',
		'type' => 'enum',
		'massupdate' => 0,
		'comments' => 'Quickbook COGS Account',
		'help' => 'Quickbook COGS Account',
		'importable' => true,
		'duplicate_merge' => 'disabled',
		'audited' => true,
		'reportable' => false,
		'len' => '255',
		'function' => 'getQBAccount'
);
$dictionary['AOS_ProductTemplates']['fields']['quickbook_income_account'] = array (
		'required' => true	,
		'name' => 'quickbook_income_account',
		'vname' => 'LBL_QUICKBOOK_INCOME_ACCOUNT',
		'type' => 'enum',
		'massupdate' => 0,
		'comments' => 'Quickbook Income Account',
		'help' => 'Quickbook Income Account',
		'importable' => true,
		'duplicate_merge' => 'disabled',
		'audited' => true,
		'reportable' => false,
		'len' => '255',
		'function' => 'getQBAccount'
);
$dictionary['AOS_ProductTemplates']['fields']['quickbook_expense_account'] = array (
		'required' => true	,
		'name' => 'quickbook_expense_account',
		'vname' => 'LBL_QUICKBOOK_EXPENSE_ACCOUNT',
		'type' => 'enum',
		'massupdate' => 0,
		'comments' => 'Quickbook Expense Account',
		'help' => 'Quickbook Expense Account',
		'importable' => true,
		'duplicate_merge' => 'disabled',
		'audited' => true,
		'reportable' => false,
		'len' => '255',
		'function' => 'getQBAccount'
);


$dictionary['AOS_ProductTemplates']['fields']['manufacturer_id']=array(
	'name' => 'manufacturer_id',
    'vname' => 'LBL_MANUFACTURER',
    'type' => 'id',
    'required'=>false,
    'reportable'=>false,
    'function'=>array('name'=>'getManufacturers', 'returns'=>'html', 'include'=>'modules/AOS_ProductTemplates/AOS_ProductTemplates.php'),
    'comment' => 'Manufacturer of product',
);

$dictionary['AOS_ProductTemplates']['fields']['name']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['mft_part_num']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['unit_measure']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['tax_class']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['description']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['discount_price']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['quickbook_income_account']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['quickbook_assets_account']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['quickbook_cogs_account']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['quickbook_expense_account']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['qty_in_stock']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['quantity']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['category_name']['qbimport'] = true;
$dictionary['AOS_ProductTemplates']['fields']['cost_price']['qbimport'] = true;
?>
