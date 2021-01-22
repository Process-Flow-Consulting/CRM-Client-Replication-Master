<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['Call']['fields']['team_set_id'] = array(
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
$dictionary['Call']['fields']['team_id'] = array(
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
$dictionary['Call']['fields']['type']=array(
		'name' => 'type',
		'vname' => 'LBL_TYPE',
		'type' => 'enum',
		'len' => 255,
		'comment' => 'Call type (Bluebook Call Type)',
		'options' => 'custom_call_type_list',
		'default'	=> '',
		'massupdate' => false,
		'required' => true,
);

// created: 2019-12-23 07:25:10
$dictionary["Call"]["fields"]["aos_quotes_activities_1_calls"] = array (
  'name' => 'aos_quotes_activities_1_calls',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_calls',
  'source' => 'non-db',
  'module' => 'AOS_Quotes',
  'bean_name' => 'AOS_Quotes',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_CALLS_FROM_AOS_QUOTES_TITLE',
);


$dictionary['Call']['fields']['type']=array(
		'name' => 'type',
		'vname' => 'LBL_TYPE',
		'type' => 'enum',
		'len' => 255,
		'comment' => 'Call type (Bluebook Call Type)',
		'options' => 'custom_call_type_list',
		'default'	=> '',
		'massupdate' => false,
		'required' => true,
);

?>