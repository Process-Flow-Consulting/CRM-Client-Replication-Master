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
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */

/**
 * *******************************************************************************
 *
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ******************************************************************************
 */
require_once ('custom/modules/Accounts/accounts_filter_result.php');

/**
 * View class for cilent search and merge action
 *
 * @author Ashutosh
 *         @date 17 Feb 2013
 */
class AccountsViewClient_dupe_merge extends ViewList {
	
	/**
	 * Constructor for the view
	 *
	 * @author Ashutosh
	 *         @date 17 Feb 2014
	 */
	function AccountsViewClient_dupe_merge() {
		parent::ViewList ();
	}
	
	/**
	 * Display handler for the client search and merge view.
	 *
	 * @author Ashutosh
	 * @see ViewDetail::display() @date 17 Feb 2014
	 */
	function display() {
		global $db, $sugar_config,$app_list_strings,$app_strings;
		$primary_client = '';
		$arClients = array ();
		$arSelectedIds = array ();
		$arMergeClients = array ();
		$obAccounts = new accounts_filter_result ();
		$arFilters = Array (
				'name' => 1,
				'proview_url' => 1,
				'phone_office' => 1,
				'billing_address_city' => 1,
				'billing_address_state' => 1,
				'date_entered' => 1,
				'favorites_only' => 1 
		);
		//Pagination Vars
		$navStrings = array('next' => $app_strings['LNK_LIST_NEXT'],
		        'previous' => $app_strings['LNK_LIST_PREVIOUS'],
		        'end' => $app_strings['LNK_LIST_END'],
		        'start' => $app_strings['LNK_LIST_START'],
		        'of' => $app_strings['LBL_LIST_OF']);
		// post vars
		if (isset ( $_REQUEST ['select_to_merge'] ) && isset ( $_REQUEST ['mass'] ) && ! empty ( $_REQUEST ['mass'] )) {
			$arSelectedIds = $_REQUEST ['mass'];
		}
		
		if (isset ( $_REQUEST ['selected_ids'] ) && ! empty ( $_REQUEST ['selected_ids'] )) {
			
			$arSelectedIds = array_merge ( $arSelectedIds, explode ( ',', $_REQUEST ['selected_ids'] ) );
		}			
		
		$stOrderBy = (isset ( $_REQUEST ['order_by'] ) && trim ( $_REQUEST ['order_by'] ) != '') ? $_REQUEST ['order_by'] : 'name';
		$stOrder = (isset ( $_REQUEST ['order'] ) && trim ( $_REQUEST ['order'] ) != '') ? $_REQUEST ['order'] : ' ASC';
		$stPrimaryClient = (isset ( $_REQUEST ["record"] ) && trim ( $_REQUEST ["record"] ) != '') ? $_REQUEST ["record"] : '';
		
		//add primary if not added to the list
		if($stPrimaryClient != ''){
			$arSelectedIds = array_merge(array($stPrimaryClient),$arSelectedIds);			
		}
		
		// if remove from merge request is set
		if (isset ( $_REQUEST ['remove_merge_client'] ) && ! empty ( $_REQUEST ['remove_merge_client'] )) {
		    $atTmpIds = array_flip ( $arSelectedIds );
		    unset ( $atTmpIds [$_REQUEST ['remove_merge_client']] );
		    $arSelectedIds = array_flip ( $atTmpIds );
		    
		    if($stPrimaryClient == $_REQUEST ['remove_merge_client'] ){
		    	$stPrimaryClient ='';
		    }
		}
		// clear all the merge clients
		if (isset ( $_REQUEST ['clear_merge'] ) && $_REQUEST ['clear_merge'] != '') {
		    	
		    $arSelectedIds = array ();
		}		
		$stOrderClause = 'accounts.' . $stOrderBy . ' ' . $stOrder;
		
		$stSearchClauses = (isset ( $_REQUEST ['name_basic'] ) && ! empty ( $_REQUEST ['name_basic'] )) ? ' AND accounts.name LIKE ' . $db->quoted ( '%' . $_REQUEST ['name_basic'] . '%' ) : '';
		$stSelectedIds = (count ( $arSelectedIds ) > 0) ? 'AND accounts.id NOT IN ("' . implode ( '","', $arSelectedIds ) . '")' : '';
		$stMergeSelectedIds = (count ( $arSelectedIds ) > 0) ? 'AND accounts.id IN ("' . implode ( '","', $arSelectedIds ) . '")' : '';
		//pr($stSelectedIds);
		//pr($stMergeSelectedIds);
		if (isset ( $_REQUEST ['Accounts2_ACCOUNT_offset'] ) && trim ( $_REQUEST ['Accounts2_ACCOUNT_offset'] ) != '') {
			$limit = $_REQUEST ['Accounts2_ACCOUNT_offset'];
			$offset = $_REQUEST ['Accounts2_ACCOUNT_offset'];
		} else {
			$limit = 0;
			$offset = 0;
		}
		
		$stSearchClauses .= $stSelectedIds;
		
		$stWhere = 'accounts.visibility =1 ' . $stSearchClauses;
		$arSql = $obAccounts->create_new_list_query ( $stOrderClause, $stWhere, $arFilters, array (), 0, '', 1 );
		
		$stSql = $arSql ['select'] . ' ' . $arSql ['from'] . ' ' . $arSql ['where'] . $arSql ['order_by']; // .' LIMIT '.$limit.',10';
		
		$stCountSql = $obAccounts->create_list_count_query ( $stSql );
		$rsResouCount = $db->query ( $stCountSql );
		$arTotalCount = $db->fetchByAssoc ( $rsResouCount );
		
		$arClients = $this->bean->process_list_query ( $stSql, $limit );
		
		$lvd = new ListViewData ();
		$module = $_REQUEST ['module'];
		$baseName = $this->bean->object_name;
		$lvd->var_name = $module . '2_' . strtoupper ( $baseName );
		$lvd->var_order_by = $lvd->var_name . '_ORDER_BY';
		$lvd->var_offset = $lvd->var_name . '_offset';
		$lvd->count_query = $stCountSql;
		$count = $lvd->getTotalCount ( $stSql );
		
		$totalCounted = empty ( $sugar_config ['disable_count_query'] );
		$limit = 20;
		
		$nextOffset = - 1;
		$prevOffset = - 1;
		$endOffset = - 1;
		
		if (strcmp ( $offset, 'end' ) == 0) {
			$totalCount = $lvd->getTotalCount ( $stSql );
			$offset = (floor ( ($totalCount - 1) / $limit )) * $limit;
		}
		
		if ($count > $arClients ['next_offset']) {
			$nextOffset = $offset + $limit;
		}
		
		if ($offset > 0) {
			$prevOffset = $offset - $limit;
			if ($prevOffset < 0)
				$prevOffset = 0;
		}
		$totalCount = $count + $offset;
		
		if ($count >= $limit && $totalCounted) {
			$totalCount = $lvd->getTotalCount ( $stSql );
		}
		
		$endOffset = (floor ( ($totalCount - 1) / $limit )) * $limit;
		
		$arClients ['pageData'] ['urls'] = $lvd->generateURLS ( '', $offset, $prevOffset, $nextOffset, $endOffset, $totalCounted );
		
		$arClients ['pageData'] ['offsets'] = array (
				'current' => $offset,
				'next' => $nextOffset,
				'prev' => $prevOffset,
				'end' => $endOffset,
				'total' => $totalCount,
				'totalCounted' => $totalCounted 
		);
		$arClients ['pageData'] ['offsets'] ['lastOffsetOnPage'] = $arClients ['pageData'] ['offsets'] ['current'] + count ( $arClients ['list'] );
		
		// at least one record must have selected to merge
		if (trim ( $stMergeSelectedIds ) != '') {
			$stSelectedWhere = 'accounts.visibility =1 ' . $stMergeSelectedIds;
			$arSql = $obAccounts->create_new_list_query ( ' accounts.name ASC ', $stSelectedWhere, $arFilters, array (), 0, '', 1 );
			
			$stSql = $arSql ['select'] . ' ' . $arSql ['from'] . ' ' . $arSql ['where'] . ' ';
		
			$arMergeClients = $obAccounts->process_list_query ( $stSql, 0,-99,-99);
		}
		
		
		$this->ss->assign ( 'ORDER_NEXT', (trim ( $stOrder ) == 'ASC') ? 'DESC' : 'ASC' );
		$this->ss->assign('APP_STATE_DOMS',$app_list_strings['state_dom']);
		$this->ss->assign ( 'ORDER', trim ( $stOrder ) );
		$this->ss->assign ( 'ORDER_BY', $stOrderBy );
		$this->ss->assign ( 'primary_client', $stPrimaryClient );
		$this->ss->assign ( 'pageData', $arClients ['pageData'] );
		$this->ss->assign ( 'AR_CLIENT_TO_MERGE_IDS', implode ( ',', $arSelectedIds ) );
		$this->ss->assign ( 'AR_CLIENT_TO_MERGE_LIST', $arMergeClients );
		$this->ss->assign ( 'AR_CLIENT_LIST', $arClients ['list'] );
		$this->ss->assign ( 'AR_CLIENT_TOTAL', $arTotalCount ['c'] );
		$this->ss->assign('navStrings',$navStrings);
		
		$this->ss->display ( 'custom/modules/Accounts/tpls/client_serach_and_merge.tpl' );
	}
}

