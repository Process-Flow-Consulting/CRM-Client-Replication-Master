<?php
$module_name = 'oss_County';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'COUNTY_ABBR' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_COUNTY_ABBR',
    'width' => '10%',
    'default' => true,
  ),
  'COUNTY_NUMBER' => 
  array (
    'type' => 'int',
    'label' => 'LBL_COUNTY_NUMBER ',
    'width' => '10%',
    'default' => true,
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
);
;
?>
