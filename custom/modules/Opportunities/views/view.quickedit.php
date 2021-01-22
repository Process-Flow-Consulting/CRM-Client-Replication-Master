<?php
require_once('include/MVC/View/views/view.quickedit.php');

class OpportunitiesViewQuickedit extends ViewQuickedit{

	/**
     * @var defaultButtons Array of default buttons assigned to the form (see function.sugar_button.php)
     */
    protected $defaultButtons = array(array('customCode' =>'<input type="button" value="Save" id="Opportunities_dcmenu_save_button" name="Opportunities_dcmenu_save_button" onclick="check_form_custom(\'form_DCQuickCreate_Opportunities\');" class="button primary" accesskey="a" title="Save">'),'DCMENUCANCEL', 'DCMENUFULLFORM');
	
	public function preDisplay()
	{
		parent::preDisplay();
	}
	
	public function display()
	{
		//Convert Bid Due Date based on TimeZone
		if(!empty($this->bean->id)){
			require_once 'custom/include/OssTimeDate.php';
			$oss_timedate = new OssTimeDate();
			$bid_due_date_time = $oss_timedate->convertDBDateForDisplay($this->bean->date_closed, $this->bean->bid_due_timezone,true);
			$this->bean->date_closed = $bid_due_date_time;		
		}
		
		if(!empty($this->bean->parent_opportunity_id))
			$_REQUEST['target_view'] = 'QuickCreateSub';
		
		parent::display();
		
	}
} 
