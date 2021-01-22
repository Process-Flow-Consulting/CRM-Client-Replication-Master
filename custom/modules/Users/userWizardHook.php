<?php
require_once 'custom/modules/Users/filters/instancePackage.class.php';

class userWizardHook {
//Changes made by parveen badoni on 03/07/2014 Name of function changed as it was acting as constructor and when object of same class was created, it was giving warning for missing parameters.
	function userWizardHookFunc($bean, $event, $arguments) {
		global $current_user;
		
		//load Package Details at the time of login
		$obInstancePackage = new instancePackage();
		$obInstancePackage->getPacakgeDetails();
		
		
		if ($current_user->is_admin) {
			//$obUserPref = new UserPreference ();
			
			// $arPref =
			// $current_user->setPreference('bbwizard','true','global');
			//$arPrefr = $current_user->getPreference ( 'bbwizard' );
			$admin=new Administration();
			$admin=$admin->retrieveSettings('instance',true);
			
			//check if Target classification are saved for instance
			//@Modified By Mohit kumar Gupta and roles condition also there
			//@date 25-06-2015 
			if (!isset($admin->settings['instance_target_classifications']) && (!isset($admin->settings['instance_bbwizard_displayed']))) {
				//SugarApplication::redirect ( "index.php?module=Users&action=mytargetclass&return_module=Users&return_action=bbwizard" );
				SugarApplication::redirect ( "index.php?module=Users&action=mytargetclass&return_module=Users&return_action=resetmyroles&transfer_action=bbwizard" );
			} 
			
			//check if roles are saved for instance
			if (!isset($admin->settings['instance_global_roles']) && (!isset($admin->settings['instance_bbwizard_displayed']))) {
				SugarApplication::redirect ( "index.php?module=Users&action=resetmyroles&return_module=Users&return_action=bbwizard" );
			}

			
			if (!isset($admin->settings['instance_bbwizard_displayed']) || $admin->settings['instance_bbwizard_displayed'] != '1') {
				
				// redirect to bluebook user wizard
				SugarApplication::redirect ( "index.php?module=Users&action=bbwizard" );
			
			}
		
		}
	
	}
	
//Changes made by parveen badoni on 03/07/2014 Values of parameters set default as null, so that when parameters are not sent, it doesnt result in warning for missing parameters.
	function checkTermCondition($bean, $event=null, $arguments=null){
		global $mod_strings;		

		$user_name = isset($_REQUEST['user_name'])
			? $_REQUEST['user_name'] : '';

		$password = isset($_REQUEST['user_password'])
			? $_REQUEST['user_password'] : '';

		if(!empty($user_name) && !empty($password)){
			if(!isset($_REQUEST['tnc'])){	
				$login_vars = $GLOBALS['app']->getLoginVars(false);
				$_SESSION['login_error'] = $mod_strings['LBL_NO_TNC'];
				$url ="index.php?module=Users&action=Login";
					if(!empty($login_vars))
					{
						$url .= '&' . http_build_query($login_vars);
					}
				// construct redirect url
				$url = 'Location: '.$url;
				// check for presence of a mobile device, redirect accordingly
				if(isset($_SESSION['isMobile'])){
					$url = $url . '&mobile=1';
				}
				
				$_SESSION['login_user_name'] = $user_name;
				$_SESSION['login_password'] = $password;
				sugar_cleanup();			
				header($url);
				exit;

			}
	}
		
	}
}

