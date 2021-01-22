<?php
// created: 2012-05-21 17:10:30
$dictionary["Contact"]["fields"]["contact_leadclientdetail"] = array (
  'name' => 'contact_leadclientdetail',
  'type' => 'link',
  'relationship' => 'contact_leadclientdetail',
  'source' => 'non-db',
  'vname' => 'LBL_LEADCLIENTDETAILS',
);
$dictionary['Contact']['relationships']['contact_leadclientdetail'] = array(
 'lhs_module'=> 'Contacts',
 'lhs_table'=> 'contacts',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_LeadClientDetail',
 'rhs_table'=> 'oss_leadclientdetail',
 'rhs_key' => 'contact_id',
 'relationship_type'=>'one-to-many'
);