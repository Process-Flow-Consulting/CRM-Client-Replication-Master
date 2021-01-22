<?php
	require_once('include/MVC/View/SugarView.php');
	require_once('include/MVC/Controller/SugarController.php');
	global $current_user;
	global $db;
	$current_user->is_admin = true;
	require_once('modules/Administration/QuickRepairAndRebuild.php');
	$randc = new RepairAndClear();
	$randc->repairAndClearAll(array('clearAll'),array(translate('LBL_ALL_MODULES')), true,false);
?>
