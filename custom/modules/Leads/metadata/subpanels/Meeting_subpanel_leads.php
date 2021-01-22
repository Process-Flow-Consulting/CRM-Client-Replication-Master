<?php
// created: 2019-12-14 07:18:28
$subpanel_layout['list_fields'] = array (
  'accept_status_name' => 
  array (
    'vname' => 'LBL_LIST_ACCEPT_STATUS',
    'width' => '11%',
    'sortable' => false,
    'default' => true,
  ),
  'name' => 
  array (
    'vname' => 'LBL_LIST_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'module' => 'Leads',
    'width' => '25%',
    'default' => true,
  ),
  'state' => 
  array (
    'type' => 'enum',
    'vname' => 'LBL_STATE',
    'width' => '10%',
    'default' => true,
  ),
  'lead_source' => 
  array (
    'type' => 'enum',
    'vname' => 'LBL_LEAD_SOURCE',
    'width' => '10%',
    'default' => true,
  ),
  'status' => 
  array (
    'type' => 'enum',
    'vname' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
  'edit_button' => 
  array (
    'vname' => 'LBL_EDIT_BUTTON',
    'widget_class' => 'SubPanelEditButton',
    'module' => 'Leads',
    'width' => '5%',
    'default' => true,
  ),
  'remove_button' => 
  array (
    'vname' => 'LBL_REMOVE',
    'widget_class' => 'SubPanelRemoveButton',
    'module' => 'Leads',
    'width' => '5%',
    'default' => true,
  ),
  'm_accept_status_fields' => 
  array (
    'usage' => 'query_only',
  ),
  'accept_status_id' => 
  array (
    'usage' => 'query_only',
  ),
  'first_name' => 
  array (
    'usage' => 'query_only',
  ),
  'last_name' => 
  array (
    'usage' => 'query_only',
  ),
  'salutation' => 
  array (
    'name' => 'salutation',
    'usage' => 'query_only',
  ),
);