<?php
include_once 'include/MVC/View/SugarView.php';
include_once 'custom/modules/Leads/views/view.convert_to_opportunity.php';
class OpportunitiesViewAssigneduser extends SugarView{
	
	function OpportunitiesViewAssigneduser(){
		parent::SugarView();
	}
	
	function display(){
		
		global  $db, $sugar_config, $current_user;
		
		/**
		 * Added by : Ashutosh
		 * Date : 9 Sept
		 * purpose: To get the Project Opportunity assigned User
		 */
		
		$admin=new Administration();
		$admin_settings = $admin->retrieveSettings('instance', true);
		$geo_filter = $admin->settings ['instance_geo_filter'];
		
		if(isset($_REQUEST['for_project_opp'])){
		    
		    if($geo_filter == 'client_location'){
				//for client location do not suggest the user
		        sugar_die(json_encode(array('balnk')));	    	
		    }
		    
		    //prepare parameters for the function		    
		    if($_POST['lead_union_c'] == 1)
		        $arOppLabor[] = 'union_c';
		    
		    if($_POST['lead_non_union'] == 1)
		        $arOppLabor[] = 'non_union';
		    
		    if($_POST['lead_prevailing_wage'] == 1)
		        $arOppLabor[] = 'prevailing_wage';
		   	    
		    $stOppState = $_POST['lead_state'];
		    $stCountyId = $_POST['lead_county'];
		    $stZipCode = $_POST['lead_zip_code'];
		    $stOppType = $_POST['lead_type'];
		    
		    //get Assigned User
		    $assigned_user =LeadsViewConvert_to_opportunity::getAssignedUser('Leads',$stOppState,$stCountyId,$stZipCode,$stOppType,'',$arOppLabor);
		    
		    //get User Details
		    $obUser = BeanFactory::getBean('Users',$assigned_user['id']);
		    
		    //should not be admin
		    if($obUser->is_admin ){
		    sugar_die( json_encode(array(
								'id' => $current_user->id, 
								'name' => $current_user->name,
								// 'team_id' => $current_user->getPrivateTeam(),
								// 'team_name' => Team::getTeamName($current_user->getPrivateTeam()),
							)));
		    }else{
		        sugar_die( json_encode(array(
		        'id' => $obUser->id,
		        'name' => $obUser->name,
		        // 'team_id' => $obUser->getPrivateTeam(),
		        // 'team_name' => Team::getTeamName($obUser->getPrivateTeam()),
		        )));
		    }
		}
		
		$useGeoFilter = true;
		//1. suggest assigned user based on contact id
		if(!empty($_REQUEST['contact_id'])){

			$contact = new Contact();
			$contact->disable_row_level_security = true;
			$contact->retrieve($_REQUEST['contact_id']);
			
			$user_role = $this->getUserRole($contact->assigned_user_id);
			
			if(!empty($contact->assigned_user_id) && ( $user_role != 'lead_reviewer' ) ){
				
				// $private_team_id = User::getPrivateTeam($contact->assigned_user_id);
				// $private_team_name = Team::getTeamName($private_team_id);
				$useGeoFilter = false;
				echo json_encode(
						array(
								'id' => $contact->assigned_user_id, 
								'name' => $contact->assigned_user_name,
								// 'team_id' => $private_team_id,
								// 'team_name' => $private_team_name,
							)
				);
				return;
			}
		}
		
		//2. suggest assigned user based on client id
		if(!empty($_REQUEST['client_id']) ){			
			$account = new Account();
			$account->disable_row_level_security = true;
			$account->retrieve($_REQUEST['client_id']);
				
			$user_role = $this->getUserRole($account->assigned_user_id);
				
			if(!empty($account->assigned_user_id) && ( $user_role != 'lead_reviewer' ) ){
					
				// $private_team_id = User::getPrivateTeam($account->assigned_user_id);
				// $private_team_name = Team::getTeamName($private_team_id);
				$useGeoFilter = false;
				echo json_encode(
					array(
						'id' => $account->assigned_user_id,
						'name' => $account->assigned_user_name,
						// 'team_id' => $private_team_id,
						// 'team_name' => $private_team_name,
					)
				);
				
				return;
			}		
		}
		
		//3. suggest assigned user based on geo location if client contact or client  
		//does not have any assigned user
		if($useGeoFilter){				
			//third suggest assigned user based on location
			global $current_user_role;
			$current_user_role = $this->getUserRole($current_user->id);
			
			//get assigned to user if client location is set
			if($geo_filter == 'client_location'){
			
				$account->load_relationship ( 'oss_classifation_accounts' );
				$classIds = $account->oss_classifation_accounts->get ();
				$assigned_user = LeadsViewConvert_to_opportunity::getAssignedUser('Accounts',$account->billing_address_state,$account->county_id,$account->billing_address_postalcode,NULL,$classIds);
				
				if(count($assigned_user) > 0){
					
					if(!empty($assigned_user['id'])){
						
						// $private_team_id = User::getPrivateTeam($assigned_user['id']);
						// $private_team_name = Team::getTeamName($private_team_id);
						
						// $assigned_user['team_id'] = $private_team_id;
						// $assigned_user['team_name'] = $private_team_name;
					}
					
					echo json_encode($assigned_user);
					return;
				}
			}else if($geo_filter == 'project_location' && !empty($_REQUEST['parent_id']) ){
				//get assigned to user if project location is set
				$get_lead_id = " SELECT project_lead_id FROM opportunities WHERE id = '".$_REQUEST['parent_id']."' AND deleted = 0";
				$get_lead_id_query = $db->query($get_lead_id);
				$get_lead_id_result = $db->fetchByAssoc($get_lead_id_query);
				
				if(!empty($get_lead_id_result['project_lead_id'])){

					$lead = new Lead();
					$lead->retrieve($get_lead_id_result['project_lead_id']);
					$lead->load_relationship ( 'oss_classification_leads' );
					$leadClassIds = $lead->oss_classification_leads->get ();
					$lead_labor = array();
						
					if($lead->union_c == 1)
						$lead_labor[] = 'union_c';
						
					if($lead->non_union == 1)
						$lead_labor[] = 'non_union';
						
					if($lead->prevailing_wage == 1)
						$lead_labor[] = 'prevailing_wage';
						
					$assigned_user =LeadsViewConvert_to_opportunity::getAssignedUser('Leads',$lead->state,$lead->county_id,$lead->zip_code,$lead->type,$leadClassIds,$lead_labor);
					
					if(count($assigned_user) > 0){
						
						if(!empty($assigned_user['id'])){
						
							// $private_team_id = User::getPrivateTeam($assigned_user['id']);
							// $private_team_name = Team::getTeamName($private_team_id);
						
							// $assigned_user['team_id'] = $private_team_id;
							// $assigned_user['team_name'] = $private_team_name;
						}
						
						echo json_encode($assigned_user);
						return;
					}
				}
			}
		}
		
		/**
		 * if action requested by client to opportunity creation for assigned user filter
		 * in case of project location assignment
		 * @modified By Mohit Kumar Gupta
		 * @date 05-dec-2013
		 */
		if(isset($_REQUEST['for_convert_client_project_opp'])){
			if($geo_filter == 'client_location'){
				//for client location do not suggest the user
				sugar_die(json_encode(array('balnk')));
			}
			$get_lead_id = " SELECT project_lead_id FROM opportunities WHERE id = '".$_REQUEST['parentOpportunityId']."' AND deleted = 0";
			$get_lead_id_query = $db->query($get_lead_id);
			$get_lead_id_result = $db->fetchByAssoc($get_lead_id_query);
			
			if(!empty($get_lead_id_result['project_lead_id'])){
			
				$lead = new Lead();
				$lead->retrieve($get_lead_id_result['project_lead_id']);
				$lead->load_relationship ( 'oss_classification_leads' );
				$leadClassIds = $lead->oss_classification_leads->get ();
				$lead_labor = array();
			
				if($lead->union_c == 1)
					$lead_labor[] = 'union_c';
			
				if($lead->non_union == 1)
					$lead_labor[] = 'non_union';
			
				if($lead->prevailing_wage == 1)
					$lead_labor[] = 'prevailing_wage';
			
				$assigned_user =LeadsViewConvert_to_opportunity::getAssignedUser('Leads',$lead->state,$lead->county_id,$lead->zip_code,$lead->type,$leadClassIds,$lead_labor);
					
				if(count($assigned_user) > 0){
					echo json_encode($assigned_user);
					return;
				} else {
					sugar_die(json_encode(array('balnk')));
				}
			} else {
				sugar_die(json_encode(array('balnk')));
			}
		}
	}

	function getUserRole($user_id){
	    
	    require_once 'custom/modules/Users/role_config.php';
		global $db,$arUserRoleConfig;
		
		 

		if(empty($user_id))
			return '';
		
		$sql = "SELECT acl_roles.id FROM users 
		INNER JOIN acl_roles_users ON acl_roles_users.user_id = users.id
		AND acl_roles_users.deleted = 0
		INNER JOIN acl_roles ON acl_roles.id  = acl_roles_users.role_id
		AND acl_roles.deleted = 0 WHERE users.deleted = 0 AND users.id ='".$user_id."' ";
		
		$query = $db->query($sql);
		
		$arUserRoleMap = array_flip($arUserRoleConfig);
		$result = $db->fetchByAssoc($query);
		
		if(!empty($arUserRoleMap[$result['id']])){
			return $arUserRoleMap[$result['id']];
		}
		
		return '';
	}
}
