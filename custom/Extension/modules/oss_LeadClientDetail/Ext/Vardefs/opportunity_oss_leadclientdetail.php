<?php

$dictionary['oss_LeadClientDetail']['fields']['opportunity_id'] = array(
    'required' => false,
    'name' => 'opportunity_id',
    'vname' => 'LBL_OPPORTUNITY_ID',
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
    'size' => '20',
);

$dictionary['oss_LeadClientDetail']['fields']['opportunity_name'] =
    array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'opportunity_name',
    'vname' => 'LBL_OPPORTUNITY_NAME',
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
    'id_name' => 'opportunity_id',
    'ext2' => 'Opportunities',
    'module' => 'Opportunities',
    'rname' => 'name',
    'quicksearch' => 'enabled',
	'link' => 'oss_leadclientdetail_opportunity',
    'studio' => 'visible',
);

$dictionary["oss_LeadClientDetail"]["fields"]["oss_leadclientdetail_to_opportunity"] = array (
  'name' => 'oss_leadclientdetail_to_opportunity',
  'type' => 'link',
  'relationship' => 'oss_leadclientdetail_opportunity',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITY_NAME',
);

$dictionary['oss_LeadClientDetail']['relationships']['oss_leadclientdetail_opportunity'] = array('lhs_module'=> 'oss_LeadClientDetail', 'lhs_table'=> 'oss_leadclientdetail', 'lhs_key' => 'opportunity_id',
							  'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'id',
							  'relationship_type'=>'one-to-one');
?>
