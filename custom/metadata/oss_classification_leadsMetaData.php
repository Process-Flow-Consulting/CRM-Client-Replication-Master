<?php
// created: 2011-11-03 14:05:19
$dictionary["oss_classification_leads"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'oss_classification_leads' => 
    array (
      'lhs_module' => 'oss_Classification',
      'lhs_table' => 'oss_classification',
      'lhs_key' => 'id',
      'rhs_module' => 'Leads',
      'rhs_table' => 'leads',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'oss_classifcation_leads_c',
      'join_key_lhs' => 'oss_classi4427ication_ida',
      'join_key_rhs' => 'oss_classi7103dsleads_idb',
    ),
  ),
  'table' => 'oss_classifcation_leads_c',
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
      'name' => 'oss_classi4427ication_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'oss_classi7103dsleads_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'oss_classification_leadsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'oss_classification_leads_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'oss_classi4427ication_ida',
        1 => 'oss_classi7103dsleads_idb',
      ),
    ),
  ),
);
?>
