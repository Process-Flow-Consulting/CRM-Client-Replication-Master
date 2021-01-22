<?php
$module_name = 'oss_BiddersList';
$listViewDefs [$module_name] = 
array (
  'OSS_BIDDERSLIST_ACCOUNTS_NAME' => 
  array (
    'type' => 'relate',
    'link' => true,
    'label' => 'LBL_OSS_BIDDERSLIST_ACCOUNTS_FROM_ACCOUNTS_TITLE',
    'id' => 'OSS_BIDDERSLIST_ACCOUNTSACCOUNTS_IDA',
    'width' => '10%',
    'default' => true,
  ),
  'OSS_BIDDERSLIST_LEADS_NAME' => 
  array (
    'type' => 'relate',
    'link' => true,
    'label' => 'LBL_OSS_BIDDERSLIST_LEADS_FROM_LEADS_TITLE',
    'id' => 'OSS_BIDDERSLIST_LEADSLEADS_IDA',
    'width' => '10%',
    'default' => true,
  ),
  'OSS_BIDDERSLIST_CONTACTS_NAME' => 
  array (
    'type' => 'relate',
    'link' => true,
    'label' => 'LBL_OSS_BIDDERSLIST_CONTACTS_FROM_CONTACTS_TITLE',
    'id' => 'OSS_BIDDERSLIST_CONTACTSCONTACTS_IDA',
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
    'label' => 'LBL_CONTACT_FAX ',
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
