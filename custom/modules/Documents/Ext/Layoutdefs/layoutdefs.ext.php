<?php 
 //WARNING: The contents of this file are auto-generated


$layout_defs["Documents"]["subpanel_setup"]["documents_quotes"] = array (
    'order' => 100,
    'module' => 'AOS_Quotes',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'title_key' => 'LBL_AOS_QUOTES_SUBPANEL_TITLE',
    'get_subpanel_data' => 'documents_quotes',
    'top_buttons' =>
    array (
        0 => array (
            'widget_class' => 'SubPanelTopButtonQuickCreate',
        ),
        1 => array (
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
        ),
    ),
);

$layout_defs["Documents"]["subpanel_setup"]["documents_leads"] = array (
    'order' => 100,
    'module' => 'Leads',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'id',
    'title_key' => 'LBL_LEADS_SUBPANEL_TITLE',
    'get_subpanel_data' => 'documents_leads',
    'top_buttons' =>
    array (
        0 => array (
            'widget_class' => 'SubPanelTopButtonQuickCreate',
        ),
        1 => array (
            'widget_class' => 'SubPanelTopSelectButton',
            'mode' => 'MultiSelect',
        ),
    ),
);

$layout_defs["Documents"]["subpanel_setup"]['documents_products'] = array (
  'order' => 100,
  'module' => 'AOS_Products',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_DOCUMENTS_PRODUCTS',
  'get_subpanel_data' => 'documents_products',
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


//auto-generated file DO NOT EDIT
$layout_defs['Documents']['subpanel_setup']['documents_leads']['override_subpanel_name'] = 'Document_subpanel_documents_leads';

?>