<?php
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * *******************************************************************************
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
* 
******/
$job_strings[] = 'ossUpdatePreviousBidCount';
/**
 * Function to update instance Previous bid to count
 * Added By : Ashutosh
 * Date : 5 Aug 2013
 */
function ossUpdatePreviousBidCount(){
    
    global  $db;
    //check if flag for previous bid to count has been set 
    $obInstanceConfig  = new Administration();
    $obAdmin = $obInstanceConfig->retrieveSettings('instance', true);
    
    //update previous bid to count
    if (isset($obAdmin->settings['instance_update_prev_bid_to']) 
        && $obAdmin->settings['instance_update_prev_bid_to']== 1 ) {
    	
        
        //update all the previous bid to counts 
        $stPreviousBidToUpdateSql = 'UPDATE project_lead_lookup 
                                     INNER JOIN ( 
                                            SELECT COALESCE(leads.parent_lead_id,leads.id) prebidleadid  
                                            ,count(DISTINCT prebid.account_id) prebidcount 
                                            FROM leads 
                                            INNER JOIN oss_leadclientdetail bid on bid.lead_id = leads.id AND bid.deleted=0 
                                            INNER JOIN accounts_opportunities prebid on bid.account_id = prebid.account_id AND prebid.deleted=0
                                            GROUP BY prebidleadid ) tmpPreBid on project_lead_lookup.project_lead_id = tmpPreBid.prebidleadid 
                                    SET project_lead_lookup.previous_bid_to =  tmpPreBid.prebidcount;';
        //update the Pre-Bid Count
        $db->query($stPreviousBidToUpdateSql);
        
        //Count has been updated reset the flag
        $obInstanceConfig->saveSetting('instance', 'update_prev_bid_to', '0');
    }    
    
    return true;
}
