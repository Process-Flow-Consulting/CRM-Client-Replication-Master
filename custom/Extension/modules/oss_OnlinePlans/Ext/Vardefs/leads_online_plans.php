<?php
$dictionary['oss_OnlinePlans']['fields']['lead_id'] = array(
		'name' => 'lead_id',
		'vname' => 'LBL_REVIEW_DATE',
		'type' => 'varchar',
		'merge_filter' => 'enabled',
		'len' => 36,
);

$dictionary['oss_OnlinePlans']['fields']['lead_name'] =
array(
		'required' => true,
		'source' => 'non-db',
		'name' => 'lead_name',
		'vname' => 'LBL_LEAD_PARENT',
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
		'id_name' => 'lead_id',
		'ext2' => 'Leads',
		'module' => 'Leads',
		'rname' => 'project_title',
		'quicksearch' => 'enabled',
		'studio' => 'visible',
);
$dictionary['oss_OnlinePlans']['fields']['leads_online_plans'] = array (
		'name' => 'leads_online_plans',
		'type' => 'link',
		'relationship' => 'leads_online_plans',
		'source' => 'non-db',
		'vname' => 'LBL_LEAD_PARENT',
);
$dictionary['oss_OnlinePlans']['relationships']['leads_online_plans'] = array('lhs_module'=> 'Leads', 'lhs_table'=> 'leads', 'lhs_key' => 'id',
		'rhs_module'=> 'oss_OnlinePlans', 'rhs_table'=> 'oss_onlineplans', 'rhs_key' => 'lead_id',
		'relationship_type'=>'one-to-many');

?>