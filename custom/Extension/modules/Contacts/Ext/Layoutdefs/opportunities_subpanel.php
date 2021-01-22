<?php
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