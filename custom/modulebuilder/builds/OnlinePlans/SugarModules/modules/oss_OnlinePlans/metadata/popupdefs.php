<?php
$popupMeta = array (
    'moduleMain' => 'oss_OnlinePlans',
    'varName' => 'oss_OnlinePlans',
    'orderBy' => 'oss_onlineplans.name',
    'whereClauses' => array (
  'name' => 'oss_onlineplans.name',
),
    'searchInputs' => array (
  0 => 'oss_onlineplans_number',
  1 => 'name',
  2 => 'priority',
  3 => 'status',
),
    'listviewdefs' => array (
  'PLAN_TYPE' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_PLAN_TYPE',
    'width' => '10%',
    'name' => 'plan_type',
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
    'name' => 'lead_name',
  ),
  'PLAN_SOURCE' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_PLAN_SOURCE',
    'width' => '10%',
    'default' => true,
    'name' => 'plan_source',
  ),
  'LAST_REVIEWED_DATE' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_LAST_REVIEWED_DATE',
    'width' => '10%',
    'default' => true,
    'name' => 'last_reviewed_date',
  ),
  'DESCRIPTION' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'label' => 'LBL_DESCRIPTION',
    'sortable' => false,
    'width' => '10%',
    'default' => true,
    'name' => 'description',
  ),
),
);
