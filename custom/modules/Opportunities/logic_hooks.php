<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
$hook_version = 1;
$hook_array = Array(); 
// position, file, function 
$hook_array['before_save'] = Array(); 
$hook_array['before_save'][] = Array(1, 'Opportunities push feed', 'modules/Opportunities/SugarFeeds/OppFeed.php','OppFeed', 'pushFeed'); 
$hook_array['before_save'][] = Array(2, 'Validate Pakcage for Opportunities', 'custom/modules/Opportunities/OppValidatePackage.php','validatePackageLimit', 'allowedOpportunities');
$hook_array['before_save'][] = Array(3, 'TimeZone', 'custom/modules/Opportunities/OppValidatePackage.php','validatePackageLimit', 'convertDueDateToTimeZone');

$hook_array['after_save'] = Array();
$hook_array['after_save'][] = Array(1, 'Update Sales Stage','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','UpdateSalesStage');
$hook_array['after_save'][] = Array(2, 'Average Amount','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','getAverageAmount');
$hook_array['after_save'][] = Array(3, 'Update Teams','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','UpdateTeams');

$hook_array['after_ui_frame'] = Array(); 
$hook_array['process_record'][] = Array(1, 'Accounts Proview Link setup', 'custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks', 'setAccountProviewLink');

$hook_array['after_save'][] = Array(4, 'Update Bid Status on BB Hub','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','UpdateBidStatusOnHub');
$hook_array['after_save'][] = Array(5, 'Update Client Bid Status','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','updateClientBidStatus');


$hook_array['before_save'][] = array(4, 'Check BoX Save', 'custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks', 'saveCheckBox');
$hook_array['before_save'][] = array(5, 'Save Fetched Row', 'custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks', 'saveFetchedRow');
$hook_array['before_save'][] = Array(6, 'Update outlook data to CRM', 'custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks', 'updateOutlookData');
$hook_array['after_save'][] = array(6, 'Create Related Project Lead','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','createUpdateRelatedProjectLead' );
$hook_array['after_save'][] = Array(5, 'Update Client Bid Status','custom/modules/Opportunities/OpportunityHooks.php','OpportunityHooks','updateZoneRelationship');

?>