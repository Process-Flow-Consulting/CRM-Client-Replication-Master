<?php
 $dictionary['AOS_ProductTemplates']['fields']['category_id'] = array(
    'required' => false,
    'name' => 'category_id',
    'vname' => 'LBL_AOS_PRODUCT_CATEGORIES_ID',
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

$dictionary['AOS_ProductTemplates']['fields']['category_name'] =
    array(
    'required' => false,
    'source' => 'non-db',
    'name' => 'category_name',
    'vname' => 'LBL_AOS_PRODUCT_CATEGORIES',
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
    'id_name' => 'category_id',
    'ext2' => 'aos_product_categories',
    'module' => 'AOS_Product_Categories',
    'rname' => 'name',
    'quicksearch' => 'enabled',
	'studio' => 'visible',
);

?>

