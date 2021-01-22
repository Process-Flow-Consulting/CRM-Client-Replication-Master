<?php
$dictionary['Contact']['fields']['mi_contact_id'] = array(
    'name' => 'mi_contact_id',
    'vname' => 'LBL_MI_CONTACT_ID',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 36,
);
$dictionary['Contact']['fields']['lead_source'] = array(
    'name' => 'lead_source',
    'vname' => 'LBL_LEAD_SOURCE',
    'type' => 'enum',
    'len' => '50',
    'options' => 'lead_source_list',
    'default' => 'bb',
	'audited' => true,
);

$dictionary['Contact']['fields']['primary_address_state']['type'] = 'enum';
$dictionary['Contact']['fields']['primary_address_state']['options'] = 'state_dom';
$dictionary['Contact']['fields']['primary_address_state']['audited'] = true;

$dictionary['Contact']['fields']['alt_address_state']['type'] = 'enum';
$dictionary['Contact']['fields']['alt_address_state']['options'] = 'state_dom';
$dictionary['Contact']['fields']['alt_address_state']['audited'] = true;

$dictionary['Contact']['fields']['first_name']['audited'] = true;
$dictionary['Contact']['fields']['last_name']['audited'] = true;
$dictionary['Contact']['fields']['account_name']['audited'] = true;
$dictionary['Contact']['fields']['account_id']['audited'] = true;

$dictionary['Contact']['fields']['visibility'] = array (
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
   ) ;

$dictionary['Contact']['fields']['account_proview_url'] = array (
		'name' => 'account_proview_url',
		'rname' => 'proview_url',
		'id_name' => 'account_id',
		'vname' => 'LBL_ACCOUNT_NAME',
		'join_name'=>'accounts',
		'type' => 'relate',
		'link' => 'accounts',
		'table' => 'accounts',
		'isnull' => 'true',
		'module' => 'Accounts',
		'dbType' => 'varchar',
		'len' => '255',
		'source' => 'non-db',
		'unified_search' => true,
		'audited' => true,
);

$dictionary['Contact']['fields']['lcd_account'] = array(
		'name' => 'lcd_account',
		'vname' => 'LBL_ACCOUNT_LEADCLIENTDETAILS_TITLE',
		'source' => 'non_db',
		'type' => 'char',
		'audited' => true,
);

//$dictionary['Contact']['fields']['primary_address_street']['len'] = 255;
//$dictionary['Contact']['fields']['primary_address_street']['audited'] = true;

$dictionary['Contact']['fields']['is_modified'] = array (
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

$dictionary['Contact']['fields']['dodge_id'] = array (
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
$dictionary['Contact']['fields']['reed_id'] = array (
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

$dictionary['Contact']['fields']['phone_work']['type'] = 'phone';
$dictionary['Contact']['fields']['phone_work']['audited'] = true;

$dictionary['Contact']['fields']['phone_mobile']['type'] = 'phone';
$dictionary['Contact']['fields']['phone_mobile']['audited'] = true;

$dictionary['Contact']['fields']['phone_fax']['type'] = 'phone';
$dictionary['Contact']['fields']['phone_fax']['audited'] = true;

$dictionary['Contact']['fields']['onvia_id'] = array (
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

//$dictionary['Contact']['fields']['email1']['function']=array('name' => 'getEmailAddressWidgetCustom','returns' => 'html','include'=>'custom/include/SugarEmailAddress/CustomSugarEmailAddress.php');
//$dictionary['Contact']['fields']['email1']['audited'] = true;

$dictionary['Contact']['fields']['do_not_call']['massupdate'] = 0;
$dictionary['Contact']['fields']['do_not_call']['audited'] = true;

$dictionary['Contact']['fields']['primary_address_state']['massupdate'] = 0;
$dictionary['Contact']['fields']['alt_address_state']['massupdate'] = 0;
$dictionary['Contact']['fields']['lead_source']['massupdate'] = 0;
$dictionary['Contact']['fields']['opportunity_role']['massupdate'] = 0;
$dictionary['Contact']['fields']['opportunity_role']['audited'] = true;

$dictionary['Contact']['fields']['report_to_name']['massupdate'] = 0;
$dictionary['Contact']['fields']['report_to_name']['audited'] = true;

$dictionary['Contact']['fields']['account_proview_url']['massupdate'] = 0;

$dictionary['Contact']['fields']['birthdate']['audited'] = true;
$dictionary['Contact']['fields']['title']['audited'] = true;
$dictionary['Contact']['fields']['salutation']['audited'] = true;
$dictionary['Contact']['fields']['department']['audited'] = true;

$dictionary['Contact']['fields']['primary_address_city']['audited'] = true;
$dictionary['Contact']['fields']['primary_address_postalcode']['audited'] = true;
$dictionary['Contact']['fields']['primary_address_country']['audited'] = true;

$dictionary['Contact']['fields']['alt_address_street']['audited'] = true;
$dictionary['Contact']['fields']['alt_address_city']['audited'] = true;
$dictionary['Contact']['fields']['alt_address_state']['audited'] = true;
$dictionary['Contact']['fields']['alt_address_postalcode']['audited'] = true;
$dictionary['Contact']['fields']['alt_address_country']['audited'] = true;
$dictionary['Contact']['fields']['description']['audited'] = true;


$dictionary['Contact']['fields']['classification'] = array (
		'required' => false,
		'name' => 'classification',
		'vname' => 'LBL_CLASSIFICATION',
		'type' => 'link',
		'source'=>'non-db',
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
$dictionary['Contact']['fields']['businessintelligence'] = array(
		'name' => 'businessintelligence',
		'vname' => 'LBL_BUSINESS_INTELLIGENCE_TYPE_TITLE',
		'source' => 'non_db',
		'type' => 'bi',
		'options' => 'bi_type_dom',
		'audited' => true
);

$dictionary['Contact']['fields']['unique_identifier_id'] = array (
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

$dictionary['Contact']['fields']['role'] = array (
        'required' => false,
        'name' => 'role',
        'vname' => 'LBL_ROLE',
        'type' => 'enum',
        'massupdate' => 0,
        'options' => 'contacts_role_dom',
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

$dictionary['Contact']['fields']['default_contact'] = array(
		'required' => false,
		'name' => 'default_contact',
		'vname' => 'LBL_DEFAULT_CONTACT',
		'type' => 'bool',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => false,
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'audited' => true,
		'reportable' => false,
		'calculated' => false,
		'len' => '255',
		'size' => '20',
		'default' => '0',
);
$dictionary['Contact']['fields']['phone_work_ext'] = array (
        'required' => false,
        'name' => 'phone_work_ext',
        'vname' => 'LBL_PHONE_WORK_EXT',
        'type' => 'varchar',
        'massupdate' => 0,
        'comments' => '',
        'help' => '',
        'importable' => 'true',
        'duplicate_merge' => 'disabled',        
        'audited' => true,
        'reportable' => true,
        'len' => '4',
        'size' => '4',
);
$dictionary['Contact']['fields']['international'] = array(
		'required' => false,
		'name' => 'international',
		'label' => 'LBL_INTERNATIONAL',
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
?>
