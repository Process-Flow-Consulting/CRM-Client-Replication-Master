<?php
require_once 'include/MVC/View/SugarView.php';

class OpportunitiesViewDocumentsubpanel extends SugarView{
	
	function OpportunitiesViewDocumentsubpanel(){
		parent::SugarView();
	}
	
	function display(){
		
		global $db, $timedate;
		
		if( !isset($_REQUEST['record']) || empty($_REQUEST['record']) ){
			$GLOBALS['log']->error('Error!! No Client Opportunity ID.');
			exit('Error!! No Client Opportunity ID.');
		}
		
		$opportunity = new Opportunity();
		$opportunity->disable_row_level_security = true;
		$opportunity->retrieve($_REQUEST['record']);
		
		if(empty($opportunity->name)){
			$GLOBALS['log']->error('Error!! Not a valid Client Opportunity ID.');
			exit('Error!! Not a valid Client Opportunity ID.');
		}
		

		
		$_POST['record'] = $opportunity->id;
		$html = '';
		$html.= '<div id="list_subpanel_client_documents">';
		require_once 'include/SubPanel/SubPanel.php';
		$subpanel = new SubPanel('Opportunities', $opportunity->id, 'client_documents', null);
		$subpanel->setTemplateFile('include/SubPanel/SubPanelDynamic.html');
		$result_array = array();
		$html.= $subpanel->ProcessSubPanelListView($subpanel->template_file, $result_array);
		$html.= '</div>';
		
		echo $html;
	}
	
}