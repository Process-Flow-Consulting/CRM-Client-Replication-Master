<?php
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