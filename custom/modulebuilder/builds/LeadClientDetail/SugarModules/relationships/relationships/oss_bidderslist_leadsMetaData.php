<?php
// created: 2019-11-07 14:36:40
$dictionary["oss_bidderslist_leads"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'oss_bidderslist_leads' => 
    array (
      'lhs_module' => 'Leads',
      'lhs_table' => 'leads',
      'lhs_key' => 'id',
      'rhs_module' => 'oss_BiddersList',
      'rhs_table' => 'oss_bidderslist',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_bidderslist_leads_c',
      'join_key_lhs' => 'oss_bidderslist_leadsleads_ida',
      'join_key_rhs' => 'oss_bidderslist_leadsoss_bidderslist_idb',
    ),
  ),
  'table' => 'oss_bidderslist_leads_c',
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
      'name' => 'oss_bidderslist_leadsleads_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'oss_bidderslist_leadsoss_bidderslist_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_bidderslist_leadsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_bidderslist_leads_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'oss_bidderslist_leadsleads_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'oss_bidderslist_leads_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_bidderslist_leadsoss_bidderslist_idb',
      ),
    ),
  ),
);