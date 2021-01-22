<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['Task']['fields']['team_set_id'] = array(
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

$dictionary['Task']['fields']['team_id'] = array(
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
$dictionary["Task"]["fields"]["aos_quotes_activities_1_tasks"] = array (
  'name' => 'aos_quotes_activities_1_tasks',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_tasks',
  'source' => 'non-db',
  'module' => 'AOS_Quotes',
  'bean_name' => 'AOS_Quotes',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_TASKS_FROM_AOS_QUOTES_TITLE',
);


 // created: 2020-04-30 09:12:01
$dictionary['Task']['fields']['team_id']['inline_edit']=true;
$dictionary['Task']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:11:48
$dictionary['Task']['fields']['team_set_id']['inline_edit']=true;
$dictionary['Task']['fields']['team_set_id']['merge_filter']='disabled';

 
?>