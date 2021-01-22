<?php

$hook_version = 1;
$hook_array = Array();
$hook_array['after_save'] = array();
$hook_array['before_save'] = array();
$hook_array['before_save'][] = array(1,'Quotes set Layout Options', 'custom/modules/AOS_Quotes/QuoteHooks.php', 'QuoteHooks', 'setLayoutOptions');
//$hook_array['before_save'][] = array(1,'Quotes Scheduled Time', 'custom/modules/Quotes/SaveTZDelivery.php', 'SaveTZDelivery', 'convertDueDateToTimeZone');
$hook_array['after_save'][] = array(1,'Quotes Documents', 'custom/modules/AOS_Quotes/SaveDocument.php', 'SaveDocument', 'SaveDocument');
$hook_array['after_save'][] = array(2,'Opportunity Amount', 'custom/modules/AOS_Quotes/SaveOpportunity.php', 'SaveOpportunity', 'SaveOpportunityAmount');
$hook_array['after_save'][] = array(3,'Reset Verify Proposal', 'custom/modules/AOS_Quotes/QuoteHooks.php', 'QuoteHooks', 'ResetVerifyProposal');
//$hook_array['after_save'][] = array(4,'Schedule Proposal Delivery', 'custom/modules/AOS_Quotes/QuoteHooks.php', 'QuoteHooks', 'scheduleProposal');
$hook_array['process_record'][] = Array(1, 'Accounts Proview Link setup', 'custom/modules/AOS_Quotes/QuoteHooks.php','QuoteHooks', 'setAccountProviewLink');


?>
