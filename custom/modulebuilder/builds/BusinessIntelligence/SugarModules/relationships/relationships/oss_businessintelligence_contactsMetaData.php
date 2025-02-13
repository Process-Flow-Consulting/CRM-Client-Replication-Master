<?php
// created: 2019-11-07 14:57:42
$dictionary["oss_businessintelligence_contacts"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'oss_businessintelligence_contacts' => 
    array (
      'lhs_module' => 'Contacts',
      'lhs_table' => 'contacts',
      'lhs_key' => 'id',
      'rhs_module' => 'oss_BusinessIntelligence',
      'rhs_table' => 'oss_businessintelligence',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_businessintelligence_contacts_c',
      'join_key_lhs' => 'oss_businessintelligence_contactscontacts_ida',
      'join_key_rhs' => 'oss_businessintelligence_contactsoss_businessintelligence_idb',
    ),
  ),
  'table' => 'oss_businessintelligence_contacts_c',
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
      'name' => 'oss_businessintelligence_contactscontacts_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'oss_businessintelligence_contactsoss_businessintelligence_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_businessintelligence_contactsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_businessintelligence_contacts_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'oss_businessintelligence_contactscontacts_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'oss_businessintelligence_contacts_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_businessintelligence_contactsoss_businessintelligence_idb',
      ),
    ),
  ),
);