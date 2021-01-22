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
require_once 'modules/Contacts/Contact.php';
require_once 'custom/include/SugarEmailAddress/CustomSugarEmailAddress.php';
require_once 'custom/modules/Users/filters/userAccessFilters.php';

/**
 * CLASS : customContact
 * Purpose : Class definition for create new list query data for list view data and sqs data
 */
class customContact extends Contact{
	
	
	function __construct() {
		
		global $modules_exempt_from_availability_check;
		//to show classification subpanel
		$modules_exempt_from_availability_check['oss_Classification'] = 'oss_Classification';
		
		parent::Contact ();
		//custom email address bean
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
			$abi_type = " ( account_bi.name ='".implode("' OR account_bi.name = '",$_REQUEST['businessintelligence_advanced'])."')";
		}
	
		if(isset($_REQUEST['businessintelligence_basic']) && !empty($_REQUEST['businessintelligence_basic'])){
			$bi_type = " ( bi.name ='".implode("' OR bi.name = '",$_REQUEST['businessintelligence_basic'])."')";
			$abi_type = " ( account_bi.name ='".implode("' OR account_bi.name = '",$_REQUEST['businessintelligence_basic'])."')";
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
		    $arUserFilters = $obUserFilters->getFilterClauseClient('contacts',$arTableAlias);
		    if (count($arUserFilters) > 0) {
    		    $where .= (!empty($arUserFilters['filters']))?" AND (( ".$arUserFilters['filters']." ) OR " : ' AND ( ';
    		    $where .= $arUserFilters['visibility']." )";
		    }
		}
		########## End of Apply User Filters ########
		
		$ret_array = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect, $ifListForExport);
	    //additional condition to handle the phone fields
		if(isset($_REQUEST['phone_advanced']) && !empty($_REQUEST['phone_advanced'])){
		    $phone_advanced = trim($_REQUEST['phone_advanced']);
		    $ret_array['where'] .= " AND ( contacts.phone_work LIKE '".clean_ph_no($phone_advanced)."%' OR
					contacts.phone_mobile LIKE '".clean_ph_no($phone_advanced)."%'  OR
						contacts.phone_other LIKE '".clean_ph_no($phone_advanced)."%'  OR
							contacts.phone_fax LIKE '".clean_ph_no($phone_advanced)."%'  OR
									contacts.assistant_phone LIKE '".clean_ph_no($phone_advanced)."%' ) ";
		}
		
		if (isset($classification) && !empty($classification)) {
			 
			$contact_classification = " ( classification.name = '".implode("' OR classification.name = '",$classification)."' )";
			$account_classification = " ( account_classification.name = '".implode("' OR account_classification.name = '",$classification)."' )";
			 
			$ret_array['from'] .= " LEFT JOIN oss_classification_contacts as cl ON contacts.id=cl.contact_id  AND cl.deleted = 0 LEFT JOIN oss_classification as classification ON  cl.oss_classification_id = classification.id and classification.deleted = 0 
			         LEFT JOIN oss_classifion_accounts_c as account_cl ON account_cl.oss_classid41cccounts_idb = accounts.id and account_cl.deleted = 0 LEFT JOIN oss_classification as account_classification ON account_cl.oss_classi48bbication_ida = account_classification.id and account_classification.deleted = 0 ";
			
			$ret_array['where'] .=" AND ( ". $contact_classification ." OR ".$account_classification." ) " ;
			 
		}
		 
		 
		if( (isset($bi_type) && !empty($bi_type))  || (isset($bi_description) && !empty($bi_description)) || ( isset($my_description) && !empty($my_description) )){
			 
			$ret_array['from'] .= " LEFT JOIN oss_businessintelligence as bi ON bi.contact_id = contacts.id and bi.deleted = 0 
			         LEFT JOIN oss_businessintelligence as account_bi ON account_bi.account_id = accounts.id and account_bi.deleted = 0 ";
			 
			if (isset($bi_type) && !empty($bi_type))
				$ret_array['where'] .= " AND ( ".$bi_type ." OR ". $abi_type .") ";
			 
			if ((isset($bi_description) && !empty($bi_description))){
				$ret_array['where'] .=" AND ( (bi.description LIKE '%".$bi_description."%' AND bi.my_only != 1 ) ";
				$ret_array['where'] .=" OR ( account_bi.description LIKE '%".$bi_description."%' AND account_bi.my_only != 1 ) )";
			}
			 
			if ((isset($my_description) && !empty($my_description))){
				$ret_array['where'] .=" AND ( (bi.description LIKE '%".$my_description."%' AND bi.my_only = 1 )";
				$ret_array['where'] .=" OR ( account_bi.description LIKE '%".$my_description."%' AND account_bi.my_only = 1 ) )";
			}
	
		}
		
		//first name search
		if(isset($_REQUEST['first_name_basic']) && !empty($_REQUEST['first_name_basic']) ) {
		    $ret_array['where'] .= " AND ( contacts.first_name LIKE '%".$_REQUEST['first_name_basic']."%' ) ";
		}
		if(isset($_REQUEST['first_name_advanced']) && !empty($_REQUEST['first_name_advanced'] ) ){
		    $ret_array['where'] .= " AND ( contacts.first_name LIKE '%".$_REQUEST['first_name_advanced']."%' ) ";
		}
		
		//last name search
		if(isset($_REQUEST['last_name_basic']) && !empty($_REQUEST['last_name_basic']) ) {
			$ret_array['where'] .= " AND ( contacts.last_name LIKE '%".$_REQUEST['last_name_basic']."%' ) ";
		
		}
		if(isset($_REQUEST['last_name_advanced']) && !empty($_REQUEST['last_name_advanced'] ) ){
			$ret_array['where'] .= " AND ( contacts.last_name LIKE '%".$_REQUEST['last_name_advanced']."%' ) ";
		}
		
		//full name search
		if(isset($_REQUEST['search_name_basic']) && !empty($_REQUEST['search_name_basic']) ) {
			$ret_array['where'] .= " AND ( contacts.first_name LIKE '%".$_REQUEST['search_name_basic']."%' OR  contacts.last_name LIKE '%".$_REQUEST['search_name_basic']."%'  ) ";
		}
	    if(isset($_REQUEST['search_name_advanced']) && !empty($_REQUEST['search_name_advanced']) ) {
			$ret_array['where'] .= " AND ( contacts.first_name LIKE '%".$_REQUEST['search_name_advanced']."%' OR  contacts.last_name LIKE '%".$_REQUEST['search_name_advanced']."%'  ) ";
		}
		
		$ret_array['where'] .=" AND contacts.deleted =0 ";
		
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
	
	function contact_opportunity_relate(){
		
		/*$bean = $GLOBALS['app']->controller->bean;
		
		$stSql = " SELECT opportunities.id
    				,opportunities.name
            		,opportunities.sales_stage
            		,accounts.id account_id
            		,accounts.name dup_account_name
            		,accounts.name lcd_account
            		,opportunities.assigned_user_id
            		,CONCAT(COALESCE(first_name),' ',COALESCE(last_name)) assigned_user_name
            		,opportunities.date_closed
					,opportunities.client_bid_status
	            	,opportunities.bid_due_timezone
            		,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz
            		,opportunities.amount_usdollar
		    FROM opportunities
		    LEFT JOIN accounts_opportunities accountOpportunity ON accountOpportunity.opportunity_id =opportunities.id AND accountOpportunity.deleted=0
            LEFT JOIN accounts ON   accountOpportunity.account_id = accounts.id AND accounts.deleted=0
            LEFT JOIN users ON opportunities.assigned_user_id = users.id AND users.deleted =0
		    INNER JOIN
    		opportunities_contacts ON opportunities.id = opportunities_contacts.opportunity_id
	        AND opportunities_contacts.contact_id = '".$bean->id."'
	        AND opportunities_contacts.deleted = 0
			where
    			opportunities.deleted = 0 ";*/
	    
	    
		/**
    	 * The sql should display opportunities based on assignment and visibility
    	 * Modified By : Ashutosh 
    	 * Date : 21 Oct 2013 
		 */
		$bean = $GLOBALS['app']->controller->bean;
		
		$obOpportunities = BeanFactory::getBean('Opportunities');
		$arOppSql = $obOpportunities->create_new_list_query('','',array(),array(),0,'',1);
		$arOppSql['from'] .= ' INNER JOIN
    		opportunities_contacts ON opportunities.id = opportunities_contacts.opportunity_id
	        AND opportunities_contacts.contact_id = "'.$bean->id.'"
	                           LEFT JOIN accounts_opportunities accountOpportunity ON accountOpportunity.opportunity_id =opportunities.id AND accountOpportunity.deleted=0
                               LEFT JOIN accounts ON   accountOpportunity.account_id = accounts.id AND accounts.deleted=0';
		$arOppSql['select'] .= ' ,accounts.id account_id
            		             ,accounts.name dup_account_name
	                             ,accounts.name lcd_account
	                             ,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz
	                             ';
		
		 
		$stReturnSql = $arOppSql['select'].' '.$arOppSql['from'].' '.$arOppSql['where'];
		return $stReturnSql;
		
		
	}
	
	
	/**
	 * Fucntion to list all the related opportunities 
	 * where contact is added as addtional contacts
	 * Added By : Ashutosh 
	 * Date : 21 Oct 2013
	 * @return string 
	 */
	function opportunities_contacts_c(){
	
	    $bean = $GLOBALS['app']->controller->bean;
	    $obOpportunities = BeanFactory::getBean('Opportunities');
	    $arOppSql = $obOpportunities->create_new_list_query('','',array(),array(),0,'',1);
	    $arOppSql['from'] .= ' INNER JOIN opportunities_contacts_c ON opportunities_contacts_c.opportunity_id  = opportunities.id AND opportunities_contacts_c.deleted = 0
	                           LEFT JOIN accounts_opportunities accountOpportunity ON accountOpportunity.opportunity_id =opportunities.id AND accountOpportunity.deleted=0
                               LEFT JOIN accounts ON   accountOpportunity.account_id = accounts.id AND accounts.deleted=0';
	    $arOppSql['select'] .= ' ,accounts.id account_id
            		             ,accounts.name dup_account_name
	                             ,accounts.name lcd_account
	                             ,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz 
	                             ';
	   $arOppSql['where'] .= ' AND opportunities_contacts_c.contact_id = "'.$bean->id.'"';
 	   
	   $stReturnSql = $arOppSql['select'].' '.$arOppSql['from'].' '.$arOppSql['where'];
	   return $stReturnSql;
	}
	
	
	
	
	/**
	 * @method get_bi_data
	 * @purpose bi supanel sql
	 * @return string
	 */
	function get_bi_data(){
		
		$return_array['select'] = " SELECT  DISTINCT 
				oss_businessintelligence.id, 
				oss_businessintelligence.source, 
				oss_businessintelligence.name,	
				oss_businessintelligence.description, 
				oss_businessintelligence.parent_id, 
				oss_businessintelligence.type_order, 
				oss_businessintelligence.account_id, 
				oss_businessintelligence.contact_id, 
				oss_businessintelligence.image_url, 
				oss_businessintelligence.image_description, 
				oss_businessintelligence.sort_order, 
				oss_businessintelligence.my_only, 
				oss_businessintelligence.assigned_user_id ";
	
		$return_array['from'] = " FROM oss_businessintelligence ";
	
		$return_array['join'] = " LEFT JOIN accounts 
				ON accounts.id = oss_businessintelligence.account_id AND accounts.deleted = 0 
				LEFT JOIN contacts 
				ON contacts.id = oss_businessintelligence.contact_id AND contacts.deleted = 0";
		 
		$return_array['where'] = " WHERE ( ";
		
		if(!empty($this->account_id)){
		 	$return_array['where'] .= "oss_businessintelligence.account_id = '{$this->account_id}' OR ";
		}
		
		$return_array['where'] .= " oss_businessintelligence.contact_id = '{$this->id}' ) 
		AND  ( oss_businessintelligence.description is NOT NULL 
		OR oss_businessintelligence.description != '' ) 
		AND oss_businessintelligence.deleted=0 ";
	
		$return_array['group_by'] = " GROUP BY 
				oss_businessintelligence.id,oss_businessintelligence.type_order, oss_businessintelligence.my_only ";
	
    	$stSubPanelSQL = $return_array['select'].$return_array['from'].$return_array['join']
    				.$return_array['where'].$return_array['group_by'] ;
	    					 
	
	    return $stSubPanelSQL;
	}
	
	/**
	 * get classification subpanel query
	 * @return string
	 */
	function oss_classification_contacts(){
		$client_contact_sql = "SELECT oss_classification.id, oss_classification.name, oss_classification.description, oss_classification.date_modified, oss_classification.assigned_user_id, 'oss_classification_contacts' panel_name FROM oss_classification INNER JOIN oss_classification_contacts ON oss_classification.id=oss_classification_contacts.oss_classification_id AND oss_classification_contacts.contact_id = '{$this->id}' AND oss_classification_contacts.deleted=0 WHERE oss_classification.deleted=0 ";
		
		$client_sql = '';
		
		if(!empty($this->account_id)){
				
			$client_sql = "SELECT oss_classification.id, oss_classification.name, oss_classification.description, oss_classification.date_modified,			oss_classification.assigned_user_id, 'oss_classification_contacts' panel_name FROM oss_classification INNER JOIN oss_classifion_accounts_c ON oss_classification.id=oss_classifion_accounts_c.oss_classi48bbication_ida AND oss_classifion_accounts_c.oss_classid41cccounts_idb = '{$this->account_id}' AND oss_classifion_accounts_c.deleted=0 WHERE oss_classification.deleted=0";
				
		}
		
		if(!empty($client_sql)){
			$sql = $client_contact_sql.") UNION ALL (".$client_sql ." ";
		}else{
			$sql = $client_contact_sql ." ORDER BY oss_classification.id asc";
		}
		
		return $sql;
	}
	
	
	/**
	 * overwrite bean count list query
	 */
	function create_list_count_query($query)
	{
		// remove the 'order by' clause which is expected to be at the end of the query
		$pattern = '/\sORDER BY.*/is';  // ignores the case
		$replacement = '';
		$query = preg_replace($pattern, $replacement, $query);
		//handle distinct clause
		$star = '*';
		if(substr_count(strtolower($query), 'distinct')){
			if (!empty($this->seed) && !empty($this->seed->table_name ))
				$star = 'DISTINCT ' . $this->seed->table_name . '.id';
			else
				$star = 'DISTINCT ' . $this->table_name . '.id';
	
		}
		
		$pattern = '/\sFROM oss_businessintelligence.*/is';
		if(preg_match($pattern,$query)){
			$star = 'DISTINCT account_id';
		}
	
		// change the select expression to 'count(*)'
		$pattern = '/SELECT(.*?)(\s){1}FROM(\s){1}/is';  // ignores the case
		$replacement = 'SELECT count(' . $star . ') c FROM ';
	
		//if the passed query has union clause then replace all instances of the pattern.
		//this is very rare. I have seen this happening only from projects module.
		//in addition to this added a condition that has  union clause and uses
		//sub-selects.
		if (strstr($query," UNION ALL ") !== false) {
	
			//separate out all the queries.
			$union_qs=explode(" UNION ALL ", $query);
			foreach ($union_qs as $key=>$union_query) {
				$star = '*';
				preg_match($pattern, $union_query, $matches);
				if (!empty($matches)) {
					if (stristr($matches[0], "distinct")) {
						if (!empty($this->seed) && !empty($this->seed->table_name ))
							$star = 'DISTINCT ' . $this->seed->table_name . '.id';
						else
							$star = 'DISTINCT ' . $this->table_name . '.id';
					}
				} // if
				$replacement = 'SELECT count(' . $star . ') c FROM ';
				$union_qs[$key] = preg_replace($pattern, $replacement, $union_query,1);
			}
			$modified_select_query=implode(" UNION ALL ",$union_qs);
		} else {
			$modified_select_query = preg_replace($pattern, $replacement, $query,1);
		}
		
		//echo $modified_select_query;
		return $modified_select_query;
	}
}
