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
* Added to get private team of a user.
* @author Mohit Kumar Gupta
* @date 21-01-2014
*/
include_once 'include/MVC/View/SugarView.php';
class OpportunitiesViewUserprivateteam extends SugarView{
	/**
	 * default constructor of the class
	 */
	function OpportunitiesViewUserprivateteam(){
		parent::SugarView();
	}
	
	/**
	 * default display method 
	 * @see SugarView::display()
	 */
	function display(){
		$userPrivateTeamId = '';
		$userPrivateTeamName = '';
		if(isset($_REQUEST['assigned_user_id'])){
			//get User Details
			$obUser = BeanFactory::getBean('Users',$_REQUEST['assigned_user_id']);
			$userPrivateTeamId = $obUser->getPrivateTeam();
			$userPrivateTeamName = Team::getTeamName($userPrivateTeamId);		    		    		
		} 
		sugar_die(json_encode(
			array(
		    	'team_id' => $userPrivateTeamId,
				'team_name' => $userPrivateTeamName,
			)
		));
	}		
}
