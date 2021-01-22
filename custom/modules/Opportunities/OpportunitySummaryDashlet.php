<?php

require_once 'modules/Opportunities/Opportunity.php';
class OpportunitySummaryDashlet extends Opportunity {
	
	function __construct() {
		parent::Opportunity ();
	}
	
	function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false) {
		
		global $current_user;
		$oppSql = parent::create_new_list_query ( $order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect );
		if(strtolower($_REQUEST['action']) != 'detailview'){	
		$oppSql['select'] .= " ,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz

							  ,avgAmountOpp.sub_amt amount_usdollar	";
		
		$oppSql['from'] .= 'INNER JOIN (	SELECT AVG(amount_usdollar) sub_amt, parent_opportunity_id 
						FROM opportunities 
						WHERE deleted = 0 AND assigned_user_id = "'.$current_user->id.'"
						GROUP BY parent_opportunity_id

					) avgAmountOpp on opportunities.id = avgAmountOpp.parent_opportunity_id';
		
		
		$oppSql ['where'] .= " AND opportunities.sales_stage != 'Won (Closed)' AND opportunities.sales_stage != 'Lost (Closed)' ";

		}
		return $oppSql;
	} 
	/**
	 * @Added By : Ashutosh 
	 * @Date : 6-Feb-2013
	 * @purpose : to list the related opportunities in 
	 * project opportunity detail view 
	 *  
	 * @return SQL string
	 */
	function opportunity_to_opportunity_relate()
	{
	    $bean = $GLOBALS['app']->controller->bean;

	     $stSql = 'SELECT opportunities.id
    				,opportunities.name
            		,opportunities.sales_stage
            		,accounts.id account_id
            		,accounts.name dup_account_name
            		,accounts.name lcd_account
            		,opportunities.assigned_user_id
            		,CONCAT(COALESCE(first_name)," ",COALESCE(last_name)) assigned_user_name
            		,opportunities.date_closed
	            	,opportunities.bid_due_timezone
            		,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz
            		,opportunities.amount_usdollar
		    FROM opportunities
		    LEFT JOIN accounts_opportunities accountOpportunity ON accountOpportunity.opportunity_id =opportunities.id AND accountOpportunity.deleted=0 
            LEFT JOIN accounts ON   accountOpportunity.account_id = accounts.id AND accounts.deleted=0
            LEFT JOIN users ON opportunities.assigned_user_id = users.id AND users.deleted =0
		    WHERE opportunities.parent_opportunity_id = "'.$bean->id.'" AND   opportunities.deleted =0';
	    return $stSql;
	}
	
	/**
	 * Created BY Hirak
	 */
	function beforeImportSave(){
		
		global $db, $timedate;
		
		if(!empty( $this->name ) ){
			if(!empty($this->date_closed) && !empty($this->bid_due_timezone) ){
				$userDateTime = $timedate->to_display_date_time ( $this->date_closed, true, true );
				require_once 'custom/include/OssTimeDate.php';
				$oss_timedate = new OssTimeDate ();
				$gmt_time = $oss_timedate->convertDateForDB ( $userDateTime, $this->bid_due_timezone );
				$this->date_closed = $gmt_time;
			}
			
			$sql = " SELECT id FROM opportunities WHERE name = '".$db->quote($this->name)."' AND deleted = 0 AND  parent_opportunity_id IS NULL ORDER BY date_entered LIMIT 0,1";
			$GLOBALS['log']->fatal($sql);
			$result = $db->query($sql);
			$row = $db->fetchByAssoc($result);
			$GLOBALS['log']->fatal($row);
			if( empty( $row['id'] ) ){
				$project_opportunity = clone $this;
				unset($project_opportunity->account_id);
				unset($project_opportunity->parent_opportunity_id);
				unset($project_opportunity->contact_id);
				$project_opportunity->save();
				$parent_opportunity_id = $project_opportunity->id;	
							
			}else{
				$parent_opportunity_id = $row['id'];
			}

			/**
			 * @author : Mohit Kumar Gupta
			 * @date   : 24-08-2016
			 * add classification to the client opportunity during the import process
			 * classfication will be added if requested classification will matchup with the saved target and roles classifications
			 */
			if (!empty($this->opportunity_classification)) {
				$classificationArray = getTargetClassDom();
				$classificationId = array_search(ucwords($this->opportunity_classification), $classificationArray);
				if (!empty($classificationId)) {
					$this->opportunity_classification = $classificationId;
				} else {
					$this->opportunity_classification = '';
				}
			}
			
			$this->parent_opportunity_id = $parent_opportunity_id; 
		}
	}

}
?>
