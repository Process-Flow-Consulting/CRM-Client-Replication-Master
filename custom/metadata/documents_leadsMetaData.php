<?php
$dictionary["documents_leads"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => false,
  'relationships' => 
  array (
    'documents_leads' => 
    array (
      'lhs_module' => 'Leads',
      'lhs_table' => 'leads',
      'lhs_key' => 'id',
      'rhs_module' => 'Documents',
      'rhs_table' => 'documents',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'documents_leads',
      'join_key_lhs' => 'leads_id',
      'join_key_rhs' => 'documents_id',
    ),
  ),
  'table' => 'documents_leads',
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
      'name' => 'leads_id',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'documents_id',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'documents_leadspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'documents_leads_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'documents_id',
        1 => 'leads_id',
      ),
    ),
  ),
);
?>
