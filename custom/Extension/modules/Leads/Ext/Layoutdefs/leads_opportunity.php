<?php
$layout_defs["Leads"]["subpanel_setup"]["lead_to_opportunity_var"] =  array(
  'order' => 100,
  'module' => 'Opportunities',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'fill_in_additional_fields'=> true,
  'title_key' => 'LBL_OPPORTUNITY_SUBPANEL_TITLE',
  'subpanel_name' => 'Lead_subpanel_lead_to_opportunity_var',
  //'get_subpanel_data' => 'lead_to_opportunity_var',
  'get_subpanel_data' => 'function:getSubOpportunitiesSubpanel',
  'top_buttons' =>
   array (
	/* 0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		), */
   ),
);

?>
