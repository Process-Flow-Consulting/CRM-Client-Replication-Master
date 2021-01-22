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
//require_once ('custom/modules/Users/filters/userAccessFilters.php');

class ContactsViewDetail extends ViewDetail
{
 	/**
 	 * @see SugarView::display()
	 *
 	 * We are overridding the display method to manipulate the portal information.
 	 * If portal is not enabled then don't show the portal fields.
 	 */
 	public function display()
 	{
 		global $db, $current_user;
 		
 		//check user filter if this is not be an admin user
 		/* if (!empty($this->bean->id ) && $current_user->is_admin != 1) {
 			$userAccessFilter = new userAccessFilters();
 			$userAccessFilter->isClientContactAccessable($this->bean->id);
 		} */
 		
    	//restrict access
        if ($this->bean->visibility=='0') {
	        //Get opportunities related to contacts
	        //@modified by Mohit Kumar Gupta
	        //@date 17-01-2014
	        $this->bean->load_relationship('opportunities');
	        $arrOpportunityId = $this->bean->opportunities->get();
	        //IF opportunities exists related to contact then restricted access condition should not apply
	        // and also set visibilty of that client to true
	        if (count($arrOpportunityId) > 0) {
	        	$updateSql = "UPDATE contacts SET visibility='1' WHERE id='".$this->bean->id."' and deleted='0'";
	        	$db->query($updateSql);
	        } else {
	        	sugar_die('You are not authorised to view this Client Contact.');
	        }            	               
		}
            
        $admin = new Administration();
        $admin->retrieveSettings();
        if(isset($admin->settings['portal_on']) && $admin->settings['portal_on']) {
        	$this->ss->assign("PORTAL_ENABLED", true);
        }
            
        //$account_proview_link = $this->setAccountProviewLink($this->bean);
        $account_name = '<a href="index.php?module=Accounts&action=DetailView&record='.$this->bean->account_id.'">'.$this->bean->account_name.'</a>';
            
        //$this->ss->assign('ACCOUNT_NAME',$account_proview_link.'&nbsp;'.$account_name);
        $this->ss->assign('ACCOUNT_NAME',$account_name);
            
        $this->ss->assign('primary_state',$GLOBALS['app_list_strings']['state_dom'][$this->bean->primary_address_state]);
        $this->ss->assign('alt_state',$GLOBALS['app_list_strings']['state_dom'][$this->bean->alt_address_state]);
            
 		parent::display();
 	}
 	
 	public function setAccountProviewLink(&$focus){

 		if($focus->account_proview_url != '')
 		{
 			$focus->account_proview_url = $focus->account_proview_url;
 			if (preg_match('/^[^:\/]*:\/\/.*/', $focus->account_proview_url)) {
 				$focus->account_proview_url= $focus->account_proview_url;
 			} else {
 				$focus->account_proview_url = 'http://' . $focus->account_proview_url;
 			}
 	
 			$focus->account_proview_url = '<a href="javascript:void(0)" onclick="window.open(\''.$focus->account_proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
 		}
 		else{
 			$focus->account_proview_url = '';
 			//$focus->account_proview_url = '<a href="javascript:void(0)" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
 		}
 		 
 		return $focus->account_proview_url;
 	}
}
