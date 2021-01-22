<?php
$popupMeta = array (
    'moduleMain' => 'AOS_ProductTemplates',
    'varName' => 'AOS_ProductTemplates',
    'orderBy' => 'aos_producttemplates.name',
    'whereClauses' => array (
  'name' => 'aos_producttemplates.name',
  'assigned_user_id' => 'aos_producttemplates.assigned_user_id',
),
    'searchInputs' => array (
  1 => 'name',
  5 => 'assigned_user_id',
),
    'searchdefs' => array (
  'name' => 
  array (
    'name' => 'name',
    'width' => '10%',
  ),
  'assigned_user_id' => 
  array (
    'name' => 'assigned_user_id',
    'label' => 'LBL_ASSIGNED_TO',
    'type' => 'enum',
    'function' => 
    array (
      'name' => 'get_user_array',
      'params' => 
      array (
        0 => false,
      ),
    ),
    'width' => '10%',
  ),
),
);
