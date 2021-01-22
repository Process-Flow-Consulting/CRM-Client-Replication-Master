<?php
$popupMeta = array (
    'moduleMain' => 'oss_BusinessIntelligence',
    'varName' => 'oss_BusinessIntelligence',
    'orderBy' => 'oss_businessintelligence.name',
    'whereClauses' => array (
  'name' => 'oss_businessintelligence.name',
  'assigned_user_id' => 'oss_businessintelligence.assigned_user_id',
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
  'OSS_BUSINESSINTELLIGENCE_ACCOUNTS_NAME' => 
  array (
    'type' => 'relate',
    'link' => true,
    'label' => 'LBL_OSS_BUSINESSINTELLIGENCE_ACCOUNTS_FROM_ACCOUNTS_TITLE',
    'id' => 'OSS_BUSINESSINTELLIGENCE_ACCOUNTSACCOUNTS_IDA',
    'width' => '10%',
    'default' => true,
  ),
  'OSS_BUSINESSINTELLIGENCE_CONTACTS_NAME' => 
  array (
    'type' => 'relate',
    'link' => true,
    'label' => 'LBL_OSS_BUSINESSINTELLIGENCE_CONTACTS_FROM_CONTACTS_TITLE',
    'id' => 'OSS_BUSINESSINTELLIGENCE_CONTACTSCONTACTS_IDA',
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
    'name' => 'description',
  ),
  'MY_DESCRIPTION' => 
  array (
    'type' => 'text',
    'studio' => 'visible',
    'label' => 'LBL_MY_DESCRIPTION',
    'sortable' => false,
    'width' => '10%',
    'default' => true,
    'name' => 'my_description',
  ),
),
);
