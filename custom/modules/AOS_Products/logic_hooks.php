<?php
// Do not store anything in this file that is not part of the array or the hook version.  This file will	
// be automatically rebuilt in the future. 
$hook_version = 1; 
$hook_array = Array(); 
// position, file, function 
$hook_array['before_save'] = Array(); 
$hook_array['before_save'][] = Array(1, 'Products save checkbox', 'custom/modules/AOS_Products/SaveCheckBox.php','SaveCheckBox', 'SaveMarkupCheckBox');
$hook_array['process_record'][] = Array(1, 'Accounts Proview Link setup', 'custom/modules/AOS_Products/SaveCheckBox.php','SaveCheckBox', 'setAccountProviewLink');

?>
