<?php
global $db;



$state_abbr = $_REQUEST['state_abbr'];
$selected_county = $_REQUEST['selected_county'];
######################################
### Project Lead List view Dropdown ##

if(isset($_REQUEST['state_advacne'])){
	//SQL T get Counties for selected states
	$county_sql = "SELECT id,`name` from oss_county where county_abbr IN( '".implode("','",$_REQUEST['state_advacne'])."') AND deleted=0   ORDER BY `name`";
	$county_query = $db->query($county_sql);
	
	while($county_list = $db->fetchByAssoc($county_query)){
		$selected = '';
		$county_name = ucwords(strtolower($county_list['name']));
		$county_id = $county_list['id'];
		if( in_array($county_id,$_REQUEST['county_id'])){
			$selected = 'selected';
		}
		$county_dropdown .= '<option value="'.$county_id.'" label="'.$county_name.'" '.$selected.'>'.$county_name.'</option>';
	}
	//send selected counties
	echo $county_dropdown;
	die;
}

### Eof Project Lead List view Dropdown ##
##########################################


//if request from user filter form
$stFilter = '';
if(isset($_REQUEST['multisel'])){

	if(isset($_REQUEST['county_filters']))
	{
		$stFilter = ' AND id NOT IN("'.implode('","',	$_REQUEST['county_filters']).'")';
	}

}
$county_sql = "SELECT id,`name` from oss_county where county_abbr = '".$state_abbr."' AND deleted=0 ".$stFilter."  ORDER BY `name`";
$county_query = $db->query($county_sql);

$county_dropdown = '';
//if multiselect requested
$stMulti = (isset($_REQUEST['multisel']) )?"multiple='true'":"";

$county_dropdown .= '<select title="" '.$stMulti.' id="county"';

if(isset($_REQUEST['fieldname']) && !empty($_REQUEST['fieldname'])){
	$county_dropdown.= ' name="'.$_REQUEST['fieldname'].'">';
}else{
	$county_dropdown.= ' name="county_id">';
}

if(trim($stMulti) =='')
$county_dropdown.= ' <option value=""></option>';

while($county_list = $db->fetchByAssoc($county_query)){
	$selected = '';
	$county_name = ucwords(strtolower($county_list['name']));
	$county_id = $county_list['id'];
	if($selected_county == trim($county_id)){
		$selected = 'selected';
	}
	$county_dropdown .= '<option value="'.$county_id.'" label="'.$county_name.'" '.$selected.'>'.$county_name.'</option>';
}
$county_dropdown .= '</select>';
echo $county_dropdown;
?>
