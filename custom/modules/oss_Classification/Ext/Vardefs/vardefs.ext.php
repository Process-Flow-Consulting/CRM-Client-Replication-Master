<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['oss_Classification']['fields']['mi_oss_classification_id']=array(
    'name' => 'mi_oss_classification_id',
    'vname' => 'MI_OSS_CLASSIFICATION_ID',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 36,
);
$dictionary['oss_Classification']['fields']['lead_source'] = array(
    'name' => 'lead_source',
    'vname' => 'LBL_LEAD_SOURCE',
    'type' => 'enum',
    'len' => '50',
    'options' => 'lead_source_list',
    'default' => 'bb',
);
$dictionary['oss_Classification']['fields']['is_modified'] = array (
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

$dictionary['oss_Classification']['indices'][1] = array(
		'name' =>'idx_mi_oss_classification_id',
		'type'=>'unique',
		'fields'=>array('mi_oss_classification_id')
);
/* $dictionary ['oss_Classification'] ['indices'] [] = array (
		'name' => 'idx_category_no',
		'type' => 'index',
		'fields' => array (
				'category_no(10)' 
		) 
); */



$dictionary['oss_Classification']['fields']['team_set_id'] = array(
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
$dictionary['oss_Classification']['fields']['team_id'] = array(
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

// created: 2011-11-14 11:11:29
$dictionary["oss_Classification"]["fields"]["oss_classification_contacts"] = array (
  'name' => 'oss_classification_contacts',
  'type' => 'link',
  'relationship' => 'oss_classification_contacts',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_CLASSIFICATION_CONTACTS_FROM_CONTACTS_TITLE',
);


// created: 2011-11-03 14:05:19
$dictionary["oss_Classification"]["fields"]["oss_classification_leads"] = array (
  'name' => 'oss_classification_leads',
  'type' => 'link',
  'relationship' => 'oss_classification_leads',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_CLASSIFICATION_LEADS_FROM_LEADS_TITLE',
);


// created: 2011-11-14 11:11:29
$dictionary["oss_Classification"]["fields"]["oss_classifation_accounts"] = array (
  'name' => 'oss_classifation_accounts',
  'type' => 'link',
  'relationship' => 'oss_classification_accounts',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_CLASSIFICATION_ACCOUNTS_FROM_ACCOUNTS_TITLE',
);


 // created: 2020-04-30 08:56:27
$dictionary['oss_Classification']['fields']['is_modified']['default']='0';
$dictionary['oss_Classification']['fields']['is_modified']['inline_edit']=true;
$dictionary['oss_Classification']['fields']['is_modified']['merge_filter']='disabled';

 
?>