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

/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/


require_once 'include/SugarEmailAddress/SugarEmailAddress.php';

class CustomSugarEmailAddress extends SugarEmailAddress{
	
	function __construct(){
		parent::SugarEmailAddress();
	}

	function getEmailAddressWidgetDetailView($focus){		
		
		if ( !($this->smarty instanceOf Sugar_Smarty ) )
            $this->smarty = new Sugar_Smarty();

        global $app_strings;
        global $current_user;
        $assign = array();
        if(empty($focus->id))return '';
        $prefillData = $this->getAddressesByGUID($focus->id, $focus->module_dir);

        foreach($prefillData as $addressItem) {
            $key = ($addressItem['primary_address'] == 1) ? 'primary' : "";
            $key = ($addressItem['reply_to_address'] == 1) ? 'reply_to' : $key;
            $key = ($addressItem['opt_out'] == 1) ? 'opt_out' : $key;
            $key = ($addressItem['invalid_email'] == 1) ? 'invalid' : $key;
            $key = ($addressItem['opt_out'] == 1) && ($addressItem['invalid_email'] == 1) ? 'opt_out_invalid' : $key;

            //$assign[] = array('key' => $key, 'address' => $current_user->getEmailLink2($addressItem['email_address'], $focus).$addressItem['email_address']."</a>");
            $assign[] = array('key' => $key, 'address' => $current_user->getEmailLink2($addressItem['email_address'], $focus)."Email</a>");
        }


        $this->smarty->assign('app_strings', $app_strings);
        $this->smarty->assign('emailAddresses', $assign);
        $templateFile = empty($tpl) ? "include/SugarEmailAddress/templates/forDetailView.tpl" : $tpl;
        $return = $this->smarty->fetch($templateFile);
        return $return;
	}
	
	function getEmailAddressWidgetEditView($id, $module, $asMetadata=false, $tpl='',$tabindex='0'){
	    //modified by mohit kumar gupta 22-06-2015
	    //for unique email address of each user
	    $tpl = 'custom/include/SugarEmailAddress/templates/forEditView.tpl';
				
	    if ($module == 'Users') {
	    	$tpl = 'custom/include/SugarEmailAddress/templates/forUserEditView.tpl';
	    }
		$newEmail = parent::getEmailAddressWidgetEditView($id, $module, $asMetadata, $tpl,$tabindex);
		return $newEmail;
	}
	
	
	/**
	 * customization by Ashutosh
	 * if logged in user is not an admin user
	 * then do not allow him to mark opt out as 0 this email
	 */
	function AddUpdateEmailAddress($addr,$invalid=0,$opt_out=0)
	{
		global $current_user;
		$address = $this->db->quote($this->_cleanAddress($addr));
		$addressCaps = strtoupper($address);
	
		$q = "SELECT * FROM email_addresses WHERE email_address_caps = '{$addressCaps}' and deleted=0";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r);
		
		/**
		 * customization by Ashutosh 
		 * if logged in user is not an admin user 
		 * then do not allow him to mark opt out as 0 this email 
		 */
		//if(!$current_user->is_admin && $opt_out  == 0 && isset($a['opt_out']))
		    
		/**
		 * modified by Mohit KUmar Gupta 23-02-2015
		 * Requested by Lenny on 18-02-2015 and 20-02-2015 call
		 * every user should be able to check or uncheck opt-out flag
		 * but if it is a new record and this email is already opted out in some another records 
		 * then it should not update the existing value of opt out for this email
		 * opt out flag should not get updated from user profile page
		 */
		if(isset($a['opt_out']) && $a['opt_out'] == '1' && (empty($_REQUEST['record']) || $_REQUEST['module']=='Users'))
		{
			//reset from database
			$opt_out =$a['opt_out'];
		}
		
		if(!empty($a) && !empty($a['id'])) {
			//verify the opt out and invalid flags.
			//bug# 39378- did not allow change of case of an email address
			if ($a['invalid_email'] != $invalid or $a['opt_out'] != $opt_out or strcasecmp(trim($a['email_address']), trim($address))==0) {
			    $upd_q="update email_addresses set email_address='{$address}', invalid_email={$invalid}, opt_out={$opt_out},date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."' where id='{$a['id']}'";
				$upd_r= $this->db->query($upd_q);
			}
			return $a['id'];
		} else {
			$guid = '';
			if(!empty($address)){
				$guid = create_guid();
				$now = TimeDate::getInstance()->nowDb();
				$qa = "INSERT INTO email_addresses (id, email_address, email_address_caps, date_created, date_modified, deleted, invalid_email, opt_out)
				VALUES('{$guid}', '{$address}', '{$addressCaps}', '$now', '$now', 0 , $invalid, $opt_out)";
				$this->db->query($qa);
			}
			return $guid;
		}
	}
	
}

function getEmailAddressWidgetCustom($focus, $field, $value, $view, $tabindex='0') {
	$sea = new CustomSugarEmailAddress();
	$sea->setView($view);

	/* $aclAccessLevel = ACLField::hasAccess($field, $focus->module_dir, $GLOBALS['current_user']->id, $focus->isOwner($GLOBALS['current_user']->id));
	if($aclAccessLevel > 1) {
		if($view == 'EditView' || $view == 'QuickCreate' || $view == 'ConvertLead') {
			$module = $focus->module_dir;
			if ($view == 'ConvertLead' && $module == "Contacts")  $module = "Leads";

			return $sea->getEmailAddressWidgetEditView($focus->id, $module, false,'',$tabindex);
		}
		elseif($view == 'wirelessedit') {
			return $sea->getEmailAddressWidgetWirelessEdit($focus->id, $focus->module_dir, false);
		}

	} */
	return $sea->getEmailAddressWidgetDetailView($focus);
}