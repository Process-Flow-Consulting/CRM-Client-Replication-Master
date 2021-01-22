<?php
$popupMeta = array (
    'moduleMain' => 'oss_Zone',
    'varName' => 'oss_Zone',
    'orderBy' => 'oss_zone.name',
    'whereClauses' => array (
  'name' => 'oss_zone.name',
  'zone_type' => 'oss_zone.zone_type',
  'assigned_user_id' => 'oss_zone.assigned_user_id',
),
    'searchInputs' => array (
  1 => 'name',
  4 => 'zone_type',
  5 => 'assigned_user_id',
),
    'searchdefs' => array (
  'zone_type' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_ZONE_TYPE ',
    'width' => '10%',
    'name' => 'zone_type',
  ),
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
);
