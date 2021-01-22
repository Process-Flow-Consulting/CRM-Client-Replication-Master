<?php
$layout_defs["Leads"]["subpanel_setup"]['lead_leadclientdetails'] = array (
  'order' => 100,
  'module' => 'oss_LeadClientDetail',
  'subpanel_name' => 'Lead_subpanel_lead_leadclientdetails',
  'sort_order' => 'asc',
  'sort_by' => 'is_viewed',
  //'fill_in_additional_fields'=> true,
  'title_key' => 'LBL_LEADCLIENTDETAILS',
  //'get_subpanel_data' => 'lead_leadclientdetails',
  //custom query to get bidders list
  'get_subpanel_data' => 'function:getLeadBidderListSubpanel',
  'top_buttons' =>
   array (
	0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		),
	1 =>
		array (
			'widget_class' => 'SubPanelMessage',
		),
   ),
);

?>
