<?php
$dictionary["Lead"]["fields"]["lead_to_opportunity_var_parent"] = array (
  'name' => 'lead_to_opportunity_var_parent',
  'type' => 'link',
  'relationship' => 'lead_to_opportunity_var_parent',
  'source' => 'non-db',
  'vname' => 'LBL_PARENT_OPPORTUNITY_SUBPANEL_TITLE',
);
$dictionary['Lead']['relationships']['lead_to_opportunity_var_parent'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'Opportunities',
 'rhs_table'=> 'opportunities',
 'rhs_key' => 'project_lead_id',
 'relationship_type'=>'one-to-many'
);

?>
