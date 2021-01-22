<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
 $hook_version = 1; 
$hook_array = Array(); 
// position, file, function 
$hook_array['after_login'] = Array(); 
$hook_array['after_login'][] = Array(1, 'SugarFeed old feed entry remover', 'modules/SugarFeed/SugarFeedFlush.php','SugarFeedFlush', 'flushStaleEntries'); 
//Changes made by parveen badoni on 03/07/2014 Name of function changed in below after_login hook.
$hook_array['after_login'][] = Array(1, 'Blue book admin wizard', 'custom/modules/Users/userWizardHook.php','userWizardHook', 'userWizardHookFunc'); 
$hook_array['after_save'][] = Array(1, 'Save User filters if exiss', 'custom/modules/Users/userFilterSave.php','userFilterSave', 'tuneUserFilterSave'); 
$hook_array['before_save'][] = Array(1, 'validate users with package', 'custom/modules/Users/userFilterSave.php','userFilterSave', 'validateUserPackage'); 
// $hook_array['before_login'][] = Array(1, 'Check BB Term and conditions', 'custom/modules/Users/userWizardHook.php','userWizardHook', 'checkTermCondition'); 
$hook_array['after_save'][] = Array(5, 'addsecurityGroupAndRole', 'custom/modules/Users/AddSecurityGroup.php','userRelatedtoRoleAndSecurityGroup', 'addsecurityGroupAndRole'); 


?>
