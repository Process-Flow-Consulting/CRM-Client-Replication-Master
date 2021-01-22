<?php
// created: 2011-12-13 19:39:33
$subpanel_layout['where'] = 'opportunities.parent_opportunity_id is not NULL';
$subpanel_layout['list_fields'] = array (
  'name' => 
  array (
    'name' => 'name',
    'vname' => 'LBL_LIST_OPPORTUNITY_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'width' => '40%',
    'default' => true,
  ),
		
		
	'lcd_account' =>
	array (
		'type' => 'relate',
		'studio' => 'visible',
		'vname' => 'LBL_LIST_ACCOUNT_NAME',
		'width' => '31%',
		'sortable' => false,
		'default' => true,
		'widget_class' => 'SubPanelDetailViewLinkBidder',
		'varname' => 'account_name',
		'target_module' => 'Accounts',
		'target_record_key' => 'account_id',
	),
	'account_proview_url' =>
	array (
		'name' => 'account_proview_url',
		'usage' => 'query_only',
	),
		
		
  'sales_stage' => 
  array (
    'name' => 'sales_stage',
    'vname' => 'LBL_LIST_SALES_STAGE',
    'width' => '15%',
    'default' => true,
  ),
  'bid_due_timezone' => 
  array (
    'name' => 'bid_due_timezone',
    'usage' => 'query_only',
  ),
  'date_closed' => 
  array (
    'name' => 'date_closed',
    'usage' => 'query_only',
  ),
  'date_closed_tz' => 
  array (
    'name' => 'date_closed_tz',
	'vname' => 'LBL_LIST_DATE_CLOSED',
	'width' => '15%',
	'type' => 'char',
	'default' => true,
	'widget_class' => 'SubPanelDisplayBidDueDate',

  ),
  'amount_usdollar' => 
  array (
    'vname' => 'LBL_LIST_AMOUNT_USDOLLAR',
    'width' => '15%',
    'default' => true,
  ),
  'assigned_user_name' => 
  array (
    'name' => 'assigned_user_name',
    'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'target_record_key' => 'assigned_user_id',
    'target_module' => 'Employees',
    'width' => '10%',
    'default' => true,
  ),

  'currency_id' => 
  array (
    'usage' => 'query_only',
  ),
);
?>
