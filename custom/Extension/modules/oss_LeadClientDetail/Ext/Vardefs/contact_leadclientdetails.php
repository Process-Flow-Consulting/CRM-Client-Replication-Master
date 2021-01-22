<?php


$dictionary['oss_LeadClientDetail']['fields']['contact_id'] = array(
    'name' => 'contact_id',
    'vname' => 'LBL_CONTACT_LEADCLIENTDETAILS_TITLE',
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



$dictionary['oss_LeadClientDetail']['fields']['contact_name'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'contact_name',
    'vname' => 'LBL_CONTACT_LEADCLIENTDETAILS_TITLE',
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
    'id_name' => 'contact_id',
    'ext2' => 'Contacts',
    'module' => 'Contacts',
    'rname' => 'name',
    'quicksearch' => 'enabled',
    'studio' => 'visible',
);

$dictionary["oss_LeadClientDetail"]["fields"]["contact_leadclientdetails"] = array (
  'name' => 'contact_leadclientdetails',
  'type' => 'link',
  'relationship' => 'contact_leadclientdetails',
  'source' => 'non-db',
  'vname' => 'LBL_CONTACT_LEADCLIENTDETAILS_TITLE',
);

$dictionary['oss_LeadClientDetail']['relationships']['contact_leadclientdetails'] = array(
    'lhs_module'=> 'Contacts', 'lhs_table'=> 'contacts', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_LeadClientDetail', 'rhs_table'=> 'oss_leadclientdetail', 'rhs_key' => 'contact_id',
    'relationship_type'=>'one-to-many'
);

?>
