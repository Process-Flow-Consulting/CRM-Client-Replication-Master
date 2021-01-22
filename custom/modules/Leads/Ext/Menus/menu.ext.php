<?php 
 //WARNING: The contents of this file are auto-generated

 
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $mod_strings, $app_strings, $sugar_config;
$module_menu = array();
if (ACLController::checkAccess('Leads', 'edit', true)) {
    $module_menu[]=array("index.php?module=Leads&action=EditView&return_module=Leads&return_action=DetailView" , $mod_strings['LNK_NEW_LEAD'],"Create");
}
if (ACLController::checkAccess('Leads', 'edit', true)) {
    $module_menu[]=array("index.php?module=Leads&action=ImportVCard", $mod_strings['LNK_IMPORT_VCARD'],"Create_Lead_Vcard", 'Leads');
}
if (ACLController::checkAccess('Leads', 'list', true)) {
    $module_menu[]=array("index.php?module=Leads&action=index&return_module=Leads&return_action=DetailView", $mod_strings['LNK_LEAD_LIST'],"List", 'Leads');
}
if(empty($sugar_config['disc_client'])){
	if(ACLController::checkAccess('Leads', 'list', true))$module_menu[] =Array("index.php?module=AOR_Reports&action=index&view=leads", $mod_strings['LNK_LEAD_REPORTS'],"List", 'LeadReports');
}

if(ACLController::checkAccess('Leads', 'import', true))$module_menu[]=Array("index.php?module=Leads&action=index&pull_project=1", $mod_strings['LNK_PULL_PROJECT_LEADS'],"Import", 'Leads');

if(ACLController::checkAccess('Leads', 'import', true))$module_menu[]=Array("index.php?module=Leads&action=index&min_one_bidder=1", $mod_strings['LNK_PULL_PROJECT_LEADS_MIN_ONE_BIDDER'],"Import", 'Leads');

if(ACLController::checkAccess('Leads', 'import', true))$module_menu[]=Array("index.php?module=Leads&action=import", $mod_strings['LNK_IMPORT_OTHER_LEADS'],"Import", 'Leads');


?>