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


require_once('include/MVC/View/views/view.detail.php');
require_once 'custom/include/common_functions.php';
require_once ('custom/modules/Users/filters/userAccessFilters.php');

class AccountsViewDetail extends ViewDetail {


 	function AccountsViewDetail(){
 		parent::ViewDetail();
 	}

 	/**
 	 * display
 	 * Override the display method to support customization for the buttons that display
 	 * a popup and allow you to copy the account's address into the selected contacts.
 	 * The custom_code_billing and custom_code_shipping Smarty variables are found in
 	 * include/SugarFields/Fields/Address/DetailView.tpl (default).  If it's a English U.S.
 	 * locale then it'll use file include/SugarFields/Fields/Address/en_us.DetailView.tpl.
 	 */
 	function display(){
		global $current_user;
		//add/Remove push to QuickBooks 
		addPushToQuickBooksButton($this->bean,$this->dv);
		
		if(empty($this->bean->id)){
			global $app_strings;
			sugar_die($app_strings['ERROR_NO_RECORD']);
		}
		
		//check user filter if this is not be an admin user
		if (!empty($this->bean->id ) && $current_user->is_admin != 1) {
			$userAccessFilter = new userAccessFilters();
			$userAccessFilter->isClientAccessable($this->bean->id);
		}
		
        //restrict access
		if ($this->bean->visibility=='0') {
        	sugar_die('You are not authorised to view this Client.');
        }
		
		$this->dv->process();
                
		global $mod_strings;
		if(ACLController::checkAccess('Contacts', 'edit', true)) {
			$push_billing = '<span class="id-ff"><button class="button btn_copy" title="' . $mod_strings['LBL_PUSH_CONTACTS_BUTTON_LABEL'] . 
								 '" type="button" onclick=\'open_contact_popup("Contacts", 600, 600, "&account_name=' .
								 urlencode($this->bean->name) . '&html=change_address' .
								 '&primary_address_street=' . str_replace(array("\rn", "\r", "\n"), array('','','<br>'), urlencode($this->bean->billing_address_street)) . 
								 '&primary_address_city=' . $this->bean->billing_address_city . 
								 '&primary_address_state=' . $this->bean->billing_address_state . 
								 '&primary_address_postalcode=' . $this->bean->billing_address_postalcode . 
								 '&primary_address_country=' . $this->bean->billing_address_country .
								 '", true, false);\' value="' . $mod_strings['LBL_PUSH_CONTACTS_BUTTON_TITLE']. '">'.
								 SugarThemeRegistry::current()->getImage("id-ff-copy","").
								 '</button></span>';
								 
			$push_shipping = '<span class="id-ff"><button class="button btn_copy" title="' . $mod_strings['LBL_PUSH_CONTACTS_BUTTON_LABEL'] . 
								 '" type="button" onclick=\'open_contact_popup("Contacts", 600, 600, "&account_name=' .
								 urlencode($this->bean->name) . '&html=change_address' .
								 '&primary_address_street=' . str_replace(array("\rn", "\r", "\n"), array('','','<br>'), urlencode($this->bean->shipping_address_street)) .
								 '&primary_address_city=' . $this->bean->shipping_address_city .
								 '&primary_address_state=' . $this->bean->shipping_address_state .
								 '&primary_address_postalcode=' . $this->bean->shipping_address_postalcode .
								 '&primary_address_country=' . $this->bean->shipping_address_country .
								 '", true, false);\' value="' . $mod_strings['LBL_PUSH_CONTACTS_BUTTON_TITLE'] . '">'.
								  SugarThemeRegistry::current()->getImage("id-ff-copy","").
								 '</button></span>';
		} else {
			$push_billing = '';
			$push_shipping = '';
		}
		
		$this->ss->assign('SAVED_COUNTY','');
		
		if(isset($this->bean->county_id) && !empty($this->bean->county_id)){
			
			$obCounties = new oss_County();
			$obCounties->retrieve($this->bean->county_id);
			$this->ss->assign('SAVED_COUNTY',strtoupper($obCounties->name));
			
		}
		if ($this->bean->show_update_icon =='1'  ){
		    $this->ss->assign("show_hide_update_panel", '');
		} else {
		
		//Hirak - Update Client Detail View
		$update_available = false;
		
		if (!empty($this->bean->mi_account_id) && ($this->bean->lead_source == 'bb') && $this->bean->is_bb_update == 1) {
                $update_available = true;
            } else {
                $account_no = getCurrentInstanceAccountNo();
                $url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetclient_status.p?sugarcrm_account=" . $account_no . "&client_id=" . $this->bean->mi_account_id;
                $output = getRemoteData($url);
                $remote_data = json_decode($output);
                
                if ($remote_data->response->status == 'success' && $remote_data->response->update == 'true') {
                    $update_available = true;
                }
            }
            
            if ($update_available) {
                echo <<<EQQ
        <script type='text/javascript' >
		$(document).ready(function() {
        var updateParams = {url:'index.php?module=Accounts&action=check_bb_update&to_pdf=1&record='+$('#formDetailView input[name=record]').val()
 	                    ,success :function(res,st){
                            response = JSON.parse(res)
		
                            if(response.status == true){
                               // document.getElementById('bb_update').scrollIntoView(true);
                                $('.actionsContainer').focus();
                                options = { percent: 100 };
                               $('#bb_update').show('pulsate', options, 500)
 	                        }
 	                     }
 	                    }
        $.ajax(updateParams);
		            });
        </script>
		
EQQ;
            }
            $this->ss->assign("show_hide_update_panel", 'display:none');
        }
		
		
		$this->ss->assign("update_available", $update_available);
		$this->ss->assign("custom_code_billing", $push_billing);
		$this->ss->assign("custom_code_shipping", $push_shipping);
        
        if(empty($this->bean->id)){
			global $app_strings;
			sugar_die($app_strings['ERROR_NO_RECORD']);
		}				
		
		
	
	
	echo $this->dv->display();
	/**
	 * @modified by Mohit Kumar Gupta
	 * @date 07-nov-2013
	 * for update default contact flag to 0 in contacts module
	 * chnage call_back_function to updateDeafaultContact
	 */
	echo <<<EQQ
        <script type='text/javascript' >
			function updateDeafaultContact(popup_reply_data){
				var name_to_value_array=popup_reply_data.name_to_value_array;
			    var query_array=new Array();			    
				var selection_list=popup_reply_data.selection_list;
				if(selection_list!='undefined'){
					for(var the_key in selection_list)
					{
						query_array.push(selection_list[the_key])
	 				}
	 			}
				$.ajax({
				        type: 'POST',
				        url: 'index.php?module=Contacts&action=update_default_contact&to_pdf=true',
				        data: {recordIds: query_array},
				        dataType: 'json',
				       	cache: false,
				       	async:false,
				        success: function (json) {	        	
				            set_return_and_save_background(popup_reply_data);	             
				        },
				});
 			}
        </script>
		
EQQ;
 	 	} 	
}

?>