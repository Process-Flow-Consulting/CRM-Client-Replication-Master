<?php
// created: 2011-11-14 11:11:29
$layout_defs["oss_Classification"]["subpanel_setup"]["oss_classifation_accounts"] = array (
  'order' => 100,
  'module' => 'Accounts',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_CLASSIFICATION_ACCOUNTS_FROM_ACCOUNTS_TITLE',
  'get_subpanel_data' => 'oss_classifation_accounts',
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
