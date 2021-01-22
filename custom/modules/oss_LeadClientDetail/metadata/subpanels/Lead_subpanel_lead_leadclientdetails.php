<?php
// created: 2012-01-18 16:07:32
$subpanel_layout['list_fields'] = array (
  'is_viewed' =>
	array(
		'width' => '3%',
		'sortable' => true,
		'vname' => 'LBL_IS_VIEWED',
		'default' => true,
  ),
  'fav_button' =>
	array (
		'vname' => 'LBL_FAV_BUTTON',
		'widget_class' => 'SubPanelFavButton',
		'width' => '4%',
		'default' => true,
	),
  'lcd_account' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'vname' => 'LBL_ACCOUNT_LEADCLIENTDETAILS_TITLE',
    'width' => '25%',
    'sortable' => true,
    'default' => true,
    'widget_class' => 'SubPanelDetailViewLinkBidder',
  	'varname' => 'account_name',
    'target_module' => 'Accounts',
    'target_record_key' => 'account_id',
  ),
  'lcd_contact' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'vname' => 'LBL_CONTACT_LEADCLIENTDETAILS_TITLE',
    'sortable' => false,
    'width' => '25%',
    'default' => true,
  	'varname' => 'contact_name',
    'widget_class' => 'SubPanelDetailViewLinkBidder',
    'target_module' => 'Contacts',
    'target_record_key' => 'contact_id',
  ),
  'contact_phone_no' => 
  array (
    'type' => 'varchar',
    'sortable' => false,
    'vname' => 'LBL_CONTACT_PHONE_NO',
    'width' => '10%',
    'default' => true,
  ),
  /*'contact_fax' => 
  array (
    'type' => 'varchar',
    'sortable' => false,
    'vname' => 'LBL_CONTACT_FAX',
    'width' => '10%',
    'default' => true,
  ),
  'contact_email' => 
  array (
    'type' => 'varchar',
    'sortable' => false,
    'vname' => 'LBL_CONTACT_EMAIL',
    'width' => '10%',
    'default' => true,
  ),*/
  'city_state' =>
  array (
  		'type' => 'varchar',
  		'sortable' => false,
  		'vname' => 'LBL_CITY_STATE',
  		'width' => '10%',
  		'default' => true,
  ),
  'classifications'=>array('type' => 'char',
    'sortable' => true,
    'widget_class' => 'SubPanelClassificationToggle',
    'sortable' => false,
    'width' => '20%',
    'vname' => 'LBL_BIDDERS_CLASSIFICATIONS',
    'default' => true,),
  'role' => 
  array (
    'type' => 'enum',
    'sortable' => false,
    'vname' => 'LBL_ROLE',
    'sortable' => true,
    'width' => '10%',
    'default' => true,
  ),
  'bid_status' =>
  array (
  		'type' => 'enum',
  		'sortable' => false,
  		'vname' => 'LBL_BID_STATUS',
  		'sortable' => true,
  		'width' => '10%',
  		'default' => true,
  ),
  /*'proview_link'=>
  array('widget_class' => 'SubPanelProViewButton',
    'module' => 'Accounts',
    'sortable' => false,
    'width' => '2%',
    'default' => true,),*/
 /*'view_button' => 
  array (
    'widget_class' => 'SubPanelViewButton',
    'module' => 'oss_LeadClientDetail',
    'width' => '4%',
    'default' => true,
  ),*/
  'account_proview_url' =>
  array (
  		'name' => 'account_proview_url',
  		'usage' => 'query_only',
  ),
  'lead_id' => 
  array (
    'name' => 'lead_id',
    'usage' => 'query_only',
  ),
  'account_id' => 
  array (
    'name' => 'account_id',
    'usage' => 'query_only',
  ),
  'contact_id' => 
  array (
    'name' => 'contact_id',
    'usage' => 'query_only',
  ),
 'convert_to_oppr' => 
 array(
	'name' => 'convert_to_oppr',
	'usage' => 'query_only',
  ),
 
  
);
?>
