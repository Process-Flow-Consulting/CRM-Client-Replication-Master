<?php
require_once('include/MVC/View/views/view.quickedit.php');

class OpportunitiesViewQuickcreate extends ViewQuickcreate{

	/**
     * @var defaultButtons Array of default buttons assigned to the form (see function.sugar_button.php)
     */
    protected $defaultButtons = array(array('customCode' =>'<input type="button" value="Save" id="Opportunities_dcmenu_save_button" name="Opportunities_dcmenu_save_button" onclick="check_form_custom(\'form_DCQuickCreate_Opportunities\',1);" class="button primary" accesskey="a" title="Save">'),'DCMENUCANCEL', 'DCMENUFULLFORM');
	
	public function preDisplay()
	{
		parent::preDisplay();
	}
	
	public function display()
	{
		$this->bean = new Opportunity();	
		
		// change sales stage dropdown for project opportunity.
		if(!empty($this->bean->parent_opportunity_id)){			
			$this->bean->field_defs ['sales_stage'] ['options'] = 'client_sales_stage_dom';	
		}else{			
			$this->bean->field_defs ['sales_stage'] ['options'] = 'project_sales_stage_dom';
		}		
		
		/**
		 * @author: Basudeba Rath.
		 * @date:27 Nov 2012
		 * @Desc: validate quick create opportunity form.
		 */
		echo "<script type='text/javascript'> 
				 function check_form_custom(frm,flag){
					if(flag){			
					    document.forms[frm].action.value = 'Save';
					    if(check_form(frm)){					
							DCMenu.save(frm, 'Opportunities_subpanel_save_button');						
				        }	
				    }
	
			     }</script>";
		parent::display();
		
	}
} 