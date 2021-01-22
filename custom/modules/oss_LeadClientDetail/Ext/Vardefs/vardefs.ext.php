<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['oss_LeadClientDetail']['fields']['team_set_id'] = array(
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

$dictionary['oss_LeadClientDetail']['fields']['team_id'] = array(
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


$dictionary['oss_LeadClientDetail']['fields']['opportunity_id'] = array(
    'required' => false,
    'name' => 'opportunity_id',
    'vname' => 'LBL_OPPORTUNITY_ID',
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
    'size' => '20',
);

$dictionary['oss_LeadClientDetail']['fields']['opportunity_name'] =
    array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'opportunity_name',
    'vname' => 'LBL_OPPORTUNITY_NAME',
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
    'id_name' => 'opportunity_id',
    'ext2' => 'Opportunities',
    'module' => 'Opportunities',
    'rname' => 'name',
    'quicksearch' => 'enabled',
	'link' => 'oss_leadclientdetail_opportunity',
    'studio' => 'visible',
);

$dictionary["oss_LeadClientDetail"]["fields"]["oss_leadclientdetail_to_opportunity"] = array (
  'name' => 'oss_leadclientdetail_to_opportunity',
  'type' => 'link',
  'relationship' => 'oss_leadclientdetail_opportunity',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITY_NAME',
);

$dictionary['oss_LeadClientDetail']['relationships']['oss_leadclientdetail_opportunity'] = array('lhs_module'=> 'oss_LeadClientDetail', 'lhs_table'=> 'oss_leadclientdetail', 'lhs_key' => 'opportunity_id',
							  'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'id',
							  'relationship_type'=>'one-to-one');




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






$dictionary['oss_LeadClientDetail']['fields']['account_id'] = array(
    'name' => 'account_id',
    'vname' => 'LBL_ACCOUNT_LEADCLIENTDETAILS_TITLE',
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


$dictionary['oss_LeadClientDetail']['fields']['account_name'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'account_name',
    'vname' => 'LBL_ACCOUNT_LEADCLIENTDETAILS_TITLE',
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

$dictionary["oss_LeadClientDetail"]["fields"]["account_leadclientdetails"] = array (
  'name' => 'account_leadclientdetails',
  'type' => 'link',
  'relationship' => 'account_leadclientdetails',
  'source' => 'non-db',
  'vname' => 'LBL_ACCOUNT_LEADCLIENTDETAILS_TITLE',
);

$dictionary['oss_LeadClientDetail']['relationships']['account_leadclientdetails'] = array(
    'lhs_module'=> 'Accounts', 'lhs_table'=> 'accounts', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_LeadClientDetail', 'rhs_table'=> 'oss_leadclientdetail', 'rhs_key' => 'account_id',
    'relationship_type'=>'one-to-many'
);






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




		
$dictionary['oss_LeadClientDetail']['indices'][0] = array (
        'name' => 'idx_lead_id',
        'type' => 'index',
        'fields' => array ('lead_id'));
		

$dictionary['oss_LeadClientDetail']['indices'][0] = array (
		'name' => 'idx_mi_oss_leadclientdetail_id',
		'type' => 'unique',
		'fields' => array ('mi_oss_leadclientdetail_id'));
		



 // created: 2019-12-17 13:26:13
$dictionary['oss_LeadClientDetail']['fields']['opportunity_name']['inline_edit']=true;
$dictionary['oss_LeadClientDetail']['fields']['opportunity_name']['merge_filter']='disabled';

 

 // created: 2019-12-17 13:26:41

 

 // created: 2019-12-17 13:26:27

 
?>