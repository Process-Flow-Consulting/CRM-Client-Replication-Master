<?php
// created: 2011-11-14 11:11:29
$dictionary["oss_classification_accounts"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'oss_classification_accounts' => 
    array (
      'lhs_module' => 'oss_Classification',
      'lhs_table' => 'oss_classification',
      'lhs_key' => 'id',
      'rhs_module' => 'Accounts',
      'rhs_table' => 'accounts',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_classifion_accounts_c',
      'join_key_lhs' => 'oss_classi48bbication_ida',
      'join_key_rhs' => 'oss_classid41cccounts_idb',
    ),
  ),
  'table' => 'oss_classifion_accounts_c',
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
      'name' => 'oss_classi48bbication_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'oss_classid41cccounts_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_classifation_accountsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_classifation_accounts_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_classi48bbication_ida',
        1 => 'oss_classid41cccounts_idb',
      ),
    ),
  ),
);
?>
