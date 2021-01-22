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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/**
 * use to change auto increment value of proposal number
 * @author Mohit Kumar Gupta
 * @date 05-03-2014
 */
class AdministrationViewUpdatequotenum extends SugarView {
    
    //old auto increment value
    private $autoIncrementNum;
    /**
     * default constructor
     * @author Mohit Kumar Gupta
     * @date 05-03-2014
     */
 	function AdministrationViewUpdatequotenum(){ 	    
 		parent::SugarView(); 		
 	}    
    /**
     * default display function
     * use to display the edit view
     * @see SugarView::display()
     * @author Mohit Kumar Gupta
     * @date 05-03-2014
     */
    function display() {
       global $db;
       $query = "SHOW TABLE STATUS LIKE 'quotes'";
       $queryResult = $db->query($query);
       $queryData= $db->fetchByAssoc($queryResult);
       $this->autoIncrementNum = $queryData['Auto_increment'];
       
       if (isset($_REQUEST['custom_quote_num']) && $_REQUEST['custom_quote_num'] > 0) {
            $this->saveAutoIncrementNum($_REQUEST['custom_quote_num']);
       }              
       
       $this->ss->assign('OLDQUOTENUM',$this->autoIncrementNum);
       $this->ss->assign('RETURN_MODULE',(isset($_REQUEST['return_module']))?$_REQUEST['return_module']:'Administration');
       $this->ss->assign('RETURN_ACTION',(isset($_REQUEST['return_action']))?$_REQUEST['return_action']:'index');
       $this->ss->display('custom/modules/Administration/tpls/updatequotenum.tpl');
    }
    /**
     * save modified auto increment value at table level
     * redirect to the return module and return action
     * @param string $newQuoteNum
     * @author Mohit Kumar Gupta
     * @date 05-03-2014
     */
    function saveAutoIncrementNum($newQuoteNum){

        global $db;
        if ($newQuoteNum > $this->autoIncrementNum) {
        	$query = "ALTER TABLE quotes auto_increment=".$newQuoteNum;
 	        $db->query($query);
        }
        SugarApplication::redirect('index.php?module='.$_REQUEST['return_module'].'&action='.$_REQUEST['return_action']);    	
    }
}