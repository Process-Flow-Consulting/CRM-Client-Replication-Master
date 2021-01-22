<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['oss_BusinessIntelligence']['fields']['name']['name']='name';
$dictionary['oss_BusinessIntelligence']['fields']['name']['vname']='Type';
$dictionary["oss_BusinessIntelligence"]["fields"]["name"]["type"] = "enum";
$dictionary["oss_BusinessIntelligence"]["fields"]["name"]["options"] = "bi_type_dom";


$dictionary['oss_BusinessIntelligence']['fields']['team_set_id'] = array(
    'name' => 'team_set_id',
    'vname' => 'Team Set ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
    'size' => '20',
);
$dictionary['oss_BusinessIntelligence']['fields']['team_id'] = array(
    'name' => 'team_id',
    'vname' => 'Team ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
    'size' => '20',
);
$dictionary["oss_BusinessIntelligence"]["fields"]["parent_id"] = array(
		'name' => 'parent_id',
		'vname' => 'LBL_PARENT_ID',
		'type' => 'char',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'audited' => true,
		'reportable' => true,
		'len' => 36,
		'size' => 20,
);


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



 // created: 2019-12-17 13:19:40
$dictionary['oss_BusinessIntelligence']['fields']['account_name']['inline_edit']=true;
$dictionary['oss_BusinessIntelligence']['fields']['account_name']['merge_filter']='disabled';

 

 // created: 2019-12-17 13:19:57
$dictionary['oss_BusinessIntelligence']['fields']['contact_name']['inline_edit']=true;
$dictionary['oss_BusinessIntelligence']['fields']['contact_name']['merge_filter']='disabled';

 
?>