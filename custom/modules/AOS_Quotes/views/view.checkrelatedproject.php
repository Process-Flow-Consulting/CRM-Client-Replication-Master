<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)	die ( 'Not A Valid Entry Point' );
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
*/

require_once 'include/MVC/View/SugarView.php';
class AOS_QuotesViewCheckrelatedproject extends SugarView{
    
    function AOS_QuotesViewCheckrelatedproject(){
        parent::SugarView();
    }
    
    function display(){
        global $db, $sugar_config;
        
        $parent_opportunity = '';
        
        if(isset($_REQUEST['entire_list']) && ($_REQUEST['entire_list'] == '1') ){
            $getOpportunitySql = " SELECT aos_quotes.id, opportunities.id, opportunities.parent_opportunity_id parent_opportunity_id  FROM aos_quotes  LEFT JOIN  aos_quotes_opportunities qo ON qo.quote_id = aos_quotes.id AND qo.deleted = 0 LEFT JOIN opportunities ON opportunities.id = qo.opportunity_id AND opportunities.deleted = 0 WHERE aos_quotes.deleted = 0 ";
            $getOpportunityResult = $db->query($getOpportunitySql);
            while( $getOpportunityRow = $db->fetchByAssoc($getOpportunityResult) ){
                
                if(empty($parent_opportunity)) {
                    //store parent opportunity as base
                    $parent_opportunity = $getOpportunityRow['parent_opportunity_id'];
                    
                }else if( $parent_opportunity != $getOpportunityRow['parent_opportunity_id'] ){
                    //if different parent opportunity found end process here
                    echo '0'; die;
                }
            }
            
        }else if ($_REQUEST['uid'] != '') {
            $pids = explode(',', $_REQUEST['uid']);
            
            foreach($pids as $pid){
                
                $getOpportunitySql = " SELECT aos_quotes.id, opportunities.id, opportunities.parent_opportunity_id parent_opportunity_id  FROM aos_quotes  LEFT JOIN  aos_quotes_opportunities qo ON qo.quote_id = aos_quotes.id AND qo.deleted = 0 LEFT JOIN opportunities ON opportunities.id = qo.opportunity_id AND opportunities.deleted = 0 WHERE aos_quotes.deleted = 0 AND aos_quotes.id = '".$pid."' ";
                $getOpportunityResult = $db->query($getOpportunitySql);
                while( $getOpportunityRow = $db->fetchByAssoc($getOpportunityResult) ){
                
                	if(empty($parent_opportunity)) {
                		//store parent opportunity as base
                		$parent_opportunity = $getOpportunityRow['parent_opportunity_id'];
                		
                	}else if( $parent_opportunity != $getOpportunityRow['parent_opportunity_id'] ){
                		//if different parent opportunity found end process here
                		echo '0'; die;
                	}
                }
            }
        }
        echo '1'; die;
    }
}