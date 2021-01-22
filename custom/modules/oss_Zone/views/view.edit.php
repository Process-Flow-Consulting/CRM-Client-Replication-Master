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
require_once 'include/MVC/View/views/view.edit.php';
/**
 * class for Zone module
 * @author Mohit kumar Gupta
 * @date 10-oct-2013
 */
class oss_ZoneViewEdit extends ViewEdit {
	/**
	 * default constructor for Zone module
	 * @author Mohit kumar Gupta
	 * @date 11-oct-2013
	 */
    function oss_ZoneViewEdit() {
        parent::ViewEdit();
    }
    /**
     * custom display function for Zone module
     * @author Mohit kumar Gupta
     * @date 11-oct-2013
     */
    function display() {
    	global $mod_strings;
    	echo $this->getModuleTitle();    	
    	$arFilterSaved = array(
    		'city'=>array(),
    		'state'=>array(),
    		'county'=>array(),
    		'zip'=>array(),
    		'geo_filter_for' => array()
    	);
    	$zoneName = "";
    	$returnAction = "index";
    	$recordId = "";
    	$cancelRecordId = "";
    	if (!empty($this->bean->fetched_row['id'])) {
    		$recordId = $cancelRecordId = $this->bean->fetched_row['id'];
    		if (isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == "true") {
    			$recordId = "";
    		}
    		$returnAction = "DetailView";
    		$arFilterSaved['geo_filter_for'][0] = $this->bean->fetched_row['zone_type'];
    		$zoneName = $this->bean->fetched_row['name'];
    		if ($this->bean->fetched_row['zone_type'] == 'state') {
    			$arFilterSaved['state'] = explode(",",str_replace("^","",$this->bean->fetched_row['zone_value']));
    		} else if ($this->bean->fetched_row['zone_type'] == 'county') {
    			$arFilterSaved['county'] = explode(",",str_replace("^","",$this->bean->fetched_row['zone_value']));
    		} else if ($this->bean->fetched_row['zone_type'] == 'city') {
    			$arFilterSaved['city'] = explode(",",str_replace("^","",$this->bean->fetched_row['zone_value']));
    			$arFilterSaved['city'] = array_combine($arFilterSaved['city'],array_values($arFilterSaved['city']));
    		} else if ($this->bean->fetched_row['zone_type'] == 'zip') {
    			$arFilterSaved['zip'] = explode(",",str_replace("^","",$this->bean->fetched_row['zone_value']));
    			$arFilterSaved['zip'] = array_combine($arFilterSaved['zip'],array_values($arFilterSaved['zip']));
    		}
    	}    	
    	
    	$stSelectedFilters = isset($arFilterSaved['geo_filter_for'][0])?$arFilterSaved['geo_filter_for'][0]:'city';
    	
    	$this->ss->assign('GEO_FILTER_FOR',$stSelectedFilters);
    	
    	//set selected options for state
    	$arSelState = $arFilterSaved['state'];
    	
    	global $arTmpSelState;
    	$fun = function($key){
    		global $arTmpSelState;
    		$arTmpSelState[$key] = $GLOBALS['app_list_strings']['state_dom'][$key];
    		return $arTmpSelState;
    	};
    	array_map($fun,$arSelState);
    	//check if there are
    	$arRmainingStats = (count($arTmpSelState)>0) ?array_diff_assoc($GLOBALS['app_list_strings']['state_dom'],$arTmpSelState):$GLOBALS['app_list_strings']['state_dom'];
    	$this->ss->assign('DOM_STATE', $arRmainingStats);
    	//now we are displaying all the STATES FOR COUNTY
    	$this->ss->assign('STATE_OPTOIONS',$arTmpSelState);

    	//set selected options for county
    	$arCountySelectedOptions =(count($arFilterSaved['county'])>0)? $this->getUserCounties($arFilterSaved['county']):array();
    	$this->ss->assign('COUNTY_OPTOIONS',$arCountySelectedOptions);
    	$this->ss->assign('CITY_OPTOIONS',$arFilterSaved['city']);
    	$this->ss->assign('ZIP_OPTOIONS',$arFilterSaved['zip']);
        $this->ss->assign('MOD',$mod_strings);
        $this->ss->assign("ZONE_NAME_VALUE",$zoneName);
        $this->ss->assign("RETURN_ACTION",$returnAction);
        $this->ss->assign("RECORD_ID",$recordId);
        $this->ss->assign("CANCEL_RECORD_ID",$cancelRecordId);
        echo "<script type='text/javascript'>var recordId = '".$recordId."'</script>";
        echo "<script type='text/javascript' src='custom/modules/oss_Zone/oss_zone.js'></script>";
        $this->ss->display($this->getCustomFilePathIfExists('custom/modules/oss_Zone/tpls/zone.tpl'));        
    }
    /**
     * function for get county for selected state
     * @author Mohit kumar Gupta
     * @date 11-oct-2013
     */
    function getUserCounties($arFilterSaved){
    	$arReturn = array();
    
    	$obCounty = new oss_County();
    	$arData = $obCounty->get_full_list(' oss_county.name ASC',' oss_county.id IN ("'.implode('","',$arFilterSaved).'")');
    	if(count($arData)){
    		foreach ($arData as $obUserCounty)
    		{
    			$arReturn[$obUserCounty->id] = ucfirst(strtolower($obUserCounty->name));
    		}
    	}
    	return  $arReturn;
    }
}

?>
