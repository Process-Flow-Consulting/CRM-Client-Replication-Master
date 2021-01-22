<?php
require_once 'include/ListView/ListViewData.php';
/**
 * extends the list view data from main include 
 * @author Mohit Kumar Gupta
 *
 */
class CustomListViewData extends ListViewData{
	/**
	 * deafult constructor
	 */
	function __construct(){
		parent::ListViewData();
	}
	/**
	 * change the onclick function for additional information
	 * @param string $id  
	 * id of the lead
	 * @param string $name
	 * name of the lead
	 * @see ListViewData::getAdditionalDetailsAjax()
	 * @return array
	 */ 
// Changes made by parveen badoni on 03/07/2014 $name set as default null so when no value is supplied for $name when function is called, it doesnt result in any warning.
	function getAdditionalDetailsAjax($id,$name=null)
	{
		global $app_strings;		
		
		$name = htmlspecialchars_decode($name,ENT_QUOTES);
		$name = addslashes($name);
		
		$jscalendarImage = SugarThemeRegistry::current()->getImageURL('info_inline.gif');
		$name = htmlspecialchars($name);
		$extra = "<span id='adspan_" . $id . "' "
				. "onclick=\"showPLDetailModal('$id','$name')\" "
				. " style='position: relative;'><!--not_in_theme!--><img vertical-align='middle' class='info' border='0' alt='".$app_strings['LBL_ADDITIONAL_DETAILS']."' src='$jscalendarImage'></span>";
	
				return array('fieldToAddTo' => $this->additionalDetailsFieldToAdd, 'string' => $extra);
	}
}
