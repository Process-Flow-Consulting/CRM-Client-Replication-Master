<?php
// created: 2012-05-21 17:10:30
$dictionary["Account"]["fields"]["account_leadclientdetail"] = array (
  'name' => 'account_leadclientdetail',
  'type' => 'link',
  'relationship' => 'account_leadclientdetail',
  'source' => 'non-db',
  'vname' => 'LBL_LEADCLIENTDETAILS',
);
$dictionary['Account']['relationships']['account_leadclientdetail'] = array(
 'lhs_module'=> 'Accounts',
 'lhs_table'=> 'accounts',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_LeadClientDetail',
 'rhs_table'=> 'oss_leadclientdetail',
 'rhs_key' => 'account_id',
 'relationship_type'=>'one-to-many'
);