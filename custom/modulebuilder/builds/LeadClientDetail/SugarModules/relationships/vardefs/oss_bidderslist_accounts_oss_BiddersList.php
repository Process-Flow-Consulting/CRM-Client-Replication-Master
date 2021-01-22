<?php
// created: 2019-11-07 14:36:40
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_accounts"] = array (
  'name' => 'oss_bidderslist_accounts',
  'type' => 'link',
  'relationship' => 'oss_bidderslist_accounts',
  'source' => 'non-db',
  'module' => 'Accounts',
  'bean_name' => 'Account',
  'vname' => 'LBL_OSS_BIDDERSLIST_ACCOUNTS_FROM_ACCOUNTS_TITLE',
  'id_name' => 'oss_bidderslist_accountsaccounts_ida',
);
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_accounts_name"] = array (
  'name' => 'oss_bidderslist_accounts_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_BIDDERSLIST_ACCOUNTS_FROM_ACCOUNTS_TITLE',
  'save' => true,
  'id_name' => 'oss_bidderslist_accountsaccounts_ida',
  'link' => 'oss_bidderslist_accounts',
  'table' => 'accounts',
  'module' => 'Accounts',
  'rname' => 'name',
);
$dictionary["oss_BiddersList"]["fields"]["oss_bidderslist_accountsaccounts_ida"] = array (
  'name' => 'oss_bidderslist_accountsaccounts_ida',
  'type' => 'link',
  'relationship' => 'oss_bidderslist_accounts',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OSS_BIDDERSLIST_ACCOUNTS_FROM_OSS_BIDDERSLIST_TITLE',
);
