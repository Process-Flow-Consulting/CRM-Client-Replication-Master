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



$layout_defs["AOS_Quotes"]["subpanel_setup"]["proposal_proposal_tracker"] =  array(
'order' => 90,
 'module' => 'oss_ProposalTracker',
 'subpanel_name' => 'default',
 'get_subpanel_data' => 'proposal_proposal_tracker',
 'title_key' => 'LBL_PROPOSAL_TRACKER_SUBPANEL_TITLE',
 'top_buttons' => array(
));

$layout_defs['AOS_Quotes']['subpanel_setup']['documents']['subpanel_name']='quotes_subpanel';
$layout_defs['AOS_Quotes']['subpanel_setup']['documents']['top_buttons'] = array(
		array('widget_class' => 'SubPanelTopCreateButton'),
		array (
				'widget_class' => 'SubPanelTopSelectProposalDocumentButton',
				'mode' => 'MultiSelect',
				'initial_filter_fields'=>array('proposal_docs'=>'proposal_docs')
		));



$layout_defs["AOS_Quotes"]["subpanel_setup"]["documents_quotes"] = array (
    'order' => 100,
    'module' => 'Documents',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'title_key' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
    'get_subpanel_data' => 'documents_quotes',
    'top_buttons' =>
    array (
        0 => array (
            'widget_class' => 'SubPanelTopCreateButton',
        ),
        1 => array (
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
        ),
    ),
);


 // created: 2019-12-23 07:25:10
$layout_defs["AOS_Quotes"]["subpanel_setup"]['activities'] = array (
  'order' => 10,
  'sort_order' => 'desc',
  'sort_by' => 'date_due',
  'title_key' => 'LBL_ACTIVITIES_SUBPANEL_TITLE',
  'type' => 'collection',
  'subpanel_name' => 'activities',
  'module' => 'Activities',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopCreateTaskButton',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopScheduleMeetingButton',
    ),
    2 => 
    array (
      'widget_class' => 'SubPanelTopScheduleCallButton',
    ),
    3 => 
    array (
      'widget_class' => 'SubPanelTopComposeEmailButton',
    ),
  ),
  'collection_list' => 
  array (
    'meetings' => 
    array (
      'module' => 'Meetings',
      'subpanel_name' => 'ForActivities',
      'get_subpanel_data' => 'aos_quotes_activities_1_meetings',
    ),
    'tasks' => 
    array (
      'module' => 'Tasks',
      'subpanel_name' => 'ForActivities',
      'get_subpanel_data' => 'aos_quotes_activities_1_tasks',
    ),
    'calls' => 
    array (
      'module' => 'Calls',
      'subpanel_name' => 'ForActivities',
      'get_subpanel_data' => 'aos_quotes_activities_1_calls',
    ),
  ),
  'get_subpanel_data' => 'activities',
);
$layout_defs["AOS_Quotes"]["subpanel_setup"]['history'] = array (
  'order' => 20,
  'sort_order' => 'desc',
  'sort_by' => 'date_modified',
  'title_key' => 'LBL_HISTORY',
  'type' => 'collection',
  'subpanel_name' => 'history',
  'module' => 'History',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopCreateNoteButton',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopArchiveEmailButton',
    ),
    2 => 
    array (
      'widget_class' => 'SubPanelTopSummaryButton',
    ),
  ),
  'collection_list' => 
  array (
    'meetings' => 
    array (
      'module' => 'Meetings',
      'subpanel_name' => 'ForHistory',
      'get_subpanel_data' => 'aos_quotes_activities_1_meetings',
    ),
    'tasks' => 
    array (
      'module' => 'Tasks',
      'subpanel_name' => 'ForHistory',
      'get_subpanel_data' => 'aos_quotes_activities_1_tasks',
    ),
    'calls' => 
    array (
      'module' => 'Calls',
      'subpanel_name' => 'ForHistory',
      'get_subpanel_data' => 'aos_quotes_activities_1_calls',
    ),
    'notes' => 
    array (
      'module' => 'Notes',
      'subpanel_name' => 'ForHistory',
      'get_subpanel_data' => 'aos_quotes_activities_1_notes',
    ),
    'emails' => 
    array (
      'module' => 'Emails',
      'subpanel_name' => 'ForHistory',
      'get_subpanel_data' => 'aos_quotes_activities_1_emails',
    ),
  ),
  'get_subpanel_data' => 'history',
);


//auto-generated file DO NOT EDIT
$layout_defs['AOS_Quotes']['subpanel_setup']['proposal_proposal_tracker']['override_subpanel_name'] = 'AOS_Quotes_subpanel_proposal_proposal_tracker';


//auto-generated file DO NOT EDIT
$layout_defs['AOS_Quotes']['subpanel_setup']['documents_aos_quotes']['override_subpanel_name'] = 'AOS_Quotes_subpanel_documents_aos_quotes';

?>