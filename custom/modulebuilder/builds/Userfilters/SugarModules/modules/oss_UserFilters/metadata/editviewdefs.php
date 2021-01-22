<?php
$module_name = 'oss_UserFilters';
$viewdefs [$module_name] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'useTabs' => false,
      'tabDefs' => 
      array (
        'DEFAULT' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'filter_type',
            'studio' => 'visible',
            'label' => 'LBL_FILTER_TYPE ',
          ),
          1 => 
          array (
            'name' => 'filter_value',
            'label' => 'LBL_FILTER_VALUE',
          ),
        ),
        1 => 
        array (
          0 => '',
          1 => '',
        ),
      ),
    ),
  ),
);
;
?>
