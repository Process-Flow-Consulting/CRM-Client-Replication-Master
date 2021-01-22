<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['AOS_Contracts']['fields']['team_set_id'] = array(
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

$dictionary['AOS_Contracts']['fields']['team_id'] = array(
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
	


 // created: 2019-12-02 09:25:50
$dictionary['AOS_Contracts']['fields']['type']['default']='$vardef.default';
$dictionary['AOS_Contracts']['fields']['type']['inline_edit']=true;
$dictionary['AOS_Contracts']['fields']['type']['options']='';
$dictionary['AOS_Contracts']['fields']['type']['comments']='The dropdown options for Contract types';
$dictionary['AOS_Contracts']['fields']['type']['merge_filter']='disabled';

 

 // created: 2019-12-02 09:25:32
$dictionary['AOS_Contracts']['fields']['expiration_notice']['inline_edit']=true;
$dictionary['AOS_Contracts']['fields']['expiration_notice']['comments']='Date to issue an expiration notice (useful for workflow rules)';
$dictionary['AOS_Contracts']['fields']['expiration_notice']['merge_filter']='disabled';
$dictionary['AOS_Contracts']['fields']['expiration_notice']['reportable']=true;
$dictionary['AOS_Contracts']['fields']['expiration_notice']['enable_range_search']=false;

 

 // created: 2019-11-29 13:33:16
$dictionary['AOS_Contracts']['fields']['name']['inline_edit']=true;
$dictionary['AOS_Contracts']['fields']['name']['merge_filter']='disabled';
$dictionary['AOS_Contracts']['fields']['name']['unified_search']=false;

 
?>