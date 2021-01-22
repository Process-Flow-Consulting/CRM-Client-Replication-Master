<?php

require_once 'modules/Opportunities/Opportunity.php';
require_once 'modules/AOS_Quotes/AOS_Quotes.php';
class OpportunityPopupSummary extends Opportunity {

	function __construct() {
		parent::Opportunity ();
	}

	function create_new_list_query($order_by, $where, $filter = array(), $params = array(), $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false) {
		$oppSql = parent::create_new_list_query ( $order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect );

		if(isset($_REQUEST['parent_opportunity_only'])){
			$oppSql['where'] .= " AND opportunities.parent_opportunity_id IS NULL";
		}else{
			$oppSql ['select'] .= " ,COALESCE(countt,1) clients ";
			$oppSql ['from'] .= " LEFT JOIN (SELECT count(*) countt, parent_opportunity_id id from opportunities WHERE parent_opportunity_id is not null and deleted=0 group by parent_opportunity_id) tmp on opportunities.id = tmp.id ";
			$oppSql ['where'] .= " AND opportunities.parent_opportunity_id IS NOT NULL";
		}
		
		$oppSql['select'] .= " ,getBidsDueDate(opportunities.date_closed,opportunities.bid_due_timezone) date_closed_tz ";
		
		if($return_array){
			return $oppSql;
		}else{
			return $oppSql['select'].' '.$oppSql['from'].' '.$oppSql['where'];
		}
	}
}
?>
