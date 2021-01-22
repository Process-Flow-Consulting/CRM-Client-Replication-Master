<?php
// created: 2012-01-18 16:04:36
$subpanel_layout['list_fields'] = array (
   'is_viewed' =>
	array('width' => '3%',
		'sortable' => true,
		'vname' => 'LBL_IS_VIEWED',
		'default' => true,
	),   
  'lead_name' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'vname' => 'LBL_LEAD_LEADCLIENTDETAILS_TITLE',
    'width' => '20%',
    'default' => true,
    'widget_class' => 'SubPanelDetailViewLink',
    'target_module' => 'Leads',
    'target_record_key' => 'lead_id',
  ),
  'contact_name' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'vname' => 'LBL_CONTACT_LEADCLIENTDETAILS_TITLE',
    'width' => '20%',
    'default' => true,
    'widget_class' => 'SubPanelDetailViewLink',
    'target_module' => 'Contacts',
    'target_record_key' => 'contact_id',
  ),
  'contact_phone_no' => 
  array (
    'type' => 'varchar',
    'vname' => 'LBL_CONTACT_PHONE_NO',
    'width' => '10%',
    'default' => true,
  ),
  'contact_fax' => 
  array (
    'type' => 'varchar',
    'vname' => 'LBL_CONTACT_FAX',
    'width' => '10%',
    'default' => true,
  ),
  'contact_email' => 
  array (
    'type' => 'varchar',
    'vname' => 'LBL_CONTACT_EMAIL',
    'width' => '10%',
    'default' => true,
  ),
  'role' => 
  array (
    'type' => 'enum',
    'vname' => 'LBL_ROLE',
    'sortable' => false,
    'width' => '10%',
    'default' => true,
  ),
  'view_button' => 
  array (
    'widget_class' => 'SubPanelViewButton',
    'module' => 'oss_LeadClientDetail',
    'width' => '4%',
    'default' => true,
  ), 
  'lead_id' => 
  array (
    'name' => 'lead_id',
    'usage' => 'query_only',
  ),
  'account_id' => 
  array (
    'name' => 'account_id',
    'usage' => 'query_only',
  ),
  'contact_id' => 
  array (
    'name' => 'contact_id',
    'usage' => 'query_only',
  ),
);
?>
