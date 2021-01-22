<?php
// created: 2019-11-07 14:36:39
$dictionary["oss_bidderslist_accounts"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'oss_bidderslist_accounts' => 
    array (
      'lhs_module' => 'Accounts',
      'lhs_table' => 'accounts',
      'lhs_key' => 'id',
      'rhs_module' => 'oss_BiddersList',
      'rhs_table' => 'oss_bidderslist',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_bidderslist_accounts_c',
      'join_key_lhs' => 'oss_bidderslist_accountsaccounts_ida',
      'join_key_rhs' => 'oss_bidderslist_accountsoss_bidderslist_idb',
    ),
  ),
  'table' => 'oss_bidderslist_accounts_c',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    1 => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    2 => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    3 => 
    array (
      'name' => 'oss_bidderslist_accountsaccounts_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'oss_bidderslist_accountsoss_bidderslist_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_bidderslist_accountsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_bidderslist_accounts_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'oss_bidderslist_accountsaccounts_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'oss_bidderslist_accounts_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_bidderslist_accountsoss_bidderslist_idb',
      ),
    ),
  ),
);