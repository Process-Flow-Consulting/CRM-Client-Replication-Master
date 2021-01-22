<?php

$subpanel_layout = array(
	'top_buttons' => array(
		array('widget_class' => 'SubPanelTopCreateButton'),
		array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Opportunities'),
	),

	'where' => '',

	'list_fields' => array(
		'name'=>array(
	 		'name' => 'name',
	 		'vname' => 'LBL_LIST_OPPORTUNITY_NAME',
			'widget_class' => 'SubPanelDetailViewLink',
			'width' => '40%',
		),
			'lcd_account'=>array(
					'vname' => 'LBL_LIST_ACCOUNT_NAME',
					'widget_class' => 'SubPanelDetailViewLinkBidder',
					'module' => 'Accounts',
					'width' => '31%',
					'target_record_key' => 'account_id',
					'target_module' => 'Accounts',
					'varname' => 'dup_account_name',
			),	

			'account_proview_url' =>
			array(
					'name' => 'account_proview_url',
					'usage' => 'query_only'
			),
			'dup_account_name' =>
			array(
					'name' => 'dup_account_name',
					'usage' => 'query_only'
			),
			
		'sales_stage'=>array(
			'name' => 'sales_stage',
			'vname' => 'LBL_LIST_SALES_STAGE',
			'width' => '15%',
		),
		'bid_due_timezone' =>  array (
			'name' => 'bid_due_timezone',
			'usage' => 'query_only',
		  ),
		'date_closed'=>array(
			 'name' => 'date_closed',
			 'usage' => 'query_only',
		),
		'date_closed_tz'=>array(
			'name' => 'date_closed_tz',
			'vname' => 'LBL_LIST_DATE_CLOSED',
			'width' => '15%',
			'type' => 'char',
			'default' => true,
			'widget_class' => 'SubPanelDisplayBidDueDate',
		),
		'amount_usdollar'=>array(
			'vname' => 'LBL_LIST_AMOUNT_USDOLLAR',
			'width' => '15%',
		),
	   	'assigned_user_name' => array (
			'name' => 'assigned_user_name',
		 	'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
		 	'widget_class' => 'SubPanelDetailViewLink',
		 	'target_record_key' => 'assigned_user_id',
			'target_module' => 'Employees',
	    ),
		'edit_button'=>array(
	    	'vname' => 'LBL_EDIT_BUTTON',
			'widget_class' => 'SubPanelEditButton',
		 	'module' => 'Opportunities',
			'width' => '4%',
		),
		'remove_button'=>array(
			'vname' => 'LBL_UNLINK',
			'widget_class' => 'SubPanelUnlinkButton',
		 	'module' => 'Leads',
	 		'width' => '4%',
		),
		'currency_id'=>array(
			'usage'=>'query_only',
		),
	),
);

?>
