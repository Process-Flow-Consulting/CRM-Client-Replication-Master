<?php 
 //WARNING: The contents of this file are auto-generated


// created: 2011-11-14 11:11:29
$layout_defs["Accounts"]["subpanel_setup"]["oss_classifation_accounts"] = array (
  'order' => -1004,
  'module' => 'oss_Classification',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_CLASSIFICATION_ACCOUNTS_FROM_OSS_CLASSIFICATION_TITLE',
  'get_subpanel_data' => 'oss_classifation_accounts',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);


$layout_defs['Accounts']['subpanel_setup']['opportunities']['top_buttons'] = array( array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ));

 // created: 2012-03-21 12:09:39
/*$layout_defs["Accounts"]["subpanel_setup"]['opportunities_accounts'] = array (
  'order' => 100,
  'module' => 'Opportunities',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OPPORTUNITIES_ACCOUNTS_FROM_OPPORTUNITIES_TITLE',
  'get_subpanel_data' => 'opportunities_accounts',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);*/


// created: 2011-10-13 16:29:11
$layout_defs["Accounts"]["subpanel_setup"]["leads_accounts"] = array (
  'order' => 100,
  'module' => 'Leads',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_LEADS_ACCOUNTS_FROM_LEADS_TITLE',
  'get_subpanel_data' => 'leads_accounts',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);


$layout_defs["Accounts"]["subpanel_setup"]['account_leadclientdetail'] = array (
  'order' => 100,
  'module' => 'oss_LeadClientDetail',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_LEADCLIENTDETAILS',
  'get_subpanel_data' => 'account_leadclientdetail',
);

$layout_defs["Accounts"]["subpanel_setup"]['account_businessintelligence'] = array (
    'order' => 100,
	'module' => 'oss_BusinessIntelligence',
	'subpanel_name' => 'default',
	'sort_order' => 'DESC',
	'sort_by' => 'type_order, my_only',
	'title_key' => 'LBL_BUSSINESSINTELLIGENCE',
	//'get_subpanel_data' => 'account_businessintelligence',
	'get_subpanel_data' => 'function:get_bi_data',
	//'function_parameters' => array('import_function_file' => 'custom/include/common_functions.php', 'link' => 'oss_businessintelligence'),
	//'generate_select'=>true,
	//'get_distinct_data' => true,
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

//auto-generated file DO NOT EDIT
$layout_defs['Accounts']['subpanel_setup']['opportunities']['override_subpanel_name'] = 'Account_subpanel_opportunities';


unset($layout_defs['Accounts']['subpanel_setup']['account_leadclientdetail']);
unset($layout_defs["Accounts"]["subpanel_setup"]["leads_accounts"]);
unset($layout_defs["Accounts"]["subpanel_setup"]["products_services_purchased"]);
$layout_defs['Accounts']['subpanel_setup']['opportunities']["top_buttons"]=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateButton',
		),
		
);

$layout_defs['Accounts']['subpanel_setup']['contacts']['top_buttons']=array(
		0 =>
		array (
				'widget_class' => 'SubPanelTopCreateCustomAccountNameButton',
		),
		1 =>
		array (
				'widget_class' => 'SubPanelTopSelectCustomContactsButton',
				'mode' => 'MultiSelect',
		),
);

$layout_defs["Accounts"]["subpanel_setup"]["account_aos_quotes"]["top_buttons"]=array();


//auto-generated file DO NOT EDIT
$layout_defs['Accounts']['subpanel_setup']['leads']['override_subpanel_name'] = 'Account_subpanel_leads';


//auto-generated file DO NOT EDIT
$layout_defs['Accounts']['subpanel_setup']['account_aos_quotes']['override_subpanel_name'] = 'Account_subpanel_account_aos_quotes';


//auto-generated file DO NOT EDIT
$layout_defs['Accounts']['subpanel_setup']['oss_classifation_accounts']['override_subpanel_name'] = 'Account_subpanel_oss_classifation_accounts';


//auto-generated file DO NOT EDIT
$layout_defs['Accounts']['subpanel_setup']['account_businessintelligence']['override_subpanel_name'] = 'Account_subpanel_account_businessintelligence';


$layout_defs['Accounts']['subpanel_setup']['contacts']['order'] = -1005;
$layout_defs['Accounts']['subpanel_setup']['oss_classifation_accounts']['order'] = -1004;
$layout_defs['Accounts']['subpanel_setup']['activities']['order'] = -1003;
$layout_defs['Accounts']['subpanel_setup']['history']['order'] = -1002;
$layout_defs['Accounts']['subpanel_setup']['opportunities']['order'] = -1001;

//auto-generated file DO NOT EDIT
$layout_defs['Accounts']['subpanel_setup']['documents']['override_subpanel_name'] = 'Account_subpanel_documents';

?>