<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['Project']['fields']['team_set_id'] = array(
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

$dictionary['Project']['fields']['team_id'] = array(
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
$dictionary['Project']['fields']['is_template'] = array(
	'name' => 'is_template',
	'vname' => 'LBL_IS_TEMPLATE',
	'type' => 'bool',
	'required' => false,
	'default' => '0',
	'comment' => 'Should be checked if the project is a template',
	'massupdate' => false,
);	


 // created: 2019-11-07 07:41:35
$dictionary['Project']['fields']['jjwg_maps_lat_c']['inline_edit']=1;

 

 // created: 2020-04-30 09:07:52
$dictionary['Project']['fields']['team_id']['inline_edit']=true;
$dictionary['Project']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:36
$dictionary['Project']['fields']['jjwg_maps_geocode_status_c']['inline_edit']=1;

 

 // created: 2019-11-07 07:41:35
$dictionary['Project']['fields']['jjwg_maps_lng_c']['inline_edit']=1;

 

 // created: 2020-04-30 09:08:46
$dictionary['Project']['fields']['is_template']['inline_edit']=true;
$dictionary['Project']['fields']['is_template']['comments']='Should be checked if the project is a template';
$dictionary['Project']['fields']['is_template']['merge_filter']='disabled';

 

 // created: 2019-11-07 07:41:36
$dictionary['Project']['fields']['jjwg_maps_address_c']['inline_edit']=1;

 

 // created: 2020-04-30 09:08:04
$dictionary['Project']['fields']['team_set_id']['inline_edit']=true;
$dictionary['Project']['fields']['team_set_id']['merge_filter']='disabled';

 
?>