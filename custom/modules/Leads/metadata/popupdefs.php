<?php
$popupMeta = array (
    'moduleMain' => 'Lead',
    'varName' => 'LEAD',
    'orderBy' => 'last_name, first_name',
    'whereClauses' => array (
  'last_name' => 'leads.last_name',
  'lead_source' => 'leads.lead_source',
  'status' => 'leads.status',
  'account_name' => 'leads.account_name',
  'assigned_user_id' => 'leads.assigned_user_id',
),
    'searchInputs' => array (
  1 => 'last_name',
  2 => 'lead_source',
  3 => 'status',
  4 => 'account_name',
  5 => 'assigned_user_id',
),
    'searchdefs' => array (
  'last_name' => 
  array (
    'name' => 'last_name',
	'label' => 'LBL_PROJECT_TITLE',
    'width' => '10%',
  ),
  'account_name' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_ACCOUNT_NAME',
    'width' => '10%',
    'name' => 'account_name',
  ),
  'lead_source' => 
  array (
    'name' => 'lead_source',
    'width' => '10%',
  ),
  'status' => 
  array (
    'name' => 'status',
    'width' => '10%',
  ),
  'assigned_user_id' => 
  array (
    'name' => 'assigned_user_id',
    'type' => 'enum',
    'label' => 'LBL_ASSIGNED_TO',
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
  'LAST_NAME' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_PROJECT_TITLE',
    'width' => '10%',
    'default' => true,
	'link' => true,
  ),
  'ADDRESS' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_ADDRESS',
    'width' => '10%',
    'default' => true,
  ),
  'STATE' => 
  array (
    'type' => 'enum',
    'label' => 'LBL_STATE',
    'width' => '10%',
    'default' => true,
  ),
  'BIDS_DUE' => 
  array (
    'type' => 'datetimecombo',
    'studio' => 
    array (
      'required' => true,
      'no_duplicate' => true,
    ),
    'label' => 'LBL_BIDS_DUE',
    'width' => '10%',
    'default' => true,
  ),
),
);
