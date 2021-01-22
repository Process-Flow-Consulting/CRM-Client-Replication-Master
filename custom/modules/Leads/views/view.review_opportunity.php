<?php
require_once 'include/MVC/View/SugarView.php';
require_once 'custom/modules/Users/filters/instancePackage.class.php';
require_once 'custom/modules/Users/filters/userAccessFilters.php';

class LeadsViewReview_opportunity extends SugarView {
	
	function __construct($bean = null, $view_object_map = array()) {
		parent::SugarView ( $bean, $view_object_map );
	}
	
	function display() {
		global $app_strings;
		
		// validate package data
		$obPackage = new instancePackage ();
		if ($obPackage->validateOpportunities ()) {
			sugar_die ( $app_strings ['MSG_OPPORTUNITY_PACKAGE_LIMIT'] );
		}
		// EOF validate package data
		
		// Display Breadcrumb
		if (isset ( $_REQUEST ['record'] )) {
			echo $this->getModuleTitle ( false );
		}
		
		// Get parent project lead ID
		$lead = new Lead ();
		$lead->retrieve ( $_REQUEST ['record'] );
		if (! empty ( $lead->parent_lead_id )) {
			$pLeadId = $lead->parent_lead_id;
		} else {
			$pLeadId = $_REQUEST ['record'];
		}
				
		/**
		 * use for assign return action if it is not set then default is detail view
		 * @modified by Mohit Kumar Gupta
		 * @date 11-Feb-2014
		 */
		$returnAction = (isset($_REQUEST ['return_action']))?$_REQUEST ['return_action']:'DetailView';
		
		// Get Parent Opportunity of parent project lead
		require_once 'custom/modules/Opportunities/OpportunitySummary.php';
		$opportunity = new Opportunity();		
		$where = " opportunities.project_lead_id = '" . $pLeadId . "' AND (opportunities.parent_opportunity_id IS NULL || opportunities.parent_opportunity_id = '')  ";
		$oppList = $opportunity->get_full_list ('',$where);
		//echo count($oppList);die;
		
		/**
		 * Redirect on opportunity conversion screen if parent project lead does
		 * not have any opportunity.
		 */
		if (count ( $oppList ) == 0) {
			SugarApplication::redirect ( 'index.php?module=Leads&action=convert_to_opportunity&return_action='.$returnAction.'&record=' . $pLeadId );
			exit ();
		}
		
		/**
		 * Redirect on add new opportunity screen if parent opportunity id is
		 * set
		 * else redirect on previous lead conversion screen
		 */
		if (isset ( $_REQUEST ['save'] )) {
			if (! empty ( $_REQUEST ['opp_radio'] )) {
				SugarApplication::redirect ( 'index.php?module=Leads&action=convert_to_opportunity&return_action='.$returnAction.'&record=' . $pLeadId . '&opportunity_id=' . $_REQUEST ['opp_radio'] );
				exit ();
			} else {
				SugarApplication::redirect ( 'index.php?module=Leads&action=convert_to_opportunity&return_action='.$returnAction.'&record=' . $pLeadId );
				exit ();
			}
		}
		
		// Assign values for template
		$this->ss->assign ( 'opp_list', $oppList );
		$this->ss->assign ( 'record', $pLeadId );
		$this->ss->assign ( 'return_action', $returnAction );
		// Display template
		$this->ss->display ( 'custom/modules/Leads/tpls/review_opportunity.tpl' );
	}
	
	/**
	 *
	 * @see SugarView::_getModuleTitleParams()
	 */
	protected function _getModuleTitleParams($browserTitle = false) {
		global $mod_strings;
		$params = parent::_getModuleTitleParams ( $browserTitle );
		$params [] = "<a href='index.php?module=Leads&action=DetailView&record={$this->bean->id}'>{$this->bean->name}</a>";
		$params [] = $mod_strings ['LBL_CONVERTLEAD'];
		return $params;
	}
}
?>