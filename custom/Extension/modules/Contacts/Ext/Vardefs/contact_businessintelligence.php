<?php
// created: 2012-05-21 17:10:30
$dictionary["Contact"]["fields"]["contact_businessintelligence"] = array (
  'name' => 'contact_businessintelligence',
  'type' => 'link',
  'relationship' => 'contact_businessintelligence',
  'source' => 'non-db',
  'vname' => 'LBL_FP',
);
$dictionary['Contact']['relationships']['contact_businessintelligence'] = array(
 'lhs_module'=> 'Contacts',
 'lhs_table'=> 'contacts',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_BusinessIntelligence',
 'rhs_table'=> 'oss_businessintelligence',
 'rhs_key' => 'contact_id',
 'relationship_type'=>'one-to-many'
);