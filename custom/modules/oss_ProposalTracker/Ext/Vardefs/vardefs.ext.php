<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['oss_ProposalTracker']['fields']['team_set_id'] = array(
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

$dictionary['oss_ProposalTracker']['fields']['team_id'] = array(
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
	


$dictionary['oss_ProposalTracker']['fields']['proposal_id'] = array(
    'name' => 'proposal_id',
    'vname' => 'LBL_PROPOSAL_NAME',
    'type' => 'char',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'len' => '36',
);

$dictionary['oss_ProposalTracker']['fields']['proposal_name'] = array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'proposal_name',
    'vname' => 'LBL_PROPOSAL_NAME',
    'type' => 'relate',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => false,
    'reportable' => true,
    'len' => '255',
    'size' => '20',
    'id_name' => 'proposal_id',
    'ext2' => 'AOS_Quotes',
    'module' => 'aos_quotes',
    'rname' => 'name',
    'quicksearch' => 'enabled',
    'studio' => 'visible',
);

$dictionary["oss_ProposalTracker"]["fields"]["proposal_proposal_tracker"] = array (
  'name' => 'proposal_proposal_tracker',
  'type' => 'link',
  'relationship' => 'proposal_proposal_tracker',
  'source' => 'non-db',
  'vname' => 'LBL_PROPOSAL_NAME',
);

$dictionary['oss_ProposalTracker']['relationships']['proposal_proposal_tracker'] = array(
    'lhs_module'=> 'AOS_Quotes', 'lhs_table'=> 'aos_quotes', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_ProposalTracker', 'rhs_table'=> 'oss_proposaltracker', 'rhs_key' => 'proposal_id',
    'relationship_type'=>'one-to-many'
);




 // created: 2020-04-30 09:09:11
$dictionary['oss_ProposalTracker']['fields']['team_id']['inline_edit']=true;
$dictionary['oss_ProposalTracker']['fields']['team_id']['merge_filter']='disabled';

 

 // created: 2020-04-30 09:09:21
$dictionary['oss_ProposalTracker']['fields']['team_set_id']['inline_edit']=true;
$dictionary['oss_ProposalTracker']['fields']['team_set_id']['merge_filter']='disabled';

 
?>