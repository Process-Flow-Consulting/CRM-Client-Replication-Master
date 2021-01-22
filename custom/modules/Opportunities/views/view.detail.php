<?php
require_once('include/MVC/View/views/view.detail.php');
require_once 'custom/include/common_functions.php';

class OpportunitiesViewDetail extends ViewDetail{
	function OpportunitiesViewDetail(){
		parent::ViewDetail();
	}
	
	/**
	 * @see SugarView::preDisplay()
	 */
	public function preDisplay()
	{
	//vardefs file for project opportunity.
		$metadataFile = 'custom/modules/Opportunities/metadata/detailviewdefs.php';
		if(!empty($this->bean->parent_opportunity_id)){
		    //vardefs file for client opportunity.
			$metadataFile = 'custom/modules/Opportunities/metadata/detailviewdefs_sub.php';
		}	
		$this->dv = new DetailView2();
		$this->dv->ss =&  $this->ss;
		$this->dv->setup($this->module, $this->bean, $metadataFile, 'include/DetailView/DetailView.tpl');
	}

	function display(){
		global $current_user,$mod_strings,$app_list_strings,$db,$app_strings, $db;
		//Add/Remove Push to QuickBooks Button
		if(!empty($this->bean->parent_opportunity_id)){
		    addPushToQuickBooksButton($this->bean,$this->dv);
		}
		$currency = new Currency();
		if(isset($this->bean->currency_id) && !empty($this->bean->currency_id))
		{
			$currency->retrieve($this->bean->currency_id);
			if( $currency->deleted != 1){
				$this->ss->assign('CURRENCY', $currency->iso4217 .' '.$currency->symbol);
			}else {
				$this->ss->assign('CURRENCY', $currency->getDefaultISO4217() .' '.$currency->getDefaultCurrencySymbol());
			}
		}else{
			$this->ss->assign('CURRENCY', $currency->getDefaultISO4217() .' '.$currency->getDefaultCurrencySymbol());
		}
		
		//Remove filter tmporary.
		#################################
		#### 	ACCESS FILTERS 		#####
		//USER FILTERS ACCESS RULE
		/* if(!$current_user->is_admin){
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			$bIsParent = ($this->bean->parent_opportunity_id != '')?false:true;
			userAccessFilters::isOpporunityAccessable($this->bean->id,false,$bIsParent);
		} */
		#### EOF ACCESS FILTERS       ####
		##################################
		
		//access control for parent/child Opportunity
		if(isset($_REQUEST['record'])){
			 
			if(empty($this->bean->parent_opportunity_id)){
				//unset client for parent opportunity	
    			unset($this->bean->field_defs['account_name']);
    			//change the dropdown for sales stage
    			$this->bean->field_defs['sales_stage']['options'] = 'project_sales_stage_dom';
    			
    			//Get County by County id
    			$sql = "SELECT `name` FROM oss_county WHERE `id` = '".$this->bean->lead_county."' AND deleted = 0";
    			$query = $db->query($sql);
    			$result = $db->fetchByAssoc($query);
    			$this->bean->lead_county = $result['name'];
    			
			}else{	

				//Get Classification by classification id start
				//@modified by Mohit Kumar Gupta
				//@date 20-nov-2013
				$sql = "SELECT `description` FROM oss_classification WHERE `id` = '".$this->bean->opportunity_classification."' AND deleted = 0";
				$query = $db->query($sql);
				$result = $db->fetchByAssoc($query);
				$this->bean->opportunity_classification = $result['description'];
				//Get Classification by classification id end
				
                //$account_proview_link = $this->setAccountProviewLink($this->bean);
				$account_proview_link = proview_url(array('url' => $this->bean->account_proview_url));
				$account_name = '<a href="index.php?module=Accounts&action=DetailView&record='.$this->bean->account_id.'">'.$this->bean->account_name.'</a>';				
				$this->ss->assign('ACCOUNT_NAME',$account_proview_link.'&nbsp;'.$account_name);
				unset($this->bean->field_defs['opportunity_to_opportunity_var']);
                                
                //Get the Sub-Opportunity Count of Project Opportuinty.
                $parent_opp_sql = "SELECT sub_opp_count FROM opportunities WHERE id='".$this->bean->parent_opportunity_id."' AND deleted=0";
                $parent_opp_query = $db->query($parent_opp_sql);
                $parent_opp_result = $db->fetchByAssoc($parent_opp_query);
                $sub_opp_count = $parent_opp_result['sub_opp_count'];			 			
                $this->ss->assign('sub_opp_count',$sub_opp_count);                                
			}
		}
		
		
		
		//Convert Bid Due Date based on TimeZone
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();
		$bid_due_date_time = $oss_timedate->convertDBDateForDisplay($this->bean->date_closed, $this->bean->bid_due_timezone,true);
		$this->bean->date_closed = $bid_due_date_time;
		/* require_once 'custom/include/common_functions.php';                
		$bid_due_date_time = convertDbDateToTimeZone($this->bean->date_closed, $this->bean->bid_due_timezone);
		$this->bean->date_closed = $bid_due_date_time; */
		
		//delete cache template
		require_once('include/TemplateHandler/TemplateHandler.php');
		$this->th = new TemplateHandler();
		$this->th->ss =& $this->ss;
		$this->tpl = 'include/DetailView/DetailView.tpl';
		$this->focus = $this->bean;
		$this->th->deleteTemplate($this->module, 'DetailView');

		parent::display();

	}

	/**
	 * Called from process(). This method will display subpanels.
	 */
	function _displaySubPanels()
	{
		if (isset($this->bean) && !empty($this->bean->id) && (file_exists('modules/' . $this->module . '/metadata/subpaneldefs.php') || file_exists('custom/modules/' . $this->module . '/metadata/subpaneldefs.php') || file_exists('custom/modules/' . $this->module . '/Ext/Layoutdefs/layoutdefs.ext.php'))) {
			
			$layout_def_override = $this->open_layout_defs($this->module, false);
			
			//unset related oppotunity subpanel if client opportunity
			if(!empty($this->bean->parent_opportunity_id)){
				unset($layout_def_override['subpanel_setup']['opportunity_to_opportunity_var']);
			}
			
			//do not show addtional clients subpanel on Project opportunity
			if(empty($this->bean->parent_opportunity_id)){
			    unset($layout_def_override['subpanel_setup']['opportunities_contacts_c']);
			}
			
			//hide project documents subpanel - used for only popup
			unset($layout_def_override['subpanel_setup']['project_documents']);
			unset($layout_def_override['subpanel_setup']['client_documents']);
			
			$GLOBALS['focus'] = $this->bean;
			require_once ('include/SubPanel/SubPanelTiles.php');
			$subpanel = new SubPanelTiles($this->bean, $this->module, $layout_def_override);
			echo $subpanel->display();
		}
	}
	
	function open_layout_defs($layout_def_key = '', $original_only = false) {
		$layout_defs [$this->bean->module_dir] = array();
		$layout_defs [$layout_def_key] = array();
	
		if (file_exists('modules/' . $this->bean->module_dir . '/metadata/subpaneldefs.php')) {
			require ('modules/' . $this->bean->module_dir . '/metadata/subpaneldefs.php');
		}
		if (!$original_only && file_exists('custom/modules/' . $this->bean->module_dir . '/Ext/Layoutdefs/layoutdefs.ext.php')) {
	
			require ('custom/modules/' . $this->bean->module_dir . '/Ext/Layoutdefs/layoutdefs.ext.php');
		}
	
		if (!empty($layout_def_key)) {
			$layout_defs = $layout_defs [$layout_def_key];
		} else {
			$layout_defs = $layout_defs [$this->bean->module_dir];
		}
	
		return $layout_defs;
	}
	
	//public function setAccountProviewLink(&$focus){
		
		//if($_REQUEST['action'] == 'EditView'){
		//	return;
		//}
	
		//if($focus->account_proview_url != '')
		//{
			//$focus->account_proview_url = $focus->account_proview_url;
			//if (preg_match('/^[^:\/]*:\/\/.*/', $focus->account_proview_url)) {
			//	$focus->account_proview_url= $focus->account_proview_url;
			//} else {
			//	$focus->account_proview_url = 'http://' . $focus->account_proview_url;
			//}
	
			//$focus->account_proview_url = '<a href="javascript:void(0)" onclick="window.open(\''.$focus->account_proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
		//}
		//else{
		//	$focus->account_proview_url = '';
			//$focus->account_proview_url = '<a href="javascript:void(0)" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
		//}
	
		//return $focus->account_proview_url;
	//}
}
?>
