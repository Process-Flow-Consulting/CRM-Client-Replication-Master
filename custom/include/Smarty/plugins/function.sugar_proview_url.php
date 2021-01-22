<?php
require_once 'custom/include/common_functions.php';

/**
 * Smarty function for display proview icon if bluebook is present in the url
 * 
 * @param unknown_type $params
 * @param unknown_type $smarty
 * @return void|string
 */

function smarty_function_sugar_proview_url($params,&$smarty){	
	$proview_icon = '';
	if (!isset($params['url'])){
		$smarty->trigger_error("sugar_phone: missing 'url' parameter");
		return;
	}
	return proview_url($params);	
}