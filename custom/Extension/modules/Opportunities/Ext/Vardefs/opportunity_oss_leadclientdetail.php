<?php
$dictionary['Opportunity']['fields']['leadclientdetail_id'] = array(
    'required' => false,
    'name' => 'leadclientdetail_id',
    'vname' => 'LBL_LEADCLIENTDETAIL_ID',
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

$dictionary['Opportunity']['fields']['leadclientdetail_name'] =
    array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'leadclientdetail_name',
    'vname' => 'LBL_LEADCLIENTDETAIL_NAME',
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
    'id_name' => 'leadclientdetail_id',
    'ext2' => 'oss_LeadClientDetail',
    'module' => 'oss_LeadClientDetail',
    'rname' => 'name',
    'quicksearch' => 'enabled',
	'link' => 'oss_leadclientdetail_opportunity',
    'studio' => 'visible',
);


$dictionary["Opportunity"]["fields"]["oss_leadclientdetail_to_opportunity"] = array (
  'name' => 'oss_leadclientdetail_to_opportunity',
  'type' => 'link',
  'relationship' => 'oss_leadclientdetail_opportunity',
  'source' => 'non-db',
  'vname' => 'LBL_LEADCLIENTDETAIL_NAME',
);

$dictionary['Opportunity']['relationships']['oss_leadclientdetail_opportunity'] = array('lhs_module'=> 'oss_LeadClientDetail', 'lhs_table'=> 'oss_leadclientdetail', 'lhs_key' => 'id',
							  'rhs_module'=> 'Opportunities', 'rhs_table'=> 'opportunities', 'rhs_key' => 'leadclientdetail_id',
							  'relationship_type'=>'one-to-one');

?>
