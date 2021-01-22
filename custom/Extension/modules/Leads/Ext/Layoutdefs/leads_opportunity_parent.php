<?php
$layout_defs["Leads"]["subpanel_setup"]["lead_to_opportunity_var_parent"] =  array(
	'order' => 90,
	'module' => 'Opportunities',
	'subpanel_name' => 'Lead_subpanel_lead_to_opportunity_var_parent',
	'get_subpanel_data' => 'lead_to_opportunity_var',
	//need to apply filters hence custom SQL
	//'get_subpanel_data' => 'function:getParentOpportunitiesSubpanel',
	'title_key' => 'LBL_PARENT_OPPORTUNITY_SUBPANEL_TITLE',
	'top_buttons' =>
	array (
	/* 0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		), */
   ),
);
?>
