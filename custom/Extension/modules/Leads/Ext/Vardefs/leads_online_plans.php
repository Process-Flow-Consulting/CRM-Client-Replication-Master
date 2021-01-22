<?php
// created: 2012-05-21 17:10:30
$dictionary["Lead"]["fields"]["leads_online_plans"] = array (
  'name' => 'leads_online_plans',
  'type' => 'link',
  'relationship' => 'leads_online_plans',
  'source' => 'non-db',
  'vname' => 'LBL_LEADS_ONLINE_PLAN_SUBPANEL_TITLE',
);
$dictionary['Lead']['relationships']['leads_online_plans'] = array(
 'lhs_module'=> 'Leads',
 'lhs_table'=> 'leads',
 'lhs_key' => 'id',
 'rhs_module'=> 'oss_OnlinePlans',
 'rhs_table'=> 'oss_onlineplans',
 'rhs_key' => 'lead_id',
 'relationship_type'=>'one-to-many'
);