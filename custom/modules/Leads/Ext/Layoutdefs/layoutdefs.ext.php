<?php 
 //WARNING: The contents of this file are auto-generated


$layout_defs["Leads"]["subpanel_setup"]['lead_leadclientdetails'] = array (
  'order' => 100,
  'module' => 'oss_LeadClientDetail',
  'subpanel_name' => 'Lead_subpanel_lead_leadclientdetails',
  'sort_order' => 'asc',
  'sort_by' => 'is_viewed',
  //'fill_in_additional_fields'=> true,
  'title_key' => 'LBL_LEADCLIENTDETAILS',
  //'get_subpanel_data' => 'lead_leadclientdetails',
  //custom query to get bidders list
  'get_subpanel_data' => 'function:getLeadBidderListSubpanel',
  'top_buttons' =>
   array (
	0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		),
	1 =>
		array (
			'widget_class' => 'SubPanelMessage',
		),
   ),
);




// created: 2011-10-13 16:29:11
unset($layout_defs["Leads"]["subpanel_setup"]["leads_accounts"]);
/*
$layout_defs["Leads"]["subpanel_setup"]["leads_accounts"] = array (
  'order' => 100,
  'module' => 'Accounts',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_LEADS_ACCOUNTS_FROM_ACCOUNTS_TITLE',
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
*/


$layout_defs["Leads"]["subpanel_setup"]["leads_online_plans"] =  array(
  'order' => 90,
  'module' => 'oss_OnlinePlans',
  'subpanel_name' => 'default',
  'title_key' => 'LBL_LEADS_ONLINE_PLAN_SUBPANEL_TITLE',
  'get_subpanel_data' => 'function:get_leads_online_plans',
  'top_buttons' =>
   array (
	0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		),
   ),
);



$layout_defs["Leads"]["subpanel_setup"]["lead_to_opportunity_var_parent"] =  array(
	'order' => 90,
	'module' => 'Opportunities',
	'subpanel_name' => 'Lead_subpanel_lead_to_opportunity_var_parent',
	'get_subpanel_data' => 'lead_to_opportunity_var',
	//need to apply filters hence custom SQL
	//'get_subpanel_data' => 'function:getParentOpportunitiesSubpanel',
	'title_key' => 'LBL_PARENT_OPPORTUNITY_SUBPANEL_TITLE',
	'top_buttons' =>
	array (
	/* 0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		), */
   ),
);



$layout_defs["Leads"]["subpanel_setup"]["documents_leads"] = array (
    'order' => 100,
    'module' => 'Documents',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'title_key' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
    'get_subpanel_data' => 'documents_leads',
    'top_buttons' =>
    array (
        0 => array (
            'widget_class' => 'SubPanelTopCreateButton',
        ),
        1 => array (
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
        ),
    ),
);

$layout_defs["Leads"]["subpanel_setup"]["lead_to_opportunity_var"] =  array(
  'order' => 100,
  'module' => 'Opportunities',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'fill_in_additional_fields'=> true,
  'title_key' => 'LBL_OPPORTUNITY_SUBPANEL_TITLE',
  'subpanel_name' => 'Lead_subpanel_lead_to_opportunity_var',
  //'get_subpanel_data' => 'lead_to_opportunity_var',
  'get_subpanel_data' => 'function:getSubOpportunitiesSubpanel',
  'top_buttons' =>
   array (
	/* 0 =>
		array (
			'widget_class' => 'SubPanelTopButtonQuickCreateLead',
		), */
   ),
);




$layout_defs["Leads"]["subpanel_setup"]['lead_to_lead_var'] = array (
	'order' => 90,
	//'sort_order' => 'asc',
	//'sort_by' => 'name',
	'module' => 'Leads',
	'subpanel_name' => 'Lead_subpanel_lead_to_lead_var',
	'get_subpanel_data' => 'function:lead_to_lead_sql',
	//'add_subpanel_data' => 'member_id',
	'title_key' => 'LBL_LEAD_SUBPANEL',
	'fill_in_additional_fields'=> true,
	'top_buttons' => 
	  array (
		/* 0 => 
		array (
		  'widget_class' => 'SubPanelTopButtonQuickCreate',
		),
	   1 =>
	   array (
		 'widget_class' => 'SubPanelTopSelectButton',
		 'mode' => 'MultiSelect',
	   ), */
	  ),
);



// created: 2011-11-03 14:05:19
$layout_defs["Leads"]["subpanel_setup"]["oss_classification_leads"] = array (
  'order' => 100,
  'module' => 'oss_Classification',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_OSS_CLASSIFICATION_LEADS_FROM_OSS_CLASSIFICATION_TITLE',
  'get_subpanel_data' => 'oss_classification_leads',
  'top_buttons' => 
  array (
    /*0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),*/
    0 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);


//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['lead_to_opportunity_var']['override_subpanel_name'] = 'Lead_subpanel_lead_to_opportunity_var';


//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['lead_to_lead_var']['override_subpanel_name'] = 'Lead_subpanel_lead_to_lead_var';


//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['oss_classification_leads']['override_subpanel_name'] = 'Lead_subpanel_oss_classification_leads';


//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['lead_to_opportunity_var_parent']['override_subpanel_name'] = 'Lead_subpanel_lead_to_opportunity_var_parent';


//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['documents_leads']['override_subpanel_name'] = 'Lead_subpanel_documents_leads';


//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['leads_online_plans']['override_subpanel_name'] = 'default';


$layout_defs["Leads"]["subpanel_setup"]["leads_oss_ladclientdetail"]['order'] = 10000;
$layout_defs["Leads"]["subpanel_setup"]["oss_classification_leads"]['order'] = 10001;
$layout_defs["Leads"]["subpanel_setup"]["activities"]['order'] = 10002;
$layout_defs["Leads"]["subpanel_setup"]["history"]['order'] = 10003;
$layout_defs["Leads"]["subpanel_setup"]["lead_to_lead_var"]['order'] = 10004;
$layout_defs["Leads"]["subpanel_setup"]["lead_to_opportunity_var_parent"]['order'] = 10005;
$layout_defs["Leads"]["subpanel_setup"]["lead_to_opportunity_var"]['order'] = 10006;

//auto-generated file DO NOT EDIT
$layout_defs['Leads']['subpanel_setup']['oss_classifcation_leads']['override_subpanel_name'] = 'Lead_subpanel_oss_classifcation_leads';

?>