<?php
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
