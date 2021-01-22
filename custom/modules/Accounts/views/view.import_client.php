<?php 
//ob_start();
//ini_set('display_errors',1);
require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';

class AccountsViewImport_client extends SugarView {
	
	/*
	 * construct method
	 */
	function AccountsViewImport_client() {
		parent::SugarView();
	}
	
	/*
	 * Overrwite SugarView Display method
	 * 
	 */
	function display(){
		global $app_list_strings,$db,$current_user,$app_strings,$timedate,$sugar_config,$beanList;
		
		$pullObj = new PullBBH($_REQUEST['bb_id']);
		$pullObj->insertUpdateClients(true);		

		$url = "index.php?module=Accounts&action=master_lookup";
		
		if(isset($_REQUEST['company']) && !empty($_REQUEST['company'])){
		  /**
		   *  Modified by : Ashutosh
		   *  Date : 4 Apr 2014
		   *  Purpose : handle html special chars
		   *  
		   */ 		  	
		  $stCompanyName = urlencode(html_entity_decode($_REQUEST['company'],ENT_QUOTES));
		  $url .= "&company=".$stCompanyName;
		}
		if(isset($_REQUEST['phone']) && !empty($_REQUEST['phone'])){
		    $url .= "&phone=".$_REQUEST['phone'];
		}
		if(isset($_REQUEST['region']) && !empty($_REQUEST['region'])){
		    $url .= "&region=".$_REQUEST['region'];
		}
		if(isset($_REQUEST['show_button']) && !empty($_REQUEST['show_button']) && $_REQUEST['show_button'] == 1){
		    $url .= "&button=Search";
		}
		if(isset($_REQUEST['region1']) && !empty($_REQUEST['region1'])){
		    $url .= "&region1=".$_REQUEST['region1'];
		}
		if(isset($_REQUEST['classification']) && !empty($_REQUEST['classification'])){
		    $url .= "&classification=".urlencode(html_entity_decode($_REQUEST['classification'],ENT_QUOTES));
		}
		if(isset($_REQUEST['search_option']) && !empty($_REQUEST['search_option'])){
		    $url .= "&search_option=".$_REQUEST['search_option'];
		}
		
		/**
		 *  Modified by : Mohit Kumar Gupta
		 *  Date : 23-02-2015
		 *  Purpose : User should return to the previous search page when the page reloads 
		 *  after adding the clients to Project Pipeline.		 
		 */
		if(isset($_REQUEST['page']) && !empty($_REQUEST['page'])){
		    $url .= "&page=".$_REQUEST['page'];
		}
		
		//set next client id to import from bluebook
		$arAllSelectedids = array_values(array_unique($_REQUEST['mass']));
		$tmpCheckedIds = array_flip($arAllSelectedids);
		$iCurrentIndex = $tmpCheckedIds[$_REQUEST['bb_id']];
		$iNextid = ($iCurrentIndex == (count($arAllSelectedids) -1 ))?'end':$arAllSelectedids[$iCurrentIndex+1];
		
		die(json_encode(array('redirect_url'=>$url,'next_id'=>$iNextid)));
		exit(0);
		/*
		 * OLD METHOD OF IMPORTING CLIENTS FROM BLUEBOOK 
		 * 
		 *
		//get client ids from post data
		$company_ids = $_REQUEST['uid'];
		
		$client_count = 0;
		
		if(!empty($company_ids)){
			
			//create array of from the uids seperated by comma
			$company_ids = explode(',',$company_ids);
			
			foreach ($company_ids as $company_id){
				
				$pullObj = new PullBBH($company_id);
            	$pullObj->insertUpdateClients(true);
            	
            	$client_count++;
					
			}
			
		}
		
		$url = "index.php?module=Accounts&action=master_lookup";
		
		if(isset($_REQUEST['company']) && !empty($_REQUEST['company'])){
			$url .= "&company=".$_REQUEST['company'];
		}
		if(isset($_REQUEST['phone']) && !empty($_REQUEST['phone'])){
			$url .= "&phone=".$_REQUEST['phone'];
		}
		if(isset($_REQUEST['region']) && !empty($_REQUEST['region'])){
			$url .= "&region=".$_REQUEST['region'];
		}
		if(isset($_REQUEST['show_button']) && !empty($_REQUEST['show_button']) && $_REQUEST['show_button'] == 1){
			$url .= "&button=Search";
		}
		if(isset($_REQUEST['region1']) && !empty($_REQUEST['region1'])){
			$url .= "&region1=".$_REQUEST['region1'];
		}
		if(isset($_REQUEST['classification']) && !empty($_REQUEST['classification'])){
			$url .= "&classification=".$_REQUEST['classification'];
		}
		if(isset($_REQUEST['search_option']) && !empty($_REQUEST['search_option'])){
			$url .= "&search_option=".$_REQUEST['search_option'];
		}
		if($client_count > 0){
			$url .= "&count=".$client_count;
		}
		
		//redirect the page Blue Book Search Page
		//SugarApplication::redirect($url);
	*/	
	}
}
//ob_flush();


?>
