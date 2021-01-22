<?php
$module_name = 'oss_LeadClientDetail';
$listViewDefs [$module_name] = 
array (
  'LEAD_NAME' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_LEAD_LEADCLIENTDETAILS_TITLE',
    'id' => 'LEAD_ID',
    'link' => true,
    'width' => '10%',
    'default' => true,
  ),
  'ACCOUNT_NAME' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_ACCOUNT_LEADCLIENTDETAILS_TITLE',
    'id' => 'ACCOUNT_ID',
    'link' => true,
    'width' => '10%',
    'default' => true,
  ),
  'CONTACT_NAME' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_CONTACT_LEADCLIENTDETAILS_TITLE',
    'id' => 'CONTACT_ID',
    'link' => true,
    'width' => '10%',
    'default' => true,
  ),
  'ROLE' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_ROLE',
    'width' => '10%',
    'default' => true,
  ),
  'CONTACT_PHONE_NO' => 
  array (
    'type' => 'phone',
    'label' => 'LBL_CONTACT_PHONE_NO',
    'width' => '10%',
    'default' => true,
  ),
  'CONTACT_FAX' => 
  array (
    'type' => 'phone',
    'label' => 'LBL_CONTACT_FAX',
    'width' => '10%',
    'default' => true,
  ),
  'CONTACT_EMAIL' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_CONTACT_EMAIL',
    'width' => '10%',
    'default' => true,
  ),
);
;
?>
