<?php 
 //WARNING: The contents of this file are auto-generated


$admin_option_defs = array();
/* $admin_option_defs['Administration']['AOR_Reports'] = array('AOR_Reports', 'LNK_CREATE_REPORT', 'LNK_CREATE_REPORT_INFO', './index.php?module=AOR_Reports&report_module=&action=index&page=report&Create+Custom+Report=Create+Custom+Report');
$admin_option_defs['Administration']['oss_Zone'] = array('oss_Zone', 'LNK_CREATE_ZONE', 'LNK_CREATE_ZONE_INFO', './index.php?module=oss_Zone&action=EditView&return_module=oss_Zone&return_action=DetailView');
$admin_group_header[] = array('LBL_REPORTS_TITLE', '', faLse, $admin_option_defs, 'LNK_REPORT_DISC'); */

//Project Pipeline Configuration
$admin_option_defs = array();
$admin_option_defs['Administration']['geo_filters'] = array('Administration', 'LNK_GEO_FILTERS', 'LNK_GEO_FILTERS_INFO', './index.php?module=Users&action=bbwizard&geofilters=1','repair');
$admin_option_defs['Administration']['target_classification'] = array('Administration', 'LNK_TARGET_CLASS_INST', 'LNK_TARGET_CLASS_INST_DISC', './index.php?module=Users&action=mytargetclass','repair');
//$admin_option_defs['Administration']['searchsettings'] = array('Administration', 'LNK_SEARCH_SETTINGS', 'LNK_SEARCH_SETTINGS_DESC', './index.php?module=Configurator&action=searchsettings');
$admin_option_defs['Administration']['pl_import_map'] = array('Administration', 'LNK_IMPORT_MAP_SETTINGS', 'LNK_IMPORT_MAP_SETTINGS_INFO', './index.php?module=Administration&action=manage_import','repair');
$admin_option_defs['Administration']['updatequotenum'] = array('Administration', 'LNK_UPDATE_QUOTE_NUMBER', 'LNK_UPDATE_QUOTE_NUMBER_INFO', './index.php?module=Administration&action=updatequotenum&return_module=Administration&return_action=index','repair');
$admin_option_defs['Administration']['reset_roles'] = array('Administration', 'LNK_RESET_ROLES_INST', 'LNK_RESET_ROLES_INST_DISC', './index.php?module=Users&action=resetmyroles','repair');

$admin_group_header[] = array('LBL_BLUE_BOOK_INFO_TITLE', '', faLse, $admin_option_defs, 'LNK_FILTER_CLASS_DISC');
//Project Pipeline Report
$admin_option_defs = array();
$admin_option_defs['Administration']['AOR_Reports'] = array('AOR_Reports', 'LNK_CREATE_REPORT', 'LNK_CREATE_REPORT_INFO', './index.php?module=AOR_Reports&action=EditView&return_module=Administration&return_action=index','releases');
$admin_option_defs['Administration']['oss_Zone'] = array('oss_Zone', 'LNK_CREATE_ZONE', 'LNK_CREATE_ZONE_INFO', './index.php?module=oss_Zone&action=EditView&return_module=oss_Zone&return_action=DetailView','releases');
$admin_group_header[] = array('LBL_REPORTS_TITLE', '', faLse, $admin_option_defs, 'LNK_REPORT_DISC');


$admin_option_defs = array();
$admin_option_defs['Administration']['view_custom_field'] = array('Administration', 'LBL_CREATE_CUSTOM_FIELDS', 'LBL_CREATE_CUSTOM_FIELDS_SETTINGS_INFO', './index.php?module=Administration&action=viewcustomfields&return_module=Administration&return_action=index','repair');
$admin_group_header[] = array('LBL_BLUE_BOOK_CUSTOM_FIELDS', '', faLse, $admin_option_defs, 'LBL_BLUE_BOOK_CUSTOM_FIELDS_MANAGE');

//overwrite existing products and quotes listing
$admin_group_header[3][3]['Quotes']['unit_of_measure'] = Array(
    'oss_UnitOfMeasure',
    'LNK_INSERT_UNIT_OF_MEASURE',
    'LBL_INSERT_UNIT_OF_MEASURE',
    './index.php?module=oss_UnitOfMeasure&action=index',
	'releases'
);





?>