<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
	die ( 'Not A Valid Entry Point' );

/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */
require_once 'include/MVC/View/views/view.detail.php';
/**
 * class for Zone module
 * @author Mohit kumar Gupta
 * @date 10-oct-2013
 */
class oss_ZoneViewSave_zone extends ViewDetail {
	
	/**
	 * @see SugarView::display()
	 */
	public function display()
	{		
		global $db;
		if (empty($_REQUEST['name']) || empty($_REQUEST['geo_filter_for'])) {
			sugar_die("Zone Name can not be empty.");
		}
		$zoneName = $_REQUEST['name'];
		$FilterType = $_REQUEST['geo_filter_for'];
		$filterTypeData = array();
		$state = "";
		if ($FilterType == 'city') {
			$filterTypeData = $_REQUEST['city_name'];
		} else if ($FilterType == 'state') {
			$filterTypeData = $_REQUEST['state_apply'];
		} else if ($FilterType == 'county') {
			$state = $_REQUEST['state_county'];
			$filterTypeData = $_REQUEST['county_filters'];
		} else if ($FilterType == 'zip') {
			$filterTypeData = $_REQUEST['zip_filters'];
		}
		if (count($filterTypeData) > 0) {
			echo "done";
		}
		/* $params = array(
				'module'=> 'oss_Zone',
				'action'=>'DetailView',
				'id' => 'de590340-d4dd-529d-019b-52568b565d8c'
		);
		SugarApplication::redirect('index.php?' . http_build_query($params)); */
	}
	
}