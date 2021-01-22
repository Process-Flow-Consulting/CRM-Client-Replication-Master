<?php 
 //WARNING: The contents of this file are auto-generated


// created: 2011-11-14 11:11:29
$dictionary["Account"]["fields"]["oss_classifation_accounts"] = array (
  'name' => 'oss_classifation_accounts',
  'type' => 'link',
  'relationship' => 'oss_classification_accounts',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_CLASSIFICATION_ACCOUNTS_FROM_OSS_CLASSIFICATION_TITLE',
);


$dictionary['Account']['fields']['address1'] = array (
   'name' => 'address1',
   'vname' => 'LBL_ADDRESS1',
   'type' => 'varchar',
   'merge_filter' => 'enabled',
   'len' => 50,
   'audited' => true
);
$dictionary['Account']['fields']['address2'] = array (
   'name' => 'address2',
   'vname' => 'LBL_ADDRESS2',
   'type' => 'varchar',
   'merge_filter' => 'enabled',
   'len' => 50,
   'audited' => true
);
$dictionary['Account']['fields']['lead_source'] = array(
    'name' => 'lead_source',
    'vname' => 'LBL_LEAD_SOURCE',
    'type' => 'enum',
    'len' => '50',
    'options' => 'lead_source_list',
	'audited' => true
);
$dictionary['Account']['fields']['previous_bid_to'] = array (
	'required' => false,
	'name' => 'previous_bid_to',
	'vname' => 'LBL_PREVIOUS_BID_TO',
	'type' => 'int',
	'massupdate' => 0,
	'comments' => '',
	'help' => '',
	'importable' => 'fasle',
	'duplicate_merge' => 'disabled',
	'duplicate_merge_dom_value' => '0',
	'audited' => true,
	'reportable' => true,
	'studio' => 'hidden',
	'len' => '11',
	'size' => '20',
);
$dictionary['Account']['fields']['county_id'] = array(
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
	'reportable' => false,
	'len' => '36',
	'size' => '20',
);

$dictionary['Account']['fields']['county'] = array(
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
	'audited' => true,
	'reportable' => false,
	'len' => '255',
	'size' => '20',
	'id_name' => 'county_id',
	'ext2' => 'oss_County',
	'module' => 'oss_County',
	'rname' => 'name',
	'quicksearch' => 'enabled',
	'studio' => 'visible',
);
$dictionary['Account']['fields']['county_name'] = array(
		'required' => false,
		'name' => 'county_name',
		'vname' => 'LBL_COUNTY_NAME',
		'type' => 'varchar',
		'default_value' => false,
		'help' => 'County Name',
		'comment' => 'County Name',
		'audited' => true,
		'mass_update' => false,
		'duplicate_merge' => false,
		'reportable' => true,
		'importable' => false,
);
$dictionary['Account']['fields']['default_message'] = array (
   'name' => 'default_message',
   'vname' => 'LBL_DEFAULT_MESSAGE',
   'type' => 'varchar',
   'merge_filter' => 'enabled',
   'len' => 50,
   'audited' => true
);
$dictionary['Account']['fields']['delivery_method'] = array (
   'name' => 'delivery_method',
   'vname' => 'LBL_DELIVERY_METHOD',
   'type' => 'varchar',
   'merge_filter' => 'enabled',
   'len' => 50,
   'audited' => true
);
$dictionary['Account']['fields']['dodge_id'] = array (
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
		'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);
$dictionary['Account']['fields']['editsequence'] = array (
        'required' => false,
        'name' => 'editsequence',
        'vname' => 'LBL_QUICKBOOK_EDITSEQUENCE',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook EditSequence',
        'help' => 'Quickbook EditSequence',
        'importable' => 'true',
        'duplicate_merge' => 'disabled',
        'audited' => false,
        'reportable' => false,
        'len' => '10',
        'size' => '10',
);
$dictionary['Account']['fields']['first_classification'] = array (
		'required' => false,
		'name' => 'first_classification',
		'vname' => 'LBL_FIRST_CLASSIFICATION',
		'type' => 'varchar',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'false',
		'duplicate_merge' => 'disabled',		
		'audited' => true,
		'reportable' => false,
		'len' => '255',
		'size' => '20',
);
$dictionary['Account']['fields']['geographical_areas_serviced'] = array (
   'name' => 'geographical_areas_serviced',
   'vname' => 'LBL_GEOGRAPHICAL_AREAS_SERVICED',
   'type' => 'varchar',
   'merge_filter' => 'enabled',
   'len' => 1000,
   'audited' => true
);
$dictionary['Account']['fields']['international'] = array(
		'required' => false,
		'name' => 'international',
        'vname' => 'LBL_INTERNATIONAL',
        'type' => 'bool',
        'default_value' => false, 
        'help' => 'International Clients',
        'comment' => 'International Clients',
        'audited' => true, 
        'mass_update' => false, 
        'duplicate_merge' => false, 
        'reportable' => true, 
        'importable' => 'true', 
);
$dictionary['Account']['fields']['is_bb_update'] = array (
		'required' => false,
		'name' => 'is_bb_update',
		'vname' => 'LBL_IS_BB_UPDATE',
		'type' => 'bool',
		'massupdate' => 0,				
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'audited' => true,
		'reportable' => true,
		'len' => '1',
		'default' => '0',	
);
$dictionary['Account']['fields']['is_modified'] = array (
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
		'audited' => true,
		'reportable' => true,
		'calculated' => false,
		'size' => '20',
);
$dictionary['Account']['fields']['license'] = array (
   'name' => 'license',
   'vname' => 'LBL_LICENSE',
   'type' => 'varchar',
   'merge_filter' => 'enabled',
   'len' => 1000,
   'audited' => true
);
$dictionary['Account']['fields']['mi_account_id'] = array(
    'name' => 'mi_account_id',
    'vname' => 'LBL_MI_ACCOUNT_ID',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 36,
);
$dictionary['Account']['fields']['onvia_id'] = array (
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
		'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);
$dictionary['Account']['fields']['phone_office_ext'] = array (
        'required' => false,
        'name' => 'phone_office_ext',
        'vname' => 'LBL_PHONE_EXTENSION',
        'type' => 'varchar',
        'massupdate' => false,
        'comments' => 'Office Phone Extension.',
        'help' => 'Office Phone Extension.',
        'importable' => 'true',
        'len'=> 4,
        'duplicate_merge' => 'enabled',
        'audited' => true,
        'reportable' => true,
         
);
$dictionary['Account']['fields']['proview_url'] = array (
		'required' => false,
		'name' => 'proview_url',
		'vname' => 'LBL_PRO_VIEW_URL',
		'type' => 'varchar',
		'massupdate' => 0,
		'comments' => 'Stores proview url for a client',
		'help' => 'Proview url for client',
		'importable' => 'true',
		'duplicate_merge' => 'disabled',		
		'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '40',
);
$dictionary['Account']['fields']['pulled_date'] = array(
    'name' => 'pulled_date',
    'vname' => 'LBL_LAST_PULLED_DATE',
    'type' => 'datetimecombo',
    'dbType' => 'datetime',       
);
$dictionary['Account']['fields']['push_to_qb'] = array (
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
$dictionary['Account']['fields']['quickbooks_id'] = array (
        'required' => false,
        'name' => 'quickbooks_id',
        'vname' => 'LBL_QUICKBOOK_ID',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => 'Quickbook Id',
        'help' => 'Quickbook Id',
        'importable' => 'true',
        'duplicate_merge' => 'disabled',
        'audited' => true,
        'reportable' => false,
        'len' => '40',
        'size' => '40',
);
$dictionary['Account']['fields']['reed_id'] = array (
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
		'audited' => true,
		'reportable' => true,
		'len' => '255',
		'size' => '20',
);
$dictionary['Account']['fields']['show_update_icon'] = array (
        'required' => false,
        'name' => 'show_update_icon',
        'vname' => 'LBL_SHOW_BB_UPDATE_ICON',
        'type' => 'bool',
        'massupdate' => 0,
        'duplicate_merge' => 'disabled',
        'duplicate_merge_dom_value' => '0',
        'audited' => false,
        'reportable' => false,
        'len' => '1',
        'default' => '0',
);
$dictionary['Account']['fields']['unique_identifier_id'] = array (
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
$dictionary['Account']['fields']['visibility'] = array (
   'required' => false,
   'name' => 'visibility',
   'vname' => 'LBL_VISIBILITY',
   'type' => 'bool',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'default' => '1',
   'reportable' => false,
   'calculated' => false,
   'len' => '255',
   'size' => '20',
   );
   
   
   /* $dictionary['Account']['fields']['work_types'] = array (
   'required' => false,
   'name' => 'work_types',
   'vname' => 'LBL_WORK_TYPES',
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

$dictionary['Account']['fields']['memberships_assoc_certs'] = array (
   'required' => false,
   'name' => 'memberships_assoc_certs',
   'vname' => 'LBL_MEMEBERSHIPS_ASSOC_CERTS',
   'type' => 'text',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'rows' => '4',
   'cols' => '20',
);

$dictionary['Account']['fields']['bim_certified'] = array (
   'required' => false,
    'name' => 'bim_certified',
   'vname' => 'LBL_BIM_CERTIFIED',
   'type' => 'bool',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'merge_filter' => 'enabled',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'calculated' => false,
   'len' => '255',
   'size' => '20',
);

$dictionary['Account']['fields']['leed_certified'] = array (
   'required' => false,
   'name' => 'leed_certified',
   'vname' => 'LBL_LEED_CERTIFIED',
   'type' => 'bool',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'merge_filter' => 'enabled',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'calculated' => false,
   'len' => '255',
   'size' => '20',
);

$dictionary['Account']['fields']['products_brands_services'] = array (
   'required' => false,
   'name' => 'products_brands_services',
   'vname' => 'LBL_PRODUCTS_BRANDS_SERVICES',
   'type' => 'text',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'rows' => '4',
   'cols' => '20',
);

$dictionary['Account']['fields']['structure_types'] = array (
   'required' => false,
   'name' => 'structure_types',
   'vname' => 'LBL_STRUCTURE_TYPES',
   'type' => 'text',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'rows' => '4',
   'cols' => '20',
);

$dictionary['Account']['fields']['sector_private'] = array (
   'required' => false,
   'name' => 'sector_private',
   'vname' => 'LBL_PRIVATE',
   'type' => 'bool',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'merge_filter' => 'enabled',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'calculated' => false,
   'len' => '255',
   'size' => '20',
);

$dictionary['Account']['fields']['sector_public'] = array (
   'required' => false,
   'name' => 'sector_public',
   'vname' => 'LBL_PUBLIC',
   'type' => 'bool',
   'massupdate' => 0,
   'comments' => '',
   'help' => '',
   'importable' => 'true',
   'merge_filter' => 'enabled',
   'duplicate_merge' => 'disabled',
   'duplicate_merge_dom_value' => '0',
   'audited' => true,
   'reportable' => true,
   'calculated' => false,
   'len' => '255',
   'size' => '20',
);

$dictionary['Account']['fields']['union_c'] = array (
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

$dictionary['Account']['fields']['non_union'] = array (
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

$dictionary['Account']['fields']['prevailing_wage'] = array (
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
   
$dictionary['Account']['fields']['custom_field_1'] = array (
   'required' => false,
   'name' => 'custom_field_1',
   'vname' => 'LBL_CUSTOM_FIELD_1',
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

$dictionary['Account']['fields']['custom_field_2'] = array (
   'required' => false,
   'name' => 'custom_field_2',
   'vname' => 'LBL_CUSTOM_FIELD_2',
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
   */



// created: 2012-03-21 12:09:39
$dictionary["Account"]["fields"]["opportunities_accounts"] = array (
  'name' => 'opportunities_accounts',
  'type' => 'link',
  'relationship' => 'opportunities_accounts',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITIES_ACCOUNTS_FROM_OPPORTUNITIES_TITLE',
);


// created: 2011-10-13 16:29:11
$dictionary["Account"]["fields"]["leads_accounts"] = array (
  'name' => 'leads_accounts',
  'type' => 'link',
  'relationship' => 'leads_accounts',
  'source' => 'non-db',
  'vname' => 'LBL_LEADS_ACCOUNTS_FROM_LEADS_TITLE',
);


// created: 2012-05-21 17:10:30
$dictionary["Account"]["fields"]["account_leadclientdetail"] = array (
  'name' => 'account_leadclientdetail',
  'type' => 'link',
  'relationship' => 'account_leadclientdetail',
  'source' => 'non-db',
  'vname' => 'LBL_LEADCLIENTDETAILS',
);
$dictionary['Account']['relationships']['account_leadclientdetail'] = array(
 'lhs_module'=> 'Accounts',
 'lhs_table'=> 'accounts',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_LeadClientDetail',
 'rhs_table'=> 'oss_leadclientdetail',
 'rhs_key' => 'account_id',
 'relationship_type'=>'one-to-many'
);

$dictionary['Account']['fields']['team_set_id'] = array(
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

$dictionary['Account']['fields']['team_id'] = array(
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
	


// created: 2012-05-21 17:10:30
$dictionary["Account"]["fields"]["account_businessintelligence"] = array (
  'name' => 'account_businessintelligence',
  'type' => 'link',
  'relationship' => 'account_businessintelligence',
  'source' => 'non-db',
  'vname' => 'LBL_BUSSINESSINTELLIGENCE',
);
$dictionary['Account']['relationships']['account_businessintelligence'] = array(
 'lhs_module'=> 'Accounts',
 'lhs_table'=> 'accounts',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_BusinessIntelligence',
 'rhs_table'=> 'oss_businessintelligence',
 'rhs_key' => 'account_id',
 'relationship_type'=>'one-to-many'
);

 // created: 2019-11-28 13:23:51
$dictionary['Account']['fields']['website']['inline_edit']=true;
$dictionary['Account']['fields']['website']['comments']='URL of website for the company';
$dictionary['Account']['fields']['website']['merge_filter']='disabled';
$dictionary['Account']['fields']['website']['link_target']='_blank';

 

 // created: 2019-11-28 09:46:40
$dictionary['Account']['fields']['billing_address_postalcode']['inline_edit']=true;
$dictionary['Account']['fields']['billing_address_postalcode']['comments']='The postal code used for billing address';
$dictionary['Account']['fields']['billing_address_postalcode']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:19
$dictionary['Account']['fields']['jjwg_maps_lat_c']['inline_edit']=1;

 

 // created: 2019-11-28 09:50:36
$dictionary['Account']['fields']['lead_source']['len']=100;
$dictionary['Account']['fields']['lead_source']['inline_edit']=true;
$dictionary['Account']['fields']['lead_source']['merge_filter']='disabled';

 

 // created: 2019-11-28 09:45:44
$dictionary['Account']['fields']['billing_address_city']['inline_edit']=true;
$dictionary['Account']['fields']['billing_address_city']['comments']='The city used for billing address';
$dictionary['Account']['fields']['billing_address_city']['merge_filter']='disabled';

 

 // created: 2019-11-28 09:41:37
$dictionary['Account']['fields']['team_id']['inline_edit']=true;
$dictionary['Account']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:19
$dictionary['Account']['fields']['jjwg_maps_geocode_status_c']['inline_edit']=1;

 

 // created: 2019-11-07 07:41:18
$dictionary['Account']['fields']['jjwg_maps_lng_c']['inline_edit']=1;

 

 // created: 2019-11-28 09:46:23
$dictionary['Account']['fields']['billing_address_state']['inline_edit']=true;
$dictionary['Account']['fields']['billing_address_state']['comments']='The state used for billing address';
$dictionary['Account']['fields']['billing_address_state']['merge_filter']='disabled';

 

 // created: 2019-11-28 09:49:23
$dictionary['Account']['fields']['email1']['inline_edit']=true;
$dictionary['Account']['fields']['email1']['merge_filter']='disabled';

 

 // created: 2019-11-28 09:41:01
$dictionary['Account']['fields']['county']['required']=false;
$dictionary['Account']['fields']['county']['source']='non-db';
$dictionary['Account']['fields']['county']['name']='county';
$dictionary['Account']['fields']['county']['vname']='LBL_COUNTY';
$dictionary['Account']['fields']['county']['type']='relate';
$dictionary['Account']['fields']['county']['massupdate']=0;
$dictionary['Account']['fields']['county']['comments']='';
$dictionary['Account']['fields']['county']['help']='';
$dictionary['Account']['fields']['county']['importable']='true';
$dictionary['Account']['fields']['county']['duplicate_merge']='disabled';
$dictionary['Account']['fields']['county']['duplicate_merge_dom_value']='0';
$dictionary['Account']['fields']['county']['audited']=true;
$dictionary['Account']['fields']['county']['reportable']=true;
$dictionary['Account']['fields']['county']['len']='255';
$dictionary['Account']['fields']['county']['size']='20';
$dictionary['Account']['fields']['county']['id_name']='county_id';
$dictionary['Account']['fields']['county']['ext2']='oss_County';
$dictionary['Account']['fields']['county']['module']='oss_County';
$dictionary['Account']['fields']['county']['rname']='name';
$dictionary['Account']['fields']['county']['quicksearch']='enabled';
$dictionary['Account']['fields']['county']['studio']='visible';
$dictionary['Account']['fields']['county']['inline_edit']=true;
$dictionary['Account']['fields']['county']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:20
$dictionary['Account']['fields']['jjwg_maps_address_c']['inline_edit']=1;

 

 // created: 2019-11-28 09:41:21
$dictionary['Account']['fields']['county_id']['inline_edit']=true;
$dictionary['Account']['fields']['county_id']['merge_filter']='disabled';
$dictionary['Account']['fields']['county_id']['reportable']=true;

 

 // created: 2019-11-28 09:41:49
$dictionary['Account']['fields']['team_set_id']['inline_edit']=true;
$dictionary['Account']['fields']['team_set_id']['merge_filter']='disabled';

 
?>