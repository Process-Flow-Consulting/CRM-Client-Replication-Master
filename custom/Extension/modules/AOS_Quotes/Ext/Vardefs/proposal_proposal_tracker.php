<?php
$dictionary["AOS_Quotes"]["fields"]["proposal_proposal_tracker"] = array (
  'name' => 'proposal_proposal_tracker',
  'type' => 'link',
  'relationship' => 'proposal_proposal_tracker',
  'source' => 'non-db',
  'vname' => 'LBL_PROPOSAL_NAME',
);

$dictionary['AOS_Quotes']['relationships']['proposal_proposal_tracker'] = array(
    'lhs_module'=> 'Quotes', 'lhs_table'=> 'quotes', 'lhs_key' => 'id',
    'rhs_module'=> 'oss_ProposalTracker', 'rhs_table'=> 'oss_proposaltracker', 'rhs_key' => 'proposal_id',
    'relationship_type'=>'one-to-many'
);

?>
