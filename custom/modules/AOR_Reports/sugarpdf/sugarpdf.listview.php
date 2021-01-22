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

require_once('modules/AOR_Reports/sugarpdf/sugarpdf.reports.php');
require_once('custom/modules/AOR_Reports/views/view.users_all_activities.php');

class AOR_ReportsSugarpdfListview extends ReportsSugarpdfReports
{
	function getProperty($stProperty){
		return $this->$stProperty;
	}
	
    function display(){
        global $report_modules, $app_list_strings;
        global $mod_strings, $locale;
        $this->bean->run_query();
        
        //to remove random lines
        $this->setPrintHeader(true);
        
        $this->AddPage();
        
        $item = array();
        $header_row = $this->bean->get_header_row('display_columns', false, false, true);
        $count = 0;
    
        while($row = $this->bean->get_next_row('result', 'display_columns', false, true)) {
            for($i= 0 ; $i < sizeof($header_row); $i++) {
                $label = $header_row[$i];
                $value = '';
                if(!empty($row['cells'][$i])) {
                    $value = $row['cells'][$i];
                }
                $item[$count][$label] = $value;
            }
            $count++;
        }
        if($this->bean->report_def['reportIdCustom'] == '9752e959-f5c7-64ff-b0fa-53cf53cdcd87'){
        	//Call from Report List View  
        	if(isset($this->bean->DetailView) && $this->bean->DetailView == 1){
        		$this->activityData($this->bean->customReportData,$this->bean->summaryReportData);
        	}
        	//Call from Scheduler
        	else{
        		$userReportObj = new ReportsViewUsers_all_activities();
        		$userReportObj->init(BeanFactory::getBean('Reports'));
        		$activitySummaryData = $userReportObj->getReportData(1,1);        		
        		$this->activityData($activitySummaryData['customReportData'],$activitySummaryData['summaryReportData']);
        	}
        }
        else{
        	$this->writeCellTable($item, $this->options);
        }        
    }
    
    /*
     * Author : Shashank Verma
     * Date Created : 27-08-2014
     * Method Params : $reportData - List view of Report 
     * Method Params : $summartData - Html for Summary Report
     */
    function activityData($reportData,$summartData){
        global $current_user;
        $assignedUserPrefData = $current_user->getPreference('user_activity_report_preferences');
        $hideActivity = 0;
        $hideSummary = 0;
        if (isset($assignedUserPrefData['hide_activity']) && !empty($assignedUserPrefData['hide_activity'])) {
            $hideActivity = $assignedUserPrefData['hide_activity'];
        }

        if(isset($assignedUserPrefData['hide_summary']) && !empty($assignedUserPrefData['hide_summary'])){
            $hideSummary = $assignedUserPrefData['hide_summary'];
        }
        
        if ($hideSummary == 0) {
        	$this->writeHTML($summartData, true, false, false, false, '');
        	if ($hideActivity == 0) {
        	    $this->Ln1();
        	    $this->Ln1();
        	}
        }
        if ($hideActivity == 0) {
            $this->writeCellTable($reportData, $this->options);
        }    	
    } 
}