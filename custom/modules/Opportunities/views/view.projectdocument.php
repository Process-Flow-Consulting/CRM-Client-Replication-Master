<?php
require_once 'include/MVC/View/SugarView.php';

class OpportunitiesViewProjectdocument extends SugarView{
	
	function OpportunitiesViewProjectdocument(){
		parent::SugarView();
	}
	
	function display(){
		
		global $db, $timedate;
		
		if( !isset($_REQUEST['record']) || empty($_REQUEST['record']) ){
			$GLOBALS['log']->error('Error!! No Project Opportunity ID.');
			exit('Error!! No Project Opportunity ID.');
		}
		
		$opportunity = new Opportunity();
		$opportunity->disable_row_level_security = true;
		$opportunity->retrieve($_REQUEST['record']);
		
		if(empty($opportunity->name)){
			$GLOBALS['log']->error('Error!! Not a valid Project Opportunity ID.');
			exit('Error!! Not a valid Project Opportunity ID.');
		}
		
		if(isset($_REQUEST['tab'])){
			$show_tab = $_REQUEST['tab'];
		}
		
		$this->ss->assign ( 'opportunity_id', $opportunity->id );
		
		$arOnlinePlan = array();
		if(!empty($opportunity->project_lead_id)){
			
			//make this tab always visible
			$this->ss->assign('ONLINE_PLANS',true);
			$this->ss->assign('OP_TAB_SELECTED','class="selected"');
			
			$this->ss->assign('PROJECT_LEAD_ID', $opportunity->project_lead_id);
			
			// retrieve all the project urls associated with this project
			require_once 'custom/modules/Leads/bbProjectLeads.php';
			$obBbProjectLead = new bbProjectLeads();
			$obBbProjectLead->retrieve($opportunity->project_lead_id);
			$stListSql= $obBbProjectLead->get_leads_online_plans();
			$obOnlinePlans = new oss_OnlinePlans ();
			$rsResult = $obOnlinePlans->db->query ( $stListSql );
			while ( $arData = $obOnlinePlans->db->fetchByAssoc ( $rsResult ) ) {
				$arOnlinePlan [] = $arData;
			}
			//labels from the Online Plan
			$this->ss->assign ( 'AR_OP_TITLE', array (
					'TYPE' => translate ( 'LBL_PLAN_TYPE', 'oss_OnlinePlans' ),
					'SOURCE' => translate ( 'LBL_PLAN_SOURCE', 'oss_OnlinePlans' ),
					'REVIEW' => translate ( 'LBL_REVIEW_DATE', 'oss_OnlinePlans' ),
					'LINK' => translate ( 'LBL_URL_LINK', 'oss_OnlinePlans' )
			) );
			// set tpl vars
			$this->ss->assign ( 'AR_ONLINE_PLAN', $arOnlinePlan );
			$this->ss->assign ( 'timedate', $timedate );
			
			/*if(count($arOnlinePlan) > 0){
				//make the tab visible
				$this->ss->assign('ONLINE_PLANS',true);
				$this->ss->assign('OP_TAB_SELECTED','class="selected"');
			}*/
		}
		
		//find out if project opportunity has any document attached
		$opportunity->load_relationship('documents');
		$project_opp_docs =  $opportunity->documents->get();
		if( count($project_opp_docs) > 0){
			//make the tab visible
			$this->ss->assign('PROJECT_OPP_DOCS',true);
			
			/*if(empty($opportunity->project_lead_id) || count($arOnlinePlan) < 1){
				
				$this->ss->assign('PO_TAB_SELECTED','class="selected"');
			}*/
			
			//labels from the Documents
			$this->ss->assign ( 'AR_PO_TITLE', array (
					'NAME' => translate ( 'LBL_LIST_DOCUMENT_NAME', 'Documents' ),
					'FILENAME' => translate ( 'LBL_LIST_FILENAME', 'Documents' ),
					'CATEGORY' => translate ( 'LBL_LIST_CATEGORY', 'Documents' ),
					'PUBLISH_DATE' => translate ( 'LBL_LIST_ACTIVE_DATE', 'Documents' )
			) );
			
			require_once 'include/SubPanel/SubPanel.php';
			$subpanel = new SubPanel('Opportunities', $opportunity->id, 'project_documents', null);
			$subpanel->setTemplateFile('include/SubPanel/SubPanelDynamic.html');
			$result_array = array();
			$subpanel_view = $subpanel->ProcessSubPanelListView($subpanel->template_file, $result_array);
			$this->ss->assign('PRO_OP_SUBPANEL',$subpanel_view);
			
			//}
		}
		
		//find out if client opportunities has any document attached
		$sql = " SELECT count(*) c FROM opportunities opp INNER JOIN documents_opportunities dop ON dop.opportunity_id = opp.id AND dop.deleted = 0 INNER JOIN documents doc ON doc.id = dop.document_id AND doc.deleted = 0 WHERE opp.parent_opportunity_id = '".$opportunity->id."' AND opp.deleted = 0 ";
		$result =  $db->query($sql);
		$row = $db->fetchByAssoc($result);
		
		if($row['c'] > 0){
			//make the tab visible
			$this->ss->assign('CLIENT_OPP_DOCS',true);
			
			/*if( count($project_opp_docs) < 1 && ( empty($opportunity->project_lead_id) || count($arOnlinePlan) < 1 ) ){
				
				$this->ss->assign('CO_TAB_SELECTED','class="selected"');
				
			}*/
			
			$c_sql = " SELECT opp.id, opp.name, acc.name as account_name FROM opportunities opp  LEFT JOIN accounts_opportunities acop ON acop.opportunity_id = opp.id AND acop.deleted = 0 LEFT JOIN accounts acc ON acc.id = acop.account_id AND acc.deleted = 0 INNER JOIN documents_opportunities dop ON dop.opportunity_id = opp.id AND dop.deleted = 0 INNER JOIN documents doc ON doc.id = dop.document_id AND doc.deleted = 0 WHERE opp.parent_opportunity_id = '".$opportunity->id."' AND opp.deleted = 0 GROUP BY opp.id ";
			$c_result =  $db->query($c_sql);			
			
			$html = '';
			//Modified By Mohit Kumar Gupta for open/close image display
			while ( $c_row = $db->fetchByAssoc($c_result) ){
				$_POST['record'] = $c_row['id'];
				$html .= '<div class="accordionButton "><span class="showDiv" id="'.$c_row['id'].'">';
				$html .= '<img src="themes/Sugar/images/plus_inline.png" width= "14px" height="12px" class="open" id="open_'.$c_row['id'].'"> ';
				$html .= '<img src="themes/Sugar/images/delete_inline.png" width= "14px" height="12px" class="close" id="close_'.$c_row['id'].'" style="display:none;"> ';
				$html .= '</span><span class="documentName" id="documentName_'.$c_row['id'].'">'.$c_row['account_name'].'</span></div>';
				$html .= '<div class="accordionContent" id="content_'.$c_row['id'].'">';
				//$subpanel = new SubPanel('Opportunities', $c_row['id'], 'client_documents', null);
				//$subpanel->setTemplateFile('include/SubPanel/SubPanelDynamic.html');
				//$result_array = array();
				//$html.= $subpanel->ProcessSubPanelListView($subpanel->template_file, $result_array);
				$html.= '</div>';
			}
			
			$this->ss->assign('CLIENT_OP_SUBPANEL',$html);
			//}
		}
		
		
		/*(if( count($project_opp_docs) < 1 && ( empty($opportunity->project_lead_id) || count($arOnlinePlan) < 1 ) && ($row['c'] < 1 ) ){
			$this->ss->assign('NO_DOCUMENTS','No Documents Attached');
		}*/
		
		$this->ss->display('custom/modules/Opportunities/tpls/project_document.tpl');
	}
	
}