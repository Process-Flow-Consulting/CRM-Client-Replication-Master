<?php
$layout_defs["Leads"]["subpanel_setup"]['lead_to_lead_var'] = array (
	'order' => 90,
	//'sort_order' => 'asc',
	//'sort_by' => 'name',
	'module' => 'Leads',
	'subpanel_name' => 'Lead_subpanel_lead_to_lead_var',
	'get_subpanel_data' => 'function:lead_to_lead_sql',
	//'add_subpanel_data' => 'member_id',
	'title_key' => 'LBL_LEAD_SUBPANEL',
	'fill_in_additional_fields'=> true,
	'top_buttons' => 
	  array (
		/* 0 => 
		array (
		  'widget_class' => 'SubPanelTopButtonQuickCreate',
		),
	   1 =>
	   array (
		 'widget_class' => 'SubPanelTopSelectButton',
		 'mode' => 'MultiSelect',
	   ), */
	  ),
);
?>
