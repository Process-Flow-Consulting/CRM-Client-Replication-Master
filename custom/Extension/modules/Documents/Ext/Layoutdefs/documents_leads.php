<?php
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