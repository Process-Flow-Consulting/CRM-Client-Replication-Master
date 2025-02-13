<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['Lead']['duplicate_merge']= false;
$dictionary['Lead']['fields']['project_title'] = array(
    'name' => 'project_title',
    'vname' => 'LBL_PROJECT_TITLE',
    'type' => 'varchar',    
    'len' => 800,        
    'required' => true,
	'audited' => true,
	'importable' => 'required',
);

$dictionary['Lead']['fields']['mi_lead_id'] = array(
    'name' => 'mi_lead_id',
    'vname' => 'LBL_MI_LEAD_ID',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 36,
);


$dictionary['Lead']['fields']['stories_above_grade'] = array(
    'name' => 'stories_above_grade',
    'vname' => 'LBL_STORIES_ABOVE_GRADE',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);
$dictionary['Lead']['fields']['stories_below_grade'] = array(
    'name' => 'stories_below_grade',
    'vname' => 'LBL_STORIES_BELOW_GRADE',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);

$dictionary['Lead']['fields']['number_of_buildings'] = array(
    'name' => 'number_of_buildings',
    'vname' => 'LBL_NUMBER_OF_BUILDINGS',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);

$dictionary['Lead']['fields']['square_footage'] = array(
    'name' => 'square_footage',
    'vname' => 'LBL_SQUARE_FOOTAGE',
    'type' => 'int',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
	'enable_range_search' => true, //added valuation to range search -- jul 1- hirak
	'options' => 'numeric_range_search_dom', //added valuation to range search -- jul 1- hirak
);


$dictionary['Lead']['fields']['type'] = array(
    'name' => 'type',
    'vname' => 'LBL_TYPE',
    'type' => 'enum',
    'len' => '100',
    'options' => 'project_type_dom',
    'audited' => true,
    'comment' => 'TYPE OF LEAD',
    'merge_filter' => 'enabled',
);

$dictionary['Lead']['fields']['structure'] = array(
    'name' => 'structure',
    'vname' => 'LBL_STRUCTURE',
    'type' => 'enum',
    'len' => '100',
    'function' => 'getBluebookStructureDom',
    'audited' => true,
    'comment' => 'STRUCTURE OF LEAD',
    'merge_filter' => 'enabled',
);
$dictionary['Lead']['fields']['owner'] = array(
    'name' => 'owner',
    'vname' => 'LBL_OWNER',
    'type' => 'enum',
    'len' => '100',
    'options' => 'owner_dom',
    'audited' => true,
    'comment' => 'OWNER OF LEAD',
    'merge_filter' => 'enabled',
);

$dictionary['Lead']['fields']['state'] = array(
    'name' => 'state',
    'vname' => 'LBL_STATE',
    'type' => 'enum',
    'len' => '100',
    'options' => 'state_dom',
    'audited' => true,
    'comment' => 'STATE OF LEAD',
    'merge_filter' => 'enabled',
);

$dictionary['Lead']['fields']['address'] = array(
    'name' => 'address',
    'vname' => 'LBL_ADDRESS',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 255,
	'audited' => true,
);
$dictionary['Lead']['fields']['city'] = array(
    'name' => 'city',
    'vname' => 'LBL_CITY',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 255,
	'audited' => true,
);
$dictionary['Lead']['fields']['zip_code'] = array(
    'name' => 'zip_code',
    'vname' => 'LBL_ZIP_CODE',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);


$dictionary['Lead']['fields']['scope_of_work'] = array(
    'name' => 'scope_of_work',
    'vname' => 'LBL_SCOPE_OF_WORK',
    'type' => 'text',
    'merge_filter' => 'enabled',
	'audited' => true,
	//BBSMP-257 -- Start
	'editor' => 'html',
	//BBSFM-257 -- End
);
$dictionary['Lead']['fields']['contact_no'] = array(
    'name' => 'contact_no',
    'vname' => 'LBL_CONTACT_NO',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);

$dictionary['Lead']['fields']['valuation'] = array(
    'name' => 'valuation',
    'vname' => 'LBL_VALUATION',
    'type' => 'decimal',
    'merge_filter' => 'enabled',
    'len' => 50,
    'precision' => '2',
	'audited' => true,
	'enable_range_search' => true, //added valuation to range search -- jul 1- hirak
	'options' => 'numeric_range_search_dom', //added valuation to range search -- jul 1- hirak
);

$dictionary['Lead']['fields']['received'] = array(
    'name' => 'received',
    'vname' => 'LBL_RECEIVED',
    'type' => 'date',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);
$dictionary['Lead']['fields']['start_date'] = array(
    'name' => 'start_date',
    'vname' => 'LBL_START_DATE',
    'type' => 'date',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);
$dictionary['Lead']['fields']['end_date'] = array(
    'name' => 'end_date',
    'vname' => 'LBL_END_DATE',
    'type' => 'date',
    'merge_filter' => 'enabled',
    'len' => 50,
	'audited' => true,
);
$dictionary['Lead']['fields']['pre_bid_meeting'] = array(
    'name' => 'pre_bid_meeting',
    'vname' => 'LBL_PRE_BID_MEETING',
    'type' => 'datetimecombo',
    'dbType' => 'datetime',
    'group' => 'pre_bid_meeting',
    'studio' => array('required' => true, 'no_duplicate' => true),
    'options' => 'date_range_search_dom',
	'audited' => true,
);
$dictionary['Lead']['fields']['bids_due'] = array(
    'name' => 'bids_due',
    'vname' => 'LBL_BIDS_DUE',
    'type' => 'datetimecombo',
    'dbType' => 'datetime',
    'group' => 'bids_due',
    'studio' => array('required' => true, 'no_duplicate' => true),
	'enable_range_search' => true,
    'options' => 'lead_opp_date_range_dom',
	'audited' => true,
);
$dictionary['Lead']['fields']['union_c'] = array(
    'required' => false,
    'name' => 'union_c',
    'vname' => 'LBL_UNION',
    'type' => 'bool',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'calculated' => false,
    'len' => '255',
    'size' => '20',
        );

$dictionary['Lead']['fields']['non_union'] = array(
    'required' => false,
    'name' => 'non_union',
    'vname' => 'LBL_NON_UNION',
    'type' => 'bool',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'calculated' => false,
    'len' => '255',
    'size' => '20',
        );
$dictionary['Lead']['fields']['prevailing_wage'] = array(
    'required' => false,
    'name' => 'prevailing_wage',
    'vname' => 'LBL_PREVAILING_WAGE',
    'type' => 'bool',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'calculated' => false,
    'len' => '255',
    'size' => '20',
        );


$dictionary['Lead']['fields']['project_lead_id'] = array(
    'required' => false,
    'name' => 'project_lead_id',
    'vname' => 'LBL_PROJECT_LEAD_ID',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'audited' => 1,
    'reportable' => 1,
    'len' => '50',
	'audited' => true,
);


$dictionary['Lead']['fields']['bid_due_timezone'] = array(
    'name' => 'bid_due_timezone',
    'vname' => 'LBL_BID_DUE_TIMEZONE',
    'type' => 'enum',
    'len' => '100',
    'options' => 'bid_due_timezone_list',
    'audited' => true,
    'merge_filter' => 'enabled',
);

$dictionary['Lead']['fields']['asap'] = array(
    'required' => false,
    'name' => 'asap',
    'vname' => 'LBL_ASAP',
    'type' => 'bool',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'calculated' => false,
    'len' => '255',
    'size' => '20',
);

$dictionary['Lead']['fields']['project_status'] = array(
    'name' => 'project_status',
    'vname' => 'LBL_PROJECT_STATUS',
    'type' => 'enum',
    'len' => '100',
    'options' => 'project_status_dom',
    'audited' => true,
    'comment' => 'STATUS OF PROJECT',
    'merge_filter' => 'enabled',
);

$dictionary['Lead']['fields']['county_id'] = array(
    'name' => 'county_id',
    'vname' => 'LBL_COUNTY_ID',
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

$dictionary['Lead']['fields']['county'] =
        array(
            'required' => false,
            'source' => 'non-db',
            'name' => 'county',
            'vname' => 'LBL_COUNTY',
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
            'id_name' => 'county_id',
            'ext2' => 'oss_County',
            'module' => 'oss_County',
            'rname' => 'name',
            'quicksearch' => 'enabled',
            'studio' => 'visible',
        );
		
$dictionary['Lead']['fields']['is_modified'] = array (
		'required' => false,
		'name' => 'is_modified',
		'vname' => 'LBL_IS_MODIFIED',
		'type' => 'bool',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'merge_filter' => 'enabled',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		//'audited' => true,
		'reportable' => true,
		'calculated' => false,
		'size' => '20',
);

$dictionary['Lead']['fields']['dodge_id'] = array (
		'required' => false,
		'name' => 'dodge_id',
		'vname' => 'LBL_DODGE_ID',
		'type' => 'varchar',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		//'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);
$dictionary['Lead']['fields']['reed_id'] = array (
		'required' => false,
		'name' => 'reed_id',
		'vname' => 'LBL_REED_ID',
		'type' => 'varchar',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		//'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);
//BBSFM-135 -- Start
$dictionary['Lead']['fields']['client_classification'] = array (
		'required' => false,
		'name' => 'client_classification',
		'vname' => 'LBL_CLIENT_CLASSIFICATION',
		'type' => 'enum',
		'source'=>'non-db',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		//'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);

$dictionary['Lead']['fields']['bidders_role'] = array (
		'required' => false,
		'name' => 'bidders_role',
		'vname' => 'LBL_BIDDERS_ROLE',
		'type' => 'enum',
		'source'=>'non-db',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		//'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
		'options' => 'role_dom',
);
//BBSFM-135 -- End
$dictionary['Lead']['fields']['change_log_flag'] = array(
	'name' => 'change_log_flag',
	'vname' => 'LBL_CHANGE_LOG_FLAG',
	'type' => 'int',
	'len' => '1',
	'default' => '0'		
);
$dictionary['Lead']['fields']['onvia_id'] = array (
		'required' => false,
		'name' => 'onvia_id',
		'vname' => 'LBL_ONVIA_ID',
		'type' => 'varchar',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		//'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);

$dictionary['Lead']['fields']['pl_bid_status'] = array (
		'required' => false,
		'name' => 'pl_bid_status',
		'vname' => 'LBL_PL_BID_STATUS',
		'type' => 'varchar',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);

$dictionary['Lead']['fields']['converted_date'] = array(
        'name' => 'converted_date',
        'vname' => 'LBL_CONVERTED_DATE',
        'type' => 'datetimecombo',
        'dbType' => 'datetime',
);

$dictionary['Lead']['fields']['unique_identifier_id'] = array (
        'required' => false,
        'name' => 'unique_identifier_id',
        'vname' => 'LBL_UNIQUE_IDENTIFIER_ID',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => '',
        'help' => '',
        'importable' => 'true',
        'duplicate_merge' => 'disabled',
        'duplicate_merge_dom_value' => '0',
        'audited' => true,
        'reportable' => true,
        'len' => '255',
        'size' => '20',
);

$dictionary['Lead']['fields']['is_archived'] = array(
        'name' => 'is_archived',
        'vname' => 'LBL_INCLUDE_ARCHIVE',
        'type' => 'bool',
        'default' => 0,
        'audited' => true,
        'comment' => 'STATUS OF LEAD',
        'massupdate' => 1,
);
$dictionary['Lead']['fields']['parent_lead_id'] =
        array(
            'required' => false,
            'name' => 'parent_lead_id',
            'vname' => 'LBL_LEAD_ID',
            'type' => 'char',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            //'audited' => true,
            'reportable' => true,
            'len' => '36',
            'size' => '20',
);

$dictionary['Lead']['fields']['lead_name'] =
        array(
            'required' => false,
            'source' => 'non-db',
            'name' => 'lead_name',
            'vname' => 'LBL_LEAD_PARENT',
            'type' => 'relate',
            'massupdate' => 0,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            //'audited' => false,
            'reportable' => true,
            'len' => '255',
            'size' => '20',
            'id_name' => 'parent_lead_id',
            'ext2' => 'Leads',
            'module' => 'Leads',
            'rname' => 'project_title',
            'quicksearch' => 'enabled',
            'studio' => 'visible',
        );
$dictionary["Lead"]["fields"]["lead_to_lead_var"] = array (
  'name' => 'lead_to_lead_var',
  'type' => 'link',
  'relationship' => 'lead_to_lead',
  'source' => 'non-db',
  'vname' => 'LBL_LEAD_PARENT',
);
$dictionary['Lead']['relationships']['lead_to_lead'] = array('lhs_module'=> 'Leads', 'lhs_table'=> 'leads', 'lhs_key' => 'id',
							  'rhs_module'=> 'Leads', 'rhs_table'=> 'leads', 'rhs_key' => 'parent_lead_id',
							  'relationship_type'=>'one-to-many');
							 
$dictionary['Lead']['fields']['project_url'] = array(
    'name' => 'project_url',
    'vname' => 'LBL_PROJECT_URL',
    'type' => 'text',
    'merge_filter' => 'enabled',
);
$dictionary['Lead']['fields']['project_url_l'] = array(
    'name' => 'project_url_l',
    'vname' => 'LBL_PROJECT_URL_L',
    'type' => 'text',
    'merge_filter' => 'enabled',
);

$dictionary['Lead']['fields']['test'] = array(
    'name' => 'test',
    'vname' => 'LBL_TEST',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 50,
);							 
//BBSFM-25 -- START					 
$dictionary["Lead"]["fields"]["lead_version"] = array (
  'name' => 'lead_version',    
  'source' => 'non-db',
  'type' => 'int',
  'vname' => 'LBL_LEAD_VERSION',
);
$dictionary["Lead"]["fields"]["lead_pre_bid_to"] = array (
  'name' => 'lead_pre_bid_to',
  'source' => 'non-db',
  'type' => 'int',
  'vname' => 'LBL_LEAD_PREVIOUS_BID_TO',
);

$dictionary["Lead"]["fields"]["lead_new_total"] = array (
  'name' => 'lead_new_total',
  'source' => 'non-db',
  'type' => 'varchar',
  'vname' => 'LBL_LEAD_NEW_TOTAL',
);

$dictionary["Lead"]["fields"]["lead_plans"] = array (
  'name' => 'lead_plans',
  'source' => 'non-db',
  'type' => 'varchar',
  'vname' => 'LBL_LEAD_PLANS',
);

$dictionary["Lead"]["fields"]["lead_to_opportunity_var"] = array (
  'name' => 'lead_to_opportunity_var',
  'type' => 'link',
  'relationship' => 'lead_to_opportunity',
  'source' => 'non-db',
  'vname' => 'LBL_PROJECT_LEAD',
);
$dictionary["Lead"]["fields"]["prev_bid_to"] = array (
  'name' => 'prev_bid_to',    
  'source' => 'non-db',
  'type' => 'varchar',
  'vname' => 'LBL_PREV_BID_TO',
);

$dictionary["Lead"]["fields"]["new_total"] = array (
  'name' => 'new_total',    
  'source' => 'non-db',
  'type' => 'varchar',
  'vname' => 'LBL_NEW_TOTAL',
);

$dictionary['Lead']['fields']['bids_due_tz']=array(
	'name' => 'bids_due_tz',
	'vname' => 'LBL_BIDS_DUE',
	'type' => 'char',
	'source' => 'non-db'
);
//BBSFM-25 -- END

$dictionary['Lead']['indices'][4] = array (
	'name' => 'idx_mi_lead_id',
	'type' => 'unique',
	'fields' => array ('mi_lead_id'));
	



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



// created: 2011-10-13 16:29:11
$dictionary["Lead"]["fields"]["leads_accounts"] = array (
  'name' => 'leads_accounts',
  'type' => 'link',
  'relationship' => 'leads_accounts',
  'source' => 'non-db',
  'vname' => 'LBL_LEADS_ACCOUNTS_FROM_ACCOUNTS_TITLE',
);


// created: 2012-05-21 17:10:30
$dictionary["Lead"]["fields"]["leads_online_plans"] = array (
  'name' => 'leads_online_plans',
  'type' => 'link',
  'relationship' => 'leads_online_plans',
  'source' => 'non-db',
  'vname' => 'LBL_LEADS_ONLINE_PLAN_SUBPANEL_TITLE',
);
$dictionary['Lead']['relationships']['leads_online_plans'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_OnlinePlans',
 'rhs_table'=> 'oss_onlineplans',
 'rhs_key' => 'lead_id',
 'relationship_type'=>'one-to-many'
);

$dictionary["Lead"]["fields"]["lead_to_opportunity_var_parent"] = array (
  'name' => 'lead_to_opportunity_var_parent',
  'type' => 'link',
  'relationship' => 'lead_to_opportunity_var_parent',
  'source' => 'non-db',
  'vname' => 'LBL_PARENT_OPPORTUNITY_SUBPANEL_TITLE',
);
$dictionary['Lead']['relationships']['lead_to_opportunity_var_parent'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'Opportunities',
 'rhs_table'=> 'opportunities',
 'rhs_key' => 'project_lead_id',
 'relationship_type'=>'one-to-many'
);




$dictionary["Lead"]["fields"]["documents_leads"] = array (
    'name' => 'documents_leads',
    'type' => 'link',
    'relationship' => 'documents_leads',
    'source' => 'non-db',
    'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE'
);

$dictionary["Lead"]["fields"]["lead_to_opportunity_var"] = array (
  'name' => 'lead_to_opportunity_var',
  'type' => 'link',
  'relationship' => 'lead_to_opportunity_var',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITY_SUBPANEL_TITLE',
);
$dictionary['Lead']['relationships']['lead_to_opportunity_var'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'Opportunities',
 'rhs_table'=> 'opportunities',
 'rhs_key' => 'project_lead_id',
 'relationship_type'=>'one-to-many'
);




$dictionary['Lead']['fields']['team_set_id'] = array(
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

$dictionary['Lead']['fields']['team_id'] = array(
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


// created: 2011-11-03 14:05:19
$dictionary["Lead"]["fields"]["oss_classification_leads"] = array (
  'name' => 'oss_classification_leads',
  'type' => 'link',
  'relationship' => 'oss_classification_leads',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_CLASSIFICATION_LEADS_FROM_OSS_CLASSIFICATION_TITLE',
);


 // created: 2019-11-27 13:27:27
$dictionary['Lead']['fields']['state']['inline_edit']=true;
$dictionary['Lead']['fields']['state']['comments']='STATE OF LEAD';
$dictionary['Lead']['fields']['state']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:28
$dictionary['Lead']['fields']['jjwg_maps_lat_c']['inline_edit']=1;

 

 // created: 2019-11-28 07:10:26
$dictionary['Lead']['fields']['lead_source']['inline_edit']=true;
$dictionary['Lead']['fields']['lead_source']['options']='lead_source_list';
$dictionary['Lead']['fields']['lead_source']['comments']='Lead source (ex: Web, print)';
$dictionary['Lead']['fields']['lead_source']['merge_filter']='disabled';

 

 // created: 2019-11-28 06:34:37
$dictionary['Lead']['fields']['team_id']['inline_edit']=true;
$dictionary['Lead']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-03-03 05:10:55
$dictionary['Lead']['fields']['bids_due']['inline_edit']=true;
$dictionary['Lead']['fields']['bids_due']['options']='date_range_search_dom';
$dictionary['Lead']['fields']['bids_due']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:28
$dictionary['Lead']['fields']['jjwg_maps_geocode_status_c']['inline_edit']=1;

 

 // created: 2019-11-07 07:41:27
$dictionary['Lead']['fields']['jjwg_maps_lng_c']['inline_edit']=1;

 

 // created: 2019-11-28 08:53:38
$dictionary['Lead']['fields']['project_status']['inline_edit']=true;
$dictionary['Lead']['fields']['project_status']['comments']='STATUS OF PROJECT';
$dictionary['Lead']['fields']['project_status']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:06:09
$dictionary['Lead']['fields']['parent_lead_id']['inline_edit']=true;
$dictionary['Lead']['fields']['parent_lead_id']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:29
$dictionary['Lead']['fields']['jjwg_maps_address_c']['inline_edit']=1;

 

 // created: 2019-11-28 06:32:07
$dictionary['Lead']['fields']['test']['inline_edit']=true;
$dictionary['Lead']['fields']['test']['merge_filter']='disabled';

 

 // created: 2019-11-28 06:34:22
$dictionary['Lead']['fields']['team_set_id']['inline_edit']=true;
$dictionary['Lead']['fields']['team_set_id']['merge_filter']='disabled';

 

 // created: 2019-11-28 08:33:25
$dictionary['Lead']['fields']['bid_due_timezone']['options']='us_timezone_dom';

 
?>