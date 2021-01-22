<?php
// created: 2011-11-14 11:11:29
$layout_defs["Accounts"]["subpanel_setup"]["oss_classifation_accounts"] = array (
  'order' => -1004,
  'module' => 'oss_Classification',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_CLASSIFICATION_ACCOUNTS_FROM_OSS_CLASSIFICATION_TITLE',
  'get_subpanel_data' => 'oss_classifation_accounts',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);


$layout_defs['Accounts']['subpanel_setup']['opportunities']['top_buttons'] = array( array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ));