<?php
require_once 'include/MVC/View/views/view.list.php';
require_once('include/ListView/ListViewSmarty.php');
require_once('custom/modules/Users/filters/userAccessFilters.php');
require_once("include/ListView/ListViewData.php");


class LeadsViewGetallbidders extends Viewlist {

	function LeadsViewGetallbidders() {
		parent::ViewList();
	}
	
	function display(){
	    global $app_list_strings,$db,$current_user,$app_strings,$timedate,$sugar_config,$mod_strings;

	    
	    $obBidders = new oss_LeadClientDetail();
	    
	    $arBidderListSql['select'] = "SELECT oss_leadclientdetail . *,
                                                 oss_leadclientdetail.opportunity_id,
                                                 accounts.proview_url account_proview_url,
                                                 accounts.visibility account_visibility,
	                                             contacts.visibility   contact_visibility,
                                                 accounts.name account_name,
                                                 oss_leadclientdetail.contact_id,
                                                 LTRIM(RTRIM(CONCAT(IFNULL(contacts.first_name, ''),
                                                 ' ',IFNULL(contacts.last_name, '')))) contact_name,
                                                 accounts.first_classification classifications,
                                                 accounts.name lcd_account,
                                                 CONCAT(COALESCE(CONCAT(accounts.billing_address_city, ' / '),
                                                                ''),
                                                        accounts.billing_address_state) AS city_state
                                                ";
	    $arBidderListSql['from'] = "FROM oss_leadclientdetail 
	                                INNER JOIN leads on leads.id = oss_leadclientdetail.lead_id";

	    $arBidderListSql['select'].= ',accounts.first_classification classifications,accounts.name lcd_account,CONCAT(COALESCE(CONCAT(accounts.billing_address_city," / "),""),accounts.billing_address_state) AS city_state';
	    $arBidderListSql['from'] .= ' LEFT JOIN accounts  ON oss_leadclientdetail.account_id = accounts.id AND accounts.deleted=0
                                      LEFT JOIN contacts contacts ON oss_leadclientdetail.contact_id = contacts.id AND contacts.deleted=0 
                                      ';
	    
	    //get all bidders specific to this lead
	    $arBidderListSql['where'] =  " WHERE  COALESCE(leads.parent_lead_id, leads.id) ='".$_REQUEST['record']."'" ;
	    
	    //check to list new bidders only 
	    if(isset($_REQUEST['new_bidders'])){
	        
	        $arBidderListSql['where'] .= ' AND oss_leadclientdetail.is_viewed = "0" ';
	    }
	    
	    //bidders client having opportunity
	    if(isset($_REQUEST['pre_bid'])){
	        
	        $stJoin = "SELECT
								 bidders.id id
							FROM leads
							INNER  JOIN oss_leadclientdetail bidders on leads.id = lead_id AND bidders.deleted =0
							INNER JOIN accounts_opportunities prebid on prebid.account_id = bidders.account_id and prebid.deleted=0
							WHERE COALESCE(leads.parent_lead_id,leads.id) ='".$_REQUEST['record']."'						
							GROUP BY COALESCE(leads.parent_lead_id,leads.id),prebid.account_id,bidders.id ";
	    
	    
	        $arBidderListSql['from'] .= ' INNER JOIN ( '.$stJoin.' ) TMPBIDS on TMPBIDS.id = oss_leadclientdetail.id';
	    }
	    
	    ############################################################
	    #### USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
	    ############################################################
	    
	    $stUserFilters = '';
	    $filter_apply = false;
	    //apply USER Filters if applicable
	    if(!$current_user->is_admin){
	        $is_admin = false;	    
	        $obAccessFilters = new userAccessFilters();
	        //Need to change this property as count query is putting leads
	        $this->table_name = $obBidders->table_name;
	        	
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
	  
	    $arBidderListSql['from'] .= (!is_object($stUserFilters) && trim($stUserFilters) != '')?$stUserFilters:'';
	    ###################################################################
	    #### END OF USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
	    ###################################################################
	    
	    //Pagination Vars 
	    $navStrings = array('next' => $app_strings['LNK_LIST_NEXT'],
	            'previous' => $app_strings['LNK_LIST_PREVIOUS'],
	            'end' => $app_strings['LNK_LIST_END'],
	            'start' => $app_strings['LNK_LIST_START'],
	            'of' => $app_strings['LBL_LIST_OF']);
	    $limit = 5;
	    
	    if(isset($_REQUEST['Leads2_OSS_LEADCLIENTDETAIL_offset']) && trim($_REQUEST['Leads2_OSS_LEADCLIENTDETAIL_offset']) !='')
	    {
	        $limit = $_REQUEST['Leads2_OSS_LEADCLIENTDETAIL_offset'];
	        $offset = $_REQUEST['Leads2_OSS_LEADCLIENTDETAIL_offset'];
	    
	    }else{
	        $limit = 0;
	        $offset = 0;
	    }
	    
	    //set Order By 
	    $stOrderType = (isset($_REQUEST['odr']))?$_REQUEST['odr']:'ASC';
	    $stSortBy = (isset($_REQUEST['sort']))?$_REQUEST['sort']:'account_name';
	    $order_by = $stSortBy.' '.$stOrderType;
	    $arBidderListSql['order_by'] = ' ORDER BY '.$order_by; 
	    
	    $stBiddersSQL = implode(' ', $arBidderListSql);
	    $count_query = $arBidderListSql;
            $count_query['select'] = " select count(distinct(oss_leadclientdetail.id)) c ";
            $count_query = implode(' ',$count_query);
	    
	    
	    //execute Bidders SQL
	    $data = $obBidders->process_list_query($stBiddersSQL,$limit);
	    
	    	    	
	    $fav_array = array();
	    $new_bidder_array = array();	    	
	    
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
	        	
	        $classification_filter_query = "SELECT DISTINCT(c.name),c.id FROM `oss_user_filters` uf INNER JOIN oss_classification c ON c.id=uf.filter_value WHERE `filter_type`='classification' AND uf.assigned_user_id = '".$GLOBALS['current_user']->id."' AND uf.`deleted`=0 ";
	        $classification_filter_result = $db->query($classification_filter_query);
	        $classification_filter_count = $db->getRowCount($classification_filter_result);
	    }
	    
	    for($i = 0; $i < count($data['list']); $i++ ){
	        	
	        $module = 'Accounts';
	    
	        $bidder = $data['list'][$i]->id;
	    
	        $record = $data['list'][$i]->account_id;
	    
	    
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
	            $stGetAccountsClassifications = 'SELECT group_concat(oc.description ORDER BY oc.description) as classifications ';
	        }
	    
	        $stGetAccountsClassifications .= ' ,accounts.proview_url
														 ,accounts.visibility
														FROM accounts
														LEFT JOIN oss_classifion_accounts_c  oca ON oca.oss_classid41cccounts_idb = accounts.id AND oca.deleted =0
														LEFT JOIN oss_classification oc ON oc.id=oss_classi48bbication_ida AND oc.deleted=0
														WHERE accounts.id =  '.$db->quoted($record).'
														GROUP BY  accounts.id
														 ';
	    
	    
	        $rsResult = $db->query($stGetAccountsClassifications);
	        $arAccountClsfication = $db->fetchByAssoc($rsResult);
	       
	    
	        $data['list'][$i]->classifications = $arAccountClsfication['classifications'];
	    
	        $data['list'][$i]->proview_url=  $arAccountClsfication['proview_url'];
	        $data['list'][$i]->account_visibility=  $arAccountClsfication['visibility'];
	    
	        $is_viewed_query = " SELECT is_viewed FROM oss_leadclientdetail  WHERE id = '".$bidder."' ";
	        $is_viewed_result = $db->query($is_viewed_query);
	        $is_viewed_row = $db->fetchByAssoc($is_viewed_result);
	        	
	        $new_bidder_array[$bidder] = $is_viewed_row['is_viewed'];
	        	
	    }
	    
	    /****PAGINATION DATA******/

	    $lvd = new ListViewData();
	    $module = $_REQUEST['module'];
	    $baseName = $obBidders->object_name;
	    $lvd->var_name =  $module .'2_'. strtoupper($baseName);
	    $lvd->var_order_by = $lvd->var_name .'_ORDER_BY';
	    $lvd->var_offset = $lvd->var_name . '_offset';
	    $lvd->count_query = $count_query;
	    $count = $lvd->getTotalCount($stBiddersSQL);
	    
	    $limit =  $sugar_config['list_max_entries_per_page'];
	    $totalCounted = empty($sugar_config['disable_count_query']);
	    
	    $nextOffset = -1;
	    $prevOffset = -1;
	    $endOffset = -1;
	    
	    if(strcmp($offset, 'end') == 0){
	        $totalCount = $lvd->getTotalCount($stBiddersSQL);
	        $offset = (floor(($totalCount -1) / $limit)) * $limit;
	    }
	    
	    if($count > $data['next_offset']) {
	        $nextOffset = $offset + $limit;
	    }
	    
	    if($offset > 0) {
	        $prevOffset = $offset - $limit;
	        if($prevOffset < 0)$prevOffset = 0;
	    }
	    $totalCount = $count + $offset;
	    
	    if( $count >= $limit && $totalCounted){
	        $totalCount  = $lvd->getTotalCount($stBiddersSQL);
	    }
	    
	    $endOffset = (floor(($totalCount - 1) / $limit)) * $limit;
	    
	    $queries = $lvd->generateQueries($order_by, $offset, $prevOffset, $nextOffset,  $endOffset, $totalCounted);
	    $data['pageData']['urls'] = $lvd->generateURLS($queries);
	    
	    $data['pageData']['offsets'] = array( 'current'=>$offset, 'next'=>$nextOffset, 'prev'=>$prevOffset, 'end'=>$endOffset, 'total'=>$totalCount, 'totalCounted'=>$totalCounted);
	    $data['pageData']['offsets']['lastOffsetOnPage'] = $data['pageData']['offsets']['current'] + count($data['list']);
	    
	    $this->ss->assign('order',(isset($_REQUEST['odr']) && $_REQUEST['odr'] == 'ASC')?'DESC':'ASC');
	    $this->ss->assign('navStrings',$navStrings);
	    $this->ss->assign('ALL_BIDDERS',$data);
	    $this->ss->assign('FAV',$fav_array);
	    $this->ss->assign('NEW_BIDDER',$new_bidder_array);
	    $this->ss->assign('pageData', $data['pageData']);
	    $this->ss->assign('MOD',$mod_strings);
	    $this->ss->assign('LEAD_ID',$_REQUEST['record']);
	    	
	    $this->ss->assign('filter_apply',$filter_apply);	    
	    
	    $this->ss->display('custom/modules/Leads/tpls/bidder_data.tpl');
	    
	}
	
}

?>
