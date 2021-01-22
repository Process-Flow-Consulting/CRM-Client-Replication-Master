<?php
$module_name = 'AOS_Products';
$searchdefs [$module_name] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'name' => 
      array (
        'name' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      'favorites_only' => 
      array (
        'name' => 'favorites_only',
        'label' => 'LBL_FAVORITES_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
      ),
    ),
    'advanced_search' => 
    array (
      'name' => 
      array (
        'name' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      'tax_class' => 
      array (
        'type' => 'enum',
        'label' => 'LBL_TAX_CLASS',
        'width' => '10%',
        'default' => true,
        'name' => 'tax_class',
      ),
      'status' => 
      array (
        'type' => 'enum',
        'label' => 'LBL_STATUS',
        'width' => '10%',
        'default' => true,
        'name' => 'status',
      ),
      'category_id' => 
      array (
        'type' => 'char',
        'label' => 'LBL_AOS_PRODUCT_CATEGORIES_ID',
        'width' => '10%',
        'default' => true,
        'name' => 'category_id',
      ),
      'category_name' => 
      array (
        'type' => 'relate',
        'studio' => 'visible',
        'label' => 'LBL_AOS_PRODUCT_CATEGORIES',
        'id' => 'CATEGORY_ID',
        'link' => true,
        'width' => '10%',
        'default' => true,
        'name' => 'category_name',
      ),
      'mft_part_num' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_MFT_PART_NUM',
        'width' => '10%',
        'default' => true,
        'name' => 'mft_part_num',
      ),
      'type_name' => 
      array (
        'type' => 'relate',
        'link' => true,
        'label' => 'Type',
        'id' => 'TYPE_ID',
        'width' => '10%',
        'default' => true,
        'name' => 'type_name',
      ),
      'support_term' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_SUPPORT_TERM',
        'width' => '10%',
        'default' => true,
        'name' => 'support_term',
      ),
      'website' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_URL',
        'width' => '10%',
        'default' => true,
        'name' => 'website',
      ),
      'favorites_only' => 
      array (
        'label' => 'LBL_FAVORITES_FILTER',
        'type' => 'bool',
        'width' => '10%',
        'default' => true,
        'name' => 'favorites_only',
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);
;
?>
