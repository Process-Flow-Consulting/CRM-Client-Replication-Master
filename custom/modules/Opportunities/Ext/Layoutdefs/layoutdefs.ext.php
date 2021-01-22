<?php 
 //WARNING: The contents of this file are auto-generated


 // created: 2013-10-15 13:13:30
 /**
  * It is commented for not displaying subpanel of zone in opportunity
  * and in future it might be possible to dsiplay
  * @author Mohit Kumar Gupta
  * @date 15-oct-2013 
  */
 /*
$layout_defs["Opportunities"]["subpanel_setup"]['oss_zone_opportunities_1'] = array (
  'order' => 100,
  'module' => 'oss_Zone',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_ZONE_OPPORTUNITIES_1_FROM_OSS_ZONE_TITLE',
  'get_subpanel_data' => 'oss_zone_opportunities_1',
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
*/


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





 // created: 2012-03-21 12:09:39
/*$layout_defs["Opportunities"]["subpanel_setup"]['opportunities_accounts'] = array (
  'order' => 100,
  'module' => 'Accounts',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OPPORTUNITIES_ACCOUNTS_FROM_ACCOUNTS_TITLE',
  'get_subpanel_data' => 'opportunities_accounts',
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
);*/


$layout_defs["Opportunities"]["subpanel_setup"]['opportunities_contacts_c'] = array (
  'order' => 180,
  'module' => 'Contacts',
  'subpanel_name' => 'ForOpportunities',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OPPORTUNITIES_CONTACTS_TITLE',
  'get_subpanel_data' => 'opportunities_contacts_c',
  'add_subpanel_data' => 'contact_id',
  
  'top_buttons' =>
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
      'initial_filter_fields' => array('account_id' => 'account_id', 'account_name' => 'account_name'),
    ),
  ),
);


$layout_defs['Opportunities']['subpanel_setup']['leads'] = array();
$layout_defs['Opportunities']['subpanel_setup']['contacts'] = array();
$layout_defs['Opportunities']['subpanel_setup']['opportunity_aos_quotes']['top_buttons']=array(
	0 =>
	array (
			'widget_class' => 'SubPanelTopCreateButton',
	),
);




//auto-generated file DO NOT EDIT
$layout_defs['Opportunities']['subpanel_setup']['opportunity_aos_quotes']['override_subpanel_name'] = 'Opportunity_subpanel_opportunity_aos_quotes';

?>