<?php
$layout_defs["Leads"]["subpanel_setup"]["leads_online_plans"] =  array(
  'order' => 90,
  'module' => 'oss_OnlinePlans',
  'subpanel_name' => 'default',
  'title_key' => 'LBL_LEADS_ONLINE_PLAN_SUBPANEL_TITLE',
  'get_subpanel_data' => 'function:get_leads_online_plans',
  'top_buttons' =>
   array (
	0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		),
   ),
);

?>