<?php
$dictionary['oss_Classification']['fields']['mi_oss_classification_id']=array(
    'name' => 'mi_oss_classification_id',
    'vname' => 'MI_OSS_CLASSIFICATION_ID',
    'type' => 'varchar',
    'merge_filter' => 'enabled',
    'len' => 36,
);
$dictionary['oss_Classification']['fields']['lead_source'] = array(
    'name' => 'lead_source',
    'vname' => 'LBL_LEAD_SOURCE',
    'type' => 'enum',
    'len' => '50',
    'options' => 'lead_source_list',
    'default' => 'bb',
);
$dictionary['oss_Classification']['fields']['is_modified'] = array (
		'required' => false,
		'name' => 'is_modified',
		'vname' => 'LBL_IS_MODIFIED',
		'type' => 'bool',
		'massupdate' => 0,
		'comments' => '',
		'help' => '',
		'importable' => 'true',
		'merge_filter' => 'enabled',
		'duplicate_merge' => 'disabled',
		'duplicate_merge_dom_value' => '0',
		'audited' => true,
		'reportable' => true,
		'calculated' => false,
		'size' => '20',
);

$dictionary['oss_Classification']['indices'][1] = array(
		'name' =>'idx_mi_oss_classification_id',
		'type'=>'unique',
		'fields'=>array('mi_oss_classification_id')
);
/* $dictionary ['oss_Classification'] ['indices'] [] = array (
		'name' => 'idx_category_no',
		'type' => 'index',
		'fields' => array (
				'category_no(10)' 
		) 
); */
?>
