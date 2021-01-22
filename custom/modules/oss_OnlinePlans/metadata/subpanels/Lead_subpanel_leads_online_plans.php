<?php
// created: 2019-12-03 06:10:11
$subpanel_layout['list_fields'] = array (
  'plan_type' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'vname' => 'LBL_PLAN_TYPE',
    'width' => '10%',
  ),
  'plan_source' => 
  array (
    'type' => 'varchar',
    'vname' => 'LBL_PLAN_SOURCE',
    'width' => '10%',
    'default' => true,
  ),
  'last_reviewed_date' => 
  array (
    'type' => 'datetimecombo',
    'vname' => 'LBL_LAST_REVIEWED_DATE',
    'width' => '10%',
    'default' => true,
  ),
  'description' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'vname' => 'LBL_DESCRIPTION',
    'sortable' => false,
    'width' => '10%',
    'default' => true,
  ),
  'edit_button' => 
  array (
    'vname' => 'LBL_EDIT_BUTTON',
    'widget_class' => 'SubPanelEditButton',
    'module' => 'oss_OnlinePlans',
    'width' => '4%',
    'default' => true,
  ),
  'remove_button' => 
  array (
    'vname' => 'LBL_REMOVE',
    'widget_class' => 'SubPanelRemoveButton',
    'module' => 'oss_OnlinePlans',
    'width' => '5%',
    'default' => true,
  ),
);