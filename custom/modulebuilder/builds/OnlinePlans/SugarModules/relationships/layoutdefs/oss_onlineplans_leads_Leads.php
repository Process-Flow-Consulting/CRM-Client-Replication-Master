<?php
 // created: 2019-11-07 14:37:45
$layout_defs["Leads"]["subpanel_setup"]['oss_onlineplans_leads'] = array (
  'order' => 100,
  'module' => 'oss_OnlinePlans',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_ONLINEPLANS_LEADS_FROM_OSS_ONLINEPLANS_TITLE',
  'get_subpanel_data' => 'oss_onlineplans_leads',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);
