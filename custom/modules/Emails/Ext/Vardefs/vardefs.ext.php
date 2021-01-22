<?php 
 //WARNING: The contents of this file are auto-generated


// created: 2019-12-23 07:25:11
$dictionary["Email"]["fields"]["aos_quotes_activities_1_emails"] = array (
  'name' => 'aos_quotes_activities_1_emails',
  'type' => 'link',
  'relationship' => 'aos_quotes_activities_1_emails',
  'source' => 'non-db',
  'module' => 'AOS_Quotes',
  'bean_name' => 'AOS_Quotes',
  'vname' => 'LBL_AOS_QUOTES_ACTIVITIES_1_EMAILS_FROM_AOS_QUOTES_TITLE',
);


$dictionary['Email']['fields']['date_sent'] = array(
	'name' => 'date_sent',
	'vname' => 'LBL_DATE_SENT',
	'type' => 'datetime',
	'inline_edit' => false,
);
		
$dictionary['Email']['fields']['team_set_id'] = array(
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

$dictionary['Email']['fields']['team_id'] = array(
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
	


 // created: 2020-04-30 09:00:27
$dictionary['Email']['fields']['team_id']['inline_edit']=true;
$dictionary['Email']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-05-19 14:35:02
$dictionary['Email']['fields']['date_sent']['merge_filter']='disabled';
$dictionary['Email']['fields']['date_sent']['enable_range_search']=false;

 

 // created: 2020-04-30 09:00:16
$dictionary['Email']['fields']['team_set_id']['inline_edit']=true;
$dictionary['Email']['fields']['team_set_id']['merge_filter']='disabled';

 
?>