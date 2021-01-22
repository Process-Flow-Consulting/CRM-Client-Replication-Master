<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

/*********************************************************************************

* Description:
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
* Reserved. Contributor(s): ______________________________________..
* *******************************************************************************/
/**
 * @author Mohit Kumar Gupta
 * @date 05-03-2015
 * issue resolved of select all not working on export
 */
require_once 'custom/modules/Quotes/CustomQuote.php';
require_once 'custom/include/common_functions.php';

class CustomQuoteExport extends CustomQuote{
	
    /**
     * Overwrite Export Query
     * @see Lead::create_export_query()
     */
	function create_export_query($order_by, $where){
	    //add current post to request array in case of export data on selection of select all
	    if (!empty($_REQUEST['current_post'])) {
	        $currentPost = unserialize(base64_decode($_REQUEST['current_post']));
	        $_REQUEST = array_merge($_REQUEST, $currentPost);
	    }
	    	    
		$ret_array = parent::create_new_list_query($order_by, $where, array(), array(), 0, '', true, $this, true, true);	
		
		//if accounts table aliases as jt10 then change the table name in where clause jt0 to jt10
		//jt0.name is hard coded in custom/modules/Quotes/metadata/SearchFields.php
		if(stripos($ret_array['from'], 'accounts jt10') !== false) {
			$ret_array['where'] = str_replace('jt0.name','jt10.name',$ret_array['where']);
		}	
				
        if(!is_array($ret_array)) {
        	return $ret_array;
        } else {
        	return $ret_array['select'] . ' ' . $ret_array['from'] . ' ' . $ret_array['where'] .' '.$ret_array['group_by']. ' ' . $ret_array['order_by'];
        }		 	
	}        	
}
