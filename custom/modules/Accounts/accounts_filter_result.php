<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */
require_once 'modules/Accounts/Account.php';
require_once 'custom/include/common_functions.php';
require_once 'custom/include/SugarEmailAddress/CustomSugarEmailAddress.php';
require_once 'custom/modules/Users/filters/userAccessFilters.php';

/**
 * CLASS : accounts_filter_result
 * Purpose : Class definition for create new list query data for list view data and sqs data
 */
class accounts_filter_result extends Account{
	
	function __construct(){
		parent::Account();
		//custom email bean
		$this->emailAddress = new CustomSugarEmailAddress();
	}
    
    function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false, $ifListForExport = false) 
    {
        global $current_user;
        $classification = array();

        if (isset($_REQUEST['classification']) && !empty($_REQUEST['classification']) && ($_REQUEST['classification'] != 'Search by Classification..'))
           $classification[] = htmlspecialchars_decode($_REQUEST['classification']);

        if (isset($_REQUEST['classification_1']) && !empty($_REQUEST['classification_1']) && ($_REQUEST['classification_1'] != 'Search by Classification..'))
            $classification[] = htmlspecialchars_decode($_REQUEST['classification_1']);

        if (isset($_REQUEST['classification_2']) && !empty($_REQUEST['classification_2']) && ($_REQUEST['classification_2'] != 'Search by Classification..'))
            $classification[] = htmlspecialchars_decode($_REQUEST['classification_2']);
        
        
        if(isset($_REQUEST['businessintelligence_advanced']) && !empty($_REQUEST['businessintelligence_advanced'])){
        	$bi_type = " ( bi.name ='".implode("' OR bi.name = '",$_REQUEST['businessintelligence_advanced'])."')";
        }
        
        if(isset($_REQUEST['businessintelligence_basic']) && !empty($_REQUEST['businessintelligence_basic'])){
        	$bi_type = " ( bi.name ='".implode("' OR bi.name = '",$_REQUEST['businessintelligence_basic'])."')";
        }
        
        if (isset($_REQUEST['bi_description']) && !empty($_REQUEST['bi_description']))
        	$bi_description = htmlspecialchars_decode($_REQUEST['bi_description']);
        
        if (isset($_REQUEST['my_description']) && !empty($_REQUEST['my_description']))
        	$my_description = htmlspecialchars_decode($_REQUEST['my_description']);
        

        $params['distinct'] = ' DISTINCT ';
        ########## Apply User Filters ########
        if ($current_user->is_admin != 1) {
        
            $obUserFilters = new userAccessFilters();
            // if classification search
            if (!isset($classification) || empty($classification)) {
                $stAddtionalFilterJoin = ' LEFT JOIN oss_classifion_accounts_c as account_cl ON account_cl.oss_classid41cccounts_idb = accounts.id and account_cl.deleted = 0  ';
            }
            $arTableAlias['classification']['table'] ='account_cl';
            $arTableAlias['classification']['field'] ='oss_classi48bbication_ida';
            $arUserFilters = $obUserFilters->getFilterClauseClient('accounts',$arTableAlias);
            if (count($arUserFilters) > 0) {
                $where .= (!empty($arUserFilters['filters']))?" AND (( ".$arUserFilters['filters']." ) OR " : ' AND ( ';
                $where .= $arUserFilters['visibility']." )"; 
            }       
        
        }
        ########## End of Apply User Filters ########

        $ret_array = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect, $ifListForExport);
        
        if (isset($classification) && !empty($classification)) {
            
             // $classification = "'".implode("','",$classification)."'";
             
        	$classification = " ( classification.name = '".implode("' OR classification.name = '",$classification)."' )";
             
            $ret_array['from'] .= " LEFT JOIN oss_classifion_accounts_c as cl ON cl.oss_classid41cccounts_idb = accounts.id and cl.deleted = 0
                                    LEFT JOIN oss_classification as classification ON
                                                     cl.oss_classi48bbication_ida = classification.id and classification.deleted = 0 ";
             
            $ret_array['where'] .=" AND ". $classification ;
             
         }
         
         
        if( (isset($bi_type) && !empty($bi_type))  || (isset($bi_description) && !empty($bi_description)) || ( isset($my_description) && !empty($my_description) )){
        	
        	$ret_array['from'] .= " LEFT JOIN oss_businessintelligence as bi ON bi.account_id = accounts.id and bi.deleted = 0 ";
        	
        	if (isset($bi_type) && !empty($bi_type))
        		$ret_array['where'] .= " AND ".$bi_type;
        	
        	if ((isset($bi_description) && !empty($bi_description))){
        		$ret_array['where'] .=" AND bi.description LIKE '%".$bi_description."%' AND bi.my_only != 1";
        	}
        	
        	if ((isset($my_description) && !empty($my_description))){
        		$ret_array['where'] .=" AND bi.description LIKE '%".$my_description."%' AND bi.my_only = 1 ";
        	}
        	 
        } 
        
        //echo '<pre>'; print_r($_REQUEST); echo '</pre>';
        
        if(isset($_REQUEST['billing_address_state_advanced']) && !empty($_REQUEST['billing_address_state_advanced']) && (trim($_REQUEST['billing_address_state_advanced'][0])!='')){
        	$billing_address_state_advanced = "'". implode("', '",$_REQUEST['billing_address_state_advanced'])."'";
        	$ret_array['where'] .= " AND (accounts.billing_address_state IN (".$billing_address_state_advanced.")) ";
        }
        if(isset($_REQUEST['billing_address_state_basic']) && !empty($_REQUEST['billing_address_state_basic'])){
        	$billing_address_state_basic = "'". implode("', '",$_REQUEST['billing_address_state_advanced'])."'";
        	$ret_array['where'] .= " AND ( accounts.billing_address_state IN (".$billing_address_state_basic." )) .";
        }
         
        if(isset($_REQUEST['phone_advanced']) && !empty($_REQUEST['phone_advanced'])){
        	$phone_advanced = trim($_REQUEST['phone_advanced']);
        	$ret_array['where'] .= " AND accounts.phone_office LIKE '".clean_ph_no($phone_advanced)."%' ";
        }
        
        if(isset($arUserFilters['visibility'])){
            $ret_array['from'] .= $stAddtionalFilterJoin;
        }
        
        //If retrun array is set to true then return array else return string
        //Modified by Mohit Kumar Gupta
        //@date 27-01-2014
        if($return_array) {
        	return $ret_array;
        } else {
        	return $ret_array['select'] . ' ' . $ret_array['from'] . ' ' . $ret_array['where'] . ' ' . $ret_array['order_by'];
        }
    }
    
    /**
     * @method get_bi_data
     * @author Hirak
     * @purpose bi supanel sql
     * @return string
     */ 
    function get_bi_data(){
    
    	$return_array['select'] = " SELECT DISTINCT  oss_businessintelligence.id ,  
    			oss_businessintelligence.source ,  
    			oss_businessintelligence.name ,  
    			oss_businessintelligence.description ,  
    			oss_businessintelligence.parent_id ,  
    			oss_businessintelligence.type_order , 
    			oss_businessintelligence.account_id , 
    			oss_businessintelligence.image_url ,  
    			oss_businessintelligence.image_description ,  
    			oss_businessintelligence.sort_order ,  
    			oss_businessintelligence.my_only ,  
    			oss_businessintelligence.assigned_user_id ";
    
    	$return_array['from'] = " FROM oss_businessintelligence ";
    
		$return_array['join'] = " LEFT JOIN 
				accounts ON accounts.id = oss_businessintelligence.account_id
    			AND accounts.deleted = 0 ";
    	
    	$return_array['where'] = " WHERE oss_businessintelligence.account_id = '{$this->id}'
    	 	AND  ( oss_businessintelligence.description is NOT NULL OR
    	 		oss_businessintelligence.description != '' ) 
    		AND oss_businessintelligence.deleted=0 ";
    
    	$return_array['group_by'] = "  GROUP BY oss_businessintelligence.id,oss_businessintelligence.type_order, 
    			oss_businessintelligence.my_only ";
    
    	$stSubPanelSQL = $return_array['select']
    			.$return_array['from']
    			.$return_array['join']
    			.$return_array['where']
    			.$return_array['group_by'] ;
    	
    
    	return $stSubPanelSQL;
    }
    /**
     * Overriden function to set fields phone and address
     * @author Ashutosh
     * @date 26 June 2014
     * @see Account::fill_in_additional_list_fields()
     */
    function fill_in_additional_list_fields(){        
        
        parent::fill_in_additional_list_fields();        
        //do not apply this on quick search result
        $arRestrictedActions = array('Popup','quicksearchQuery');
        if(!in_array($_REQUEST['action'], $arRestrictedActions) ){
            $this->phone_office = formatPhoneNumber($this->phone_office);
            $this->phone_office .= ($this->phone_office_ext != '')? ' - '. $this->phone_office_ext:'';
        }  
        
        $this->billing_address_street = $this->address1 ."\n".$this->address2; 
        $this->county = ($this->county_name != '') ? $this->county_name : $this->county;      
    }

}
?>