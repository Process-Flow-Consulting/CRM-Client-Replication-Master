<?php
// created: 2012-05-21 17:10:30
$dictionary["Account"]["fields"]["account_businessintelligence"] = array (
  'name' => 'account_businessintelligence',
  'type' => 'link',
  'relationship' => 'account_businessintelligence',
  'source' => 'non-db',
  'vname' => 'LBL_BUSSINESSINTELLIGENCE',
);
$dictionary['Account']['relationships']['account_businessintelligence'] = array(
 'lhs_module'=> 'Accounts',
 'lhs_table'=> 'accounts',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_BusinessIntelligence',
 'rhs_table'=> 'oss_businessintelligence',
 'rhs_key' => 'account_id',
 'relationship_type'=>'one-to-many'
);