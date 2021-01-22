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




require_once('include/generic/SugarWidgets/SugarWidgetField.php');
require_once('custom/include/common_functions.php' );
require_once 'custom/include/OssTimeDate.php';
class SugarWidgetSubPanelDisplayBidDueDate extends SugarWidgetField
{
	
	/**
	 * @see SugarWidgetField::displayList()
	 */
	function displayList(&$layout_def)
	{
		global $timedate;
		$oss_timezone = new OssTimeDate();


		//Display converted date based on timezone into subpanel of opportunity and for all modules
		if($layout_def['module'] == 'Opportunities'){
			//echo "test oppo";
			$layout_def['fields']['DATE_CLOSED_TZ'] = $layout_def['fields']['DATE_CLOSED'];
			/*$stDate =  $timedate->to_db($layout_def['fields']['DATE_CLOSED_TZ']);
			$layout_def['fields']['BID_DUE_TIMEZONE'];
			$stDate =  convertDbDateToTimeZone($timedate->to_display_date_time($stDate),$layout_def['fields']['BID_DUE_TIMEZONE']); */
			
			$stDate = $oss_timezone->convertDBDateForDisplay($layout_def['fields']['DATE_CLOSED_TZ'], $layout_def['fields']['BID_DUE_TIMEZONE'],true);

		}
		else{
			
			if($layout_def['fields']['BIDS_DUE_TZ']){//echo "bid tz";
				$stDate = $timedate->to_display_date_time($layout_def['fields']['BIDS_DUE_TZ'],true,false);
			}
			if($layout_def['fields']['BIDS_DUE']){
				//echo "test";
				/* $layout_def['fields']['DATE_CLOSED_TZ'] = $layout_def['fields']['BIDS_DUE'];
				$stDate =  $timedate->to_db($layout_def['fields']['DATE_CLOSED_TZ']);
				$layout_def['fields']['BID_DUE_TIMEZONE'];
				$stDate =  convertDbDateToTimeZone($timedate->to_display_date_time($stDate),$layout_def['fields']['BID_DUE_TIMEZONE']); */
				$oss_timezone->convertDBDateForDisplay($layout_def['fields']['DATE_CLOSED_TZ'], $layout_def['fields']['BID_DUE_TIMEZONE'],true);

			}
		}
		
		return $stDate;
	}
	
	function displayHeaderCell($layout_def) {
	    $module_name = $this->layout_manager->getAttribute('module_name');
	
	    $this->local_current_module = $_REQUEST['module'];
	    $this->is_dynamic = true;
	    // don't show sort links if name isn't defined
	    if ((empty ($layout_def['name']) || (isset ($layout_def['sortable']) && !$layout_def['sortable']))
	            && !empty ($layout_def['label'])) {
	        return $layout_def['label'];
	    }
	    if (isset ($layout_def['sortable']) && !$layout_def['sortable']) {
	        return $this->displayHeaderCellPlain($layout_def);
	    }
	
	    $header_cell_text = $this->displayHeaderCellPlain($layout_def);
	
	    $subpanel_module = $layout_def['subpanel_module'];
	    $html_var = $subpanel_module . "_CELL";
	    if (empty ($this->base_URL)) {
	        $this->base_URL = ListView :: getBaseURL($html_var);
	        $split_url = explode('&to_pdf=true&action=SubPanelViewer&subpanel=', $this->base_URL);
	        $this->base_URL = $split_url[0];
	        $this->base_URL .= '&inline=true&to_pdf=true&action=SubPanelViewer&subpanel=';
	    }
	    $sort_by_name = ($layout_def['name'] == 'date_closed_tz')?'date_closed':$layout_def['name'];
	    
	    if (isset ($layout_def['sort_by'])) {
	        $sort_by_name = $layout_def['sort_by'];
	    }
	
	    $sort_by = ListView :: getSessionVariableName($html_var, "ORDER_BY").'='.$sort_by_name;
	
	    $start = (empty ($layout_def['start_link_wrapper'])) ? '' : $layout_def['start_link_wrapper'];
	    $end = (empty ($layout_def['end_link_wrapper'])) ? '' : $layout_def['end_link_wrapper'];
	
	    $header_cell = "<a class=\"listViewThLinkS1\" href=\"".$start.$this->base_URL.$subpanel_module.'&'.$sort_by.$end."\">";
	    $header_cell .= $header_cell_text;
	
	    $imgArrow = '';
	    
	    if (isset ($layout_def['sort'])) {
	        $imgArrow = $layout_def['sort'];
	    }
	    
	    /***************for up and down arrow *************/
	    $sorted_by = $_SESSION[ListView :: getSessionVariableName($html_var, "ORDER_BY")];
	   	$desc = $_SESSION['last_sub' . $subpanel_module . '_order'];
	   	
		if( !empty($desc) && ( $sorted_by == 'date_closed') ){
			
			if($desc == 'desc')
				$imgArrow = '_down';
			
			if($desc == 'asc')
				$imgArrow = '_up';
		}
		/***************for up and down arrow *************/
		
	    $arrow_start = ListView::getArrowUpDownStart($imgArrow);
	    $arrow_end = ListView::getArrowUpDownEnd($imgArrow);
	    $header_cell .= " ".$arrow_start.$arrow_end."</a>";
	
	    return $header_cell;
	
	}
}

?>
