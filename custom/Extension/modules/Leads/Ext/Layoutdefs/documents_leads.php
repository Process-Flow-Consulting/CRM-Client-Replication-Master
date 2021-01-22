<?php
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