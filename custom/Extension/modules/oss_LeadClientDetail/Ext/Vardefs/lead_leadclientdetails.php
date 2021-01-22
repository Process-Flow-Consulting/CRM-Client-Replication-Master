<?php

$dictionary['oss_LeadClientDetail']['fields']['lead_id'] = array(
    'name' => 'lead_id',
    'vname' => 'LBL_LEAD_LEADCLIENTDETAILS_TITLE',
    'type' => 'char',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
);


$dictionary['oss_LeadClientDetail']['fields']['lead_name'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'lead_name',
    'vname' => 'LBL_LEAD_LEADCLIENTDETAILS_TITLE',
    'type' => 'relate',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => false,
    'reportable' => true,
    'len' => '255',
    'size' => '20',
    'id_name' => 'lead_id',
    'ext2' => 'Leads',
    'module' => 'Leads',
    'rname' => 'project_title',
    'quicksearch' => 'enabled',
    'studio' => 'visible',
);

$dictionary["oss_LeadClientDetail"]["fields"]["lead_leadclientdetails"] = array (
  'name' => 'lead_leadclientdetails',
  'type' => 'link',
  'relationship' => 'lead_leadclientdetails',
  'source' => 'non-db',
  'vname' => 'LBL_LEAD_LEADCLIENTDETAILS_TITLE',
);

$dictionary['oss_LeadClientDetail']['relationships']['lead_leadclientdetails'] = array(
    'lhs_module'=> 'Leads', 'lhs_table'=> 'leads', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_LeadClientDetail', 'rhs_table'=> 'oss_leadclientdetail', 'rhs_key' => 'lead_id',
    'relationship_type'=>'one-to-many'
);

?>
