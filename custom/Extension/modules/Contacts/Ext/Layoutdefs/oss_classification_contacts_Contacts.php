<?php
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