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
require_once 'include/MassUpdate.php';
require_once 'custom/include/SearchForm/PlOppSearchForm2.php';

class OpportunitiesViewDeleterelateddata extends ViewList{

    function  OpportunitiesViewDeleterelateddata(){
        parent::SugarView();
    }

    function display(){

        global $db, $sugar_config, $current_user;

        require_once('custom/modules/Opportunities/OpportunitySummary.php');
        $this->bean = new OpportunitySummary();

        $params = unserialize(base64_decode($_REQUEST['current_query_by_page']) );

        if(!empty($params)) {
            $_REQUEST = array_merge($_REQUEST, $params);
        }
        $opportunityIds = array();

        $searchMetaData = SearchForm::retrieveSearchDefs($this->module);
        $this->searchForm = $this->getSearchForm2($this->bean, $this->module, $this->action);
        $this->searchForm->setup($searchMetaData['searchdefs'], $searchMetaData['searchFields'], 'SearchFormGeneric.tpl');
        $this->searchForm->populateFromRequest();;



        $where_clauses = $this->searchForm->generateSearchWhere(true, $this->bean->module_dir);
        if (count($where_clauses) > 0 )$this->where = '('. implode(' ) AND ( ', $where_clauses) . ')';


        if( $_REQUEST['massupdate'] == true){

            if ($_REQUEST['select_entire_list'] == '1') {

               // $stAdditionalWhere = !empty($additionalWhere) ? ' AND ' . implode(' AND ', $additionalWhere) : ' ';

               //echo  $stProjectOpportunities = " SELECT id FROM opportunities WHERE parent_opportunity_id IS NULL AND deleted = 0 " . $stAdditionalWhere;
                $stProjectOpportunities = $this->bean->create_new_list_query('',$this->where,array('id'),$params,0);
                $rsProjectOpportunities = $db->query($stProjectOpportunities);
                while ($rowProjectOpportunities = $db->fetchByAssoc($rsProjectOpportunities)) {
                    $opportunityIds[] = $rowProjectOpportunities['id'];
                }
            } else {
                if (!empty($_REQUEST['uid'])) {
                    $opportunityIds = explode(',', $_REQUEST['uid']);
                }
            }
            $opportunityType = 'Project';

        }else{
            $opportunityIds[] = $_REQUEST['opportunity_id'];
            $opportunityType = $_REQUEST['type'];
        }


        if(count($opportunityIds) < 1){
            sugar_die('Opportunity ID not available.');
        }

        if(empty($opportunityType)){
            sugar_die('Opportunity type not defined.');
        }

        if (!empty($current_user)) {
        	$this->modified_user_id = $current_user->id;
        } else {
        	$this->modified_user_id = 1;
        }


        foreach ( $opportunityIds as $opportunityId){
            $this->deleteOpportunity($opportunityId, $opportunityType);
        }


        if( $_REQUEST['massupdate'] == true ){
            $redirect_url = 'index.php?module=Opportunities&action=index';
            SugarApplication::redirect($redirect_url);
            exit();
        }else{
            echo $opportunityType.' Opportunity Deleted';
        }

    }

    /**
     * Delete Opportunity
     * @param String $opportunityId
     * @param String $opportunity_type
     * @return string
     */
    function deleteOpportunity($opportunityId, $opportunityType){

        global $db, $sugar_config;

        if(empty($opportunityId) || empty($opportunityType) ){
            return true;
        }

        $date_modified = $GLOBALS['timedate']->nowDb();

        if($opportunityType == 'Project'){

        	$stClientOpportunities = " SELECT id FROM opportunities WHERE parent_opportunity_id = '".$opportunityId."' AND deleted = 0";
        	$rsClientOpportunities = $db->query($stClientOpportunities);
        	while( $rowClientOpportunities = $db->fetchByAssoc($rsClientOpportunities) ){
        		//delete all related activities
        		$this->deleteActivities($rowClientOpportunities['id']);
        		//delete all related Proposals
        		$this->deleteProposalData($rowClientOpportunities['id']);
        		//delete client opportunity
        		$stDelete = " UPDATE opportunities SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id'  WHERE id = '".$rowClientOpportunities['id']."' ";
        		$db->query($stDelete);
        	}

        }

        //delete all related activities
        $this->deleteActivities($opportunityId);
        //delete all related Proposals
        $this->deleteProposalData($opportunityId);

        if($opportunityType != 'Project'){
        	$opportunity = new Opportunity();
        	$opportunity->retrieve($opportunityId);
        	$opportunity->deleted = '1';
        	$opportunity->save();
        }else{
        	$stDelete = " UPDATE opportunities SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id'  WHERE id = '".$opportunityId."' ";
        	$db->query($stDelete);
        }

        //Delete Quickbook id for Client Opportunities
        if(!empty($opportunityId)){
        	$stClientOpportunityUpdate = "UPDATE opportunities SET quickbooks_id=NULL WHERE id = '".$opportunityId."' AND parent_opportunity_id IS NOT NULL";
        	$db->query($stClientOpportunityUpdate);
        }

        return true;
    }


    /**
     * Delete Activities of a record
     * @param string $recordId
     * @return boolean
     */
    function deleteActivities($recordId){
        global $db, $sugar_config;

        if(empty($recordId))
            return  true;

        $date_modified = $GLOBALS['timedate']->nowDb();


        //delete related Tasks
        $deleteRelatedTasks = " UPDATE tasks SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE parent_id = '".$recordId."'  ";
        $db->query($deleteRelatedTasks);

        //delete related Calls
        $deleteRelatedTasks = " UPDATE calls SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE parent_id = '".$recordId."' ";
        $db->query($deleteRelatedTasks);

        //delete related Meetings
        $deleteRelatedTasks = " UPDATE meetings SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE parent_id = '".$recordId."' ";
        $db->query($deleteRelatedTasks);

        //delete related Notes
        $deleteRelatedTasks = " UPDATE notes SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE parent_id = '".$recordId."'  ";
        $db->query($deleteRelatedTasks);

        //delete related Emails
        $deleteRelatedTasks = " UPDATE emails_beans SET deleted = '1', date_modified = '$date_modified' WHERE bean_id = '".$recordId."' ";
        $db->query($deleteRelatedTasks);

        return true;
    }

    /**
     * Delete Proposals of a Opportunity
     * @param string $opportunityId
     * @return boolean
     */
    function deleteProposalData($opportunityId){
        global $db, $sugar_config;

        if(empty($opportunityId))
        	return  true;

        $date_modified = $GLOBALS['timedate']->nowDb();

        //delete Proposal Related Data
        $stProposals = " SELECT  quote_id FROM quotes_opportunities WHERE opportunity_id = '".$opportunityId."'  AND deleted = 0";
        $rsProposals = $db->query($stProposals);
        while( $rowProposals = $db->fetchByAssoc($rsProposals) ){

            $this->deleteActivities($rowProposals['quote_id']);

            //delete relationships with accounts
            $deleteAccountRelationShip = " UPDATE quotes_accounts SET deleted = '1', date_modified = '$date_modified' WHERE quote_id = '".$rowProposals['quote_id']."' ";
            $db->query($deleteAccountRelationShip);

            //delete relationships with contacts
            $deleteContactRelationShip = " UPDATE quotes_contacts SET deleted = '1', date_modified = '$date_modified' WHERE quote_id = '".$rowProposals['quote_id']."' ";
            $db->query($deleteContactRelationShip);

            //delete related products
            $deleteProducts = " UPDATE products SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE quote_id = '".$rowProposals['quote_id']."' ";
            $db->query($deleteProducts);

            //delete related trackers
            $deleteTrackers = " UPDATE oss_proposaltracker SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE proposal_id = '".$rowProposals['quote_id']."'  ";
            $db->query($deleteTrackers);

            //delete proposal
            $deleteProposal = " UPDATE quotes SET deleted = '1', date_modified = '$date_modified', modified_user_id = '$this->modified_user_id' WHERE id = '".$rowProposals['quote_id']."'  ";
            $db->query($deleteProposal);
        }

        //delete relationship with proposals
        $deleteProposalRelationShip = " UPDATE quotes_opportunities SET deleted = '1', date_modified = '$date_modified' WHERE opportunity_id = '".$opportunityId."' ";
        $db->query($deleteProposalRelationShip);

        return true;
    }
}