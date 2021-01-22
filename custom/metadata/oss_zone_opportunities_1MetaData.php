<?php
// created: 2013-10-15 13:13:30
$dictionary["oss_zone_opportunities_1"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'oss_zone_opportunities_1' => 
    array (
      'lhs_module' => 'oss_Zone',
      'lhs_table' => 'oss_zone',
      'lhs_key' => 'id',
      'rhs_module' => 'Opportunities',
      'rhs_table' => 'opportunities',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_zone_opportunities_1_c',
      'join_key_lhs' => 'oss_zone_opportunities_1oss_zone_ida',
      'join_key_rhs' => 'oss_zone_opportunities_1opportunities_idb',
    ),
  ),
  'table' => 'oss_zone_opportunities_1_c',
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
      'name' => 'oss_zone_opportunities_1oss_zone_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'oss_zone_opportunities_1opportunities_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_zone_opportunities_1spk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_zone_opportunities_1_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_zone_opportunities_1oss_zone_ida',
        1 => 'oss_zone_opportunities_1opportunities_idb',
      ),
    ),
  ),
);