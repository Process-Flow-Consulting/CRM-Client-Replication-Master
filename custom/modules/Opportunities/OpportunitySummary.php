<?php

require_once 'modules/Opportunities/Opportunity.php';
class OpportunitySummary extends Opportunity {
	
	function __construct() {
		parent::Opportunity ();
		
	}
	
	function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false) {
		
	    $addSubOppJoin = true;
		//if client name in display column of layout options start
		//@modified by Mohit Kumar Gupta
		//@date 25 Nov 2013
		$displayColumns = explode("|",$_REQUEST['displayColumns']);
		$_REQUEST['displayAccountName'] = false;
		if (in_array("ACCOUNT_NAME", $displayColumns)) {
			$_REQUEST['displayAccountName'] = true;
		}
		//if client name in display column of layout options start
		//@modified by Mohit Kumar Gupta
		//@date 25 Nov 2013
		
		// remove from opportunities default search
		if (isset($_REQUEST['name_advanced']) && !empty($_REQUEST['name_advanced'])) {
		    $stOpportunityName  = $_REQUEST['name_advanced'];
		    //unset($_REQUEST['name_advanced']);
		}
		if (isset($_REQUEST['name_basic']) && !empty($_REQUEST['name_basic'])) {
		    $stOpportunityName = $_REQUEST['name_basic'];
		    //unset($_REQUEST['name_basic']);
		}
            
        // check if zone name is added in search criteria
        if ((isset($_REQUEST['zone_name_advanced']) && count($_REQUEST['zone_name_advanced']) >0  )) {
           
            $arAllSelectedZones = $_REQUEST['zone_name_advanced'];           
            //unset($_REQUEST['zone_name_advanced']);
        }
        // check if zone name is passed from zone report 
        if(isset($_REQUEST['zone_name']) && trim($_REQUEST['zone_name']) != ''  ){
            $arAllSelectedZones = array($_REQUEST['zone_name']);
            //unset($_REQUEST['zone_name_advanced']);
        } 
        
        /**
         * add distict project opportunity validation either on client sales stage search or client name search 
         * @modified By Mohit Kumar Gupta
         * @date 14-04-2014
         */
        if((isset($_REQUEST['client_sales_stage_advanced']) && !empty($_REQUEST['client_sales_stage_advanced'])) || (isset($_REQUEST['account_name_advanced']) && !empty($_REQUEST['account_name_advanced']))){
            $params['distinct'] = 1;
        } 
               
		$oppSql = parent::create_new_list_query ( $order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect );
		
		if(isset($_REQUEST['account_name_advanced']) && !empty($_REQUEST['account_name_advanced'])){
			$oppSql['from'] = "FROM opportunities
					LEFT JOIN
				opportunities sub_opp ON sub_opp.parent_opportunity_id=opportunities.id
			        LEFT JOIN
			    accounts_opportunities jtl0 ON sub_opp.id = jtl0.opportunity_id
			        AND jtl0.deleted = 0
			        LEFT JOIN
			    accounts accounts ON accounts.id = jtl0.account_id
			        AND accounts.deleted = 0
			        AND accounts.deleted = 0
			        LEFT JOIN
			    sugarfavorites sfav ON sfav.module = 'Opportunities'
			        AND sfav.record_id = opportunities.id
			        AND sfav.created_by = '1'
			        AND sfav.deleted = 0";
			$addSubOppJoin = false;
		}	
		
		if(empty($_REQUEST['searchFormTab']) && ($_REQUEST['module'] == 'Opportunities')){
//			$oppSql['where'] .= " AND opportunities.sales_stage in ('Qualification','Estimating', 'Follow Up') ";
		}
		$oppSql['select'] .= ' ,oss_county.name lead_county_name'; 
		$oppSql['from'] .= ' LEFT JOIN oss_county on opportunities.lead_county = oss_county.id AND oss_county.deleted =0 ';
		
		if (isset($arAllSelectedZones) && count($arAllSelectedZones) > 0) {
            $oppSql['from'] .= ' LEFT JOIN oss_zone_opportunities_1_c  zonerel ON  oss_zone_opportunities_1opportunities_idb = opportunities.id AND zonerel.deleted =0
                                 LEFT JOIN oss_zone  ON zonerel.oss_zone_opportunities_1oss_zone_ida=oss_zone.id AND oss_zone.deleted =0     ';
            $oppSql['where'] .= ' AND IF(oss_zone.name IS NULL,"Others",oss_zone.name) IN("' . implode('","', $arAllSelectedZones) . '")';
        }
		
		
		// add opportunities full text search
		if (isset($stOpportunityName) && !empty($stOpportunityName)) {
		
		   /*
		    * turn off full text search 
		     $oppSql['where'] .= " AND (
						MATCH(opportunities.name) AGAINST('" . $stOpportunityName . "' IN BOOLEAN MODE)
					) ";
			*/
		    $oppSql['where'] .= " AND ( opportunities.name LIKE '%".$stOpportunityName."%' ) ";
		}
			
		if(isset($_REQUEST['from_bean'])){			
			$oppSql['where'] .= " AND opportunities.parent_opportunity_id IS NOT NULL";
		}else{
			$oppSql ['select'] .= " ,if(opportunities.sub_opp_count IS NOT 	NULL ,opportunities.sub_opp_count,0) clients ";
			$oppSql ['where'] .= " AND (opportunities.parent_opportunity_id IS NULL OR opportunities.parent_opportunity_id = '')";
		}
		$oppSql['select'] .= " ,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz ";
		
		if( !isset($filter['include_archive_open_only']) && $_REQUEST['module'] == 'Opportunities' ){
		    $oppSql ['where'] .= " AND (opportunities.is_archived=0 )"; 
		}	

		/**
		 * add client sales stage condition and group by for collecting one project opportunity only once
		 * @modified By Mohit Kumar Gupta
		 * @date 31-03-2014
		 */
		if(isset($_REQUEST['client_sales_stage_advanced']) && !empty($_REQUEST['client_sales_stage_advanced'])){
		    if ($addSubOppJoin) {
		        $oppSql['from'] .= " LEFT JOIN opportunities sub_opp ON sub_opp.parent_opportunity_id=opportunities.id AND sub_opp.deleted=0";
		    }
		    $oppSql['where'] .= ' AND sub_opp.sales_stage IN("' . implode('","', $_REQUEST['client_sales_stage_advanced']) . '")';
		}
		
		if($return_array){
			return $oppSql;
		}else{
			return $oppSql['select'].' '.$oppSql['from'].' '.$oppSql['where'];
		}
		
	}
	
	function fill_in_additional_list_fields() {				
		
		//Display date in user fromat	
		global $timedate;
		$this->date_closed_tz = $timedate->to_display_date_time($this->date_closed_tz,true,false);	
						
		if(!isset($_REQUEST['from_bean'])){
		$this->name = '<a href="index.php?module=Opportunities&action=DetailView&record=' . $this->id . '&ClubbedView=1">' . $this->name . '</a>';		
		
		if ($this->clients > 1) {
			$proposal_text = '<a href="index.php?module=Opportunities&action=DetailView&record='.$this->id.'&ClubbedView=1">Manage</a>';
		}else{
			
			//get relationship and its id(if relationship exists)
			//$this->load_relationship('quotes');
			//$relate_data = $this->quotes->get();
			$relate_data = '';
			
			if(!empty($relate_data)){
				$obj_quote = new Quote();
				$quote_data = $obj_quote->retrieve($relate_data[0]);
				//echo "<pre>";print_r($quote_data);echo "</pre>";die;
				$date_sent = explode(' ', $quote_data->date_time_sent);
				$date_received = explode(' ', $quote_data->date_time_received);
				$proposal_text = '<a href="index.php?module=Quotes&action=DetailView&record='.$relate_data[0].'&parent_module=Opportunities&parent_id='.$this->id.'&return_module=Opportunities&return_id='.$this->id.'&return_action=DetailView">'.$date_sent[0].' - '.$date_received[0].'</a>';
			}
			else{
				//Fetching Child Opportunity Id
				$oppr = new Opportunity();
				$oppr->retrieve_by_string_fields(array('parent_opportunity_id' => $this->id));
				$proposal_text = '<a href="index.php?module=Quotes&action=EditView&opportunity_id='.$oppr->id.'&opportunity_name='.$oppr->name.'">Create</a>';
			}
		}
		//check online plan exists for related project Lead
		/*require_once 'custom/modules/Leads/bbProjectLeads.php';
		$stLeadId =$this->project_lead_id ;
		$obLeads = new bbProjectLeads();
		$obLeads->retrieve($stLeadId);
		$stLeadTitle = addslashes(htmlspecialchars_decode($obLeads->project_title,ENT_QUOTES));
		$stCountSql = $obLeads->create_list_count_query( $obLeads->get_leads_online_plans());
		$rsResult = $obLeads->db->query($stCountSql);
		$arResult = $obLeads->db->fetchByAssoc($rsResult);
		
			
		if($arResult['c'] >0){		
			$this->project_online_plan = '<div id="urlpln'.$this->id.'"> </div><a id="pln'.$this->id.'" href="javascript:void(0)" onclick="javascript:open_urls(event,\'index.php?module=Leads&action=projecturl&record='.$stLeadId.'&to_pdf=true&all=1\',\'Online Plans - '.$stLeadTitle.'\')" onmouseout="return nd();">View</a>';
		}else{
			$this->project_online_plan ='';
		}*/
		//show project document instead of online plans only
		global $db;
		$sql = "SELECT name FROM opportunities WHERE id = '".$this->id."' AND deleted = 0";
		$result = $db->query($sql);
		$row = $db->fetchByAssoc($result);
		$name = htmlspecialchars($row['name']);
		
		$this->project_online_plan = "<a href='javascript:void(0);' onclick='javascript:showProjectDocument(\"$this->id\",\"$name\");'>View</a>";
		
		$this->add_me_to_bidderlist = '<a href="">Add</a>';
		$this->sent_reviewed = $proposal_text;
		
		$this->date_modified = "<a href='javascript:void(0);' onclick='javascript:showPopupBidBoard(\"$this->project_lead_id\",\"$this->id\");'>$this->date_modified</a>";
		
		//if client name in display column of layout options start
		//@modified by Mohit Kumar Gupta
		//@date 25 Nov 2013 
		if ($this->clients > 0 && $_REQUEST['displayAccountName'] == true) {
			$this->disable_row_level_security = false;
			$accountSql = "SELECT ac.id account_id, ac.name account_name, opportunities.id FROM accounts ac
					LEFT JOIN
			    accounts_opportunities acop ON ac.id = acop.account_id
			        AND acop.deleted = 0
			        LEFT JOIN
			    opportunities ON opportunities.id = acop.opportunity_id
			        AND ac.deleted = 0 ";
			// $this->add_team_security_where_clause($accountSql);
		    $accountSql .= " WHERE opportunities.parent_opportunity_id = '".$this->id."' AND opportunities.deleted = 0 
		        GROUP BY ac.id ORDER BY ac.name";
			$accountResult = $db->query($accountSql);
			$accountNameCount = $db->getRowCount($accountResult);
			$accountNameArr = array();
			while ($accountData = $db->fetchByAssoc($accountResult)) {
				$accountNameArr[] = $accountData['account_name'];
			}
			if ($accountNameCount > 1) {
				$accountDisplayText = '<a id="displayText_'.$this->id.'"
					href="javascript:toggle(\''.$this->id.'\');">&nbsp;<strong>+</strong>&nbsp;</a>';
				$accountDisplayText .= $accountNameArr[0];
				$accountDisplayText .= '<div class="role_div" id="role-div_'.$this->id.'" style="display: none; padding-left: 10px;">';
				foreach ($accountNameArr as $key => $value) {
					if ($key !=0) {
						$accountDisplayText .= $value.'<br/>';
					}
				}
				$accountDisplayText .= '</div>';
			} else {
				$accountDisplayText .= $accountNameArr[0];
			}
			$this->account_name = $accountDisplayText;
		}
		//if client name in display column of layout options end
		
		}
	}
	

	/**
	 * overwrite parent notification body
	 * @see Opportunity::set_notification_body()
	 */
	function set_notification_body($xtpl, $oppty)
	{
		global $app_list_strings;
	
		$xtpl->assign("OPPORTUNITY_NAME", $oppty->name);
		$xtpl->assign("OPPORTUNITY_AMOUNT", $oppty->amount);
		
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();
		$bid_due_date_time = $oss_timedate->convertDBDateForDisplay($oppty->date_closed, $oppty->bid_due_timezone,false);
		 
		$xtpl->assign("OPPORTUNITY_CLOSEDATE", $bid_due_date_time);
		
		
		$xtpl->assign("OPPORTUNITY_STAGE", (isset($oppty->sales_stage)?$app_list_strings['sales_stage_dom'][$oppty->sales_stage]:""));
		$xtpl->assign("OPPORTUNITY_DESCRIPTION", $oppty->description);
	
		return $xtpl;
	}

}


?>
