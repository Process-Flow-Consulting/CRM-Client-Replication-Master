<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 
$hook_array['before_save'] = Array(); 
$hook_array['before_save'][] = Array(77, 'updateGeocodeInfo', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'updateGeocodeInfo'); 
$hook_array['after_save'] = Array(); 
$hook_array['after_save'][] = Array(77, 'updateRelatedMeetingsGeocodeInfo', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'updateRelatedMeetingsGeocodeInfo'); 
$hook_array['after_save'][] = Array(78, 'updateRelatedProjectGeocodeInfo', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'updateRelatedProjectGeocodeInfo'); 
$hook_array['after_save'][] = Array(79, 'updateRelatedOpportunitiesGeocodeInfo', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'updateRelatedOpportunitiesGeocodeInfo'); 
$hook_array['after_save'][] = Array(80, 'updateRelatedCasesGeocodeInfo', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'updateRelatedCasesGeocodeInfo'); 
$hook_array['after_relationship_add'] = Array(); 
$hook_array['after_relationship_add'][] = Array(77, 'addRelationship', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'addRelationship'); 
$hook_array['after_relationship_delete'] = Array(); 
$hook_array['after_relationship_delete'][] = Array(77, 'deleteRelationship', 'modules/Accounts/AccountsJjwg_MapsLogicHook.php','AccountsJjwg_MapsLogicHook', 'deleteRelationship'); 
$hook_array['before_save'][] = Array(1, 'Accounts', 'custom/modules/Accounts/SaveData.php','SaveData', 'SaveDataAccounts');
$hook_array['before_save'][] = Array(2, 'set Modified', 'custom/modules/Accounts/SaveData.php','SaveData', 'setModified');
$hook_array['process_record'][] = Array(1, 'Accounts Proview Link setup', 'custom/modules/Accounts/SaveData.php','SaveData', 'setProviewLink');
//$hook_array['after_retrieve'][] = Array(1, 'Accounts Proview Link setup', 'custom/modules/Accounts/SaveData.php','SaveData', 'setProviewLink');
$hook_array['after_save'][] = Array(2, 'Set first sorted classification', 'custom/modules/Accounts/SaveData.php','SaveData', 'setFirstSortedClassification');
$hook_array['after_save'][] = Array(3, 'Update outlook data to CRM', 'custom/modules/Accounts/SaveData.php','SaveData', 'updateOutlookData');
$hook_array['before_save'][] = Array(3, 'set county name', 'custom/modules/Accounts/SaveData.php','SaveData', 'updateCountyName');


?>