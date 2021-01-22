<?php 
 //WARNING: The contents of this file are auto-generated


// created: 2013-10-15 13:13:30
$dictionary["Opportunity"]["fields"]["oss_zone_opportunities_1"] = array (
  'name' => 'oss_zone_opportunities_1',
  'type' => 'link',
  'relationship' => 'oss_zone_opportunities_1',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_ZONE_OPPORTUNITIES_1_FROM_OSS_ZONE_TITLE',
);

$dictionary["Opportunity"]["fields"]["zone_name"] = array (
  'name' => 'zone_name',
  'type' => 'enum',
//  'relationship' => 'oss_zone_opportunities_1',
  'function' => array('name'=>'get_all_zones','return'=>'html'),
  'source' => 'non-db',
  'vname' => 'LBL_OSS_ZONE_OPPORTUNITIES_1_FROM_OSS_ZONE_TITLE',
);



$dictionary['Opportunity']['fields']['contact_id'] = array(
		'name' => 'contact_id',
		'vname' => 'LBL_CONTACT_ID',
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

$dictionary['Opportunity']['fields']['contact_name'] = array(
		'required' => true,
		'source' => 'non-db',
		'name' => 'contact_name',
		'vname' => 'LBL_CONTACTS',
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

$dictionary["Opportunity"]["fields"]["opportunities_contact"] = array (
		'name' => 'opportunities_contact',
		'type' => 'link',
		'relationship' => 'opportunities_contact',
		'source' => 'non-db',
		'vname' => 'LBL_CONTACTS',
);

$dictionary['Opportunity']['relationships']['opportunities_contact'] = array(
		'lhs_module'=> 'Contacts', 'lhs_table'=> 'contacts', 'lhs_key' => 'id',
		'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'contact_id',
		'relationship_type'=>'one-to-many'
);


$dictionary['Opportunity']['duplicate_merge']= false;

$dictionary['Opportunity']['fields']['parent_opportunity_id'] =
array(
'required' => false,
'name' => 'parent_opportunity_id',
'vname' => 'LBL_OPPORTUNITY_ID',
'type' => 'char',
'massupdate' => 0,
'comments' => '',
'help' => '',
'importable' => 'true',
'duplicate_merge' => 'disabled',
'duplicate_merge_dom_value' => '0', 
'reportable' => true,
'len' => '36',
'size' => '20', 
);

$dictionary['Opportunity']['fields']['opportunity_name'] =
array(
'required' => true,
'source' => 'non-db',
'name' => 'opportunity_name',
'vname' => 'Project Opportunity',
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
'id_name' => 'parent_opportunity_id',
//'ext2' => 'Opportunity',
'ext2' => 'Opportunities',
'module' => 'Opportunities',
'rname' => 'name',
'quicksearch' => 'enabled',
'studio' => 'visible',
);
$dictionary["Opportunity"]["fields"]["opportunity_to_opportunity"] = array (
'name' => 'opportunity_to_opportunity',
'type' => 'link',
'relationship' => 'opportunity_to_opportunity',
'source' => 'non-db',
'vname' => 'LBL_OPPORTUNITY_PARENT',
);
$dictionary['Opportunity']['relationships']['opportunity_to_opportunity'] = array('lhs_module'=> 'Opportunities', 'lhs_table'=> 'opportunities', 'lhs_key' => 'id',
'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'parent_opportunity_id',
'relationship_type'=>'one-to-many');



//opportunity relationship
$dictionary['Opportunity']['fields']['project_lead_id'] =
array(
'required' => false,
'name' => 'project_lead_id',
'vname' => 'LBL_PROJECT_LEAD_ID',
'type' => 'char',
'massupdate' => 0,
'comments' => '',
'help' => '',
'importable' => 'true',
'duplicate_merge' => 'disabled',
'duplicate_merge_dom_value' => '0', 
'reportable' => true,
'len' => '36',
'size' => '20',
);

$dictionary['Opportunity']['fields']['project_lead_name'] =
array(
'required' => false,
'source' => 'non-db',
'name' => 'project_lead_name',
'vname' => 'LBL_PROJECT_LEAD',
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
'id_name' => 'project_lead_id',
'ext2' => 'Leads',
'module' => 'Leads',
'rname' => 'name',
'quicksearch' => 'enabled',
'studio' => 'visible',
);
$dictionary["Opportunity"]["fields"]["lead_to_opportunity_var"] = array (
'name' => 'lead_to_opportunity_var',
'type' => 'link',
'relationship' => 'lead_to_opportunity',
'source' => 'non-db',
'vname' => 'LBL_PROJECT_LEAD',
);
$dictionary['Opportunity']['fields']['lead_source'] = array(
'name' => 'lead_source',
'vname' => 'LBL_LEAD_SOURCE',
'type' => 'enum',
'len' => '50',
'options' => 'lead_source_list',
);
$dictionary['Opportunity']['relationships']['lead_to_opportunity'] = array('lhs_module'=> 'Leads', 'lhs_table'=> 'leads', 'lhs_key' => 'id',
'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'project_lead_id',
'relationship_type'=>'one-to-many');



$dictionary['Opportunity']['fields']['next_action_date'] =
        array(
            'required' => false,
            'name' => 'next_action_date',
            'vname' => 'LBL_NEXT_ACTION_DATE',
            'type' => 'datetimecombo',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
);

$dictionary['Opportunity']['fields']['date_closed'] =
        array(
            'required' => true,
            'name' => 'date_closed',
            'vname' => 'LBL_DATE_CLOSED',
            'type' => 'datetimecombo',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => true,
            'reportable' => true,
            'enable_range_search' => true,
            'options' => 'lead_opp_date_range_dom',
);

$dictionary['Opportunity']['fields']['bid_due_timezone'] = array(
    'name' => 'bid_due_timezone',
    'vname' => 'LBL_BID_DUE_TIMEZONE',
    'type' => 'enum',
    'len' => '100',
    'options' => 'us_timezone_dom',
    'audited' => true,
    'merge_filter' => 'enabled',
	'required' => true,
);
$dictionary['Opportunity']['fields']['sub_opp_count'] =
array(
		'required' => false,
		'name' => 'sub_opp_count',
		'vname' => 'LBL_ACCOUNTS',
		'type' => 'int',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',		
		'audited' => false,
		'reportable' => true,
		'len' => '11',
		
);
// BBSMP  -- start  
$dictionary["Opportunity"]["fields"]["dup_account_name"] = array (
		'name' => 'dup_account_name',
		'rname' => 'name',
		'id_name' => 'account_id',
		'vname' => 'LBL_ACCOUNT_NAME',
		'type' => 'relate',
		'table' => 'accounts',
		'join_name'=>'accounts',
		'isnull' => 'true',
		'module' => 'Accounts',
		'dbType' => 'varchar',
		'link'=>'accounts',
		'len' => '255',
		'source'=>'non-db',
		'unified_search' => true,
		'required' => false,
		'importable' => true,
		'massupdate' => 0,
);
$dictionary['Opportunity']['fields']['lcd_account']=array(
		'name' => 'lcd_account',
		'vname' => 'LBL_LIST_ACCOUNT_NAME',
		'type' => 'char',
		'source' => 'non-db'
);
// BBSMP  -- End
$dictionary["Opportunity"]["fields"]["my_project_status"] = array(
		'name' => 'my_project_status',
		'vname' => 'LBL_MY_PROJECT_STATUS',
		'type' => 'enum',
		'options' => 'my_project_status_dom',
);
$dictionary["Opportunity"]["fields"]["client_bid_status"] = array(
		'name' => 'client_bid_status',
		'vname' => 'LBL_CLIENT_BID_STATUS',
		'type' => 'enum',
		'options' => 'client_bid_status_dom',
);

$dictionary['Opportunity']['fields']['project_status'] = array(
		'name' => 'project_status',
		'vname' => 'LBL_PROJECT_STATUS',
		'type' => 'enum',
		'len' => '100',
		'options' => 'project_status_dom',
		'audited' => true,
		'comment' => 'STATUS OF PROJECT',
		'merge_filter' => 'enabled',
);

$dictionary['Opportunity']['fields']['is_archived'] = array(
        'name' => 'is_archived',
        'vname' => 'LBL_ARCHIVE',
        'type' => 'bool',
        'default' => 0,       
        'audited' => true,
        'comment' => 'STATUS OF PROJECT OPPORTUNITY',
        
);

$dictionary['Opportunity']['fields']['opportunity_classification'] = array(
	'name' => 'opportunity_classification',
	'vname' => 'LBL_OPPORTUNITY_CLASSFICATION',
	'type' => 'enum',
	'len' => '100',
	'audited' => false,
	'merge_filter' => 'enabled',
	'required' => false,
    'function' => 'getTargetClassDom',	
);
$dictionary['Opportunity']['indices'][] = array(
		'name' => 'idx_opportunity_classification',
		'type' => 'index',
		'fields'=> array('opportunity_classification'),
);

$dictionary['Opportunity']['fields']['initial_pulled_user_email'] = array (
    'required' => false,
    'name' => 'initial_pulled_user_email',
    'vname' => 'LBL_INITIAL_PULLED_USER_EMAIL',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'len' => '100',
);
$dictionary["Opportunity"]["fields"]["clients"] = array (
  'name' => 'clients',
  'source' => 'non-db',
  'type' => 'int',
  'vname' => 'LBL_OPP_CLIENTS',
);
$dictionary['Opportunity']['fields']['date_closed_tz']=array(
		'name' => 'date_closed_tz',
		'vname' => 'LBL_DATE_DATE',
		'type' => 'char',
		'source' => 'non-db'
);
$dictionary["Opportunity"]["fields"]["project_online_plan"] = array (
		'name' => 'project_online_plan',
		'type' => 'char',
		'source' => 'non-db',
		'vname' => 'LBL_LINK_URL',
);


// created: 2012-03-21 12:09:39
$dictionary["Opportunity"]["fields"]["opportunities_accounts"] = array (
  'name' => 'opportunities_accounts',
  'type' => 'link',
  'relationship' => 'opportunities_accounts',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITIES_ACCOUNTS_FROM_ACCOUNTS_TITLE',
);


$dictionary['Opportunity']['fields']['leadclientdetail_id'] = array(
    'required' => false,
    'name' => 'leadclientdetail_id',
    'vname' => 'LBL_LEADCLIENTDETAIL_ID',
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

$dictionary['Opportunity']['fields']['leadclientdetail_name'] =
    array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'leadclientdetail_name',
    'vname' => 'LBL_LEADCLIENTDETAIL_NAME',
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
    'id_name' => 'leadclientdetail_id',
    'ext2' => 'oss_LeadClientDetail',
    'module' => 'oss_LeadClientDetail',
    'rname' => 'name',
    'quicksearch' => 'enabled',
	'link' => 'oss_leadclientdetail_opportunity',
    'studio' => 'visible',
);


$dictionary["Opportunity"]["fields"]["oss_leadclientdetail_to_opportunity"] = array (
  'name' => 'oss_leadclientdetail_to_opportunity',
  'type' => 'link',
  'relationship' => 'oss_leadclientdetail_opportunity',
  'source' => 'non-db',
  'vname' => 'LBL_LEADCLIENTDETAIL_NAME',
);

$dictionary['Opportunity']['relationships']['oss_leadclientdetail_opportunity'] = array('lhs_module'=> 'oss_LeadClientDetail', 'lhs_table'=> 'oss_leadclientdetail', 'lhs_key' => 'id',
							  'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'leadclientdetail_id',
							  'relationship_type'=>'one-to-one');




$dictionary["Opportunity"]["fields"]["lead_address"] = array (
		'name' => 'lead_address',		
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_ADDRESS',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_source"] = array (
		'name' => 'lead_source',		
		'type' => 'enum',
		'vname' => 'LBL_LEAD_SOURCE',
		'import' => false,
		'options' => 'lead_source_list',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_state"] = array (
		'name' => 'lead_state',
		'type' => 'enum',
		'vname' => 'LBL_LEAD_STATE',
		'import' => false,
		'options' => 'state_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_received"] = array (
		'name' => 'lead_received',
		'type' => 'date',
		'vname' => 'LBL_LEAD_RECEIVED',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_county"] = array (
		'name' => 'lead_county',	
		'type' => 'enum',
		'vname' => 'LBL_LEAD_COUNTY',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_structure"] = array (
		'name' => 'lead_structure',	
		'type' => 'enum',
		'vname' => 'LBL_LEAD_STRUCTURE',
		'import' => false,
		//'options' => 'all_structure_dom',
		//'function' => 'getBluebookStructureDom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_city"] = array (
		'name' => 'lead_city',	
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_CITY',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_type"] = array (
		'name' => 'lead_type',
		'type' => 'enum',
		'vname' => 'LBL_LEAD_TYPE',
		'import' => false,
		'options' => 'project_type_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_zip_code"] = array (
		'name' => 'lead_zip_code',	
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_ZIP_CODE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_owner"] = array (
		'name' => 'lead_owner',		
		'type' => 'enum',
		'vname' => 'LBL_LEAD_OWNER',
		'import' => false,
		'options' => 'owner_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_project_status"] = array (
		'name' => 'lead_project_status',
		'type' => 'enum',
		'vname' => 'LBL_LEAD_PROJECT_STATUS',
		'import' => false,
		'options' => 'project_status_dom',
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_union_c"] = array (
		'name' => 'lead_union_c',
		'type' => 'bool',
		'vname' => 'LBL_LEAD_UNION_C',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_non_union"] = array (
		'name' => 'lead_non_union',	
		'type' => 'bool',
		'vname' => 'LBL_LEAD_NON_UNION',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_prevailing_wage"] = array (
		'name' => 'lead_prevailing_wage',
		'type' => 'bool',
		'vname' => 'LBL_LEAD_PREVAILING_WAGE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_start_date"] = array (
		'name' => 'lead_start_date',
		'type' => 'date',
		'vname' => 'LBL_LEAD_START_DATE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
        'enable_range_search' => true,                
        'options' => 'date_range_search_dom',
);

$dictionary["Opportunity"]["fields"]["lead_square_footage"] = array (
		'name' => 'lead_square_footage',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_SQUARE_FOOTAGE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_end_date"] = array (
		'name' => 'lead_end_date',
		'type' => 'date',
		'vname' => 'LBL_LEAD_END_DATE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
        'enable_range_search' => true,
        'options' => 'date_range_search_dom',
);

$dictionary["Opportunity"]["fields"]["lead_contact_no"] = array (
		'name' => 'lead_contact_no',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_CONTACT_NO',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_stories_below_grade"] = array (
		'name' => 'lead_stories_below_grade',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_STORIES_BELOW_GRADE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_valuation"] = array (
		'name' => 'lead_valuation',
		'type' => 'decimal',
		'vname' => 'LBL_LEAD_VALUATION',
		'import' => false,
		'len' => 10,
		'precision' => 2,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_scope_of_work"] = array (
		'name' => 'lead_scope_of_work',
		'type' => 'text',
		'vname' => 'LBL_LEAD_SCOPE_OF_WORK',
		'import' => false,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_stories_above_grade"] = array (
		'name' => 'lead_stories_above_grade',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_STORIES_ABOVE_GRADE',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);

$dictionary["Opportunity"]["fields"]["lead_number_of_buildings"] = array (
		'name' => 'lead_number_of_buildings',
		'type' => 'varchar',
		'vname' => 'LBL_LEAD_NUMBER_OF_BUILDINGS',
		'import' => false,
		'len' => 255,
		'size' => 30,
		'auditable' => true,
);


$dictionary['Opportunity']['fields']['editsequence'] = array (
        'required' => false,
        'name' => 'editsequence',
        'vname' => 'LBL_QUICKBOOK_EDITSEQUENCE',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook EditSequence',
        'help' => 'Quickbook EditSequence',
        'importable' => 'false',
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
        'len' => '10',
        'size' => '10',
);
$dictionary['Opportunity']['fields']['quickbooks_id'] = array (
        'required' => false,
        'name' => 'quickbooks_id',
        'vname' => 'LBL_QUICKBOOK_ID',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook Id',
        'help' => 'Quickbook Id',
        'importable' => 'false',
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '40',
        'size' => '40',
);
$dictionary['Opportunity']['fields']['push_to_qb'] = array (
        'required' => false,
        'name' => 'push_to_qb',
        'vname' => 'LBL_PUSH_TOQUICKBOOK',
        'type' => 'bool',
        'massupdate' => 1,
        'comments' => 'Push to Quickbook flag',
        'help' => 'Push to Quickbook flag',
        'importable' => 'false',
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
         
);
$dictionary['Opportunity']['indices'][] = array(
        'name' =>'idx_quickbooks_id0',
        'type'=>'unique',
        'fields'=>array('quickbooks_id')
);
$dictionary['Opportunity']['indices'][] = array(
        'name' =>'idx_quickbooks_id1',
        'type'=>'index',
        'fields'=>array('quickbooks_id'),
        'source'=>'non-db'
);

$dictionary["Opportunity"]["fields"]["opportunities_contacts_c"] = array (
        'name' => 'opportunities_contacts_c',
        'type' => 'link',
        'relationship' => 'opportunities_contacts_c',
        'source' => 'non-db',
        'vname' => 'LBL_OPPORTUNITIES_CONTACTS_TITLE',
);


$dictionary['Opportunity']['fields']['team_set_id'] = array(
    'name' => 'team_set_id',
    'vname' => 'LBL_TEAM_SET_ID',
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

$dictionary['Opportunity']['fields']['team_id'] = array(
    'name' => 'team_id',
    'vname' => 'LBL_TEAM_ID',
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
	


 // created: 2019-12-11 07:09:48
$dictionary['Opportunity']['fields']['lead_type']['len']=100;
$dictionary['Opportunity']['fields']['lead_type']['inline_edit']=true;
$dictionary['Opportunity']['fields']['lead_type']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:03:46
$dictionary['Opportunity']['fields']['quickbooks_id']['inline_edit']=true;
$dictionary['Opportunity']['fields']['quickbooks_id']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['quickbooks_id']['reportable']=true;

 

 // created: 2019-11-28 09:36:22
$dictionary['Opportunity']['fields']['leadclientdetail_name']['inline_edit']=true;
$dictionary['Opportunity']['fields']['leadclientdetail_name']['merge_filter']='disabled';

 

 // created: 2020-04-17 10:33:50
$dictionary['Opportunity']['fields']['date_closed']['inline_edit']=true;
$dictionary['Opportunity']['fields']['date_closed']['options']='date_range_search_dom';
$dictionary['Opportunity']['fields']['date_closed']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:33
$dictionary['Opportunity']['fields']['jjwg_maps_lat_c']['inline_edit']=1;

 

 // created: 2019-11-28 07:07:46
$dictionary['Opportunity']['fields']['lead_source']['len']=100;
$dictionary['Opportunity']['fields']['lead_source']['inline_edit']=true;
$dictionary['Opportunity']['fields']['lead_source']['options']='lead_source_list';
$dictionary['Opportunity']['fields']['lead_source']['comments']='Source of the opportunity';
$dictionary['Opportunity']['fields']['lead_source']['merge_filter']='disabled';

 

 // created: 2019-12-11 07:03:36
$dictionary['Opportunity']['fields']['probability']['default']='0.0000';
$dictionary['Opportunity']['fields']['probability']['inline_edit']=true;
$dictionary['Opportunity']['fields']['probability']['comments']='The probability of closure';
$dictionary['Opportunity']['fields']['probability']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['probability']['enable_range_search']=false;
$dictionary['Opportunity']['fields']['probability']['precision']='4';

 

 // created: 2019-11-28 09:36:47
$dictionary['Opportunity']['fields']['team_id']['inline_edit']=true;
$dictionary['Opportunity']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:04:06
$dictionary['Opportunity']['fields']['push_to_qb']['default']='0';
$dictionary['Opportunity']['fields']['push_to_qb']['inline_edit']=true;
$dictionary['Opportunity']['fields']['push_to_qb']['massupdate']=0;
$dictionary['Opportunity']['fields']['push_to_qb']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['push_to_qb']['reportable']=true;

 

 // created: 2019-11-28 09:42:07
$dictionary['Opportunity']['fields']['custom_field_1']['inline_edit']=true;
$dictionary['Opportunity']['fields']['custom_field_1']['merge_filter']='disabled';

 

 // created: 2019-11-28 09:38:26
$dictionary['Opportunity']['fields']['parent_opportunity_id']['inline_edit']=true;
$dictionary['Opportunity']['fields']['parent_opportunity_id']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:33
$dictionary['Opportunity']['fields']['jjwg_maps_geocode_status_c']['inline_edit']=1;

 

 // created: 2019-11-28 09:35:52
$dictionary['Opportunity']['fields']['leadclientdetail_id']['inline_edit']=true;
$dictionary['Opportunity']['fields']['leadclientdetail_id']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:32
$dictionary['Opportunity']['fields']['jjwg_maps_lng_c']['inline_edit']=1;

 

 // created: 2019-11-28 09:37:58
$dictionary['Opportunity']['fields']['project_lead_name']['inline_edit']=true;
$dictionary['Opportunity']['fields']['project_lead_name']['merge_filter']='disabled';

 

 // created: 2019-11-28 09:37:35
$dictionary['Opportunity']['fields']['project_lead_id']['inline_edit']=true;
$dictionary['Opportunity']['fields']['project_lead_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:03:28
$dictionary['Opportunity']['fields']['editsequence']['inline_edit']=true;
$dictionary['Opportunity']['fields']['editsequence']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['editsequence']['reportable']=true;

 

 // created: 2020-05-08 13:55:31
$dictionary['Opportunity']['fields']['name']['len']='100';
$dictionary['Opportunity']['fields']['name']['inline_edit']=true;
$dictionary['Opportunity']['fields']['name']['comments']='Name of the opportunity';
$dictionary['Opportunity']['fields']['name']['merge_filter']='disabled';
$dictionary['Opportunity']['fields']['name']['full_text_search']=array (
);

 

 // created: 2019-11-07 07:41:34
$dictionary['Opportunity']['fields']['jjwg_maps_address_c']['inline_edit']=1;

 

 // created: 2019-11-28 09:37:05
$dictionary['Opportunity']['fields']['team_set_id']['inline_edit']=true;
$dictionary['Opportunity']['fields']['team_set_id']['merge_filter']='disabled';

 
?>