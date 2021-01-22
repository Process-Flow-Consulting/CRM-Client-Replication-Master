<?php

if (! defined ( 'sugarEntry' ))
	define ( 'sugarEntry', true );

class LeadsViewProjecturl extends SugarView {
	
	function LeadsViewProjecturl() {
		parent::SugarView ();
	}
	/**
	 * function to render the online project url popup
	 * 
	 * @see SugarView::display()
	 */
	function display() {
		
		global $app_strings,$timedate;
		$arListData = array ();
		// check if project lead id exists
		if ($_REQUEST ['record']) {
			$stPrjId = $_REQUEST ['record'];
		}
		
		// retrieve all the project urls associated with this project
		require_once 'custom/modules/Leads/bbProjectLeads.php';
		$obBbProjectLead = new bbProjectLeads();
		$obBbProjectLead->retrieve($stPrjId);
		$stListSql= $obBbProjectLead->get_leads_online_plans();
		$obOnlinePlans = new oss_OnlinePlans ();
				
		//$stListSql = $obOnlinePlans->create_new_list_query ( '', ' oss_onlineplans.lead_id ="' . $stPrjId . '"', array (), array (), 0, '', false );
		
		$rsResult = $obOnlinePlans->db->query ( $stListSql );
		
		while ( $arData = $obOnlinePlans->db->fetchByAssoc ( $rsResult ) ) {
			$arListData [] = $arData;
		
		}
		
		$this->ss->assign ( 'AR_TITLE', array (
				'type' => translate ( 'LBL_PLAN_TYPE', 'oss_OnlinePlans' ),
				'source' => translate ( 'LBL_PLAN_SOURCE', 'oss_OnlinePlans' ),
				'review' => translate ( 'LBL_REVIEW_DATE', 'oss_OnlinePlans' ),
				'link' => translate ( 'LBL_URL_LINK', 'oss_OnlinePlans' ) 
		) );
		// set tpl vars
		$this->ss->assign ( 'AR_DATA', $arListData );
		$this->ss->assign ( 'timedate', $timedate );
		
		$this->ss->display ( 'custom/modules/Leads/tpls/online_plans.tpl' );
	}
}

?>
