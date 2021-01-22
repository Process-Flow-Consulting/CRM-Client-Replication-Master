<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('modules/Opportunities/OpportunityFormBase.php');
require_once ('include/utils/logic_utils.php');

class CustomOpportunityFormBase extends OpportunityFormBase{


	function handleSave($prefix,$redirect=true, $useRequired=false){
	    global $current_user;
		
		
		require_once('include/formbase.php');
		
		//use custom bean for overwriting notification body
		require_once('custom/modules/Opportunities/OpportunitySummary.php');
		$focus = new OpportunitySummary();
		
		if($useRequired &&  !checkRequired($prefix, array_keys($focus->required_fields))){
			return null;
		}
	
	    if(empty($_POST['currency_id'])){
	        $currency_id = $current_user->getPreference('currency');
	        if(isset($currency_id)){
	            $focus->currency_id =   $currency_id;
	        }
	    }
		$focus = populateFromPost($prefix, $focus);
		
		if( !ACLController::checkAccess($focus->module_dir, 'edit', $focus->isOwner($current_user->id))){
			ACLController::displayNoAccess(true);
		}
		$check_notify = FALSE;
		if (isset($GLOBALS['check_notify'])) {
			$check_notify = $GLOBALS['check_notify'];
		}
	
		$focus->save($check_notify);
		/**
		 * Added By : Ashutosh 
		 * Date : 5 Sept 2013
		 * purpose : if the change_client_op_assigned flag is set 
		 *           then change the assignement for all the realted 
		 *           client opportunities. 
		 */
		if(isset($_REQUEST['change_client_op_assigned'])){
			//remove previous teams and assignment and update with the 
			// same assigned user and users team
            //load relationship for linked opportunities
            $focus->disable_row_level_security = 1;
            $focus = BeanFactory::getBean('Opportunities',$focus->id);
            $focus->load_relationship('opportunity_to_opportunity_var');
           
            //Get All the linked opportunities
            $arClientOpportunityIds =   $focus->opportunity_to_opportunity_var->getBeans();
                                            
            /*
             * add project opportunity logic commented for the requirement of 
             * Auto Update Team Name when Re-Assigning Project Opportunity BSI-783
             * Modified by Mohit Kumar Gupta 16-10-2015             
             */
            //add Project Opportunity for the assignment
            //$arClientOpportunityIds[] = $focus;
            
            foreach($arClientOpportunityIds as $opId => $obClientOpportunity){
            
                $obClientOpportunity->disable_row_level_security = 1;
                //load relationship with teams
                $obClientOpportunity->load_relationship('teams');
               
                //get assigned User details and private team
                $obAssignedUser = BeanFactory::getBean('Users',$focus->assigned_user_id );
                $stAssignedUsersPrivateTeam =  $obAssignedUser->getPrivateTeam();                            
                
                //set team for assigned User
                $obClientOpportunity->assigned_user_id = $focus->assigned_user_id;
                $obClientOpportunity->team_id = $stAssignedUsersPrivateTeam;
                $obClientOpportunity->teams->replace(array($stAssignedUsersPrivateTeam));
                
                //do not execute the logic hooks
                $obClientOpportunity->processed = true;
                                          
                $obClientOpportunity->save();
            }           
		}
	
		if(!empty($_POST['duplicate_parent_id'])){
			clone_relationship($focus->db, array('opportunities_contacts'),'opportunity_id',  $_POST['duplicate_parent_id'], $focus->id);
		}
		$return_id = $focus->id;
		
		$GLOBALS['log']->debug("Saved record with id of ".$return_id);
		
		if($redirect){
			
			//if project opportunity create/edited send to create new client opportunity
			/////////////////////if edited from subpanel//////////////////////
			if(isset($_REQUEST['return_module']) && isset($_REQUEST['return_relationship']) && ($_REQUEST['save_action'] == 'save')){
				handleRedirect($return_id,"Opportunities" );
			}else if( ($_REQUEST['action'] == 'Save') && ($focus->module_dir == 'Opportunities') && empty($focus->parent_opportunity_id) && !empty($focus->fetched_row['id']) ){
				SugarApplication::redirect('index.php?module='.$focus->module_dir.'&action=DetailView&record='.$return_id);
			}else if( ($_REQUEST['action'] == 'Save') && ($focus->module_dir == 'Opportunities') && empty($focus->parent_opportunity_id)){
				SugarApplication::redirect('index.php?module='.$focus->module_dir.'&action=EditView&parent_id='.$return_id);
			}else if( ($_REQUEST['action'] == 'Save') && ($_REQUEST['save_action'] == 'save_and_create') && ($focus->module_dir == 'Opportunities') && !empty($focus->parent_opportunity_id)){
				SugarApplication::redirect('index.php?module='.$focus->module_dir.'&action=EditView&parent_id='.$focus->parent_opportunity_id);
			}else if( ($_REQUEST['action'] == 'Save') && ($_REQUEST['save_action'] == 'save') && ($focus->module_dir == 'Opportunities') && !empty($focus->parent_opportunity_id)){
				SugarApplication::redirect('index.php?module='.$focus->module_dir.'&action=DetailView&record='.$focus->parent_opportunity_id.'&ClubbedView=1');
			}else{
				handleRedirect($return_id,"Opportunities" );
			}
			
		}else{
			return $focus;
		}
	}

}
?>
