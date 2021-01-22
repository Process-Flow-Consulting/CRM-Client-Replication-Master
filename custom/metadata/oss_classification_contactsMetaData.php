<?php
// created: 2011-11-14 11:11:29
$dictionary["oss_classification_contacts"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'oss_classification_contacts' => 
    array (
      'lhs_module' => 'oss_Classification',
      'lhs_table' => 'oss_classification',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_classifion_accounts_c',
      'join_key_lhs' => 'oss_classification_id',
      'join_key_rhs' => 'contact_id',
    ),
  ),
  'table' => 'oss_classification_contacts',
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
      'name' => 'oss_classification_id',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'contact_id',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_classifation_contactspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_classifation_contacts_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_classification_id',
        1 => 'contact_id',
      ),
    ),
  ),
);
?>
