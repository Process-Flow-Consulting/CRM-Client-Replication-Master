<?php
$dictionary["Lead"]["fields"]["lead_to_opportunity_var"] = array (
  'name' => 'lead_to_opportunity_var',
  'type' => 'link',
  'relationship' => 'lead_to_opportunity_var',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITY_SUBPANEL_TITLE',
);
$dictionary['Lead']['relationships']['lead_to_opportunity_var'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'Opportunities',
 'rhs_table'=> 'opportunities',
 'rhs_key' => 'project_lead_id',
 'relationship_type'=>'one-to-many'
);

?>
