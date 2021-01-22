<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['AOS_Quotes']['fields']['date_time_sent'] = array(
    'required' => false,
    'name' => 'date_time_sent',
    'vname' => 'LBL_DATE_TIME_SENT',
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

$dictionary['AOS_Quotes']['fields']['date_time_received'] = array(
    'required' => false,
    'name' => 'date_time_received',
    'vname' => 'LBL_DATE_TIME_RECEIVED',
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

$dictionary['AOS_Quotes']['fields']['date_time_opened'] = array(
    'required' => false,
    'name' => 'date_time_opened',
    'vname' => 'LBL_DATE_TIME_OPENED',
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

$dictionary['AOS_Quotes']['fields']['contact_phone'] = array(
    'name' => 'contact_phone',
    'vname' => 'LBL_CONTACT_PHONE',
    'type' => 'phone',
	'dbType' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 255,
	'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['contact_fax'] = array(
    'name' => 'contact_fax',
    'vname' => 'LBL_CONTACT_FAX',
    'type' => 'phone',
	'dbType' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 255,
	'audited' => true,
);
$dictionary['AOS_Quotes']['fields']['purchase_order_num'] = array(
    'name' => 'purchase_order_num',
    'vname' => 'Purchase Order Num',
    'type' => 'varchar',
	'dbType' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 255,
	'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['contact_email'] = array(
    'name' => 'contact_email',
    'vname' => 'LBL_CONTACT_EMAIL',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 255,
	'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['date_time_delivery'] = array(
    'required' => false,
    'name' => 'date_time_delivery',
    'vname' => 'LBL_DATE_TIME_DELIVERY',
    'type' => 'datetimecombo',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',    
    'reportable' => true,    
);

$dictionary['AOS_Quotes']['fields']['delivery_timezone'] = array(
    'name' => 'delivery_timezone',
    'vname' => 'LBL_DELIVERY_TIMEZONE',
    'type' => 'enum',
    'len' => '100',
    'options' => 'us_timezone_dom',    
    'merge_filter' => 'enabled',    
);

$dictionary['AOS_Quotes']['fields']['delivery_method_email'] = array(
    'required' => false,
    'name' => 'delivery_method_email',
    'vname' => 'LBL_DELIVERY_METHOD_EMAIL',
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

$dictionary['AOS_Quotes']['fields']['delivery_method_fax'] = array(
    'required' => false,
    'name' => 'delivery_method_fax',
    'vname' => 'LBL_DELIVERY_METHOD_FAX',
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

$dictionary['AOS_Quotes']['fields']['delivery_method_both'] = array(
    'required' => false,
    'name' => 'delivery_method_both',
    'vname' => 'LBL_DELIVERY_METHOD_BOTH',
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

$dictionary['AOS_Quotes']['fields']['billing_address_street']=array(
    'name' => 'billing_address_street',
    'vname' => 'LBL_BILLING_ADDRESS_STREET',
    'type' => 'text',
    'rows' => 2,
    'cols' => 30,
	'audited' => true,
   
);


$dictionary['AOS_Quotes']['fields']['scheduled_proposal_sent'] = array(
    'required' => false,
    'name' => 'scheduled_proposal_sent',
    'vname' => 'LBL_SCHEDULED_PROPOSAL_SENT',
    'type' => 'bool',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => false,
    'reportable' => true,
    'calculated' => false,
    'len' => '255',
    'size' => '20',
    'default' => '0',
);


$dictionary['AOS_Quotes']['fields']['tz_date_time_delivery'] = array(
    'required' => false,
    'name' => 'tz_date_time_delivery',
    'vname' => 'LBL_TIMEZONE_DATE_TIME_DELIVERY',
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

$dictionary['AOS_Quotes']['fields']['subtotal_inclusion']=array(
	'name' => 'subtotal_inclusion',
	'vname' => 'LBL_SUBTOTAL',
	'type' => 'decimal',
	'len' => 26,
	'precision' => 6,
	'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['grand_subtotal']=array(
	'name' => 'grand_subtotal',
	'vname' => 'LBL_LIST_GRAND_TOTAL',
	'type' => 'decimal',
	'len' => 26,
	'precision' => 6,
	'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['skip_delivery_date']=array(
	'name' => 'skip_delivery_date',
	'vname' => 'LBL_SKIP_DELIVERY_DATE',
	'type' => 'bool',
	'default'=> 0,	
);

$dictionary['AOS_Quotes']['fields']['skip_delivery_method']=array(
	'name' => 'skip_delivery_method',
	'vname' => 'LBL_SKIP_DELIVERY_METHOD',
	'type' => 'bool',
	'default'=> 0,
	'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['skip_line_items']=array(
	'name' => 'skip_line_items',
	'vname' => 'LBL_SKIP_LINE_ITEMS',
	'type' => 'bool',
	'default'=> 0,
	'audited' => true,
);
 
$dictionary['AOS_Quotes']['fields']['account_proview_url'] = array (
		'name'=>'account_proview_url',
		'rname'=>'proview_url',
		'id_name'=>'account_id',
		'vname'=>'LBL_ACCOUNT_NAME',
		'type'=>'relate',
		'group'=>'billing_address',
		'link'=>'billing_accounts',
		'table'=>'billing_accounts',
		'isnull'=>'true',
		'module'=>'Accounts',
		'source'=>'non-db',
		'massupdate' => false,
		'studio' => 'false',
		
		
);
$dictionary['AOS_Quotes']['fields']['proposal_verified'] = array(
		'name' => 'proposal_verified',
		'vname' => 'LBL_PROPOSAL_VERIFIED',
		'type' => 'radioenum',
		'dbType' => 'enum',		
		'options' => 'radio_dom',
		'default' => '2',
	);
$dictionary['AOS_Quotes']['fields']['verify_email_sent'] = array(
		'name' => 'verify_email_sent',
		'vname' => 'LBL_VERIFY_EMAIL_SENT',
		'type' => 'bool',
		'default' => '0',		
	);
	
//BBSMP-69 -- Start
$dictionary['AOS_Quotes']['fields']['billing_address_state']['type'] = 'enum';
$dictionary['AOS_Quotes']['fields']['billing_address_state']['options'] = 'state_dom';
//BBSMP-69 -- End

$dictionary['AOS_Quotes']['fields']['name']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['description']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['purchase_order_num']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['subtotal']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['shipping']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['shipping_usdollar']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['discount']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['tax']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['tax_usdollar']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['total']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['total_usdollar']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_city']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_state']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_postalcode']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_account_name']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_account_id']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_contact_name']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['billing_contact_id']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['opportunity_name']['audited'] = true;
$dictionary['AOS_Quotes']['fields']['opportunity_id']['audited'] = true;

$dictionary['AOS_Quotes']['fields']['easy_email_verify_mrn'] = array(
		'name' => 'easy_email_verify_mrn',
		'vname' => 'LBL_EASY_MESSAGE_API_MRN',
		'type' => 'varchar',		
		'len' => 50,
		'audited' => false,
);
$dictionary['AOS_Quotes']['fields']['easy_fax_verify_mrn'] = array(
		'name' => 'easy_fax_verify_mrn',
		'vname' => 'LBL_EASY_MESSAGE_API_MRN',
		'type' => 'varchar',
		'len' => 50,
		'audited' => false,
);


$dictionary['AOS_Quotes']['fields']['proposal_delivery_method'] = array(
	'name' => 'proposal_delivery_method',
	'vname' => 'LBL_DELIVERY_METHOD',
	'audited' => true,
	'type' => 'radioenum',
	'dbType' => 'enum',
	'options' => 'proposal_delivery_method',
	'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'reportable' => true,
    'len' => 10,
    'size' => 20,
);

/**
 * proposal verisoning
 * Hirak - 07.02.2013
 */

$dictionary['AOS_Quotes']['fields']['is_proposal_modified'] = array(
		'name' => 'is_proposal_modified',
		'vname' => 'LBL_IS_PROPOSAL_MODIFIED',
		'type' => 'bool',
		'default' => '1',
		'audited' => false,
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'reportable' => false,
);

$dictionary['AOS_Quotes']['fields']['proposal_sent_count'] = array(
		'name' => 'proposal_sent_count',
		'vname' => 'LBL_PROPOSAL_SENT_COUNT',
		'type' => 'int',
		'default' => '0',
		'len' => 10,
		'audited' => false,
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'reportable' => false,
);

$dictionary['AOS_Quotes']['fields']['proposal_version'] = array(
		'name' => 'proposal_version',
		'vname' => 'LBL_PROPOSAL_VERSION',
        'default' => '1.0',        
		'type' => 'varchar',
		'len' => '20',
		'audited' => false,
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'reportable' => false,
);

$dictionary['AOS_Quotes']['fields']['verified_date'] = array(
        'name' => 'verified_date',
        'vname' => 'LBL_VERIFIED_DATE',
        'type' => 'datetimecombo',
        'dbType' => 'datetime',
);

$dictionary['AOS_Quotes']['fields']['proposal_amount']=array(
        'name' => 'proposal_amount',
        'vname' => 'LBL_PROPOSAL_AMOUNT',
        'type' => 'readonly',
        'dbType' => 'decimal',
        'len' => 26,
        'precision' => 2,
        'audited' => true,
);

$dictionary['AOS_Quotes']['fields']['layout_options'] = array(
        'name' => 'layout_options',
        'vname' => 'LBL_LAYOUT_OPTIONS',
        'type' => 'text',
        'dbType' => 'text',
);
$dictionary['AOS_Quotes']['fields']['editsequence'] = array (
        'required' => false,
        'name' => 'editsequence',
        'vname' => 'LBL_QUICKBOOK_EDITSEQUENCE',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook EditSequence',
        'help' => 'Quickbook EditSequence',
        'importable' => true,
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
        'len' => '10',
        'size' => '10',
);

/**
 * this field will be used in CRM for Estimate QuickBooks ID mapping
 * and this is the default and already existing field for proposal
 */
$dictionary['AOS_Quotes']['fields']['quickbooks_id'] = array (
        'required' => false,
        'name' => 'quickbooks_id',
        'vname' => 'LBL_QUICKBOOK_ID',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook Estimate Id',
        'help' => 'Quickbook Estimate Id',
        'importable' => true,
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '40',
        'size' => '40',
);
/**
 * this field will be used in CRM for Invoice QuickBooks ID mapping
 */
$dictionary['AOS_Quotes']['fields']['quickbooks_invoice_id'] = array (
        'required' => false,
        'name' => 'quickbooks_invoice_id',
        'vname' => 'LBL_QUICKBOOK_INVOICE_ID',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook Invoice Id',
        'help' => 'Quickbook Invoice Id',
        'importable' => true,
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '40',
        'size' => '40',
);
$dictionary['AOS_Quotes']['indices'][] = array(
        'name' =>'idx_quickbooks_id0',
        'type'=>'unique',
        'fields'=>array('quickbooks_id')
);
$dictionary['AOS_Quotes']['indices'][] = array(
        'name' =>'idx_quickbooks_id1',
        'type'=>'index',
        'fields'=>array('quickbooks_id'),
        'source'=>'non-db'
);

$dictionary['AOS_Quotes']['indices'][] = array(
        'name' =>'idx_quickbooks_invoice_id0',
        'type'=>'unique',
        'fields'=>array('quickbooks_invoice_id')
);
$dictionary['AOS_Quotes']['indices'][] = array(
        'name' =>'idx_quickbooks_invoice_id1',
        'type'=>'index',
        'fields'=>array('quickbooks_invoice_id'),
        'source'=>'non-db'
);
$dictionary['AOS_Quotes']['fields']['sales_tax_flag'] = array (
        'required' => false,
        'name' => 'sales_tax_flag',
        'vname' => 'LBL_SALES_TAX_FLAG',
        'type' => 'varchar',
        'massupdate' => 0,
        'importable' => true,
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '40',
        'size' => '40',
);

$dictionary['AOS_Quotes']['fields']['push_to_qb'] = array (
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
$dictionary['AOS_Quotes']['fields']['quickbooks_proposal_type'] = array (
        'required' => false,
        'name' => 'quickbooks_proposal_type',
        'vname' => 'LBL_QUICKBOOK_PROPOSAL_TYPE',
        'type' => 'text',
        'dbType' => 'text',
        'comments' => 'Quickbook Proposal Type',
        'help' => 'Quickbook Proposal Type',
        'importable' => false,
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
);
$dictionary['AOS_Quotes']['fields']['name']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['purchase_order_num']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['quote_stage']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['quote_num']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_street']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_city']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_state']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['billing_address_postalcode']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['date_time_delivery']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['description']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['total']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['contact_email']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['contact_fax']['qbimport'] = true;
$dictionary['AOS_Quotes']['fields']['contact_phone']['qbimport'] = true;



// created: 2019-12-23 07:25:10
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_activities_1_notes"] = array (
  'name' => 'aos_quotes_activities_1_notes',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_notes',
  'source' => 'non-db',
  'module' => 'Notes',
  'bean_name' => 'Note',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_NOTES_FROM_NOTES_TITLE',
);


$dictionary["AOS_Quotes"]["fields"]["proposal_proposal_tracker"] = array (
  'name' => 'proposal_proposal_tracker',
  'type' => 'link',
  'relationship' => 'proposal_proposal_tracker',
  'source' => 'non-db',
  'vname' => 'LBL_PROPOSAL_NAME',
);

$dictionary['AOS_Quotes']['relationships']['proposal_proposal_tracker'] = array(
    'lhs_module'=> 'Quotes', 'lhs_table'=> 'quotes', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_ProposalTracker', 'rhs_table'=> 'oss_proposaltracker', 'rhs_key' => 'proposal_id',
    'relationship_type'=>'one-to-many'
);




$dictionary["AOS_Quotes"]["fields"]["documents_quotes"] = array (
    'name' => 'documents_quotes',
    'type' => 'link',
    'relationship' => 'documents_quotes',
    'source' => 'non-db',
    'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE'
);


// created: 2019-12-23 07:25:10
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_activities_1_meetings"] = array (
  'name' => 'aos_quotes_activities_1_meetings',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_meetings',
  'source' => 'non-db',
  'module' => 'Meetings',
  'bean_name' => 'Meeting',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_MEETINGS_FROM_MEETINGS_TITLE',
);


// created: 2019-12-23 07:25:11
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_activities_1_emails"] = array (
  'name' => 'aos_quotes_activities_1_emails',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_emails',
  'source' => 'non-db',
  'module' => 'Emails',
  'bean_name' => 'Email',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_EMAILS_FROM_EMAILS_TITLE',
);


// created: 2019-12-23 07:25:10
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_activities_1_calls"] = array (
  'name' => 'aos_quotes_activities_1_calls',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_calls',
  'source' => 'non-db',
  'module' => 'Calls',
  'bean_name' => 'Call',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_CALLS_FROM_CALLS_TITLE',
);


$dictionary['AOS_Quotes']['fields']['team_set_id'] = array(
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

$dictionary['AOS_Quotes']['fields']['team_id'] = array(
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
	


// created: 2019-12-23 07:25:11
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_activities_1_tasks"] = array (
  'name' => 'aos_quotes_activities_1_tasks',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_tasks',
  'source' => 'non-db',
  'module' => 'Tasks',
  'bean_name' => 'Task',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_TASKS_FROM_TASKS_TITLE',
);


$dictionary['AOS_Quotes']['fields']['shipper_id'] = array(
    'name' => 'shipper_id',
    'vname' => 'LBL_SHIPPER_ID',
    'type' => 'id',
    'required'=>false,
    'do_report'=>false,
    'reportable'=>false,
);
$dictionary['AOS_Quotes']['fields']['shipper_name'] = array(
     'name' => 'shipper_name',
    'rname' => 'name',
    'id_name' => 'shipper_id',
    'join_name' => 'AOS_Shippers',
    'type' => 'relate',
    'link' => 'AOS_Shippers',
    'table' => 'aos_shippers',
    'isnull' => 'true',
    'module' => 'AOS_Shippers',
    'dbType' => 'varchar',
    'len' => '255',
    'vname' => 'LBL_SHIPPING_PROVIDER',
    'source'=>'non-db',
    'comment' => 'Shipper Name'
);
$dictionary["AOS_Quotes"]["fields"]["shippers"] = array (
	'name' => 'shippers',
	'type' => 'link',
	'relationship' => 'shipper_quotes',
	'vname' => 'LBL_SHIPPING_PROVIDER',
	'source'=>'non-db',
);



$dictionary['AOS_Quotes']['fields']['taxrate_id'] = array(
    'name' => 'taxrate_id',
    'vname' => 'LBL_TAXRATE_ID',
    'type' => 'id',
    'required'=>false,
    'do_report'=>false,
    'reportable'=>false,
);
$dictionary['AOS_Quotes']['fields']['subtotal'] = array(
    'name' => 'subtotal',
    'vname' => 'LBL_SUBTOTAL',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar'] = array(
    'name' => 'subtotal_usdollar',
    'group'=>'subtotal',
    'vname' => 'LBL_SUBTOTAL_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
);
$dictionary['AOS_Quotes']['fields']['tax'] = array(
    'name' => 'tax',
    'vname' => 'LBL_TAX',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['tax_usdollar'] = array(
    'name' => 'tax_usdollar',
    'vname' => 'LBL_TAX_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'tax',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
);
$dictionary['AOS_Quotes']['fields']['shipping'] = array(
     'name' => 'shipping',
    'vname' => 'LBL_SHIPPING',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['shipping_usdollar'] = array(
   'name' => 'shipping_usdollar',
    'vname' => 'LBL_SHIPPING_USDOLLAR',
    'group'=>'shipping',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['total'] = array(
    'name' => 'total',
    'vname' => 'LBL_TOTAL',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['total_usdollar'] = array(
    'name' => 'total_usdollar',
    'vname' => 'LBL_TOTAL_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'total',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
  	'enable_range_search' => true,
  	'options' => 'numeric_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['show_line_nums'] = array(
    'name' => 'show_line_nums',
    'vname' => 'LBL_SHOW_LINE_NUMS',
    'type' => 'bool',
    'default'=>true,
    'reportable'=>false,
    'massupdate'=>false,
	
);
$dictionary['AOS_Quotes']['fields']['calc_grand_total'] = array(
    'name' => 'calc_grand_total',
    'vname' => 'LBL_CALC_GRAND',
    'type' => 'bool',
    'reportable'=>false,
    'default'=>true,
    'massupdate' => false,
);
$dictionary['AOS_Quotes']['fields']['quote_type'] = array(
	'name' => 'quote_type',
	'vname' => 'LBL_QUOTE_TYPE',
	'type' => 'varchar',
	'len' => 100,
);
$dictionary['AOS_Quotes']['fields']['date_quote_expected_closed'] = array(
	'name' => 'date_quote_expected_closed',
    'vname' => 'LBL_DATE_QUOTE_EXPECTED_CLOSED',
    'type' => 'date',
    'audited'=>true,
    'reportable'=>true,
    'importable' => 'required',
    'required'=>true,
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['original_po_date'] = array(
	'name' => 'original_po_date',
    'vname' => 'LBL_ORIGINAL_PO_DATE',
    'type' => 'date',
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['payment_terms'] = array(
	'name' => 'payment_terms',
    'vname' => 'LBL_PAYMENT_TERMS',
    'type' => 'enum',
    'options' => 'payment_terms',
    'len' => '128',
);
$dictionary['AOS_Quotes']['fields']['date_quote_closed'] = array(
	'name' => 'date_quote_closed',
    'massupdate' => false,
    'vname' => 'LBL_DATE_QUOTE_CLOSED',
    'type' => 'date',
    'reportable'=>false,
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['date_order_shipped'] = array(
	'name' => 'date_order_shipped',
    'massupdate' => false,
    'vname' => 'LBL_LIST_DATE_QUOTE_CLOSED',
    'type' => 'date',
    'reportable' => false,
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['order_stage'] = array(
	'name' => 'order_stage',
    'vname' => 'LBL_ORDER_STAGE',
    'type' => 'enum',
    'options' => 'order_stage_dom',
    'massupdate'=>false,
    'len' => 100,
);
$dictionary['AOS_Quotes']['fields']['quote_stage'] = array(
	'name' => 'quote_stage',
    'vname' => 'LBL_QUOTE_STAGE',
    'type' => 'enum',
    'options' => 'quote_stage_dom',
    'len' => 100,
    'audited'=>true,
    'importable' => 'required',
    'required'=>true,
);
$dictionary['AOS_Quotes']['fields']['quote_num'] = array(
	'name' => 'quote_num',
    'vname' => 'LBL_QUOTE_NUM',
    'type' => 'int',
    'required'=>true,
    'unified_search' => true,
    'full_text_search' => array('boost' => 3),
    'options' => 'numeric_range_search_dom',
);
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar'] = array(
	'name' => 'subtotal_usdollar',
    'group'=>'subtotal',
    'vname' => 'LBL_SUBTOTAL_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
);
$dictionary['AOS_Quotes']['fields']['discount'] = array(
	'name' => 'discount',
    'vname' => 'LBL_DISCOUNT_TOTAL',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['deal_tot_usdollar'] = array(
	'name' => 'deal_tot_usdollar',
    'vname' => 'LBL_DEAL_TOT_USDOLLAR',
    'dbType' => 'decimal',
    'type' => 'decimal',
    'len' => '26,2',
);
$dictionary['AOS_Quotes']['fields']['deal_tot'] = array(
	'name' => 'deal_tot',
    'vname' => 'LBL_DEAL_TOT',
    'dbType' => 'decimal',
    'type' => 'decimal',
    'len' => '26,2',
);
$dictionary['AOS_Quotes']['fields']['new_sub'] = array(
	'name' => 'new_sub',
    'vname' => 'LBL_NEW_SUB',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['new_sub_usdollar'] = array(
	'name' => 'new_sub_usdollar',
    'vname' => 'LBL_NEW_SUB',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
);
$dictionary['AOS_Quotes']['fields']['system_id'] = array(
	'name' => 'system_id',
    'vname' => 'LBL_SYSTEM_ID',
    'type' => 'int',
); 



 // created: 2020-04-30 09:40:14
$dictionary['AOS_Quotes']['fields']['new_sub_usdollar']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['new_sub_usdollar']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['new_sub_usdollar']['enable_range_search']=false;

 

 // created: 2020-04-30 09:43:33
$dictionary['AOS_Quotes']['fields']['original_po_date']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['original_po_date']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:34:50
$dictionary['AOS_Quotes']['fields']['calc_grand_total']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['calc_grand_total']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['calc_grand_total']['reportable']=true;

 

 // created: 2020-04-30 09:34:24
$dictionary['AOS_Quotes']['fields']['show_line_nums']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['show_line_nums']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['show_line_nums']['reportable']=true;

 

 // created: 2020-04-30 09:42:47
$dictionary['AOS_Quotes']['fields']['date_quote_closed']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['date_quote_closed']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['date_quote_closed']['reportable']=true;

 

 // created: 2020-04-30 09:35:19
$dictionary['AOS_Quotes']['fields']['system_id']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['system_id']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['system_id']['enable_range_search']=false;
$dictionary['AOS_Quotes']['fields']['system_id']['min']=false;
$dictionary['AOS_Quotes']['fields']['system_id']['max']=false;
$dictionary['AOS_Quotes']['fields']['system_id']['disable_num_format']='';

 

 // created: 2020-04-30 09:31:42
$dictionary['AOS_Quotes']['fields']['team_id']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:44:01
$dictionary['AOS_Quotes']['fields']['date_quote_expected_closed']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['date_quote_expected_closed']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:33:44
$dictionary['AOS_Quotes']['fields']['tax_usdollar']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['tax_usdollar']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['tax_usdollar']['enable_range_search']=false;

 

 // created: 2020-04-30 09:32:38
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['subtotal_usdollar']['enable_range_search']=false;

 

 // created: 2020-04-30 09:41:59
$dictionary['AOS_Quotes']['fields']['deal_tot_usdollar']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['deal_tot_usdollar']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['deal_tot_usdollar']['enable_range_search']=false;

 

 // created: 2020-04-30 09:42:22
$dictionary['AOS_Quotes']['fields']['date_order_shipped']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['date_order_shipped']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['date_order_shipped']['reportable']=true;

 

 // created: 2020-04-30 09:32:59
$dictionary['AOS_Quotes']['fields']['shipping_usdollar']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['shipping_usdollar']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['shipping_usdollar']['enable_range_search']=false;

 

 // created: 2020-04-30 09:43:08
$dictionary['AOS_Quotes']['fields']['payment_terms']['len']=100;
$dictionary['AOS_Quotes']['fields']['payment_terms']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['payment_terms']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:40:48
$dictionary['AOS_Quotes']['fields']['deal_tot']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['deal_tot']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['deal_tot']['enable_range_search']=false;

 

 // created: 2020-04-30 09:41:29
$dictionary['AOS_Quotes']['fields']['order_stage']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['order_stage']['options']=' region_dom';
$dictionary['AOS_Quotes']['fields']['order_stage']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:31:56
$dictionary['AOS_Quotes']['fields']['team_set_id']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['team_set_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:33:23
$dictionary['AOS_Quotes']['fields']['discount']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['discount']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['discount']['enable_range_search']=false;

 
?>