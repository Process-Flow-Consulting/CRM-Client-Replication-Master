<?php

/*

Modification information for LGPL compliance

r56990 - 2010-06-16 13:05:36 -0700 (Wed, 16 Jun 2010) - kjing - snapshot "Mango" svn branch to a new one for GitHub sync

r56989 - 2010-06-16 13:01:33 -0700 (Wed, 16 Jun 2010) - kjing - defunt "Mango" svn dev branch before github cutover

r55980 - 2010-04-19 13:31:28 -0700 (Mon, 19 Apr 2010) - kjing - create Mango (6.1) based on windex

r51719 - 2009-10-22 10:18:00 -0700 (Thu, 22 Oct 2009) - mitani - Converted to Build 3  tags and updated the build system 

r51634 - 2009-10-19 13:32:22 -0700 (Mon, 19 Oct 2009) - mitani - Windex is the branch for Sugar Sales 1.0 development

r50752 - 2009-09-10 15:18:28 -0700 (Thu, 10 Sep 2009) - dwong - Merged branches/tokyo from revision 50372 to 50729 to branches/kobe2
Discard lzhang r50568 changes in Email.php and corresponding en_us.lang.php

r50375 - 2009-08-24 18:07:43 -0700 (Mon, 24 Aug 2009) - dwong - branch kobe2 from tokyo r50372

r42807 - 2008-12-29 11:16:59 -0800 (Mon, 29 Dec 2008) - dwong - Branch from trunk/sugarcrm r42806 to branches/tokyo/sugarcrm

r36563 - 2008-06-11 10:36:05 -0700 (Wed, 11 Jun 2008) - jmertic - Bug 22877: Make a new wireless detail view and have the smarty sugar_phone plugin check for being in a mobile session; if true then format the phone number as a link in the form of "tel:1234567890" per RFC 3966.
Added:
- include/SugarFields/Fields/Phone/WirelessDetailView.tpl
Touched:
- include/Smarty/plugins/function.sugar_phone.php
- include/SugarWireless/css/wireless.css

r29406 - 2007-11-08 16:36:28 -0800 (Thu, 08 Nov 2007) - bsoufflet - Bug 17690 : [RC] No license and/or entry point check in the files

r28844 - 2007-10-24 22:52:24 -0700 (Wed, 24 Oct 2007) - clee - Fix for 16807
It appears that the formats may be giving some trouble.  It is best to consolidate the formatting to a known skype format.  Adding function in include/utils.php to format the phone number to a known skype format that will work for sure.
Modified:
include/utils.php
include/Smarty/plugins/function.sugar_phone.php
Code review by Ajay

r28841 - 2007-10-24 20:11:24 -0700 (Wed, 24 Oct 2007) - ajay - 16807: added support for skypeout in detail view..


*/


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
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugar_translate} function plugin
 *
 * Type:     function<br>
 * Name:     sugar_translate<br>
 * Purpose:  translates a label into the users current language
 * 
 * @author Majed Itani {majed at sugarcrm.com
 * @param array
 * @param Smarty
 */
function smarty_function_sugar_phone_number_format_edit($params, &$smarty)
{
	
	if (!isset($params['value'])){
		//$smarty->trigger_error("sugar_phone: missing 'value' parameter");
		return '';
	}
	
	$rawField = ph_field_clean_text_edit_view($params['value']);
	$len_rawField =strlen($rawField);
	
	if( $len_rawField >= 7){
	
		$area =  substr($rawField,0,3);
		$prefix = substr($rawField,3,3);
		$ext = substr($rawField,6,$len_rawField);
		$new_field = $area."-".$prefix."-".$ext;
	
	}else if($len_rawField >= 4 && $len_rawField < 7){
	
		$area =  substr($rawField,0,3);
		$ext = substr($rawField,3,$len_rawField);
		$new_field = $area."-".$ext;
	
	}else{
		$new_field = $rawField;
	}
	
	$params['value'] = $new_field;

    return $params['value'];
 
}
function ph_field_clean_text_edit_view($text)
{
	$code_entities_match = array('&quot;','&quot; ','!','@','#','$','%','^','&','*','(',')','+','{','}',':','"','<','>','?','[',']','\\',';',"'","' ",',','*','+','~','`','=',' ','-');
	$code_entities_replace = array('','','','','','','','','','','','','','','','','','','','','','','','-');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	return $text;
}

?>
