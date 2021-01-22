<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

/*********************************************************************************

 * Description: This file is used to override the default Meta-data EditView behavior
 * to provide customization specific to the Contacts module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/MVC/View/views/view.edit.php');
require_once ('custom/modules/Users/filters/userAccessFilters.php');

class AccountsViewEdit extends ViewEdit 
{   
 	public function __construct()
 	{
 		parent::ViewEdit();
 		$this->useForSubpanel = true;
 		//$this->useModuleQuickCreateTemplate = true;
 	}
 	
 	/**
 	 * @see SugarView::display()
	 * 
 	 * We are overridding the display method to manipulate the sectionPanels.
 	 * If portal is not enabled then don't show the Portal Information panel.
 	 */
 	public function display() 
 	{
        global $app_list_strings, $current_user;

        //check user filter if this is not be an admin user
        if ((!empty($this->bean->fetched_row['id']) || !empty($_REQUEST['record'])) && $current_user->is_admin != 1) {
        	$checkRecordId = !empty($this->bean->fetched_row['id'])?$this->bean->fetched_row['id']:$_REQUEST['record'];
        	$userAccessFilter = new userAccessFilters();
        	$userAccessFilter->isClientAccessable($checkRecordId);
        }
        
        //restrict access
		if ($this->bean->visibility=='0') {
        	sugar_die('You are not authorised to view this Client.');
        }
         
        //by default keep assigned user blank
        if(empty($this->bean->fetched_row['id'])){
         	$this->bean->assigned_user_id = '';
        	$this->bean->assigned_user_name = '';
        }
                
        $this->ev->process();
                
        ###### customization for county dom ####
        //get state DOM values
        $this->ss->assign('STATE_DOM',$app_list_strings['state_dom']);
        //if its in edit mode then retrive counties to correspoding state
        if(isset($_REQUEST['record']) && trim($_REQUEST['record']) != '')
        {
        	$obCounties = new oss_County();
        	$arAllCounties = $obCounties->get_full_list(''," county_abbr ='".$this->bean->billing_address_state."'");
        	$arCountyDOM[] = '';
        	if(!empty($arAllCounties)) {
        	foreach($arAllCounties as $obSelCounty){
        		$arCountyDOM[$obSelCounty->id] = strtolower( $obSelCounty->name); 
        		
        	}
        }
        	$this->ss->assign('COUNTY_DOM',$arCountyDOM);
        	$this->ss->assign('SAVED_COUNTY',$this->bean->county_id);
        }
        
         
               
		echo $this->ev->display($this->showTitle);
		echo '<script type="text/javascript" src="custom/include/javascript/serialize.0.2.min.js"></script>';
		echo "<script type='text/javascript'>								
				function check_form_custom(){ 					
 					//called for international client add remove validation at time of save
		            endisStateCounty();
		        
 					if(check_form('EditView')){ 						
 						if(document.forms['EditView'].record.value != ''){
	 						var pre_form_val = document.getElementById('pre_form_string').value;
	 						document.getElementById('pre_form_string').value = ''; 						
	 						var new_form_val = serialize(document.forms['EditView']); 								 					
			 					if(pre_form_val != new_form_val){		 						
			 						document.getElementById('form_updated').value = '1';		 						
			 					}
		 				}	
	
							if(document.forms['EditView'].record.value == ''){							
								document.getElementById('form_updated').value = '1';
 							}	 				
		 					document.EditView.action.value='Save';
		 					SUGAR.ajaxUI.submitForm(document.EditView);
 							return false; 	
 						}	
 					}
 					
 				YAHOO.util.Event.onDOMReady(function(){	 					
	 					
	 					if(document.forms['EditView'].record.value != ''){
	 						var form_string = serialize(document.forms['EditView']);
	 						document.getElementById('pre_form_string').value = form_string;
	 					}
	 					
	 					var btn_save = YAHOO.util.Selector.query('input[name^=button]');	 					
	 						for(var i=0; i<btn_save.length; i++){
								if(btn_save[i].id == 'SAVE'){
			 					btn_save[i].disabled=false;
			 				}
			 			}
	 			});
 					
 				function saveQuickEdit(){ 		
                    //called for international client add remove validation at time of save
		            endisStateCounty();
		        
		 			if(check_form('form_DCQuickCreate_Accounts')){
		 				var pre_form_val = document.getElementById('pre_form_string').value;
		 				document.getElementById('pre_form_string').value = '';						
						var new_form_val = serialize(document.forms['form_DCQuickCreate_Accounts']);		 				
		 				if(pre_form_val != new_form_val){		 					
		 					document.getElementById('is_form_updated').value = '1'; 				 	
		 				} 				
		 				document.forms['form_DCQuickCreate_Accounts'].action.value='Save';
		 				return DCMenu.save(document.forms['form_DCQuickCreate_Accounts'].id, 'Accounts_subpanel_save_button');		 								
		 			} 
		 			return false; 			
 				}	 				
 			 	YAHOO.util.Event.onAvailable('Accounts_dcmenu_save_button',function(){ 			 			
	 				if(document.getElementById('from_dcmenu').value == '1'){	 					
	 					document.getElementById('pre_form_string').value = '';
	 					var form_stringDC = serialize(document.forms['form_DCQuickCreate_Accounts']);	 					
	 					document.getElementById('pre_form_string').value = form_stringDC;
	 				}				
 				}); 
			</script>";
 	}	
}
