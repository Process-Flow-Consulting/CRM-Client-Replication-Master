<?php
$listViewDefs ['AOS_Products'] = 
array (
  'NAME' => 
  array (
    'width' => '40%',
    'label' => 'LBL_LIST_NAME',
    'link' => true,
    'default' => true,
  ),
  'ACCOUNT_NAME' => 
  array (
    'width' => '20%',
    'label' => 'LBL_LIST_ACCOUNT_NAME',
    'id' => 'ACCOUNT_ID',
    'module' => 'Accounts',
    'link' => true,
    'default' => true,
    'sortable' => true,
    'ACLTag' => 'ACCOUNT',
    'related_fields' => 
    array (
      0 => 'account_id',
      1 => 'account_proview_url',
    ),
    'customCode' => '{$ACCOUNT_PROVIEW_URL}&nbsp;&nbsp;<a href="index.php?module=Accounts&action=DetailView&retun_module=AOS_Products&return_action=ListView&record={$ACCOUNT_ID}">{$ACCOUNT_NAME}</a>',
  ),
  'STATUS' => 
  array (
    'width' => '10%',
    'label' => 'Status',
    'link' => false,
    'default' => true,
  ),
  'QUANTITY' => 
  array (
    'width' => '10%',
    'label' => 'Quantity',
    'link' => false,
    'related_fields' => 
    array (
      0 => 'unit_measure',
      1 => 'unit_measure_name',
    ),
    'customCode' => '{$QUANTITY}&nbsp;{$UNIT_MEASURE_NAME}',
    'default' => true,
  ),
  'DISCOUNT_PRICE' => 
  array (
    'width' => '10%',
    'label' => 'Price',
    'link' => false,
    'default' => true,
    'currency_format' => true,
    'align' => 'center',
  ),
  'LIST_PRICE' => 
  array (
    'width' => '10%',
    'label' => 'Mark Up',
    'default' => true,
    'align' => 'center',
    'related_fields' => 
    array (
      0 => 'markup_inper',
    ),
    'customCode' => '{$LIST_PRICE}',
  ),
  'DATE_PURCHASED' => 
  array (
    'width' => '10%',
    'label' => 'Purchased',
    'link' => false,
    'default' => true,
  ),
  'DATE_SUPPORT_EXPIRES' => 
  array (
    'width' => '10%',
    'label' => 'Support Expires',
    'link' => false,
    'default' => true,
  ),
  'DATE_ENTERED' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'default' => true,
  ),
  'UNIT_MEASURE' => 
  array (
    'width' => '20%',
    'label' => 'LBL_UNIT_MEASURE_NAME',
    'id' => 'unit_measure',
    'module' => 'oss_UnitOfMeasure',
    'link' => false,
    'default' => false,
    'related_fields' => 
    array (
      0 => 'unit_measure_name',
    ),
  ),
  'CATEGORY_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'product_categories_link',
    'label' => 'LBL_CATEGORY_NAME',
    'width' => '10%',
    'default' => false,
  ),
  'CONTACT_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'contact_link',
    'label' => 'LBL_CONTACT_NAME',
    'width' => '10%',
    'default' => false,
  ),
  'QUOTE_NAME' => 
  array (
    'type' => 'relate',
    'link' => 'quotes',
    'label' => 'LBL_QUOTE_NAME',
    'width' => '10%',
    'default' => false,
  ),
  'TYPE_NAME' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_TYPE',
    'width' => '10%',
    'default' => false,
  ),
  'SERIAL_NUMBER' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_SERIAL_NUMBER',
    'width' => '10%',
    'default' => false,
  ),
);
appendFieldsOnViews($editView=array(),$detailView=array(),$searchDefs=array(),$listViewDefs['AOS_Products'],$searchFields=array(),'AOS_Products','ListDefs');
?>
