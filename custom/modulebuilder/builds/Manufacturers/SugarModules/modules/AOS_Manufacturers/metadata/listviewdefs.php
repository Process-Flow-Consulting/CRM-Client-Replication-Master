<?php
$module_name = 'AOS_Manufacturers';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
  'LIST_ORDER' => 
  array (
    'type' => 'int',
    'label' => 'LBL_LIST_ORDER',
    'width' => '10%',
    'default' => true,
  ),
);
;
?>
