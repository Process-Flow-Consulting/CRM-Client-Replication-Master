<?php


$dictionary['oss_BusinessIntelligence']['fields']['account_id'] = array(
    'name' => 'account_id',
    'vname' => 'LBL_ACCOUNT_BUSINESS_INTELLIGENCE_TITLE',
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


$dictionary['oss_BusinessIntelligence']['fields']['account_name'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'account_name',
    'vname' => 'LBL_ACCOUNT_BUSINESS_INTELLIGENCE_TITLE',
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
    'id_name' => 'account_id',
    'ext2' => 'Accounts',
    'module' => 'Accounts',
    'rname' => 'name',
    'quicksearch' => 'enabled',
    'studio' => 'visible',
);

$dictionary["oss_BusinessIntelligence"]["fields"]["account_businessintelligence"] = array (
  'name' => 'account_businessintelligence',
  'type' => 'link',
  'relationship' => 'account_businessintelligence',
  'source' => 'non-db',
  'vname' => 'LBL_ACCOUNT_BUSINESS_INTELLIGENCE_TITLE',
);

$dictionary['oss_BusinessIntelligence']['relationships']['account_businessintelligence'] = array(
    'lhs_module'=> 'Accounts', 'lhs_table'=> 'accounts', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_BusinessIntelligence', 'rhs_table'=> 'oss_businessintelligence', 'rhs_key' => 'account_id',
    'relationship_type'=>'one-to-many'
);

?>