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
* by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/
require_once('include/MVC/View/SugarView.php');
require_once 'custom/include/common_functions.php';

class AOS_QuotesViewCopyproposal extends SugarView {

    function AOS_QuotesViewCopyproposal() {
        parent::SugarView();
    }

    function preDisplay() {
        parent::preDisplay();
    }

    function display() {
        
        if (!isset($_REQUEST['record']) && empty($_REQUEST['record']) ){
            sugar_die("Unauthorized access to this area.");
        }

        
        global  $db, $app_strings, $app_list_strings, $mod_strings, $current_user;

        $this->ss->assign("MOD", $mod_strings);
        $this->ss->assign("APP", $app_strings);
        $this->ss->assign("MODULE", $_REQUEST['module']);
        $this->ss->assign('record', $_REQUEST['record']);
        
        $proposal = new AOS_Quotes();
        $proposal->retrieve($_REQUEST['record']);
        
        $this->ss->assign('PROPOSAL_SUBJECT', $proposal->name);
        $this->ss->assign("CLIENT_NAME", $proposal->account_name);
        
        require_once 'custom/include/OssTimeDate.php';
        $oss_timedate = new OssTimeDate();
        
        //date time proposal scheduled delivery
        $date_time_delivery = $oss_timedate->convertDBDateForDisplay($proposal->date_time_delivery, $proposal->delivery_timezone, true);
        $this->ss->assign("PROPOSAL_DELIVERY_TIME", $date_time_delivery);
        
        $this->ss->assign("PROPOSAL_DELIVERY_TIMEZONE", $proposal->delivery_timezone);
        $this->ss->assign("PROPOSAL_DELIVERY_METHOD", $app_list_strings['proposal_delivery_method'][$proposal->proposal_delivery_method]);
        
        $clientOpportunity = $proposal->opportunity_id;
        
        $this->ss->assign('OPPORTUNITY_NAME', $proposal->opportunity_name);
        
        $sqlParentOpp = " SELECT parent_opportunity_id FROM opportunities WHERE id = '".$clientOpportunity."' ";
        $resultParentOpp = $db->query($sqlParentOpp);
        $rowParentOpp = $db->fetchByAssoc($resultParentOpp);
        
        $projectOpprtunity = $rowParentOpp['parent_opportunity_id'];
        
        if(isset($_REQUEST['handleSave']) && ($_REQUEST['handleSave'] == '1') ){
            $this->handleSave();
            SugarApplication::redirect('index.php?module=Opportunities&action=DetailView&record='.$projectOpprtunity.'&ClubbedView=1');
        }
        
        
        $sqlOtherClientOpp = "SELECT opportunities.id, opportunities.contact_id, 
        LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name, ''),' ',IFNULL(contacts.last_name, '')))) contact_name,
        cea.email_address contact_email, contacts.phone_fax as contact_fax,
        accounts.proview_url,accounts.id as client_id, accounts.name as client_name,
        aos_quotes.id qid, aos_quotes.proposal_delivery_method as pdm,
        aos_quotes.delivery_timezone dtz,aos_quotes.proposal_verified, aos_quotes.verify_email_sent
        FROM opportunities
        LEFT JOIN contacts ON opportunities.contact_id = contacts.id AND contacts.deleted = 0
        LEFT JOIN email_addr_bean_rel ceabr ON ceabr.bean_id = contacts.id AND ceabr.deleted = 0
        LEFT JOIN email_addresses cea ON cea.id = ceabr.email_address_id AND cea.deleted = 0
        LEFT JOIN accounts_opportunities ON accounts_opportunities.opportunity_id = opportunities.id and accounts_opportunities.deleted = 0
        LEFT JOIN accounts ON accounts.id = accounts_opportunities.account_id and accounts.deleted = 0
        LEFT JOIN aos_quotes_opportunities qo ON qo.opportunity_id = opportunities.id AND qo.deleted = 0
        LEFT JOIN aos_quotes ON aos_quotes.id = qo.quote_id AND aos_quotes.deleted = 0 ";
        
        //if not admin user show only client opportunities that are visible to the user
        if( !$current_user->isAdmin() ){
            $sqlOtherClientOpp .=" INNER JOIN
                   ( SELECT 
                    tst.team_set_id
                FROM
                    team_sets_teams tst
                INNER JOIN 
                    team_memberships team_memberships ON tst.team_id = team_memberships.team_id
                    AND team_memberships.user_id = '".$current_user->id."'
                    AND team_memberships.deleted = 0
                GROUP BY tst.team_set_id) opportunities_tf
                     ON opportunities_tf.team_set_id = opportunities.team_set_id
                    LEFT JOIN
                users jt0 ON opportunities.modified_user_id = jt0.id
                    AND jt0.deleted = 0
                    AND jt0.deleted = 0
                    LEFT JOIN
                users jt1 ON opportunities.created_by = jt1.id
                    AND jt1.deleted = 0
                    AND jt1.deleted = 0
                    LEFT JOIN
                users jt2 ON opportunities.assigned_user_id = jt2.id
                    AND jt2.deleted = 0
                    AND jt2.deleted = 0 ";
        }
        
        $sqlOtherClientOpp .= " WHERE
        (opportunities.parent_opportunity_id = '".$projectOpprtunity."') 
        AND opportunities.deleted = 0 
        AND opportunities.id != '".$clientOpportunity."' ";

        //echo $sqlOtherClientOpp;
        
        $resultOtherClientOpp = $db->query($sqlOtherClientOpp);
        $i=0;
        while (($rowOtherClientOpp = $db->fetchByAssoc($resultOtherClientOpp)) != null) {
            $otherClientOpp[$i] = $rowOtherClientOpp;
            $i++;
        }
        
        //echo '<pre>'; print_r($otherClientOpp); echo '</pre>';
        
        $this->ss->assign('clientOpps', $otherClientOpp);
        

        if(!empty($_POST['return_module'])) $this->ss->assign('RETURN_MODULE', $_POST['return_module']);
        else $this->ss->assign('RETURN_MODULE', 'AOS_Quotes');
        
        if(!empty($_POST['return_action'])) $this->ss->assign('RETURN_ACTION', $_POST['return_action']);
        else $this->ss->assign('RETURN_ACTION', 'DetailView');
        
        if(!empty($_POST['return_id']))  $this->ss->assign('RETURN_ID', $_POST['return_id']);
        else $this->ss->assign('RETURN_ID', $_REQUEST['record']);

        $this->ss->display('custom/modules/AOS_Quotes/tpls/copyProposal.tpl');
    }
    
    function handleSave(){
        
        global  $db, $app_strings, $app_list_strings, $mod_strings, $current_user, $sugar_config;
        //echo '<pre>'; print_r($_REQUEST); echo '</pre>';
       
        if(count($_REQUEST['mass']) > 0){
            
            $quote = new AOS_Quotes();
            $quote->retrieve($_REQUEST['record']);
            
            $quote->load_relationships('aos_products');
            $productIds = $quote->products->get();
            
            $quote->load_relationships('documents');
            $documents = $quote->documents->get();
            
            //echo '<pre>'; print_r($products); echo '</pre>';
            //die;

            foreach ($_REQUEST['mass'] as $ids){

                $idArray = explode('_', $ids);
                
                $opportunity_id = $idArray[0];
                $proposal_id = $idArray[1];
                
                if(!empty($opportunity_id)){
                    
                    $sqlContactDetail = " SELECT accounts.id as account_id, contacts.id as contact_id, 
                            cea.email_address as contact_email, contacts.phone_fax as contact_fax,
                            contacts.phone_work as contact_phone
                            FROM opportunities 
                            LEFT JOIN contacts ON opportunities.contact_id = contacts.id 
                                AND contacts.deleted = 0
                            LEFT JOIN email_addr_bean_rel ceabr ON ceabr.bean_id = contacts.id 
                                AND ceabr.deleted = 0
                            LEFT JOIN email_addresses cea ON cea.id = ceabr.email_address_id 
                                AND cea.deleted = 0
                            LEFT JOIN accounts_opportunities ON accounts_opportunities.opportunity_id = opportunities.id 
                                AND accounts_opportunities.deleted = 0
                            LEFT JOIN accounts ON accounts.id = accounts_opportunities.account_id 
                                AND accounts.deleted = 0
                            WHERE opportunities.deleted = 0  
                                AND opportunities.id = '".$opportunity_id."'";
                    $resultContactDetail = $db->query($sqlContactDetail);
                    $rowContactDetail = $db->fetchByAssoc($resultContactDetail);
                    
                    $proposal = clone $quote;
                    unset($proposal->id);
                    
                    //overwrite proposal
                    if(!empty($proposal_id)){
                        $sqlDeleteProposal = " UPDATE aos_quotes SET deleted = '1' WHERE  id = '".$proposal_id."' ";
                        $db->query($sqlDeleteProposal); 
                        $sqlDeleteProposalRelationship = " UPDATE aos_quotes_opportunities SET deleted = '1' WHERE  quote_id = '".$proposal_id."' ";
                        $db->query($sqlDeleteProposalRelationship);
                    }
                    
                    $proposal->opportunity_id = $opportunity_id;
                    $proposal->billing_account_id = $rowContactDetail['account_id'];
                    $proposal->billing_contact_id = $rowContactDetail['contact_id'];
                    $proposal->contact_email = $rowContactDetail['contact_email'];
                    $proposal->contact_fax = $rowContactDetail['contact_fax'];
                    $proposal->contact_phone = $rowContactDetail['contact_phone'];
                    $proposal->proposal_delivery_method = $_POST['proposal_delivery_method_'.$opportunity_id.'_'.$proposal_id];
                    
                    $proposal->proposal_verified = '2';
                    
                    if($proposal->proposal_delivery_method == 'M'){
                        $proposal->proposal_verified = '1';
                    }
                    
                    $proposal->verify_email_sent = '0';
                    $proposal->easy_email_verify_mrn = '';
                    $proposal->easy_fax_verify_mrn = '';
                    $proposal->proposal_sent_count = '';
                    $proposal->proposal_version = '';
                    $proposal->verified_date = '';
                    $proposal->date_time_sent = '';
                    $proposal->date_time_received = '';
                    $proposal->date_time_opened = '';
                    
                    //modified by Mohit Kumar Gupta 02-04-2015
                    //Bug BSI-654 : Since Quickbooks id and Invoice id are unique then unset the id which is null
                    unset($proposal->quickbooks_id);
                    unset($proposal->quickbooks_invoice_id);
                    
                    $proposal->save();
                    

                    //Get Previous Value of Opportunity Sales Stage
                    $opp_ss_sql = "SELECT sales_stage FROM opportunities WHERE id='".$opportunity_id."' AND deleted=0";
                    $opp_ss_query = $db->query($opp_ss_sql);
                    $opp_ss_result = $db->fetchByAssoc($opp_ss_query);
                    $old_sales_stage = $opp_ss_result['sales_stage'];
                    	
                    //Change Opportunity Sales Stage
                    $sqlUpdateSalesStage = "UPDATE opportunities SET sales_stage = 'Proposal - Unverified' WHERE id = '".$opportunity_id."'";
                    $db->query( $sqlUpdateSalesStage );
                    	
                    //Insert Change Log on opportunity audit table.
                    insertChangeLog($db, 'opportunities', $opportunity_id, $old_sales_stage, 'Proposal - Unverified', 'sales_stage', 'enum', $current_user->id);
                    
                    
                    //add products relationship
                    foreach($productIds as $productId){
                        $product = new Product();
                        $product->retrieve($productId);
                        
                        $newProduct = clone $product;
                        unset($newProduct->id);
                        $newProduct->account_id = $rowContactDetail['account_id'];
                        $newProduct->contact_id = $rowContactDetail['contact_id'];
                        $newProduct->quote_id = $proposal->id;
                        $newProduct->save();
                    }
                    
                    //add documents relationship
                    foreach($documents as $document){
                        $proposal->load_relationship('documents');
                        $proposal->documents->add($document);
                    }
                    
                }
            }
        }
    }
    
}
?>
