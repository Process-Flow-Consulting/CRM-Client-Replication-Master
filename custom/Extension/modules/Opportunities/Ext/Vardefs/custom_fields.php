<?php
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
?>