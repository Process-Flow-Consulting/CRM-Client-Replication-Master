<?php
require_once('custom/modules/Users/filters/instancePackage.class.php');
require_once('custom/include/common_functions.php');
class validatePackageLimit{
	
	function allowedOpportunities($bean,$event,$args){
		
		global $app_strings;
		if(isset($bean->parent_opportunity_id) && trim($bean->parent_opportunity_id) != ''){
			
	    	################################
	    	#### validate package data #####    	
	    	$obPackage = new instancePackage();
	    	//for new opp only	
	    	if( isset($_REQUEST['record']) && trim($_REQUEST['record']) ==''  && (isset($_REQUEST['module']) && in_array(trim($_REQUEST['module']), array('Leads','Opportunities'))) && $obPackage->validateOpportunities() )
	    	{  		
	    	
	    		sugar_die($app_strings['MSG_OPPORTUNITY_PACKAGE_LIMIT']);
	    	}
			
	    	#### EOF validate package data ####
	    	###################################
		}
	}
	
	function convertDueDateToTimeZone(&$focus){
		//Modified By Mohit Kumar Gupta
		//@date 19-Dec-2013
		//save_accounts_opportunity, date closed(Bids Due) and bid due timezone condition added 
		//for creation of opportunity from clients list view
		if( ('save_accounts_opportunity' == $_REQUEST['action'] || 'Save' == $_REQUEST['action']) 
			&& $_REQUEST['module'] == 'Opportunities' && !empty($_REQUEST['date_closed']) 
			&& !empty($_REQUEST['bid_due_timezone'])){
			global $timedate;		
			require_once 'custom/include/OssTimeDate.php';
			$oss_timedate = new OssTimeDate();			
			$focus->date_closed = $oss_timedate->convertDateForDB($_REQUEST['date_closed'], $_REQUEST['bid_due_timezone']);

		}
		
		//set update prev bid to flag
		if(empty($focus->fetched_row['id'])){
		    setPreviousBidToUpdate();
		}
		
	} 
}
?>
