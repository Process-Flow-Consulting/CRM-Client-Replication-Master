<?php
$module_name = 'AOS_Quotes';
$_module_name = 'aos_quotes';
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
      'current_user_only' => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
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
      'number' => 
      array (
        'name' => 'number',
        'default' => true,
        'width' => '10%',
      ),
      'billing_account' => 
      array (
        'name' => 'billing_account',
        'default' => true,
        'width' => '10%',
      ),
      'total_usdollar' => 
      array (
        'type' => 'currency',
        'label' => 'LBL_TOTAL_USDOLLAR',
        'currency_format' => true,
        'width' => '10%',
        'default' => true,
        'name' => 'total_usdollar',
      ),
      'quote_type' => 
      array (
        'type' => 'varchar',
        'label' => 'LBL_QUOTE_TYPE',
        'width' => '10%',
        'default' => true,
        'name' => 'quote_type',
      ),
      'expiration' => 
      array (
        'name' => 'expiration',
        'default' => true,
        'width' => '10%',
      ),
      'assigned_user_id' => 
      array (
        'name' => 'assigned_user_id',
        'type' => 'enum',
        'label' => 'LBL_ASSIGNED_TO',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
        'width' => '10%',
      ),
      'stage' => 
      array (
        'name' => 'stage',
        'default' => true,
        'width' => '10%',
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
