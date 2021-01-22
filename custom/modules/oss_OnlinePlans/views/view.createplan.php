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


require_once 'include/MVC/View/SugarView.php';

class oss_OnlinePlansViewCreateplan extends SugarView{
	
	function oss_OnlinePlansViewCreateplan(){
		parent::SugarView();
	}
	
	function display(){
		
		global $mod_strings, $timedate, $app_strings;
		
		if(empty($_REQUEST['description'])){
			sugar_die('No URL Link');
		}else{
			$description = $_REQUEST['description'];
		}
		
		if(empty($_REQUEST['project_lead_id'])){
			sugar_die('No Project Lead Attached.');
		}else{
			$project_lead_id = $_REQUEST['project_lead_id'];
		}
		
		$online_plan=  new oss_OnlinePlans();
		$online_plan->description =$description;
		$online_plan->lead_id = $project_lead_id;
		$online_plan->save();
		
		
		// retrieve all the project urls associated with this project
		require_once 'custom/modules/Leads/bbProjectLeads.php';
		$obBbProjectLead = new bbProjectLeads();
		$obBbProjectLead->retrieve($project_lead_id);
		$stListSql= $obBbProjectLead->get_leads_online_plans();
		$obOnlinePlans = new oss_OnlinePlans ();
		$rsResult = $obOnlinePlans->db->query ( $stListSql );
		while ( $arData = $obOnlinePlans->db->fetchByAssoc ( $rsResult ) ) {
			$arOnlinePlan [] = $arData;
		}
		
		$html = '<table class="atag_table" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<th width="20%" align="center" class="atag_head1">'.$mod_strings['LBL_PLAN_TYPE'].'</th>
						<th width="20%" align="center" class="atag_head1">'.$mod_strings['LBL_PLAN_SOURCE'].'</th>
						<th width="20%" align="center" class="atag_head1">'.$mod_strings['LBL_REVIEW_DATE'].'</th>
						<th width="20%" align="center" class="atag_head1">'.$mod_strings['LBL_URL_LINK'].'</th>
					</tr>';
		
		$i = 0;
		foreach($arOnlinePlan as $data){
			$html .= '<tr>
				<td width="20%" align="center"><span >'.$data['plan_type'].'</span></td>
				<td width="20%" align="center"><span >'.$data['plan_source'].'</span></td>
				<td width="20%" align="center"><span >'.$timedate->to_display_date_time($data['last_reviewed_date']).'</span></td>
				<td width="20%" align="center">
					<span >
						<a href="index.php?module=oss_OnlinePlans&action=openUrl&record='.$data['id'].'" target="_blank">Open</a>
					</span>
				</td>
			</tr>';
			$i++;
		}
		
		if($i == '0' ){
			$html .= '<tr><td>'.$app_strings['LBL_NO_DATA'].'</td></tr>';
		}
		
		$html .= '<tr>
					<td colspan="4"></td>
				</tr>
			</table>';
		
		echo $html;
		
	}
}