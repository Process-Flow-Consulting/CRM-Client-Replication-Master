<?php
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */
/**
 * Instance roles View
 * @author Mohit Kumar Gupta
 * @date 19-06-2015
 */
require_once('modules/Users/Forms.php');
require_once('modules/Configurator/Configurator.php');

class UsersViewResetmyroles extends SugarView
{
    /**
     * default constructor
     */
    function __construct()
    {
        
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_javascript'] = false;
        parent::SugarView();
    }
    
    /**
     * default display function
     * extended from parent
     * @see SugarView::display()
     */
    function display()
    {
		global $db;
        //if request to save the target calssifications
        if(isset($_POST['save'])){
			
			$this->saveGlobalRoles();			
			$this->ss->assign('MSG_SAVED','Role(s) saved successfully.');
		}
		
		//total roles in system
		$rolesArray = array_filter($GLOBALS['app_list_strings']['role_dom']);		
		
		//selected roles for instance
		$selectedRolesArray = array();
		
		//get saved ROLES
		$obAdmin = new Administration ();
		$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
		$arSelectedRoles = $arAdminData->settings['instance_global_roles'];
		if (isset($arSelectedRoles) && !empty($arSelectedRoles)) {
            $arSelectedIds = json_decode(base64_decode($arSelectedRoles));
		    $selectedRolesArray = array_combine($arSelectedIds,$arSelectedIds);
		}
		
		//array walk use for getting roles array difference
		array_walk($rolesArray,array($this, 'decodeArrayValue'));
		array_walk($selectedRolesArray,array($this, 'decodeArrayValue'));
		$roleDiff = array_diff($rolesArray, $selectedRolesArray);
		array_walk($roleDiff,array($this, 'encodeArrayValue'));
		array_walk($selectedRolesArray,array($this, 'encodeArrayValue'));
		
		$this->ss->assign('SAVED_ROLES_CLASS', array_filter($selectedRolesArray));
		$this->ss->assign('TOTAL_ROLES_CLASS', $roleDiff);
		
        $themeObject = SugarThemeRegistry::current();
        $css = $themeObject->getCSS();
        $this->ss->assign('SUGAR_CSS', $css);
        $favicon = $themeObject->getImageURL('sugar_icon.ico', false);
        $returnModule = isset($_REQUEST['return_module']) ? $_REQUEST['return_module']:'';
		$returnAction = isset($_REQUEST['return_action']) ? $_REQUEST['return_action']:'';
		
		//if request is coming from first time instance creation
        if(isset($_REQUEST['transfer_action']) && $_REQUEST['transfer_action'] == 'bbwizard') {
			$returnModule = 'Users';
			$returnAction = 'bbwizard';
		}
        
        $this->ss->assign('RETURN_MODULE', $returnModule);
        $this->ss->assign('RETURN_ACTION',$returnAction);       
        
        $this->ss->assign('FAVICON_URL', getJSPath($favicon));
        $this->ss->assign('CSS', '<link rel="stylesheet" type="text/css" href="' . SugarThemeRegistry::current()->getCSSURL('wizard.css') . '" />');
        $this->ss->assign('JAVASCRIPT', user_get_validate_record_js() . user_get_chooser_js() . user_get_confsettings_js());
        $this->ss->assign('PRINT_URL', 'index.php?' . $GLOBALS['request_string']);
                
        // get javascript
        ob_start();
        $this->options['show_javascript'] = true;
        $this->renderJavascript();
        $this->options['show_javascript'] = false;
        $this->ss->assign("SUGAR_JS", ob_get_contents() . $themeObject->getJS());
        ob_end_clean();
        
        $this->ss->display('custom/modules/Users/tpls/resetmyroles.tpl');
        
    }
    
	/**
     * Function to save roles
     */
	function saveGlobalRoles(){
		$selectedRoles = array();
		if (isset($_REQUEST['my_global_roles']) && !empty($_REQUEST['my_global_roles']))
		    $selectedRoles = array_filter($_REQUEST['my_global_roles']);
		
		if(count($selectedRoles)>0){
		    $GLOBALS['db']->query('DELETE FROM config where category = "instance" AND name="global_roles"');
			$selectedRoles = base64_encode(json_encode($selectedRoles));
			$obAdmin=new Administration();
			
			//reset the roles			
 			$obAdmin->saveSetting('instance','global_roles',$selectedRoles); 
		}
		
		if(isset($_REQUEST['return_module']) && !empty($_REQUEST['return_module']) && isset($_REQUEST['return_action']) && !empty($_REQUEST['return_action'])){
						
			SugarApplication::redirect('index.php?module='.$_REQUEST['return_module'].'&action='.$_REQUEST['return_action']);
			
		}		
	}
	
	/**
	 * decode array value
	 * @param string $value
	 * @param string $key
	 */
	function decodeArrayValue (&$value, &$key) {
		$value = str_replace(array("'","&#039;"),array("&|&","&|&"),$value);
	}
	
	/**
	 * decode array value
	 * @param string $value
	 * @param string $key
	 */
	function encodeArrayValue (&$value, &$key) {
	    $value = str_replace("&|&","'",$value);
	}
}
