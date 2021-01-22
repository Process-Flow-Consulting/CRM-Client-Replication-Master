<?php 

$hook_version = 1;
$hook_array = Array();
$hook_array['after_save'][] = Array(1, 'update Lead Look up count for online links ', 'custom/modules/oss_OnlinePlans/onlinePlanLogicHooks.php','OnlinPlanHooks', 'updateLookupCounts');
$hook_array['before_save'][] = Array(1, 'Update Type and Source', 'custom/modules/oss_OnlinePlans/onlinePlanLogicHooks.php','OnlinPlanHooks', 'updateTypeAndSource');
$hook_array['before_delete'][] = array(1, 'Restrict BB data', 'custom/modules/oss_OnlinePlans/onlinePlanLogicHooks.php','OnlinPlanHooks', 'restrictBBData');
?>