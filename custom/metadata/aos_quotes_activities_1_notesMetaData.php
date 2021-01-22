<?php
// created: 2019-12-23 07:25:10
$dictionary["quote_notes"] = array (
  'relationships' => 
  array (
    'quote_notes' => 
    array (
      'lhs_module' => 'AOS_Quotes',
      'lhs_table' => 'aos_quotes',
      'lhs_key' => 'id',
      'rhs_module' => 'Notes',
      'rhs_table' => 'notes',
      'rhs_key' => 'parent_id',
      'relationship_type' => 'one-to-many',
      'relationship_role_column' => 'parent_type',
      'relationship_role_column_value' => 'AOS_Quotes',
    ),
  ),
  'fields' => '',
  'indices' => '',
  'table' => '',
);