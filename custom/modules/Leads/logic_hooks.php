<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 
$hook_array['before_save'] = Array(); 
$hook_array['before_save'][] = Array(1, 'Leads save checkbox', 'custom/modules/Leads/SaveData.php','LeadHooks', 'SaveData');
$hook_array['before_save'][] = Array(2, 'Leads push feed', 'modules/Leads/SugarFeeds/LeadFeed.php','LeadFeed', 'pushFeed');
$hook_array['before_save'][] = Array(77, 'updateGeocodeInfo', 'modules/Leads/LeadsJjwg_MapsLogicHook.php','LeadsJjwg_MapsLogicHook', 'updateGeocodeInfo'); 
$hook_array['after_save'] = Array(); 
$hook_array['after_save'][] = Array(77, 'updateRelatedMeetingsGeocodeInfo', 'modules/Leads/LeadsJjwg_MapsLogicHook.php','LeadsJjwg_MapsLogicHook', 'updateRelatedMeetingsGeocodeInfo');
$hook_array['before_save'][] = Array(2, 'Leads push feed', 'modules/Leads/SugarFeeds/LeadFeed.php','LeadFeed', 'pushFeed');
$hook_array['before_save'][] = Array(3, 'Leads TimeZone', 'custom/modules/Leads/SaveData.php','LeadHooks', 'convertDueDateToTimeZone'); 
$hook_array['before_save'][] = Array(4, 'leads save_url_field_hook', 'custom/modules/Leads/save_url_field_hook.php','save_url_field_hook', 'save_field');
$hook_array['before_save'][] = Array(5, 'Leads save modified', 'custom/modules/Leads/SaveData.php','LeadHooks', 'setModified');

$hook_array['after_save'][] = Array(1, 'Lead Look up ', 'custom/modules/Leads/SaveData.php','LeadHooks', 'updateLookupCounts');
$hook_array['after_save'][] = Array(2, 'Lead Change Log Flag ', 'custom/modules/Leads/SaveData.php','LeadHooks', 'changeLogFlag');
$hook_array['after_save'][] = Array(3, 'Update outlook data to CRM', 'custom/modules/Leads/SaveData.php','LeadHooks', 'updateOutlookData'); 
$hook_array['after_save'][] = Array(4, 'update project status to Converted', 'custom/modules/Leads/SaveData.php','LeadHooks', 'updateProjectStatus');
$hook_array['after_ui_frame'] = Array(); 

$hook_array['before_delete'] = Array();
$hook_array['before_delete'][] = Array(1, 'Delete Related Data', 'custom/modules/Leads/SaveData.php','LeadHooks','deleteRelatedData');
 



?>