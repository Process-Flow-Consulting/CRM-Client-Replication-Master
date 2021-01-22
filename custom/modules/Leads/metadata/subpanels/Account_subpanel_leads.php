<?php
// created: 2019-12-14 06:47:34
$subpanel_layout['list_fields'] = array (
  'name' => 
  array (
    'vname' => 'LBL_LIST_NAME',
    'widget_class' => 'SubPanelDetailViewLink',
    'sort_order' => 'asc',
    'sort_by' => 'last_name',
    'module' => 'Leads',
    'width' => '20%',
    'default' => true,
  ),
  'state' => 
  array (
    'type' => 'enum',
    'vname' => 'LBL_STATE',
    'width' => '10%',
    'default' => true,
  ),
  'bids_due' => 
  array (
    'type' => 'datetimecombo',
    'studio' => 
    array (
      'required' => true,
      'no_duplicate' => true,
    ),
    'vname' => 'LBL_BIDS_DUE',
    'width' => '10%',
    'default' => true,
  ),
  'date_entered' => 
  array (
    'type' => 'datetime',
    'vname' => 'LBL_DATE_ENTERED',
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
  'lead_source' => 
  array (
    'vname' => 'LBL_LIST_LEAD_SOURCE',
    'width' => '13%',
    'default' => true,
  ),
  'edit_button' => 
  array (
    'vname' => 'LBL_EDIT_BUTTON',
    'widget_class' => 'SubPanelEditButton',
    'module' => 'Leads',
    'width' => '4%',
    'default' => true,
  ),
  'remove_button' => 
  array (
    'vname' => 'LBL_REMOVE',
    'widget_class' => 'SubPanelRemoveButton',
    'module' => 'Leads',
    'width' => '4%',
    'default' => true,
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