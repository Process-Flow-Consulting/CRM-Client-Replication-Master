<?php
$listViewDefs ['AOS_Quotes'] = 
array (
  'NUMBER' => 
  array (
    'width' => '8%',
    'label' => 'LBL_LIST_QUOTE_NUM',
    'link' => false,
    'default' => true,
    'align' => 'center',
  ),
  'NAME' => 
  array (
    'width' => '20%',
    'label' => 'LBL_LIST_QUOTE_NAME',
    'link' => true,
    'default' => true,
  ),
  'BILLING_ACCOUNT' => 
  array (
    'width' => '18%',
    'label' => 'LBL_LIST_ACCOUNT_NAME',
    'id' => 'BILLING_ACCOUNT_ID',
    'module' => 'Accounts',
    'link' => true,
    'default' => true,
    'related_fields' => 
    array (
      0 => 'billing_account_id',
      1 => 'account_proview_url',
    ),
    'customCode' => '{$account_proview_url}&nbsp;&nbsp;<a href="index.php?module=Accounts&action=DetailView&retun_module=AOS_Quotes&return_action=ListView&record={$BILLING_ACCOUNT_ID}">{$BILLING_ACCOUNT}</a>',
  ),
  'PROPOSAL_AMOUNT' => 
  array (
    'width' => '8%',
    'label' => 'LBL_LIST_AMOUNT_USDOLLAR',
    'link' => false,
    'default' => true,
    'type' => 'currency',
    'currency_format' => true,    
  ),
  'DATE_TIME_DELIVERY' => 
  array (
    'type' => 'datetimecombo',
    'label' => 'LBL_DATE_TIME_DELIVERY_LV',
    'width' => '10%',
    'default' => true,
  	'sortable' => false,  	
  	'related_fields' =>
  		array (
  			0 => 'delivery_timezone',
  			1 => 'date_time_sent',
  			2 => 'date_time_received',
  			3 => 'date_time_opened',
  			4 => 'proposal_delivery_method',
  		),
  ),  
  'PROPOSAL_VERIFIED' => 
  array (
    'type' => 'radioenum',
    'default' => true,
    'label' => 'LBL_PROPOSAL_VERIFIED_LV',
    'width' => '8%',
  	'align' => 'center',
 	'related_fields' =>
  		array (
  			0 => 'proposal_verified',
  			1 => 'verify_email_sent',
  		), 		
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
  'DATE_ENTERED' => 
  array (
    'width' => '10%',
    'label' => 'LBL_DATE_ENTERED',
    'default' => false,
  ),
);
?>
