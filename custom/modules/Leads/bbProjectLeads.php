<?php
require_once 'modules/Leads/Lead.php';
require_once ('custom/modules/Users/filters/userAccessFilters.php');
class bbProjectLeads extends Lead
{
    function bbProjectLeads()
    {
        parent::Lead();
    }
    function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false)
    {
        global $current_user, $timedate;
        //Changes made by parveen badoni on 03/07/2014, $arAddtionalJoins defined as array so that it doesnt result in warning when used in implode .
        $arAddtionalJoins = array();
        //echo '<pre>';print_r(func_get_args());echo '</pre>';
        // if request to sort the list through new_total then
        // sort by only new count
        /* if (strstr($order_by, 'new_total')) {
            $order_by = str_replace('new_total', 'new_bidder', $order_by);
        } */
        if (strstr($order_by, 'state')) {
            $order_by = $order_by . ', city ASC';
        }
        
        // remove from project title default search
        if (isset($_REQUEST['project_title_advanced']) && !empty($_REQUEST['project_title_advanced'])) {
            $project_title = $_REQUEST['project_title_advanced'];
           // unset($_REQUEST['project_title_advanced']);
        }
        if (isset($_REQUEST['project_title_basic']) && !empty($_REQUEST['project_title_basic'])) {
            $project_title = $_REQUEST['project_title_basic'];
          //  unset($_REQUEST['project_title_basic']);
        }
        
        //Modified By Mohit Kumar Gupta 28-02-2014
        //default allow only unarchieve leads
        $includeArchieve = true;
        if (isset($_REQUEST['include_archive_open_only_basic']) && $_REQUEST['include_archive_open_only_basic'] == '1' && $_REQUEST['searchFormTab'] == 'basic_search') {
            $includeArchieve = false;
        }else if (isset($_REQUEST['include_archive_open_only_advanced']) && $_REQUEST['include_archive_open_only_advanced'] == '1' && $_REQUEST['searchFormTab'] == 'advanced_search') {
        	$includeArchieve = false;
        }
        
        //check if extra fields are added in filter array   
        
        if(isset($filter['county']) && trim($filter['county']) == '1'){
                    	
        	$arAddtionalSelectFields[] = ' oss_county.name county';
        	$arAddtionalJoins[] = 'LEFT JOIN oss_county  ON leads.county_id = oss_county.id AND oss_county.deleted=0';
        	unset($filter['county']);
        }
        if(isset($filter['created_by_name']) && trim($filter['created_by_name']) == '1'){
             
            $arAddtionalSelectFields[] = " LTRIM(RTRIM(CONCAT(IFNULL(created_user.first_name,''),' ',IFNULL(created_user.last_name,'')))) created_by_name ";
            $arAddtionalJoins[] = ' LEFT JOIN users created_user ON leads.created_by=created_user.id AND created_user.deleted=0 AND created_user.deleted=0';
            unset($filter['created_by_name']);
        }
        if(isset($filter['modified_by_name']) && trim($filter['modified_by_name']) == '1'){
             
            $arAddtionalSelectFields[] = " LTRIM(RTRIM(CONCAT(IFNULL(modified_user.first_name,''),' ',IFNULL(modified_user.last_name,'')))) modified_by_name ";
            $arAddtionalJoins[] = ' LEFT JOIN users modified_user ON leads.created_by=modified_user.id AND modified_user.deleted=0 AND modified_user.deleted=0';
            unset($filter['modified_by_name']);
        }
        
        $stLeadSQL = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect);
        // if its for deduping do not modify the due date
        
        if ($_REQUEST['action'] != 'deduping' && empty($_REQUEST['bids_due_advanced_range_choice'])) {
            // $stLeadSQL['select'] = str_replace('leads.bids_due','(if(countt
            // IS NULL ,bids_due,SUBSTRING(tmp.bids_due_grops, -19, 19)))
            // bids_due',$stLeadSQL['select']);
            $stLeadSQL['select'] = str_replace('leads.bids_due', 'tmp.bids_due_grops bids_due', $stLeadSQL['select']);
        }
        $stDedupingConditions = '';
        
        if ($_REQUEST['action'] == 'deduping') {
            $stDedupingConditions = ' AND leads.id <>"' . $_REQUEST['record'] . '" ';
        }
        
        // add project title full text search
        if (isset($project_title) && !empty($project_title)) {
            
           /* 
            * turn offfull text search
            $stLeadSQL['where'] .= " AND (
						MATCH(project_title) AGAINST('" . $project_title . "' IN BOOLEAN MODE)
					) "; */
           //This is used for mannually created leads 
           $stLeadSQL['where'] .= ' AND ( leads.project_title LIKE ' . $GLOBALS['db']->quoted('%' . $project_title . '%');
           //This is used for leads those were pulled from BBCRM
           $stLeadSQL['where'] .= ' OR leads.project_title LIKE ' . str_replace("&", "&amp;",$GLOBALS['db']->quoted('%' . $project_title . '%')).')';
		    
        }
        
        // ##################################
        // ## USER FILTERS FOR LEADS ########
        // ##################################
        // apply USER Filters if applicable
        if (!$current_user->is_admin) {
            
            $obAccessFilters = new userAccessFilters();
            $arUserFilters = $obAccessFilters->getLeadFilterClause();
            
            if ($arUserFilters != '' && isset($arUserFilters->listview)) {
                $stLeadSQL['from'] .= $arUserFilters->listview->joins;
                $stLeadSQL['where'] .= $arUserFilters->listview->where;
            }
        }
        // #########################################
        // ## END OF USER FILTERS FOR LEADS ########
        // #########################################
        
        // ##########################
        // # SET SEARCH PARAMS ####
        // ##########################
        // BIDS DUE FROM
        if (isset($_REQUEST['bids_due_from_advanced']) && !empty($_REQUEST['bids_due_from_advanced'])) {
            $bids_due_from = trim($_REQUEST['bids_due_from_advanced']);
            $stLeadSQL['where'] .= " AND leads.bids_due >='" . $timedate->to_db($bids_due_from) . "' ";
        }
        // BIDS DUE TO
        if (isset($_REQUEST['bids_due_to_advanced']) && !empty($_REQUEST['bids_due_to_advanced'])) {
            $bids_due_to = trim($_REQUEST['bids_due_to_advanced']);
            $gmt_bids_due_to = $timedate->getDayStartEndGMT($bids_due_to, $current_user);
            $stLeadSQL['where'] .= " AND leads.bids_due <='" . $gmt_bids_due_to['end'] . "'  ";
        }
        
        // add full text saerch condition for scope keyword
        // modified by Mohit Kumar Gupta
        // @date 15 july 2013
        if (isset($_REQUEST['scope_of_work_advanced']) && trim($_REQUEST['scope_of_work_advanced']) != '') {
            $stScopeKeyword = trim($_REQUEST['scope_of_work_advanced']);
           // $stLeadSQL['where'] .= ' AND MATCH (leads.scope_of_work) AGAINST ('.$GLOBALS['db']->quoted($stScopeKeyword).' IN BOOLEAN MODE)';
           $stLeadSQL['where'] .= ' AND leads.scope_of_work  LIKE ' . $GLOBALS['db']->quoted('%' . $stScopeKeyword . '%');
        }
        
        //add condition for is archive to project lead on list view
        //Moiht Kumar Gupta 04-02-2014        
        if($includeArchieve && $_REQUEST['module'] == 'Leads' ){
            $stLeadSQL ['where'] .= " AND (leads.is_archived=0 )";
        }
        
        // PROJECT CLASSIFICATION PARAMS        
        $classification_array = array ();
        $classification_basic = trim($_REQUEST['classification_basic']);
        $classification_advanced = trim($_REQUEST['classification_advanced']);
        
        if (isset($classification_basic) && !empty($classification_basic)) {
            $classification_array[] = $classification_basic;
        } else if (isset($classification_advanced) && !empty($classification_advanced)) {
            $classification_array[] = $classification_advanced;
        }
        
        $classification_array_1 = trim($_REQUEST['classification']);
        $classification_array_2 = trim($_REQUEST['classification_1']);
        $classification_array_3 = trim($_REQUEST['classification_2']);
        
        if (isset($classification_array_1) && !empty($classification_array_1) && ($classification_array_1 != 'Search by Classification..')) {
            $classification_array[] = $classification_array_1;
        }
        if (isset($classification_array_2) && !empty($classification_array_2) && ($classification_array_2 != 'Search by Classification..')) {
            $classification_array[] = $classification_array_2;
        }
        if (isset($classification_array_3) && !empty($classification_array_3) && ($classification_array_3 != 'Search by Classification..')) {
            $classification_array[] = $classification_array_3;
        }
        if (isset($classification_array) && count($classification_array) > 0) {
            $classification_array = " ( lead_search_cls.name = '" . implode("' OR lead_search_cls.name = '", $classification_array) . "' )";
            $classification_search = htmlspecialchars_decode($classification_array);
            
            $stLeadSQL['from'] .= ' INNER JOIN oss_classifcation_leads_c lead_rel_class ON lead_rel_class.oss_classi7103dsleads_idb= childLead.id AND lead_rel_class.deleted =0 
    								INNER JOIN oss_classification lead_search_cls ON lead_rel_class.oss_classi4427ication_ida = lead_search_cls.id AND lead_search_cls.deleted = 0
    								';
            $stLeadSQL['where'] .= ' AND ' . $classification_search;
        }
        
        // add join query for related module based search
        if (!empty($_REQUEST['accounts_advanced']) || !empty($_REQUEST['fav_bidders_only_advanced']) || !empty($_REQUEST['client_classification_advanced']) || !empty($_REQUEST['bidders_role_advanced'])) {
            
            /**
             * Check if admin User or Client Location filters are
             */
            $obAdministration = new Administration();
            $admin = $obAdministration->retrieveSettings('instance', true);
            
            $arAllFilters = userAccessFilters::getCurrentUserFilters();
            
            // if there any filter added for classification
            if (isset($arAllFilters['classification']) && $admin->settings['instance_geo_filter'] == 'client_location') {
                $isClientClassificationFilters = true;
            } else {
                
                $isClientClassificationFilters = false;
            }
            // if geo filter location is Project location then also add joins
            if ($admin->settings['instance_geo_filter'] == 'project_location') {
                $isClientClassificationFilters = false;
            }
            
            if ($current_user->is_admin || !$isClientClassificationFilters) {
               
                
                $stLeadSQL['from'] .= " INNER JOIN	oss_leadclientdetail lcd ON lcd.lead_id = childLead.id 
    											AND lcd.deleted=0
        								INNER JOIN 	accounts lead_accounts ON lead_accounts.id = lcd.account_id 
    											AND lead_accounts.deleted=0 ";
                $stBidderAlias = 'lcd';
                $stBidderAccountAlias = 'lead_accounts';
            } else {
                
                $stBidderAlias = 'bidders';
                $stBidderAccountAlias = 'accounts';
            }
        }
        
        // search with related accounts
        if (!empty($_REQUEST['accounts_advanced'])) {
            $account_name = htmlspecialchars_decode(trim($_REQUEST['accounts_advanced']), ENT_QUOTES);
            $account_name = addslashes($account_name);
            // $stLeadSQL['from'] .= " AND MATCH(lead_accounts.name) AGAINST
            // ('".$account_name."' IN BOOLEAN MODE) ";
            $stLeadSQL['from'] .= " AND lead_accounts.name LIKE '%" . $account_name . "%'";
        }
        // search for favorites bidders
        if (!empty($_REQUEST['fav_bidders_only_advanced'])) {
            $stLeadSQL['from'] .= " INNER JOIN
        		favorites sf ON sf.parent_id = lead_accounts.id AND sf.deleted = 0 AND sf.parent_type = 'Accounts' AND sf.assigned_user_id = '" . $current_user->id . "' ";
        }
        // search project leads whose bidders has the selected classifications
        if (!empty($_REQUEST['client_classification_advanced'])) {
            
            $client_classification = implode("','", $_REQUEST['client_classification_advanced']);
            
            $stLeadSQL['from'] .= "  INNER JOIN oss_classifion_accounts_c as oca ON oca.oss_classid41cccounts_idb = " . $stBidderAccountAlias . ".id and oca.deleted = 0 ";
            $stLeadSQL['where'] .= " AND oca.oss_classi48bbication_ida IN ( '" . $client_classification . "' ) ";
        }
        // bidders role search
        if (!empty($_REQUEST['bidders_role_advanced'])) {
            
            $role_search_array = array ();
            
            $bidders_role = " ( ";
            
            $i = 0;
            
            foreach ($_REQUEST['bidders_role_advanced'] as $bidders_role_advanced) {
                
                if ($i != 0)
                    $bidders_role .= " OR ";
                
                if (trim($bidders_role_advanced) != '') {
                    $bidders_role .= " lcd.role = '" . addslashes($bidders_role_advanced) . "'";
                } else {
                    $bidders_role .= "  ( lcd.role IS NULL ";
                    $bidders_role .= " OR lcd.role = '' ) ";
                }
                
                $i++;
            }
            
            $bidders_role .= " )";
            $stLeadSQL['where'] .= " AND  " . $bidders_role;
        }
        
        $stLeadMainSQL['select'] = $stLeadSQL['select'];
		
		
        
      //  $stLeadMainSQL['select'] = str_replace('leads.','parentLead.',$stLeadMainSQL['select']);
      
        $stLeadMainSQL['select'] = str_replace('SELECT','SELECT  DISTINCT ',$stLeadMainSQL['select']);
		
		
        /*
        $stLeadMainSQL['from'] = ' FROM leads 
    							   INNER JOIN(
    										  SELECT  COALESCE(leads.parent_lead_id,leads.id)  id, sfav.id is_favorite ' . $stLeadSQL['from'] . '  ' . $stLeadSQL['where'] . ' GROUP BY COALESCE(leads.parent_lead_id, leads.id)
    										  )filtered_leads USING(id)
    							   ';*/
        
        $stLeadMainSQL['from'] = 
        str_replace(' FROM leads '
                 ,' FROM leads   LEFT JOIN  leads childLead  ON leads.id = childLead.parent_lead_id and childLead.deleted = 0 '
                ,$stLeadSQL['from']);
				
		
        
        // SQL to get previous bid to count
      /*  $stLeadMainSQL['from'] .= ' LEFT JOIN (
										SELECT
									COALESCE(leads.parent_lead_id,leads.id) prebidleadid
									 ,count(DISTINCT prebid.account_id) prebidcount
								FROM leads
								INNER JOIN oss_leadclientdetail bid on bid.lead_id = leads.id AND bid.deleted=0
								INNER JOIN accounts_opportunities prebid on bid.account_id = prebid.account_id AND prebid.deleted=0
								
    	
								GROUP BY prebidleadid
										)tmpPreBidCounts on tmpPreBidCounts.prebidleadid = leads.id
     
         ';*/
        $stLeadMainSQL['from'] .= ' LEFT JOIN project_lead_lookup lookup ON lookup.project_lead_id = leads.id ';
        
      //  $stLeadMainSQL['from'] .= " LEFT JOIN sugarfavorites sfav ON sfav.module = 'Leads' AND sfav.record_id = leads.id AND sfav.created_by = '{$current_user->id}' AND sfav.deleted = 0";
        
        $stLeadMainSQL['from'] .= ' '.implode(' ', $arAddtionalJoins);
        $stLeadMainSQL['select'] .= count($arAddtionalSelectFields) ?','.implode(', ', $arAddtionalSelectFields):'';
        
        $stLeadMainSQL['select'] .= ',COALESCE(lead_version,1) lead_version
                                     ,if(lead_version > 1,1000000,new_bidder) new_bidder
                                     ,CONCAT(COALESCE(new_bidder,0)," - ",COALESCE(total_bidder,0)) new_total 
                                     ,COALESCE(total_bidder,0) bidders_count
                                     ,COALESCE( lookup.previous_bid_to,0) prev_bid_to
                                     ,online_link_count lead_plans,leads.date_entered
                                     ,getBidsDueDate(first_bid_due_date,first_bid_due_timezone) bids_due_tz
                                     ,leads.change_log_flag';
        
        // $stLeadMainSQL['select'] = str_replace('SELECT',"SELECT DISTINCT
        // leads.id,",$stLeadMainSQL['select']);
        // $stLeadMainSQL['select'] = str_replace(' sfav.id is_favorite',"
        // filtered_leads.is_favorite",$stLeadMainSQL['select'] );
        
        $stLeadMainSQL['where'] .=  $stLeadSQL['where']. $stDedupingConditions;
        $stLeadMainSQL['where'] = str_replace(array(',leads.','(leads.',' leads.'),array(',childLead.','(childLead.',' childLead.'),$stLeadMainSQL['where']);
		
		$stLeadMainSQL['order_by'] = ' ' . $stLeadSQL['order_by'];
		if (strstr($stLeadMainSQL['order_by'], 'new_total')) {
            $stLeadMainSQL['order_by'] = str_replace('new_total', 'new_bidder', $stLeadMainSQL['order_by']);
        }
        
        $stLeadMainSQL['select'] . ' ' . $stLeadMainSQL['from'] . ' ' . $stLeadMainSQL['where'] . ' ' . $stLeadMainSQL['order_by'];
		if ($return_array) {
			return $stLeadMainSQL;
        } else {
			
			return $stLeadMainSQL['select'] . ' ' . $stLeadMainSQL['from'] . ' ' . $stLeadMainSQL['where'] . ' ' . $stLeadMainSQL['order_by'];
        }
	}
    function fill_in_additional_list_fields()
    {
        global $timedate;
        
        $new_total = explode('-', $this->new_total);
        
        $this->bidders_count = trim($new_total[1]);
        
        $this->opportunity_count = $this->prev_bid_to;
        
        $this->lead_plans = (empty($this->lead_plans)) ? '' : ' <div id="urlpln' . $this->id . '" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
                        <a id="pln' . $this->id . '" href="javascript:void(0)"  onclick="javascript:open_urls(event,\'index.php?module=Leads&action=projecturl&record=' . trim($this->id) . '&to_pdf=true&all=1\',\'Online Plans - ' . urlencode($this->project_title) . '\')" onmouseout="return nd();">View</a>
                       ';
        // Convert Bid due date according to time zone
        if (trim($this->bids_due_tz) == '0000-00-00 00:00:00') {
            $this->bids_due_tz = '';
        } else {
            $this->bids_due_tz = $timedate->to_display_date_time($this->bids_due_tz, true, false);
        }
        
        if ($this->lead_version > '1') {
            
            $stNewLink = ($new_total[0] > 0) ? '<a title="New Bidders" id="n' . $this->id . '" href="javascript:void(0)"  onclick="javascript:open_urls(event,\'index.php?module=Leads&action=get_dedupted_bidders&new_bidders=1&record=' . trim($this->id) . '&to_pdf=true&all=1\',' . $this->db->quoted('New Bidders - ' . urlencode($this->project_title)) . ')"  >New</a> - ' : '';
            
            $this->new_total = ' <div id="urln' . $this->id . '" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
	        	<div id="urlt' . $this->id . '" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
	        	' . $stNewLink . '
	        	<a  title="Total Bidders" id="t' . $this->id . '" href="javascript:void(0)"  onclick="javascript:open_urls(event,\'index.php?module=Leads&action=get_dedupted_bidders&total_bidders=1&record=' . trim($this->id) . '&to_pdf=true&all=1\',' . $this->db->quoted('Total Bidders - ' . urlencode($this->project_title)) . ')" >Total</a>
	        	
	        	';
        } else {
            $this->new_total = ' <div id="urln' . $this->id . '" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
        					<div id="urlt' . $this->id . '" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
                        <a title="New Bidders" id="n' . $this->id . '" href="javascript:void(0)"  onclick="javascript:open_urls(event,\'index.php?module=Leads&action=getallbidders&new_bidders=1&record=' . trim($this->id) . '&to_pdf=true&all=1\',' . $this->db->quoted('New Bidders - ' . urlencode($this->project_title)) . ')" onmouseout="return nd();" >' . $new_total[0] . '</a> 
                        - 
                        <a  title="Total Bidders" id="t' . $this->id . '" href="javascript:void(0)"  onclick="javascript:open_urls(event,\'index.php?module=Leads&action=getallbidders&total_bidders=1&record=' . trim($this->id) . '&to_pdf=true&all=1\',' . $this->db->quoted('Total Bidders - ' . urlencode($this->project_title)) . ')"  >' . $new_total[1] . '</a>
                        
                       ';
        }
        
        $this->prev_bid_to = ' <div id="urlprebid' . $this->id . '" style="position: absolute; z-index: 1000; background-image: none;   visibility: visible;"></div>
                        <a id="prebid' . $this->id . '" href="javascript:void(0)"  onclick="javascript:open_urls(event,\'index.php?module=Leads&action=getallbidders&pre_bid=1&record=' . trim($this->id) . '&to_pdf=true\',' . $this->db->quoted('Previous Bid To - ' . urlencode($this->project_title)) . ')" >' . $this->prev_bid_to . '</a>
                       ';
        
        if ($this->valuation == 0) {
            $this->valuation = '';
        }
        
        // Create Hyperlink if change log is present
        if ($this->change_log_flag == 1) {
            if ($this->lead_version > 1) {
                $this->date_modified = "<a href='javascript:void(0);' onclick='javascript:showPopupBidBoard(\"$this->id\");'>$this->date_modified</a>";
            } else {
                $this->date_modified = "<a href='' onclick='open_popup(\"Audit\", \"600\", \"400\", \"&record=$this->id&module_name=Leads\", true, false,  { \"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":[] } ); return false;'>$this->date_modified</a>";
            }
        }
        
        /**
         * use for assign hyper link to lead status and also apply the dedupping logic for the same
         * @modified by Mohit Kumar Gupta
         * @date 21-03-2014
         */
         $redirectUrl = 'index.php?module=Leads&action=deduping&record='.$this->id.'&return_lead_list_view=1';
         $this->status = "<a id='status_".$this->id."' href='".$redirectUrl."'>".$this->status."</a>";
    }
    
    /**
     *
     * @method :lead_to_lead_sql
     *         @purpose : To display subpanel for child project leads
     * @param
     *            s : void
     */
    function lead_to_lead_sql()
    {
        global $current_user;
        
        $obLeads = new Lead();
        $this->table_name = $obLeads->table_name;
        $arSubPanelSQL = $obLeads->create_new_list_query('', '(
                CASE WHEN ld1.parent_lead_id is NULL then
                (leads.parent_lead_id =ld1.id)
                ELSE
                (leads.parent_lead_id =ld1.parent_lead_id OR leads.id =ld1.parent_lead_id  )
                END

                )
                and leads.id <> "' . $this->id . '"
            ', array (), array (), 0, '', 1);
       
        $arSubPanelSQL['from'] = ' '; 
        
        $arSubPanelSQL['select'] = 'SELECT DISTINCT leads.id, leads.*, getBidsDueDate(leads.bids_due,leads.bid_due_timezone) bids_due_tz';
        
        $arSubPanelSQL['from'] .= ' FROM leads  
                LEFT JOIN users on users.id = leads.assigned_user_id and users.deleted = 0';
        
        $arSubPanelSQL['where'] = ' WHERE (leads.parent_lead_id = "' . $this->parent_lead_id . '" AND leads.id <> "' . $this->id . '" )';
        
        // ##########################################################
        // ## USER FILTERS FOR PROJECT LEADS SUBPANEL ########
        // ##########################################################
        // apply USER Filters if applicable
        if (!$current_user->is_admin) {
            
            require_once ('custom/modules/Users/filters/userAccessFilters.php');
            $obAccessFilters = new userAccessFilters();
            $obUserFilters = $obAccessFilters->getLeadFilterClause();
            if ($obUserFilters != '' && isset($obUserFilters->listview)) {
                // $arSubPanelSQL['from'] = $arSubPanelSQL['from'].' LEFT JOIN (
                // '.$arUserFilters . ' )uFilter on uFilter.id = leads.id ';
                // $arSubPanelSQL['where'] .= ' AND (uFilter.id IS NOT NULL OR
                // leads.assigned_user_id = "'.$current_user->id.'" ) ' ;
                $arSubPanelSQL['from'] .= $obUserFilters->listview->joins;
                $arSubPanelSQL['where'] .= $obUserFilters->listview->where;
            }
            // echo '<pre>'. $arSubPanelSQL['select'] . ' ' .
            // $arSubPanelSQL['from'] . ' ' . $arSubPanelSQL['where'].'
            // '.$arSubPanelSQL['order_by'];echo '</pre>';
        }
        // ##########################################################
        // ## END OF USER FILTERS FOR PROJECT LEADS SUBPANEL ########
        // ##########################################################
        
         $stSubPanelSQL = $arSubPanelSQL['select'] . ' ' . $arSubPanelSQL['from'] . ' ' . $arSubPanelSQL['where'];
        
        return $stSubPanelSQL;
    }
    
    /**
     *
     * @method : getLeadBidderListSubpanel
     *         @purpose : Function to list bidders list and if Geo Filters are
     *         set as
     *         Client location then to apply filters for accounts in bidder list
     *        
     *        
     */
    function getLeadBidderListSubpanel()
    {
        global $current_user, $db;
        
        ##########################################################
        ## USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
        ##########################################################
        
        $stUserFilters = '';
        // apply USER Filters if applicable
        if (!$current_user->is_admin) {
            require_once ('custom/modules/Users/filters/userAccessFilters.php');
            $obAccessFilters = new userAccessFilters();
            // Need to change this property as count query is putting leads
            $this->table_name = $obBidders->table_name;
            
            $stUserFilters = $obAccessFilters->getBiddersFilterClause();
            if (is_object($stUserFilters)) {
                // do nothing there should be no impact on bidders list
                // if geo location is set as Project location
            } else {
                // get unique bidders [NOTE :: this will be applicable when geo
                // loation is client]
                $arBidderListSql['select'] = str_replace('SELECT ', 'SELECT DISTINCT  oss_leadclientdetail.id, ', $arBidderListSql['select']);
            }
            // $arBidderListSql['where'] .= ' GROUP BY oss_leadclientdetail.id
            // ';
        }
        #################################################################
        ## END OF USER FILTERS FOR BIDDERS LIST IN LEAD SUBPANEL ########
        #################################################################
        
        // create ref bidders
        $stLeadId = ($this->parent_lead_id == '') ? $this->id : $this->parent_lead_id;
        
        // check lead version for this project lead
        $rsGetLeadVersion = $db->query('SELECT lead_version FROM project_lead_lookup WHERE project_lead_id="' . $stLeadId . '"');
        $arLeadVersion = $db->fetchByAssoc($rsGetLeadVersion);
        
        if (isset($arLeadVersion['lead_version']) && $arLeadVersion['lead_version'] > 1) {
            $stFilterJoin .= (!is_object($stUserFilters) && trim($stUserFilters) != '') ? $stUserFilters : '';
            /**
             * if action is subPanelViewer then don't
             * apply the deduping logic for other action
             * apply the deduping logic
             */
            if ($_REQUEST['to_pdf'] == true && trim($_SESSION['bidders_sql' . $this->id]) != '') {
                
                $arResult['SQL_QUERY'] = $_SESSION['bidders_sql' . $this->id];
                
            } else {
                
                $stSPGetBiddersCount = 'call get_deduped_bidders("' . $stLeadId . '","0",0,"0","0", ""," account_name ASC",\'' . $stFilterJoin . '\',1,"' . $current_user->id . '");';
                $rsResult = $db->query($stSPGetBiddersCount);
                $arResult = $db->fetchByAssoc($rsResult);
                $_SESSION['bidders_sql' . $this->id] = $arResult['SQL_QUERY'];
            }
            
            $db->disconnect();
            $db->connect();
            $arSearchWords = array (
                    '&quot;' 
            );
            $arReplaceWords = array (
                    "'" 
            );
            $stSQL = str_replace($arSearchWords, $arReplaceWords, $arResult['SQL_QUERY']);
        } else {
            
           // $obBidders = new oss_LeadClientDetail();
           // $arBidderListSql = $obBidders->create_new_list_query('', '', array (), array (), 0, '', 1);
           /* echo '<pre>';
            print_r($arBidderListSql);
            echo '</pre>';
           */
            
            $arBidderListSql['select'] = "SELECT oss_leadclientdetail . *,   
                                                 oss_leadclientdetail.opportunity_id,    
                                                 accounts.proview_url account_proview_url,    
                                                 accounts.visibility account_visibility,
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
            $arBidderListSql['from'] = "FROM oss_leadclientdetail ";
            
            $arBidderListSql['select'] .= ',accounts.first_classification classifications,accounts.name lcd_account,CONCAT(COALESCE(CONCAT(accounts.billing_address_city," / "),""),accounts.billing_address_state) AS city_state';
            $arBidderListSql['from'] .= ' LEFT JOIN accounts  ON oss_leadclientdetail.account_id = accounts.id AND accounts.deleted=0
        LEFT JOIN contacts contacts ON oss_leadclientdetail.contact_id = contacts.id AND contacts.deleted=0 ';
            // get leads table alias
            $arTmpAliases = explode(' ', $arBidderListSql['from']);
            foreach ($arTmpAliases as $iKey => $stValue) {
                if ($stValue == 'leads') {
                    $stLeadsAlias = $arTmpAliases[$iKey + 1];
                }
            }
            $stParentProjectLeadid = ($this->parent_lead_id == '') ? $this->id : $this->parent_lead_id;
          /*  if ($stLeadsAlias == '') {
                $stLeadsAlias = ' leads';
                $arBidderListSql['from'] .= ' LEFT JOIN leads on oss_leadclientdetail.lead_id = leads.id and leads.deleted=0 ';
            }
            $stParentProjectLeadid = ($this->parent_lead_id == '') ? $this->id : $this->parent_lead_id;
            $arBidderListSql['where'] .= " AND ( COALESCE($stLeadsAlias.parent_lead_id,$stLeadsAlias.id)= '" . $stParentProjectLeadid . "' AND oss_leadclientdetail.deleted=0)";
            */
            
            $arBidderListSql['where'] = " WHERE ( oss_leadclientdetail.lead_id= '" . $stParentProjectLeadid . "' AND oss_leadclientdetail.deleted=0)";
            
            $arBidderListSql['from'] .= (!is_object($stUserFilters) && trim($stUserFilters) != '') ? $stUserFilters : '';
            
            $stSQL = $arBidderListSql['select'] . ' ' . $arBidderListSql['from'] . ' ' . $arBidderListSql['where'];
        }
        
        return $stSQL;
    }
    /**
     * Function to list parent opportunities in Leads subpanel
     */
    function getParentOpportunitiesSubpanel()
    {
        require_once 'custom/modules/Opportunities/OpportunitySummary.php';
        
        $obOpp = new OpportunitySummary();
        $stSql = $obOpp->create_new_list_query('', '', '', '', '', '', true);
        
        $stSql['where'] .= ' AND opportunities.project_lead_id = "' . $this->id . '" ';
        $stReturnSql = $stSql['select'] . ' ' . $stSql['from'] . ' ' . $stSql['where'];
        // echo '<pre>';print_R($stReturnSql);echo '</pre>';
        return $stReturnSql;
    }
    /**
     * Function to list all the related sub Opportunities
     */
    function getSubOpportunitiesSubpanel()
    {
        global $current_user;
        
        $obOpportunities = new Opportunity();
        
        $arSql = $obOpportunities->create_new_list_query("", "", array (), array (
                'joined_tables' => array (
                        'accounts_opportunities' 
                ) 
        ), 0, '', 1);
        
        $arSql['where'] .= ' AND (opportunities.parent_opportunity_id IS NOT NULL 
    							  AND opportunities.project_lead_id= "' . $this->id . '"
    							  
    							) ';
        $arSql['select'] .= ' ,accounts.name account_name,accounts.id account_id ';
        $arSql['from'] .= ' LEFT JOIN accounts_opportunities on opportunities.id = accounts_opportunities.opportunity_id AND accounts_opportunities.deleted=0
				LEFT JOIN accounts on accounts.id = accounts_opportunities.account_id AND accounts.deleted =0 ';
        // Removed Filter Temorary.
        // ##########################################
        // ## USER FILTERS FOR OPPORTUNITIES ########
        // ##########################################
        global $current_user;
        // apply USER Filters if applicable
        /*
         * if(!$current_user->is_admin){
         * require_once('custom/modules/Users/filters/userAccessFilters.php');
         * $obAccessFilters = new userAccessFilters(); $obUserFilters =
         * $obAccessFilters->getOpporutnityFilterWehreClause(); //echo
         * '<pre>';print_r($obUserFilters);die; if($obUserFilters != '' &&
         * isset($obUserFilters->summaryview )) { //remove assigned user record
         * filter $obUserFilters->listview->where = str_replace('OR
         * opportunities.assigned_user_id = "'.$current_user->id.'"', '',
         * $obUserFilters->listview->where); //$arSql['from'] = $arSql['from'].'
         * '.$arUserFilters ; $arSql['from'] .= '
         * '.$obUserFilters->summaryview->joins; $arSql['where'] .=
         * $obUserFilters->summaryview->where .' GROUP BY opportunities.id '; }
         * }
         */
        // ##############################################
        // ## EOF USER FILTERS FOR OPPORTUNITIES ########
        // ##############################################
        $stReturnSql = $arSql['select'] . ' ' . $arSql['from'] . ' ' . $arSql['where'];
        return $stReturnSql;
    }
    /**
     * Function to list all the leads Project URLs
     */
    function get_leads_online_plans()
    {
        global $db;
        $order_by = '';
        $where = '';
        $stParentLeadId = ($this->parent_lead_id != '') ? $this->parent_lead_id : $this->id;
        
        // check if project lead is deduped
        if ($this->parent_lead_id != $this->id  && $this->parent_lead_id != '') {
            /*$obLeads = new Lead();
            $this->table_name = $obLeads->table_name;
            $stChildLeadCountSql = $obLeads->create_list_count_query($obLeads->create_new_list_query('', ' leads.parent_lead_id = "' . $this->id . '"', array (), array (
                    'distinct' => 1 
            )));
            $rsResult = $db->query($stChildLeadCountSql);
            $arChildCount = $db->fetchByAssoc($rsResult);
            
            $isDeduped = ($arChildCount['c'] > 0);
            */
            
            // if parent lead id exists then its a deduped lead
            $isDeduped = 1;
        } else {
            // if parent lead id matches then its not a deduped lead
            $isDeduped = 0;
        }
        
        // get all the Project URLs
        $obOnlinePlans = new oss_OnlinePlans();
        $this->table_name = $obOnlinePlans->table_name;
        
        $arSql = $obOnlinePlans->create_new_list_query($order_by, $where, array (), array (
                'distinct' => 1 
        ), 0, '', 1);
        // add leads
        $arSql['from'] .= ' INNER JOIN leads on oss_onlineplans.lead_id = leads.id AND leads.deleted =0 ';
        $arSql['where'] .= ' AND leads.parent_lead_id = "' . $stParentLeadId . '"';
        // if lead is not dedduped then show all URLS
        if ($isDeduped) {
            $arSql['where'] .= ' GROUP BY REPLACE(REPLACE(REPLACE(oss_onlineplans.description,"http://www." ,""),"www.",""),"http://","")';
        }
         $stSql = $arSql['select'] . $arSql['from'] . ' ' . $arSql['where'];
        
        return $stSql;
    }
    /**
     * Overridden function _get_num_rows_in_query
     * to fix the count issue in online plans subpanel
     *
     * @see SugarBean::_get_num_rows_in_query()
     */
    function _get_num_rows_in_query($query, $is_count_query = false)
    {
        $num_rows_in_query = 0;
        if (!$is_count_query) {
            $count_query = SugarBean::create_list_count_query($query);
        } else {
            $count_query = $query;
            /**
             * Modification for online plans COUNT SQL for Leads subpanel
             * count(DISTINCT oss_onlineplans.id) is creating problems in
             * pagination count
             * hence replaced with
             * count(DISTINCT oss_onlineplans.id)', 'count(DISTINCT
             * REPLACE(REPLACE(REPLACE(oss_onlineplans.description,"http://www."
             * ,""),"www.",""),"http://",""))
             */
            if (stristr($count_query, 'count(DISTINCT oss_onlineplans.id)')) {
                $count_query = str_replace('count(DISTINCT oss_onlineplans.id)', 'count(DISTINCT REPLACE(REPLACE(REPLACE(oss_onlineplans.description,"http://www." ,""),"www.",""),"http://","")) ', $count_query);
            }
        }
        
        $result = $this->db->query($count_query, true, "Error running count query for $this->object_name List: ");
        $row_num = 0;
        
        while ($row = $this->db->fetchByAssoc($result, true)) {
            
            $num_rows_in_query += current($row);
        }
        
        return $num_rows_in_query;
    }
    
    /**
     * Overwrite Export Query
     * @modified By Mohit Kumar Gupta
     * @date 08/07/2014
     * @see Lead::create_export_query()
     */
    function create_export_query(&$order_by, &$where, $relate_link_join=''){
        //add current post to request array in case of export data on selection of select all
        //Modified By mohit Kumar Gupta 11-Aug-2014
        if (!empty($_REQUEST['current_post'])) {
            $currentPost = unserialize(base64_decode($_REQUEST['current_post']));
            $_REQUEST = array_merge($_REQUEST, $currentPost);
        }
    
        //Set Appropriate where clause        
        $filter = array('county' => '1');
        
        //SQL for export        
        $stLeadSQL = self::create_new_list_query($order_by, $where, $filter, array(), 0, '', true, $this, true);
        $stLeadSQL['select'] = "SELECT DISTINCT
                leads.id,
                leads.project_title,
                leads.state,
                leads.city,
                leads.date_modified,
                leads.status,
                leads.project_lead_id,
                leads.received,
                leads.address,
                leads.structure,
                leads.type,
                leads.owner,
                leads.zip_code,
                leads.pre_bid_meeting,
                leads.start_date,
                leads.asap,
                leads.end_date,
                leads.contact_no,
                leads.bid_due_timezone,
                leads.valuation,
                leads.union_c,
                leads.non_union,
                leads.prevailing_wage,
                leads.number_of_buildings,
                leads.square_footage,
                leads.stories_above_grade,
                leads.stories_below_grade,
                leads.scope_of_work,
                leads.description,
                leads.date_entered,
                leads.project_status,
                leads.team_id,
                leads.team_set_id,
                tj.name as team_name,
                LTRIM(RTRIM(CONCAT(IFNULL(jt2.first_name, ''),
                                        ' ',
                                        IFNULL(jt2.last_name, '')))) assigned_by_name,
                oss_county.name county,
                LTRIM(RTRIM(CONCAT(IFNULL(jt1.first_name, ''),
                                        ' ',
                                        IFNULL(jt1.last_name, '')))) created_by_name,
                LTRIM(RTRIM(CONCAT(IFNULL(jt10.first_name, ''),
                                        ' ',
                                        IFNULL(jt10.last_name, '')))) modified_by_name,
                COALESCE(lead_version, 1) lead_version,
                if(lead_version > 1,
                    1000000,
                    new_bidder) new_bidder,
                CONCAT(COALESCE(new_bidder, 0),
                        ' - ',
                        COALESCE(total_bidder, 0)) new_total,
                COALESCE(total_bidder, 0) bidders_count,
                COALESCE(lookup.previous_bid_to, 0) prev_bid_to,
                getBidsDueDate(first_bid_due_date,
                        first_bid_due_timezone) bids_due_tz";
        $stExportSql = $stLeadSQL['select'] . ' ' . $stLeadSQL['from'] . ' ' . $stLeadSQL['where'] . ' ' . $stLeadSQL['order_by'];
        return $stExportSql;
    }
}

?>
