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

 * Description: view handler for last step of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/MVC/View/SugarView.php');
          
class ViewComparelead extends SugarView 
{	
    function ViewComparelead(){
    	parent::SugarView();
    }
    
 	public function display()
    {
        global $mod_strings, $current_user, $sugar_config,$app_list_strings,$db;
        
        //get the primary lead to compare
        if(isset($_REQUEST['record']) && !empty($_REQUEST['record'])){
        	$primary_lead = $_REQUEST['record'];
        }else{
        	die('No Primary Lead to Compare');
        }
        
        //get the lead to compare with
        if(isset($_REQUEST['lead']) && !empty($_REQUEST['lead'])){
        	$com_lead = $_REQUEST['lead']; 
        }else{
        	die('No Lead to Compare');
        }
        
       	$lead = new Lead();
       	$lead->retrieve($primary_lead);
       	
       	/**
       	 * update status to viewed if lead status is new
       	 * @modified by Mohit Kumar Gupta
       	 * @date 20-02-2014
       	 */
       	if($lead->status == 'New' ){       	    
       	    $updateSqlLead = "UPDATE leads SET status='Viewed' WHERE id='".$lead->id."'";
       	    $db->query($updateSqlLead);
       	}
       	
       	$sec_lead = new Lead();
       	$sec_lead->retrieve($com_lead);
       	
       	/**
       	 * update status to viewed if lead status is new
       	 * @modified by Mohit Kumar Gupta
       	 * @date 20-02-2014
       	 */
       	if($sec_lead->status == 'New' ){
       	    $updateSqlSecLead = "UPDATE leads SET status='Viewed' WHERE id='".$sec_lead->id."'";
       	    $db->query($updateSqlSecLead);
       	}
       	
       	$this->ss->assign ('ORIGINAL_LEAD_DATA', $lead);
       	$this->ss->assign ('POTENTIAL_DUPLICATE_LEAD_DATA', $sec_lead);
       	$this->ss->display ( 'custom/modules/Leads/tpls/comparelead.tpl' );
    }
}
?>
