<?php
$dictionary["Lead"]["fields"]["lead_leadclientdetails"] = array (
  'name' => 'lead_leadclientdetails',
  'type' => 'link',
  'relationship' => 'lead_leadclientdetails',
  'source' => 'non-db',
  'module'=>'Leads',
  'vname' => 'LBL_LEADCLIENTDETAILS',
);
$dictionary['Lead']['relationships']['lead_leadclientdetails'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_LeadClientDetail',
 'rhs_table'=> 'oss_leadclientdetail',
 'rhs_key' => 'lead_id',
 'relationship_type'=>'one-to-many'
);
?>
