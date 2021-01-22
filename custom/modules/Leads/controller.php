<?php


//require_once 'include/MVC/Controller/SugarController.php';
require_once 'modules/Leads/controller.php';

class CustomLeadsController extends LeadsController {

    function CustomLeadsController() {
        parent::LeadsController();
    }

    /*function action_delete() {
        $lead = new Lead();
        $lead->retrieve($_REQUEST['record']);
        #####################
		### ACCESS FILTER ###
		global $current_user;
		if( !$current_user->is_admin){
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			
			$bAccess = userAccessFilters::isLeadAccessable($lead->id,true);
			if(!$bAccess){
				//if this record is not with in current users filters do 
				// not delete
				sugar_die('Access Restriction to delete this data.');		
			}
		}
		### END OF ACCESS FILTER ###
		############################

        if (isset($lead->lead_source) && ($lead->lead_source=='bb')) {            
			$_SESSION['message']='You are not authorised to delete this project lead.';
			SugarApplication::redirect('index.php?module=Leads&action=DetailView&record='.$_REQUEST['record']);						
        }
        parent::action_delete();
    }*/

    function action_detailview(){
        require_once 'custom/modules/Leads/bbProjectLeads.php';
        $stLeadRecordId = $this->bean->id;
        $this->bean = new bbProjectLeads();
        $this->bean->retrieve($stLeadRecordId);
        $this->view = 'detail';

    }
    
    function action_subpanelviewer(){
      global $focus,$beanList,$beanFiles;
        require_once 'custom/modules/Leads/bbProjectLeads.php';
        $beanList['bbProjectLeads'] = 'bbProjectLeads';
        $beanFiles['bbProjectLeads'] = 'custom/modules/Leads/bbProjectLeads.php';
        $this->parent_bean = new bbProjectLeads();
        
        $this->view = 'subpanvier';

    }

    function action_dedupe_details(){
        
        $this->view = 'dedupe_details';
        
    }

    function action_pull_project_leads() {
        $this->view = 'pull_project_leads';
    }

    function action_deduping() {
        $this->view = 'deduping';
    }

    function action_massupdate() {
    	
    	global  $db;
    	
        require_once 'custom/modules/Leads/bbProjectLeads.php';

        if (!empty($_REQUEST['massupdate']) && $_REQUEST['massupdate'] == 'true' && (!empty($_REQUEST['uid']) || !empty($_REQUEST['entire']))) {
            if (!empty($_REQUEST['Delete']) && $_REQUEST['Delete'] == 'true' && !$this->bean->ACLAccess('delete')
                    || (empty($_REQUEST['Delete']) || $_REQUEST['Delete'] != 'true') && !$this->bean->ACLAccess('save')) {
                ACLController::displayNoAccess(true);
                sugar_cleanup(true);
            }

            set_time_limit(0); //I'm wondering if we will set it never goes timeout here.
            // until we have more efficient way of handling MU, we have to disable the limit
            $GLOBALS['db']->setQueryLimit(0);
            require_once("include/MassUpdate.php");
            require_once('modules/MySettings/StoreQuery.php');
            //$seed = loadBean($_REQUEST['module']);
            $seed = new bbProjectLeads();
            $mass = new MassUpdate();
            
            $mass->setSugarBean($seed);
            if (isset($_REQUEST['entire']) && empty($_POST['mass'])) {
                //Modified By Mohit Kumar Gupta 28-02-2014
                //for assigning serach fields in request object to access in create new list query 
                $_REQUEST = array_merge($_REQUEST,unserialize(base64_decode($_REQUEST['current_query_by_page'])));
                $mass->generateSearchWhere('Leads', $_REQUEST['current_query_by_page']);
            }
            
            if(!empty($_REQUEST['uid'])) $_POST['mass'] = explode(',', $_REQUEST['uid']); // coming from listview
            elseif(isset($_REQUEST['entire']) && empty($_POST['mass'])) {
            	if(empty($order_by))$order_by = ''; 
            	//Modified By Mohit Kumar Gupta 28-02-2014
            	//for accessing create new list query with search fields
            	$query = $seed->create_new_list_query($order_by, $mass->where_clauses);
            	$result = $db->query($query,true);
            	$new_arr = array();
            	while($val = $db->fetchByAssoc($result,false))
            	{
            		array_push($new_arr, $val['id']);
            	}
            	$_POST['mass'] = $new_arr;
            }
            
            //update assigned user id
            if(isset($_POST['a_assigned_user_id']) && !empty($_POST['a_assigned_user_id'])){
            	 
            	if(isset($_POST['mass']) && is_array($_POST['mass'])  && $_REQUEST['massupdate'] == 'true'){
            		 
            		foreach($_POST['mass'] as $id){
            			if(empty($id)) {
            				continue;
            			}
            			 
            			$lead = new Lead();
            			$lead->retrieve($id);
            			if($lead->ACLAccess('Save')){
            				 
            				$check_notify = FALSE;
            				 
            				if (isset( $lead->assigned_user_id)) {
            					$old_assigned_user_id = $lead->assigned_user_id;
            					if (!empty($_POST['a_assigned_user_id'])
            							&& ($old_assigned_user_id != $_POST['a_assigned_user_id'])
            							&& ($_POST['a_assigned_user_id'] != $current_user->id)) {
            						$check_notify = TRUE;
            					}
            				}
            				$lead->assigned_user_id = $_POST['a_assigned_user_id'];
            				$lead->save($check_notify);
            			}
            		}
            	}
            }else{
            	
            	foreach($_POST['mass'] as $key => $id){
            		if(empty($id)) {
            			continue;
            		}
            		$lead = new Lead();
            		$lead->retrieve($id);
            		
            		if( !isset($_REQUEST['status']) ){
            		
	            		if($_REQUEST['Delete'] != true ){
						
	            		
		            		if($lead->lead_source == 'bb' ){
		            			unset($_POST['mass'][$key]);
		            		}
	            		}
	            		unset($lead);
            		}
            	}
            	
            	$_REQUEST['uid'] = implode(',',$_POST['mass']);
            	unset($_REQUEST['entire']);
            	
            	$mass->handleMassUpdate();
            }
           // die;
            $storeQuery = new StoreQuery(); //restore the current search. to solve bug 24722 for multi tabs massupdate.
            $temp_req = array('current_query_by_page' => $_REQUEST['current_query_by_page'], 'return_module' => $_REQUEST['return_module'], 'return_action' => $_REQUEST['return_action']);
            if ($_REQUEST['return_module'] == 'Emails') {
                if (!empty($_REQUEST['type']) && !empty($_REQUEST['ie_assigned_user_id'])) {
                    $this->req_for_email = array('type' => $_REQUEST['type'], 'ie_assigned_user_id' => $_REQUEST['ie_assigned_user_id']); //specificly for My Achieves
                }
            }
            $_REQUEST = array();
            $_REQUEST = unserialize(base64_decode($temp_req['current_query_by_page']));
            unset($_REQUEST[$seed->module_dir . '2_' . strtoupper($seed->object_name) . '_offset']); //after massupdate, the page should redirect to no offset page
            $storeQuery->saveFromRequest($_REQUEST['module']);
            $_REQUEST = array('return_module' => $temp_req['return_module'], 'return_action' => $temp_req['return_action']); //for post_massupdate, to go back to original page.
        } else {
            sugar_die("You must massupdate at least one record");
        }
    }

    function action_convert_to_opportunity(){
        $this->view = 'convert_to_opportunity';
    }

    function action_save_opportunity(){
        $this->view = 'save_opportunity';
    }
    
	function action_getallbidders(){
        $this->view = 'getallbidders';
    }
    
    function action_autocomplete_account(){
    	$this->view = 'autocomplete_account';
    }
    
    function action_deletededuping(){
    	require_once 'custom/modules/Leads/Deletededuping.php';
    }
    
    function action_review_opportunity(){
    	$this->view = 'review_opportunity';
    }

    function action_save_new_opportunity(){
    	$this->view = 'save_new_opportunity';
    }
    
    function action_get_dedupted_bidders(){
    	$this->view = 'get_dedupted_bidders';
    	
    }
    
    function action_projecturl(){
    	$this->view = 'projecturl';
    	 
    }
    
    function action_relatedpl(){
    	$this->view = 'relatedpl';
    }
    
    function action_pldetails(){
    	$this->view = 'pldetails';
    }
	
    function action_mass_deduping(){
    	$this->view = 'mass_deduping';
    }
    
    function action_link_projects(){
    	$this->view = 'link_projects';
    }
    function action_importcsvstep1(){
        $this->view = 'importcsvstep1';
    }
    function action_importcsvconfirm(){
        $this->view = 'importcsvconfirm';
    }
    function action_importcsvstep2(){
        $this->view = 'importcsvstep2';
    }
    function action_importcsvstep3(){
        $this->view = 'importcsvstep3';
    }
    function action_importcsvfinal(){
        $this->view = 'importcsvfinal';
    }
    function action_importcsvmapping(){
        $this->view = 'importcsvmapping';
    }
    function action_importcsvbidders(){
        $this->view = 'importcsvbidders';
    }
    function action_importcsvstart1(){
        $this->view = 'importcsvstart1';
    }
    function action_importcsvstart2(){
        $this->view = 'importcsvstart2';
    }
	function action_finishcsvimport(){
        $this->view = 'finishcsvimport';
    }
	function action_import(){
        $this->view = 'import';
    }
}

?>
