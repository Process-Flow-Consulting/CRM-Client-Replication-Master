<?php 
 //WARNING: The contents of this file are auto-generated


$layout_defs["Contacts"]["subpanel_setup"]['contact_businessintelligence'] = array (
	'order' => 100,
	'module' => 'oss_BusinessIntelligence',
	'subpanel_name' => 'default',
	'sort_order' => 'DESC',
	'sort_by' => 'type_order, my_only',
	'title_key' => 'LBL_CONTACTS_OSS_BUSINESS_INTELLIGENCE_TITLE',
	//'get_subpanel_data' => 'contact_businessintelligence',
	'get_subpanel_data' => 'function:get_bi_data',
	//'function_parameters' => array('import_function_file' => 'custom/include/common_functions.php', 'link' => 'oss_businessintelligence'),
	//'generate_select'=>true,
	'get_distinct_data' => true,
	'top_buttons' =>
		array (
			0 =>
			array (
				'widget_class' => 'SubPanelTopCreateButton',
			),
			/*1 =>
			array (
				'widget_class' => 'SubPanelTopSelectButton',
				'mode' => 'MultiSelect',
			),*/
		),
);

$layout_defs["Contacts"]["subpanel_setup"]['contact_leadclientdetail'] = array (
  'order' => 100,
  'module' => 'oss_LeadClientDetail',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_LEADCLIENTDETAILS',
  'get_subpanel_data' => 'contact_leadclientdetail',
);

$layout_defs["Contacts"]["subpanel_setup"]["opportunities"] = array
(
		'order' => 30,
		'module' => 'Opportunities',
		'sort_order' => 'desc',
		//'sort_by' => 'date_closed',
		'subpanel_name' => 'ForContacts',
		'get_subpanel_data' => 'function:contact_opportunity_relate',
		//'add_subpanel_data' => 'opportunity_id',
		'title_key' => 'LBL_OPPORTUNITIES_PRIMARY_SUBPANEL_TITLE',
		'top_buttons' => array(
				array('widget_class' => 'SubPanelTopButtonQuickCreate'),
				array('widget_class' => 'SubPanelTopSelectButton', 'mode'=>'MultiSelect')
		),
);

// created: 2011-11-14 11:11:29
/*$layout_defs["Contacts"]["subpanel_setup"]["oss_classification_contacts"] = array (
  'order' => 1004,
  'module' => 'oss_Classification',
  'subpanel_name' => 'forContacts',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_CLASSIFICATION_CONTACTS_FROM_OSS_CLASSIFICATION_TITLE',
  //'get_subpanel_data' => 'oss_classification_contacts',
  'get_subpanel_data' => 'function:oss_classification_contacts',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);*/


$layout_defs["Contacts"]["subpanel_setup"]["oss_classification_contacts"] = array (
		'order' => 1004,
		'module' => 'oss_Classification',
		'subpanel_name' => 'forContacts',
		'sort_order' => 'asc',
		'sort_by' => 'id',
		'type' => 'collection',
		'title_key' => 'LBL_OSS_CLASSIFICATION_CONTACTS_FROM_OSS_CLASSIFICATION_TITLE',
		'get_subpanel_data' => 'oss_classification_contacts',
		'collection_list' => array(
				'oss_classification_contacts' => array(
						'module' => 'oss_Classification',
						'subpanel_name' => 'forContacts',
						'get_subpanel_data' => 'function:oss_classification_contacts',
						//'get_distinct_data'=> true,
						//'generate_select'=>true,
				),
		),
		'top_buttons' =>
		array (
				0 =>
				array (
						'widget_class' => 'SubPanelTopSelectButton',
						'mode' => 'MultiSelect',
				),
		),
);

$layout_defs["Contacts"]["subpanel_setup"]["opportunities_contacts_c"] = array
(
	'order' => 30,
	'module' => 'Opportunities',
	'sort_order' => 'desc',
	//'sort_by' => 'date_closed',
	'subpanel_name' => 'ForContacts',
	'get_subpanel_data' => 'function:opportunities_contacts_c',
	//'add_subpanel_data' => 'opportunity_id',
	'title_key' => 'LBL_OPPORTUNITIES_ADDTIONAL_SUBPANEL_TITLE',
	'top_buttons' => array(    
			array('widget_class' => 'SubPanelTopCreateButton'),
			//array('widget_class' => 'SubPanelTopSelectButton', 'mode'=>'MultiSelect')
	),
);

//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['opportunities_contacts_c']['override_subpanel_name'] = 'Contact_subpanel_opportunities_contacts_c';


//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['contact_aos_quotes']['override_subpanel_name'] = 'Contact_subpanel_contact_aos_quotes';


unset($layout_defs['Contacts']['subpanel_setup']['contact_leadclientdetail']);
unset($layout_defs['Contacts']['subpanel_setup']['leads']);
$layout_defs['Contacts']['subpanel_setup']['opportunities']['top_buttons']=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateButton',
		),
);
$layout_defs['Contacts']['subpanel_setup']['contact_aos_quotes']['top_buttons']=array();
$layout_defs['Contacts']['subpanel_setup']['documents']['top_buttons']=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateButton',
		),
		1 =>
		array (
				'widget_class' => 'SubPanelTopSelectButton',
				'mode' => 'MultiSelect',
		),
);
$layout_defs['Contacts']['subpanel_setup']['history']['top_buttons']=array(
		array (
				'widget_class' => 'SubPanelTopCreateCustomNoteButton',
		),
		array (
				'widget_class' => 'SubPanelTopArchiveEmailButton',
		),
		array (
				'widget_class' => 'SubPanelTopSummaryButton',
		),
		
);

//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['documents']['override_subpanel_name'] = 'Contact_subpanel_documents';


//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['oss_classification_contacts']['override_subpanel_name'] = 'Contact_subpanel_oss_classification_contacts';


//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['opportunities']['override_subpanel_name'] = 'Contact_subpanel_opportunities';


//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['contacts']['override_subpanel_name'] = 'Contact_subpanel_contacts';


//auto-generated file DO NOT EDIT
$layout_defs['Contacts']['subpanel_setup']['contact_businessintelligence']['override_subpanel_name'] = 'Contact_subpanel_contact_businessintelligence';

?>