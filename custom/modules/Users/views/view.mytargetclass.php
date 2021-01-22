<?php
/**
 * Instance Target Classification View
 */
require_once('modules/Users/Forms.php');
require_once('modules/Configurator/Configurator.php');
require_once('custom/modules/Users/role_config.php');
require_once 'custom/include/OssTimeDate.php';

class UsersViewMytargetclass extends SugarView
{
    function __construct()
    {
        
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_javascript'] = false;
        parent::SugarView();
    }
    
    
    function display()
    {
		global $db;
		//check if its a request to change the selected classifiction
		if(isset($_REQUEST['handleRequest'])){			
			$this->handleRequests();				
		}
		
        //if request to save the target calssifications
        if(isset($_POST['save'])){
			
			$this->saveTargetClassification();			
			$this->ss->assign('MSG_SAVED','Target classification(s) saved successfully.');
		}
		
		//get saved targetClassifications
		$obAdmin = new Administration ();
		$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
		$obTargetClass = new oss_Classification();
		$arSelectedClass = $arAdminData->settings['instance_target_classifications'];
		$arSelectedId = json_decode(base64_decode($arSelectedClass));
		$arSavedTargetClass = $obTargetClass->get_full_list(' name ASC',' oss_classification.id IN ("'.implode('","',$arSelectedId).'")');
		$arSavedTargetClassifications = array();
		if(is_array($arSavedTargetClass)) {
		foreach($arSavedTargetClass as $obSavedClass){
			
			$arSavedTargetClassifications[$obSavedClass->id] = $obSavedClass->name ;
		}
		}
		
		$this->ss->assign('SAVED_TARGET_CLASS_HTML', get_select_options_with_id($arSavedTargetClassifications,''));
        $themeObject = SugarThemeRegistry::current();
        $css = $themeObject->getCSS();
        $this->ss->assign('SUGAR_CSS', $css);
        $favicon = $themeObject->getImageURL('sugar_icon.ico', false);
        
        $this->ss->assign('RETURN_MODULE',(isset($_REQUEST['return_module']))?$_REQUEST['return_module']:'');
        $this->ss->assign('RETURN_ACTION',(isset($_REQUEST['return_action']))?$_REQUEST['return_action']:'');       
        $this->ss->assign('TRANSFER_ACTION',(isset($_REQUEST['transfer_action']))?$_REQUEST['transfer_action']:'');
        
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
        
        $this->ss->display('custom/modules/Users/tpls/mytargetcalss.tpl');
        
    }
    /**
     * Function to handle various ajax requests
     */
    function handleRequests(){
		
		
		global $db;
		$arSelectedOptions= array();
		
		if(isset($_POST['selected_list'])){
			
			$arSelected = isset($_POST['selected_list'])?$_POST['selected_list']:array();
			$arAlreadySelected = isset($_POST['my_target_classifications'])?$_POST['my_target_classifications']:array();
			$arSelectedList = array_unique(array_merge($arSelected,$arAlreadySelected));
			$rsResult = $db->query('SELECT id,name  FROM oss_classification WHERE id IN ( "'.implode('","',$arSelectedList).'")  ORDER BY name ASC');		   

			while($arRowData = $db->fetchByAssoc($rsResult)){
				
				$arSelectedOptions[$arRowData['id']]= $arRowData['name'];
							
			}
			echo get_select_options_with_id($arSelectedOptions,'');
			die;
		}
		
		
		if(isset($_POST['select_all']) && trim($_POST['select_all']) =='1'){
			
			$rsResult = $db->query('SELECT id,name  FROM oss_classification where deleted="0" ORDER BY name ASC ');		   

			while($arRowData = $db->fetchByAssoc($rsResult)){
				
				$arSelectedOptions[$arRowData['id']]= $arRowData['name'];
							
			}
			echo get_select_options_with_id($arSelectedOptions,'');
			die;
		}
		
		die();
		
	}
	/**
     * Function to save target classifications
     */
	function saveTargetClassification(){
		
		
		$arSelectedClass = isset($_REQUEST['selected_classifications'])?explode('|',$_REQUEST['selected_classifications']):array();
		//echo '<pre>';print_r($arSelectedClass);echo '</pre>';die;
		$GLOBALS['db']->query('DELETE FROM config where category = "instance" AND name="target_classifications" ');
		if(count($arSelectedClass)>0){
			
			$stTargetClass = base64_encode(json_encode($arSelectedClass));
			$obAdmin=new Administration();
			//reset the classification
			
 			$obAdmin->saveSetting('instance','target_classifications',$stTargetClass); 
			
		}
		
		if(isset($_REQUEST['return_module']) && !empty($_REQUEST['return_module']) && isset($_REQUEST['return_action']) && !empty($_REQUEST['return_action'])){
			
			//Pull Instance Project Leads 
			exec('/usr/local/zend/bin/php -f cmdscripts/PullBBHCommand.php ');
			
			//if request is coming from first time instance creation
			$addTransfer = '';
			if(isset($_REQUEST['transfer_action']) && $_REQUEST['transfer_action'] == 'bbwizard') {
				$addTransfer = '&transfer_action=bbwizard';			
			}
			SugarApplication::redirect('index.php?module='.$_REQUEST['return_module'].'&action='.$_REQUEST['return_action'].$addTransfer);
			
		}		
		
	}
} 
