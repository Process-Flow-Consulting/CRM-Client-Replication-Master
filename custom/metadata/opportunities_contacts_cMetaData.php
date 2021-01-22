<?php
$dictionary["opportunities_contacts_c"] = array (
  'true_relationship_type' => 'many-to-many',
  'relationships' => 
  array (
    'opportunities_contacts_c' => 
    array (
      'lhs_module' => 'Opportunities',
      'lhs_table' => 'opportunities',
      'lhs_key' => 'id',
      'rhs_module' => 'Contacts',
      'rhs_table' => 'contacts',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'opportunities_contacts_c',
      'join_key_lhs' => 'opportunity_id',
      'join_key_rhs' => 'contact_id',
    ),
  ),
  'table' => 'opportunities_contacts_c',
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
      'name' => 'opportunity_id',
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
      'name' => 'opportunities_contactsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'opportunities_contacts_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'opportunity_id',
        1 => 'contact_id',
      ),
    ),
  ),
);
?>
