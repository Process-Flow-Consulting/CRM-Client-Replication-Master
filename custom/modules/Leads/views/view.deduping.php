<?php

require_once('include/MVC/View/views/view.list.php');
require_once('custom/include/common_functions.php');

class LeadsViewDeduping extends ViewList {

	function LeadsViewDeduping() {
		parent::ViewList();
	}

	function display() {

		global $timedate, $current_user;
		$order_by="";
		$show_deleted="0";
		$check_dates="";

		require_once('custom/modules/Leads/bbProjectLeads.php');

		$obCurProjectLeads = new bbProjectLeads();
		$obProjectLeads = new bbProjectLeads();
		$arFilters = Array('project_title' => 1
            				,'state' => 1
           ,'city' => 1
            ,'lead_version' => 1
            ,'prev_bid_to' => 1
            ,'new_total' => 1
            ,'bids_due_tz' => 1
            ,'lead_plans' => 1
           ,'date_modified' => 1
            ,'status' => 1
            ,'classification' => 1
            ,'fav_bidders_only' => 1
			,'pre_bid_meeting' =>1
			,'contact_no' =>1
        );
		
		$params = array('favorites' => 1);
		$stAction = $_REQUEST['action'];
		unset($_REQUEST['action']);
		//get details for current lead like lead version
		$stPlquery = $obCurProjectLeads->create_new_list_query('', ' leads.id = "'.$this->bean->id.'"', $arFilters, $params, false);
		$arCurLeadObj = $obProjectLeads->process_full_list_query($stPlquery, '');
		$_REQUEST['action'] = $stAction;



		//check if request to link projects
		if (isset($_REQUEST['mass']) && count($_REQUEST['mass']) > 0) {
			//relate these project leads as sub project leads
			$this->mergeDuplicate($_REQUEST);
		}

		//check type of order
		$order = (isset($_REQUEST['odr'])) ? $_REQUEST['odr'] : 'ASC';
		$orderBy = (isset($_REQUEST['sort'])) ? $_REQUEST['sort'] : 'project_title';

		//exclude parent leads, bid due date should between +-7 days
		//prepare duplicate criteria array
		$arDupOrCriteriaFields = array(
		'leads.type' => $this->bean->type);

		$arDupAndCriteriaFields = array(
		'leads.structure' => $this->bean->structure
		, 'leads.state' => $this->bean->state
		);
		$where = ' ';
		/* $iCount = 0;
		foreach ($arDupOrCriteriaFields as $stFieldName => $stFieldValue) {
			$stOperator = ($iCount == 0) ? '(' : ' OR ';
			$where .= " $stOperator $stFieldName = '{$stFieldValue}' ";
			$iCount++;
		}
		$obUser = new User();
		$obUser->getUserDateTimePreferences();



		$stTimeZone = $timedate->UserTimezone($current_user);
		$arAllTimeZone = $timedate->getTimezoneList();
		$arTmpTime = explode('GMT', $arAllTimeZone[$stTimeZone]);
		$stHourDiff = str_replace(')', '', $arTmpTime[1]);

		$where .= ' OR leads.project_title LIKE "%' . $this->bean->project_title . '%" OR SUBSTRING("' . $this->bean->project_title . '", 1, 8) = SUBSTRING(leads.project_title, 1, 8)';
		$where .= ( trim($this->bean->bids_due) != '') ? ' OR TIMESTAMP(leads.bids_due) BETWEEN  DATE_SUB(TIMESTAMP("' . $this->bean->fetched_row['bids_due'] . '") , INTERVAL +7 DAY) AND DATE_SUB(TIMESTAMP("' . $this->bean->fetched_row['bids_due'] . '") , INTERVAL -7 DAY)' : '';
		;

		$where .= ' ) AND ';

		$iCount = 0;

		foreach ($arDupAndCriteriaFields as $stFieldName => $stFieldValue) {
			$stOperator = ($iCount == 0) ? '(' : ' AND ';
			//check if its null or empty
			if(trim($stFieldValue) == ''){
				$where .= " $stOperator ($stFieldName = '' OR $stFieldName IS NULL)";
			}else{
				$where .= " $stOperator $stFieldName = '{$stFieldValue}' ";
			}
			$iCount++;
		}
		$where .= ' )'; */

		//current lead id should not be as part of the list
		$stBidsDueCondition = ( trim($this->bean->bids_due) != '') ? '( TIMESTAMP(leads.bids_due) BETWEEN  DATE_SUB(TIMESTAMP("' . $this->bean->fetched_row['bids_due'] . '") , INTERVAL +7 DAY) AND DATE_SUB(TIMESTAMP("' . $this->bean->fetched_row['bids_due'] . '") , INTERVAL -7 DAY) OR leads.asap = 1 )' 
							  : '( (leads.bids_due IS NULL OR leads.bids_due="" OR leads.bids_due="0000-00-00 00:00:00" ) OR leads.asap = 1  )';
		;
		
		$where .= ' (
						(leads.type = "'.$this->bean->type.'" 
						OR SUBSTRING("' . $this->bean->project_title . '", 1, 8) = SUBSTRING(leads.project_title, 1, 8)
						OR leads.structure = "'.$this->bean->structure.'"
						)
						AND
						(
							'.$stBidsDueCondition.'
						)
						AND
						(
							leads.state = "'.$this->bean->state.'"
							AND 
							leads.city = "'.$this->bean->city.'"
						)
						
					)';

		//make sure no lead is associated to this
		// $where .= ' AND  coalesce(countt,1)  = 1 ';

		$order_by = $orderBy . ' ' . $order;
		//get duplicate records based on matcing criteria
		$arQuery = $obProjectLeads->create_new_list_query($order_by, $where, $arFilters, $params, $show_deleted,'',1);

		$arQuery['where'] .= ' AND leads.id <> "' . $this->bean->id . '"';
		$query = $arQuery['select'].' '.$arQuery['from'].' '.$arQuery['where'].' '.$arQuery['order_by'];
		$arDupList = $obProjectLeads->process_full_list_query($query, $check_dates);

		$order = ($order == 'ASC') ? 'DESC' : 'ASC';
		
		/**
        * use for check duplicate leads if it is coming from leads list view
        * @modified by Mohit Kumar Gupta
        * @date 11-Feb-2014
		*/
		$returnLeadListView = '0';
		if (isset($_REQUEST['return_lead_list_view']) && $_REQUEST['return_lead_list_view'] == '1') {
		    $returnLeadListView = $_REQUEST['return_lead_list_view'];
		    //If duplicate leads not exists then redirect this leads to conversion screen
		    if (count($arDupList) == 0) {
		    	SugarApplication::redirect('index.php?module=Leads&action=review_opportunity&return_action=ListView&record='.$this->bean->id);
		    }		    
		}
		
		//pr($arDupList[0]);
		global $app_list_strings;

		$this->ss->assign('TITLE', getClassicModuleTitle('LEAD', '', true));
		$this->ss->assign('STATE_DOM', $app_list_strings['state_dom']);
		$this->ss->assign('order', $order);
		$this->ss->assign('OB_LEAD_DATA', $this->bean);
		$this->ss->assign('OB_LEAD_OTHER_DATA', array_shift($arCurLeadObj));
		$this->ss->assign('timedate', $timedate);
		$this->ss->assign('AR_DUP_DATA', $arDupList);
		$this->ss->assign('MODULE_TITLE', $this->getModuleTitle());
		$this->ss->assign('returnLeadListView', $returnLeadListView);
		$this->ss->display('custom/modules/Leads/tpls/deduping.tpl');
	}

	/*
	* @function to relate the project leads to a primary based on created
	*   date criteria.
	*/

	function mergeDuplicate($arPostData) {
		global $db;
		//pr($arPostData);
		//get project leads

		$arAllLeadIds = array_values($arPostData['mass']);
		array_push($arAllLeadIds, $arPostData['record']);
		//pr($arAllLeadIds);die;
		//get the one which was created earlieast
		$arLeadsList = $this->bean->get_full_list(' leads.date_entered ASC ', ' leads.id in ("' . implode('","', $arAllLeadIds) . '") OR leads.parent_lead_id in ("' . implode('","', $arAllLeadIds) . '")  ');

		if (isset($arLeadsList[0]->id) && trim($arLeadsList[0]->id) != '') {

			//set all the project leads parent_lead_ids to this one
			$child_pl_change_log = false;
			foreach ($arLeadsList as $obLead) {

				if($arLeadsList[0]->id != $obLead->id){
					$obLead->parent_lead_id = $arLeadsList[0]->id;
					if($obLead->change_log_flag==1){
						$child_pl_change_log = true;
					}
				}
				else{
					$obLead->parent_lead_id = NULL;
					if($child_pl_change_log == true){					
						$obLead->change_log_flag = 1;						
					}										
				}

				/**
				 * update status to viewed if lead status is new
				 * @modified by Mohit Kumar Gupta
				 * @date 20-02-2014
				 */
				if($obLead->status == 'New' ){
				    $obLead->status = 'Viewed';				    
				}
				
				$obLead->save();

			}
			
			//update lead version for this project lead
			updateLeadVersionBidDueDate($arLeadsList[0]->id);
			updateNewTotalBidderCount($arLeadsList[0]->id);
			updateOnlineCount($arLeadsList[0]->id);
			//set update prev bid to flag
	        setPreviousBidToUpdate();

			//redirect to parent project leads detail view
            
            /**
             * use for check duplicate leads if it is coming from leads list view
             * and redirect to lead conversion screen
             * @modified by Mohit Kumar Gupta
             * @date 12-Feb-2014
             */
            if (isset($_REQUEST['return_lead_list_view']) && $_REQUEST['return_lead_list_view'] == '1') {                
                SugarApplication::redirect('index.php?module=Leads&action=review_opportunity&return_action=ListView&record='.$arLeadsList[0]->id);
            } else {
                SugarApplication::redirect('index.php?module=Leads&action=dedupe_details&record=' . $arLeadsList[0]->id);
            }			
		}
	}

}
?>
