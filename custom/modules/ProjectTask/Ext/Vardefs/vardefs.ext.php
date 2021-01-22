<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['ProjectTask']['fields']['team_set_id'] = array(
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

$dictionary['ProjectTask']['fields']['team_id'] = array(
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
	


$dictionary['ProjectTask']['fields']['resource_id'] = array(
		'name' => 'resource_id',
		'vname' => 'LBL_RESOURCE_ID',
		'required' => false,
		'type' => 'text',
		'hidden' => true,
);


 // created: 2019-11-29 12:47:29
$dictionary['ProjectTask']['fields']['percent_complete']['inline_edit']=true;
$dictionary['ProjectTask']['fields']['percent_complete']['merge_filter']='disabled';
$dictionary['ProjectTask']['fields']['percent_complete']['enable_range_search']=false;
$dictionary['ProjectTask']['fields']['percent_complete']['min']=false;
$dictionary['ProjectTask']['fields']['percent_complete']['max']=false;
$dictionary['ProjectTask']['fields']['percent_complete']['disable_num_format']='';

 

 // created: 2020-04-30 09:06:57
$dictionary['ProjectTask']['fields']['team_id']['inline_edit']=true;
$dictionary['ProjectTask']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2019-12-03 07:06:38
$dictionary['ProjectTask']['fields']['description']['inline_edit']=true;
$dictionary['ProjectTask']['fields']['description']['merge_filter']='disabled';
$dictionary['ProjectTask']['fields']['description']['rows']='4';
$dictionary['ProjectTask']['fields']['description']['cols']='20';

 

 // created: 2020-04-30 09:07:10
$dictionary['ProjectTask']['fields']['team_set_id']['inline_edit']=true;
$dictionary['ProjectTask']['fields']['team_set_id']['merge_filter']='disabled';

 
?>