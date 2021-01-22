<?php
$module_name = 'oss_OnlinePlans';
$listViewDefs [$module_name] = 
array (
  'PLAN_TYPE' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_PLAN_TYPE',
    'width' => '10%',
  ),
  'LEAD_NAME' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_LEAD_NAME',
    'id' => '',
    'link' => true,
    'width' => '10%',
    'default' => true,
  ),
  'PLAN_SOURCE' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_PLAN_SOURCE',
    'width' => '10%',
    'default' => true,
  ),
  'LAST_REVIEWED_DATE' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_LAST_REVIEWED_DATE',
    'width' => '10%',
    'default' => true,
  ),
  'DESCRIPTION' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'label' => 'LBL_DESCRIPTION',
    'sortable' => false,
    'width' => '10%',
    'default' => true,
  ),
);
;
?>
