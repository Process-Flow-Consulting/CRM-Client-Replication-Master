<?php
$layout_defs["Accounts"]["subpanel_setup"]['account_businessintelligence'] = array (
    'order' => 100,
	'module' => 'oss_BusinessIntelligence',
	'subpanel_name' => 'default',
	'sort_order' => 'DESC',
	'sort_by' => 'type_order, my_only',
	'title_key' => 'LBL_BUSSINESSINTELLIGENCE',
	//'get_subpanel_data' => 'account_businessintelligence',
	'get_subpanel_data' => 'function:get_bi_data',
	//'function_parameters' => array('import_function_file' => 'custom/include/common_functions.php', 'link' => 'oss_businessintelligence'),
	//'generate_select'=>true,
	//'get_distinct_data' => true,
	'top_buttons' =>
		array (
			0 =>
			array (
				'widget_class' => 'SubPanelTopCreateButton',
			),
			/*1 =>
			array (
				'widget_class' => 'SubPanelTopSelectButton',
				'mode' => 'MultiSelect',
			),*/
		),
);