<?php
// created: 2019-11-07 14:57:42
$dictionary["oss_BusinessIntelligence"]["fields"]["oss_businessintelligence_accounts"] = array (
  'name' => 'oss_businessintelligence_accounts',
  'type' => 'link',
  'relationship' => 'oss_businessintelligence_accounts',
  'source' => 'non-db',
  'module' => 'Accounts',
  'bean_name' => 'Account',
  'vname' => 'LBL_OSS_BUSINESSINTELLIGENCE_ACCOUNTS_FROM_ACCOUNTS_TITLE',
  'id_name' => 'oss_businessintelligence_accountsaccounts_ida',
);
$dictionary["oss_BusinessIntelligence"]["fields"]["oss_businessintelligence_accounts_name"] = array (
  'name' => 'oss_businessintelligence_accounts_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_BUSINESSINTELLIGENCE_ACCOUNTS_FROM_ACCOUNTS_TITLE',
  'save' => true,
  'id_name' => 'oss_businessintelligence_accountsaccounts_ida',
  'link' => 'oss_businessintelligence_accounts',
  'table' => 'accounts',
  'module' => 'Accounts',
  'rname' => 'name',
);
$dictionary["oss_BusinessIntelligence"]["fields"]["oss_businessintelligence_accountsaccounts_ida"] = array (
  'name' => 'oss_businessintelligence_accountsaccounts_ida',
  'type' => 'link',
  'relationship' => 'oss_businessintelligence_accounts',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OSS_BUSINESSINTELLIGENCE_ACCOUNTS_FROM_OSS_BUSINESSINTELLIGENCE_TITLE',
);
