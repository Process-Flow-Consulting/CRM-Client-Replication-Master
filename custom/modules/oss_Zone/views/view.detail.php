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

 * Description: This file is used to override the default Meta-data EditView behavior
 * to provide customization specific to the Calls module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/MVC/View/views/view.detail.php');
/*
 * used for customized detail view
 * @author Mohit Kumar Gupta
*/
class oss_ZoneViewDetail extends ViewDetail 
{
    
    /**
 	 * @see SugarView::display()
 	 */
 	public function display() 
 	{
		global $mod_strings,$db;
		$zoneType = $this->bean->zone_type;
		$zoneValue = $this->bean->zone_value;
		$zoneValueArray = explode(",",str_replace("^","",$zoneValue));
 		if ($zoneType == 'city') { 			
 			$zoneValue = '<li style="margin-left: 15px;">';
 			$zoneValue .= implode('</li><li style="margin-left: 15px;">',$zoneValueArray);
 			$zoneValue .= '</li>';
			$zoneType = $mod_strings['LBL_CITY_NAME'];
		} else if ($zoneType == 'state') {
			foreach ($zoneValueArray as &$value) {
				$value = $GLOBALS['app_list_strings']['state_dom'][$value];
			}
			$zoneValue = '<li style="margin-left: 15px;">';
			$zoneValue .= implode('</li><li style="margin-left: 15px;">',$zoneValueArray);
			$zoneValue .= '</li>';
			$zoneType = $mod_strings['LBL_STATE_NAME'];
		} else if ($zoneType == 'county') {
			$dataString = "'".implode("','",$zoneValueArray)."'";
			$selectQuery = "SELECT name from oss_county WHERE deleted = 0 AND id IN (".$dataString.")";
			$dataResult = $db->query($selectQuery);
			$html = '';
			while ($dataSet = $db->fetchByAssoc($dataResult)) {
				$html .= '<li style="margin-left: 15px;">'.ucwords(strtolower($dataSet['name'])).'</li>';
			}
			$zoneValue = $html;
			$zoneType = $mod_strings['LBL_COUNTY_NAME'];
		} else if ($zoneType == 'zip') {
			$zoneValue = '<li style="margin-left: 15px;">';
			$zoneValue .= implode('</li><li style="margin-left: 15px;">',$zoneValueArray);
			$zoneValue .= '</li>';
			$zoneType = $mod_strings['LBL_ZIP_CODE_NAME'];
		}
		$this->ss->assign("ZONE_TYPE",$zoneType);
		$this->ss->assign("ZONE_VALUE",$zoneValue);	
		
		parent::display();
		
 	}

}

