<?php
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