<?php
$popupMeta = array (
    'moduleMain' => 'oss_County',
    'varName' => 'oss_County',
    'orderBy' => 'oss_county.name',
    'whereClauses' => array (
  'name' => 'oss_county.name',
  'assigned_user_id' => 'oss_county.assigned_user_id',
),
    'searchInputs' => array (
  1 => 'name',
  4 => 'assigned_user_id',
),
    'searchdefs' => array (
  'name' => 
  array (
    'name' => 'name',
    'width' => '10%',
  ),
  'assigned_user_id' => 
  array (
    'name' => 'assigned_user_id',
    'label' => 'LBL_ASSIGNED_TO',
    'type' => 'enum',
    'function' => 
    array (
      'name' => 'get_user_array',
      'params' => 
      array (
        0 => false,
      ),
    ),
    'width' => '10%',
  ),
),
    'listviewdefs' => array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
    'name' => 'name',
  ),
  'COUNTY_ABBR' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_COUNTY_ABBR',
    'width' => '10%',
    'default' => true,
    'name' => 'county_abbr',
  ),
  'COUNTY_NUMBER' => 
  array (
    'type' => 'int',
    'label' => 'LBL_COUNTY_NUMBER ',
    'width' => '10%',
    'default' => true,
    'name' => 'county_number',
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
    'name' => 'assigned_user_name',
  ),
),
);
