<?php
require_once 'custom/include/common_functions.php';

class LeadsViewGet_dedupted_bidders extends Viewlist {
	
	function LeadsViewGet_dedupted_bidders() {
		parent::ViewList ();
	}
	
	function display() {
		global $app_list_strings, $db, $current_user, $app_strings, $timedate, $sugar_config, $mod_strings;
		
		############################################################
		#### USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
		############################################################
		global $current_user;
		$stUserFilters = '';
		$filter_apply = false;
		//apply USER Filters if applicable
		if(!$current_user->is_admin){
			$obBidders = new oss_LeadClientDetail();
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			$obAccessFilters = new userAccessFilters();
			//Need to change this property as count query is putting leads
			//$this->table_name = $obBidders->table_name;
		
			$stUserFilters =  $obAccessFilters->getBiddersFilterClause();
			if(!empty($stUserFilters)){
				$filter_apply = true;
			}
			if(is_object($stUserFilters)){
				//do nothing there should be no impact on bidders list
				// if geo location is set as Project location
			}else {
				// get unique bidders [NOTE :: this will be applicable when geo loation is client]
				$arBidderListSql['select'] = str_replace('SELECT ', 'SELECT DISTINCT  oss_leadclientdetail.id, ', $arBidderListSql['select'])  ;
			}
			//$arBidderListSql['where'] .= ' GROUP BY oss_leadclientdetail.id ';
				
				
		}
		$stUserFilters = (!is_object($stUserFilters) && trim($stUserFilters) != '')?$stUserFilters:'';
		###################################################################
		#### END OF USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
		###################################################################
		
		$iOffset = (! isset ( $_REQUEST ['offset'] ) && trim ( $_REQUEST ['offset'] ) == '') ? '0' : $_REQUEST ['offset'];
		
		$iRows = $sugar_config ['list_max_entries_per_page'];
		// check if request is for new bidders
		$iNewBidders = (isset ( $_REQUEST ['new_bidders'] ) && trim ( $_REQUEST ['new_bidders'] )) ? $_REQUEST ['new_bidders'] : 0;
		
		// get total number of bidders
		$stSPGetBiddersCount = 'call get_deduped_bidders("' . $_REQUEST ['record'] . '","' . $iNewBidders . '",1,"' . $iOffset . '","' . $iRows . '", ""," account_name ASC",\''.$stUserFilters.'\',0,"");';
		$rsCountResult = $this->bean->db->query ( $stSPGetBiddersCount );
		
		
		$arTotalResult = $this->bean->db->fetchByAssoc ( $rsCountResult );
		
		$iTotalCount = $arTotalResult ['c'];		
		
		// to free the last result need to reset the connection
		$this->bean->db->disconnect ();
		$this->bean->db->connect ();
		
		// Pagination PARAMS
		$navStrings = array (
				'next' => $app_strings ['LNK_LIST_NEXT'],
				'previous' => $app_strings ['LNK_LIST_PREVIOUS'],
				'end' => $app_strings ['LNK_LIST_END'],
				'start' => $app_strings ['LNK_LIST_START'],
				'of' => $app_strings ['LBL_LIST_OF'] 
		);
		
		$offset = $iOffset;
		$totalCount = $iTotalCount;
		$count = $iTotalCount;
		$nextOffset = - 1;
		$prevOffset = - 1;
		$endOffset = - 1;
		
		if (strcmp ( $offset, 'end' ) == 0) {
			
			$offset = (floor ( ($totalCount - 1) / $iRows )) * $iRows;
		}
		
		if ($count > $offset) {
			$nextOffset = $offset + $iRows;
		}
		
		if ($offset > 0) {
			$prevOffset = $offset - $iRows;
			
			if ($prevOffset < 0)
				$prevOffset = 0;
		}
		
		$endOffset = (floor ( ($totalCount - 1) / $iRows )) * $iRows;
		require_once ("include/ListView/ListViewData.php");
		

		$stOrderType = (isset($_REQUEST['odr']))?$_REQUEST['odr']:'ASC';
		$stSortBy = (isset($_REQUEST['sort']))?$_REQUEST['sort']:'account_name';
		$order_by = $stSortBy.' '.$stOrderType;
		$this->ss->assign('order',(isset($_REQUEST['odr']) && $_REQUEST['odr'] == 'ASC')?'DESC':'ASC');
		
		$lvd = new ListViewData ();
		$arData ['pageData'] ['urls'] = array (
				'baseURL' => 'index.php?module=Leads&action=get_dedupted_bidders&new_bidders='.$iNewBidders.'&record=' . $_REQUEST ['record'] . '&to_pdf=1&offset=' . $nextOffset.'&sort='.$stSortBy.'&odr='.$stOrderType,
				'orderBy' => 'index.php?module=Leads&action=get_dedupted_bidders&new_bidders='.$iNewBidders.'&record=' . $_REQUEST ['record'] . '&to_pdf=1&offset=' . $nextOffset.'&sort='.$stSortBy.'&odr='.$stOrderType,
				'nextPage' => 'index.php?module=Leads&action=get_dedupted_bidders&new_bidders='.$iNewBidders.'&record=' . $_REQUEST ['record'] . '&to_pdf=1&all=1&offset=' . $nextOffset.'&sort='.$stSortBy.'&odr='.$stOrderType,
				'endPage' => 'index.php?module=Leads&action=get_dedupted_bidders&new_bidders='.$iNewBidders.'&record=' . $_REQUEST ['record'] . '&to_pdf=1&offset=' . $endOffset.'&sort='.$stSortBy.'&odr='.$stOrderType,
				'startPage' => 'index.php?module=Leads&action=get_dedupted_bidders&new_bidders='.$iNewBidders.'&record=' . $_REQUEST ['record'] . '&to_pdf=1&offset=0'.'&sort='.$stSortBy.'&odr='.$stOrderType,
				'prevPage' => 'index.php?module=Leads&action=get_dedupted_bidders&new_bidders='.$iNewBidders.'&record=' . $_REQUEST ['record'] . '&to_pdf=1&offset=' . $prevOffset.'&sort='.$stSortBy.'&odr='.$stOrderType 
		);
		
		if ($prevOffset < 0) {
			unset ( $arData ['pageData'] ['urls'] ['startPage'], $arData ['pageData'] ['urls'] ['prevPage'] );
		}
		if ($nextOffset >= $totalCount) {
			unset ( $arData ['pageData'] ['urls'] ['nextPage'], $arData ['pageData'] ['urls'] ['endPage'] );
		}
		
		$arData ['pageData'] ['offsets'] = array (
				'current' => $offset,
				'next' => $nextOffset,
				'prev' => $prevOffset,
				'end' => $endOffset,
				'total' => $totalCount,
				'totalCounted' => $totalCount 
		);
		
		$arData ['pageData'] ['offsets'] ['lastOffsetOnPage'] = ($nextOffset > $totalCount) ? $totalCount : $nextOffset;
		
		//if total count is 0 then reset the pagination params
		if($iTotalCount ==0){
			$arData ['pageData'] ['offsets'] = array (
					'current' => $offset,
					'next' => $nextOffset,
					'prev' => $prevOffset,
					'end' => $endOffset,
					'total' => $totalCount,
					'totalCounted' => $totalCount,
					'lastOffsetOnPage' => 0
			);
		}
		
		$fav_array = array();
		$new_bidder_array = array();
		
			
		
		
		// Get Deduped bidders list
		$stSPGetBidders = 'call get_deduped_bidders("' . $_REQUEST ['record'] . '","' . $iNewBidders . '",0,"' . $iOffset . '","' . $iRows . '","", "'.$order_by.'",\''.$stUserFilters.'\',0,"");';
		
		$rsResult = $GLOBALS ['db']->query ( $stSPGetBidders, false, '', true, true );
		
		while ( $arResult = $GLOBALS ['db']->fetchByAssoc ( $rsResult ) ) {
			
			$arData ['list'] [] = ( object ) $arResult;
			
		}
		
		// to free the last result need to reset the connection
		$this->bean->db->disconnect ();
		$this->bean->db->connect ();
		
		if($current_user->is_admin == '1'){
		    $obAdmin = new Administration ();
		    $arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
		    $obTargetClass = new oss_Classification();
		    $arSelectedClass = $arAdminData->settings['instance_target_classifications'];
		    $arSelectedId = json_decode(base64_decode($arSelectedClass));
		    $stSelectedIds = implode("','",$arSelectedId);
		
		    $classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM config,oss_classification c WHERE  id in ('".$stSelectedIds."')";
		    $classification_filter_result = $db->query($classification_filter_query);
		    $classification_filter_count = $db->getRowCount($classification_filter_result);
		    	
		}else{
		    	
		    $classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM `oss_user_filters` uf INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE `filter_type`='classification'  AND uf.assigned_user_id = '".$GLOBALS['current_user']->id."' AND uf.`deleted`=0";
		    $classification_filter_result = $db->query($classification_filter_query);
		    $classification_filter_count = $db->getRowCount($classification_filter_result);
		}
		for($i = 0; $i < count($arData['list']); $i++ ){
				
			$module = 'Accounts';
		
			$bidder = $arData['list'][$i]->id;
		
			$record = $arData['list'][$i]->account_id;

			//is bidder a fav
			$fav = Favorites::isUserFavorite($module, $record);
			if($fav == 1){
				$fav_array[$bidder] = Favorites::generateStar($fav,$module, $record);
			}else{
				$fav_array[$bidder] = '';
			}
			
			
			if($classification_filter_count > 0){
				while($classification_filter_row = $db->fetchByAssoc($classification_filter_result)){
					$classification_filter_array[$classification_filter_row['id']] = $classification_filter_row['name'];
				}
			
				$classification_filter = implode("','", $classification_filter_array);
				$stGetAccountsClassifications = " SELECT group_concat(oc.description  ORDER BY FIELD(oc.name, '$classification_filter') DESC ) as classifications ";
			}else{
				$stGetAccountsClassifications = ' SELECT group_concat(oc.description ORDER BY oc.description) as classifications ';
			}
			
			$stGetAccountsClassifications .= ' ,accounts.proview_url
														 ,accounts.visibility
														FROM accounts 
														LEFT JOIN oss_classifion_accounts_c  oca ON oca.oss_classid41cccounts_idb = accounts.id AND oca.deleted =0
														LEFT JOIN oss_classification oc ON oc.id=oss_classi48bbication_ida AND oc.deleted=0
														WHERE accounts.id =  '.$db->quoted($record).'  
														GROUP BY  accounts.id ';
														
				
			$rsResult = $db->query($stGetAccountsClassifications);
			$arAccountClsfication = $db->fetchByAssoc($rsResult);
			$arData['list'][$i]->classifications = $arAccountClsfication['classifications'];
			
			$arData['list'][$i]->proview_url = $arAccountClsfication['proview_url'];
			$arData['list'][$i]->account_proview_url=  proview_url(array('url'=>$arData['list'][$i]->proview_url));
			
			$arData['list'][$i]->account_visibility =  $arAccountClsfication['visibility'];
			//if account is not visisble then contact should not be visible
			$arData['list'][$i]->contact_visibility =  $arAccountClsfication['visibility'];			
			 
			//$arData['list'][$i]->account_proview_url = proview_url($arData['list'][$i]->proview_url);
			/*if($arData['list'][$i]->proview_url != ''){
				
				$arData['list'][$i]->account_proview_url =
				'<a href="javascript:void(0)" onclick="window.open(\''.$arData['list'][$i]->proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
				
			}*/
			/*$new_bidder = new oss_LeadClientDetail();
			$new_bidder->retrieve($bidder);
		
			$new_bidder_array[$bidder] = 0;
		*/
			if($arData['list'][$i]->is_viewed == 1){
				$new_bidder_array[$bidder] = 1;
			}
		}
		
		$this->ss->assign ( 'navStrings', $navStrings );
		$this->ss->assign ( 'ALL_BIDDERS', $arData );
		$this->ss->assign ( 'pageData', $arData ['pageData'] );
		$this->ss->assign('FAV',$fav_array);
		$this->ss->assign('NEW_BIDDER',$new_bidder_array);
		$this->ss->assign('LEAD_ID',$_REQUEST['record']);
		$this->ss->assign('filter_apply',$filter_apply);
		
		$this->ss->display ( 'custom/modules/Leads/tpls/bidder_data.tpl' );
	}
}

?>		
