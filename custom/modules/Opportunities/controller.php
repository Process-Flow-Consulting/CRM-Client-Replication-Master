<?php

class CustomOpportunitiesController extends SugarController {

    function CustomOpportunitiesController() {
        parent::SugarController();
    }

    function action_detailview() {
        global $focus;
        if (isset($_REQUEST['ClubbedView']) && $_REQUEST['ClubbedView'] == 1) {
            
            $this->view = 'clubedview';
        } else {
           require_once 'custom/modules/Opportunities/OpportunitySummaryDashlet.php';
           $stOppId = $this->bean->id;
            $this->bean = new OpportunitySummaryDashlet();
            $this->bean->retrieve($stOppId);
            $focus = $this->bean; 
            $this->bean->id = $stOppId;
            $this->view = 'detail';
        }
    }
    
    function action_subpanelviewer(){
        global $focus,$beanList,$beanFiles;
        require_once 'custom/modules/Opportunities/OpportunitySummaryDashlet.php';
        $beanList['OpportunitySummaryDashlet'] = 'OpportunitySummaryDashlet';
        $beanFiles['OpportunitySummaryDashlet'] = 'custom/modules/Opportunities/OpportunitySummaryDashlet.php';
        $this->parent_bean = new OpportunitySummaryDashlet();
    
        $this->view = 'subpanvier';
    
    }
    
    function action_popup(){
		require_once 'custom/modules/Opportunities/OpportunitySummary.php';
		$this->bean = new OpportunitySummary();
		$this->view = 'popup';
	}
    
	function action_converted(){
		$this->view = 'converted';
	}
	function action_unlinkop(){
		$this->view = 'unlinkop';
	}
	function action_createclient(){
		$this->view = 'createclient';
	}
	function action_verifyproposal(){
		$this->view = 'verifyproposal';
	}
	function action_ataglance(){
		$this->view = 'ataglance';
	}
	function action_ataglance_graph(){
		$this->view = 'ataglance_graph';
	}
	function action_parentoppautofill(){
		$this->view = 'parentoppautofill';
	}
	function action_assigneduser(){
		$this->view = 'assigneduser';
	}
	function action_podetails(){
		$this->view = 'podetails';
	}
	function action_projectdocument(){
		$this->view = 'projectdocument';
	}
	function action_documentsubpanel(){
		$this->view = 'documentsubpanel';
	}
	function action_deleterelateddata(){
		$this->view = 'deleterelateddata';
	}
	/**
	 * Added By : Ashutosh 
	 * Date : 5 Sept 2013
	 * New Action to get the assigned users  
	 */
	function action_getoppassigned(){
	    $this->view = 'getoppassigned';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 14-nov-2013
	 * use for getting quick edit view for create opportunity from clients
	 */
	function action_getquickeditopportunity(){
		$this->view = 'getquickeditopportunity';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 13-nov-2013
	 * action for view of create opportunity from clients
	 */
	function action_accounts_opportunity(){
		$this->view = 'accounts_opportunity';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 13-nov-2013
	 * action for view of save created opportunity from clients
	 */
	function action_save_accounts_opportunity(){
		$this->view = 'save_accounts_opportunity';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 21-jan-2014
	 * action for getting private team of a user
	 */
	function action_userPrivateTeam(){
		$this->view = 'userPrivateTeam';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 02-04-2014
	 * action to create mass update form for client opportunity
	 */
	function action_clientoppmassupdateform(){
	    $this->view = 'clientoppmassupdateform';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 02-04-2014
	 * action to mass update for client opportunity
	 */
	function action_clientoppmassupdate(){
	    $this->view = 'clientoppmassupdate';
	}
	/**
	 * @author Mohit Kumar Gupta
	 * @date 02-10-2014
	 * action to generate job sheet information for client opportunities
	 */
	function action_opp_pdf(){
		$this->view = 'opp_pdf';	
	}
}

?>
