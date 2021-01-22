<?php

$layout_defs["Opportunities"]["subpanel_setup"]["opportunity_to_opportunity"] = array (
  'order' => 100,
  'module' => 'Opportunities',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OPPORTUNITY_SUBPANEL_TITLE',
  // BBSMP  -- Start
  'get_subpanel_data' => 'function:opportunity_to_opportunity_relate',
  // BBSMP  -- End
  'top_buttons' => 
  array (
   0 =>
   array (
     'widget_class' => 'SubPanelTopButtonQuickCreate',
   ),
   /* 1 =>
   array (
     'widget_class' => 'SubPanelTopSelectButton',
     'mode' => 'MultiSelect',
   ), */
  ),
);

?>

