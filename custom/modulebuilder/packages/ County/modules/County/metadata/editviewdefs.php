<?php
$module_name = 'oss_County';
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
          0 => 'name',
          1 => 
          array (
            'name' => 'county_abbr',
            'label' => 'LBL_COUNTY_ABBR',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'county_number',
            'label' => 'LBL_COUNTY_NUMBER ',
          ),
          1 => '',
        ),
      ),
    ),
  ),
);
;
?>
