<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['EmailTemplate']['fields']['base_module'] = array(
	'name' => 'base_module',
	'vname' => 'LBL_BASE_MODULE',
	'type' => 'varchar',
	'len' => '50',
	'comment' => 'In Workflow alert templates, the module to which this template is associated'
);
$dictionary['EmailTemplate']['fields']['from_name'] = array(
	'name' => 'from_name',
	'vname' => 'LBL_FROM_NAME',
	'type' => 'varchar',
	'len' => '255',
	'reportable'=>false,
);
$dictionary['EmailTemplate']['fields']['from_address'] = array(
	'name' => 'from_address',
	'vname' => 'LBL_FROM_ADDRESS',
	'type' => 'varchar',
	'len' => '255',
	'reportable'=>false,
);

$dictionary['EmailTemplate']['fields']['team_set_id'] = array(
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

$dictionary['EmailTemplate']['fields']['team_id'] = array(
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
	


 // created: 2020-04-30 08:59:44
$dictionary['EmailTemplate']['fields']['team_id']['inline_edit']=true;
$dictionary['EmailTemplate']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 08:59:57
$dictionary['EmailTemplate']['fields']['team_set_id']['inline_edit']=true;
$dictionary['EmailTemplate']['fields']['team_set_id']['merge_filter']='disabled';

 
?>