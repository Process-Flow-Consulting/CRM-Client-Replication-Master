<?php
$dictionary['oss_BusinessIntelligence']['fields']['contact_id'] = array(
    'name' => 'contact_id',
    'vname' => 'LBL_CONTACT_BUSINESS_INTELLIGENCE_TITLE',
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


$dictionary['oss_BusinessIntelligence']['fields']['contact_name'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'contact_name',
    'vname' => 'LBL_CONTACT_BUSINESS_INTELLIGENCE_TITLE',
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

$dictionary["oss_BusinessIntelligence"]["fields"]["contact_businessintelligence"] = array (
  'name' => 'contact_businessintelligence',
  'type' => 'link',
  'relationship' => 'contact_businessintelligence',
  'source' => 'non-db',
  'vname' => 'LBL_CONTACT_BUSINESS_INTELLIGENCE_TITLE',
);

$dictionary['oss_BusinessIntelligence']['relationships']['contact_businessintelligence'] = array(
    'lhs_module'=> 'Contacts', 'lhs_table'=> 'contacts', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_BusinessIntelligence', 'rhs_table'=> 'oss_businessintelligence', 'rhs_key' => 'contact_id',
    'relationship_type'=>'one-to-many'
);

?>

