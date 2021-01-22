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
/**
* mass update value for client opportunity
* @author Mohit Kumar Gupta
* @date 02-04-2014
*/
class OpportunitiesViewClientoppmassupdate extends SugarView{
	
    /**
     * default constructor
     */
	function OpportunitiesViewClientoppmassupdate(){
		parent::SugarView();
	}
	/**
	 * display to update mass update value to database
	 * @see SugarView::display()
	 */
	function display(){		
		$oppIdArray = (!empty($_REQUEST['uid']))?explode(",", $_REQUEST['uid']):array();
		$classficationId = $_REQUEST['opportunity_classification'];
		$clientBidStatus = $_REQUEST['client_bid_status'];
		$salesStage = $_REQUEST['salse_stage'];
		$assignedUserId = $_REQUEST['assigned_user_id'];
		$subOpp = new Opportunity();
		foreach ($oppIdArray as $oppId) {
		    $subOpp->disable_row_level_security = true;
		    $subOpp->retrieve($oppId);
		    if (!empty($assignedUserId)) {
		    	$subOpp->assigned_user_id = $assignedUserId;
		    	/**
		    	 * Modified by : Ashutosh
		    	 * Date : 24 Apr 2014
		    	 * Purpose : The team should be changed to the assigned users private team
		    	 *           if there are other teams, remove previously assigned teams. 
		    	 */
		    	//get assigned User details and private team
		    	$obAssignedUser = BeanFactory::getBean('Users',$assignedUserId);
		    	$stAssignedUsersPrivateTeam =  $obAssignedUser->getPrivateTeam();
		    	$subOpp->team_id = $stAssignedUsersPrivateTeam;
		    	$subOpp->team_set_id = $stAssignedUsersPrivateTeam;   	
		    	
		    }
		    if (!empty($classficationId)) {
		        $subOpp->opportunity_classification = $classficationId;
		    }
		    if (!empty($clientBidStatus)) {
		        $subOpp->client_bid_status = $clientBidStatus;
		    }
		    if (!empty($salesStage)) {
		        $subOpp->sales_stage = $salesStage;
		    }
		    
		    
		    $subOpp->save();
		}
		if (!empty($_REQUEST['parentId'])) {
		    $params = array(
	            'module' => 'Opportunities',
	            'action' => 'DetailView',
		        'ClubbedView' => '1',
	            'record' => $_REQUEST['parentId']
		    );
		} else {
		    $params = array(
		            'module'=> 'Opportunities',
		            'action'=>'index',
		    );
		}
		SugarApplication::redirect('index.php?' . http_build_query($params)); 
	}
}