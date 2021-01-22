<?php

    $hook_version = 1;
    $hook_array = Array();

    $hook_array['before_save'] = Array();
    $hook_array['before_save'][] = Array(
        //Processing index. For sorting the array.
        1,
       
        //Label. A string value to identify the hook.
        'hook of zone',
       
        //The PHP file where your class is located.
        'custom/modules/oss_Zone/zone_hooks.php',
       
        //The class the method is in.
        'zone_hooks',
       
        //The method to call.
        'saveData'
    );
    $hook_array['after_save'] = Array();
    $hook_array['after_save'][] = Array(
    		//Processing index. For sorting the array.
    		1,
    		 
    		//Label. A string value to identify the hook.
    		'hook of zone',
    		 
    		//The PHP file where your class is located.
    		'custom/modules/oss_Zone/zone_hooks.php',
    		 
    		//The class the method is in.
    		'zone_hooks',
    		 
    		//The method to call.
    		'addRelationship'
    );
?>