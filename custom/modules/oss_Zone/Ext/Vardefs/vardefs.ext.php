<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['oss_Zone']['fields']['team_set_id'] = array(
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

$dictionary['oss_Zone']['fields']['team_id'] = array(
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
	


// created: 2013-10-15 13:13:30
$dictionary["oss_Zone"]["fields"]["oss_zone_opportunities_1"] = array (
  'name' => 'oss_zone_opportunities_1',
  'type' => 'link',
  'relationship' => 'oss_zone_opportunities_1',
  'source' => 'non-db',
  'vname' => 'LBL_OSS_ZONE_OPPORTUNITIES_1_FROM_OPPORTUNITIES_TITLE',
);


 // created: 2020-04-30 09:14:45
$dictionary['oss_Zone']['fields']['team_id']['inline_edit']=true;
$dictionary['oss_Zone']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:14:33
$dictionary['oss_Zone']['fields']['team_set_id']['inline_edit']=true;
$dictionary['oss_Zone']['fields']['team_set_id']['merge_filter']='disabled';

 
?>