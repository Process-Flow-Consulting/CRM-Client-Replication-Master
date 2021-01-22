<?php

$hook_version = 1;
$hook_array = Array();

$hook_array['before_save'] = array();
$hook_array['before_save'][] = array(1, 'oss_LeadClientDetail SaveName', 'custom/modules/oss_LeadClientDetail/LeadClientHooks.php', 'LeadClientHooks','saveAccountName');
$hook_array['before_save'][] = array(2, 'oss_LeadClientDetail Save Modified', 'custom/modules/oss_LeadClientDetail/LeadClientHooks.php', 'LeadClientHooks','setModified');

$hook_array['process_record'] = array();
$hook_array['process_record'][] = array(1, 'Save Viewed ', 'custom/modules/oss_LeadClientDetail/LeadClientHooks.php', 'LeadClientHooks','setViewFlag');
$hook_array['process_record'][] = Array(2, 'Accounts Proview Link setup', 'custom/modules/oss_LeadClientDetail/LeadClientHooks.php','LeadClientHooks', 'setAccountProviewLink');
$hook_array['after_save'][] = array(1, 'Update bidder count for parent project leads', 'custom/modules/oss_LeadClientDetail/LeadClientHooks.php', 'LeadClientHooks','updateBidderCounts');
$hook_array['after_save'][] = array(1,'Update modified date of project lead','custom/modules/oss_LeadClientDetail/LeadClientHooks.php','LeadClientHooks','updateLeadModifiedDate');
?>
