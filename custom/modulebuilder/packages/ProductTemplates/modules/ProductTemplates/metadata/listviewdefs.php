<?php
$module_name = 'AOS_ProductTemplates';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '9%',
    'label' => 'LBL_ASSIGNED_TO_NAME',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
  'COST_USDOLLAR' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_COST_USDOLLAR',
    'currency_format' => true,
    'width' => '10%',
    'default' => true,
  ),
  'MARKUP' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_MARKUP',
    'currency_format' => true,
    'width' => '10%',
    'default' => true,
  ),
  'DISCOUNT_USDOLLAR' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_DISCOUNT_USDOLLAR',
    'currency_format' => true,
    'width' => '10%',
    'default' => true,
  ),
);
;
?>
