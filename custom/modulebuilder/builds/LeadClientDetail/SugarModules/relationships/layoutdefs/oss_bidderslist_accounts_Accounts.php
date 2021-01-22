<?php
 // created: 2019-11-07 14:36:40
$layout_defs["Accounts"]["subpanel_setup"]['oss_bidderslist_accounts'] = array (
  'order' => 100,
  'module' => 'oss_BiddersList',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_BIDDERSLIST_ACCOUNTS_FROM_OSS_BIDDERSLIST_TITLE',
  'get_subpanel_data' => 'oss_bidderslist_accounts',
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
