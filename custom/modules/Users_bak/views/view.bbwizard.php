<?php

require_once('modules/Users/Forms.php');
require_once('modules/Configurator/Configurator.php');
require_once('custom/modules/Users/role_config.php');
require_once 'custom/include/OssTimeDate.php';


class UsersViewBbwizard extends SugarView {

	function UsersViewBbwizard() {
		global $arUserRoleConfig;
		parent::SugarView();
		//crate role id map array
		$this->arUserRolesDetails = $arUserRoleConfig;
		//there could be four roles, validate the entries for them
		//$this->arRoleNames = array('team_manager','full_pipeline','lead_reviewer','opp_reviewer');
		$this->arRoleNames = array_keys($this->arUserRolesDetails);

		$this->options['show_header'] = false;
		$this->options['show_footer'] = false;
		$this->options['show_javascript'] = false;
		$this->bError = false;
		$this->arErrorMessages = array();
		$this->current_tpl = '';
		$_SESSION['user_filter_diplayed'] = array();
	}

	function display() {
		
		
		
		global $mod_strings, $current_user, $locale, $sugar_config, $app_list_strings, $sugar_version;
		
		//if logged in user is not admin then redirect to user detail view
		if(!$current_user->is_admin){
			SugarApplication::redirect('index.php?module=Users&action=DetailView&record='.$current_user->id);
		}
		
		//if user details form is request
		if (isset($_REQUEST['getUserTpl']) && $_REQUEST['getUserTpl'] == 'true') {

			//set password rules
			$this->ss->assign('PWDSETTINGS', isset($GLOBALS['sugar_config']['passwordsetting']) ? $GLOBALS['sugar_config']['passwordsetting'] : array());

			//Instance default
			/*$this->ss->assign('COMPANY_ADDRESS', $sugar_config['company_address']);
			$this->ss->assign('COMPANY_CITY', $sugar_config['company_city']);
			$this->ss->assign('COMPANY_STATE', $sugar_config['company_state']);
			$this->ss->assign('COMPANY_ZIP', $sugar_config['company_zip']);
			$this->ss->assign('COMPANY_PHONE', settings$sugar_config['company_phone']);
			$this->ss->assign('COMPANY_FAX', $sugar_config['company_fax']);
			*/
			$obAdmin = new Administration ();
			$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
						$this->ss->assign('COMPANY_NAME', $arAdminData->settings['instance_company_name']);
                        $this->ss->assign('COMPANY_ADDRESS', $arAdminData->settings['instance_company_address']);
                        $this->ss->assign('COMPANY_CITY', $arAdminData->settings['instance_company_city']);
                        $this->ss->assign('COMPANY_STATE', $arAdminData->settings['instance_company_state']);
                        $this->ss->assign('COMPANY_ZIP', $arAdminData->settings['instance_company_zip']);
                        $this->ss->assign('COMPANY_PHONE', $arAdminData->settings['instance_company_phone']);
                        $this->ss->assign('COMPANY_FAX', $arAdminData->settings['instance_company_fax']);
			
			//parse the user detail tpl and send back
			$this->ss->display('custom/modules/Users/tpls/usersmall.tpl');
			exit(0);
		}

		//skip this wizard and never show again
		if(isset($_REQUEST['skipwizard']) && $_REQUEST['skipwizard'] == 1){
			//set it has been displayed once and user has skipped
			$admin=new Administration();
 			$admin->saveSetting('instance','bbwizard_displayed',1); 
			SugarApplication::redirect('index.php?module=Home');
		}
		
		//if user details submited then create user
		if ( isset($_REQUEST['save_users']) && $_REQUEST['save_users'] == 1) {
			
			
			//validate entries
			//if ( !$this->validateUserDetails($_REQUEST)) {

				//create all the users
				$arUserIdsSaved = $this->createInstanceUsers($_REQUEST);

				//create tpl vars for user filters
				$this->prepareUserFilterParams();
				$this->current_tpl = 'user_filters';
				// set response
				echo $arResponse =  json_encode(array('status' =>'sucess',
				   'saved_ids'=>$arUserIdsSaved,
				'HTML'=>$this->ss->fetch('custom/modules/Users/tpls/user_filters.tpl')
				));

				exit(0);
			//}else
			/* {
				//must aware user that there are certain errors
				$this->current_tpl = 'role_user';

			} */
			
		}

		$themeObject = SugarThemeRegistry::current();
		$css = $themeObject->getCSS();
		$this->ss->assign('SUGAR_CSS', $css);
		$favicon = $themeObject->getImageURL('sugar_icon.ico', false);
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
		
		
		$this->ss->assign("NUM_GRP_SEP", (empty($num_grp_sep) ? $sugar_config['default_number_grouping_seperator'] : $num_grp_sep));
		$this->ss->assign("DEC_SEP", (empty($dec_sep) ? $sugar_config['default_decimal_seperator'] : $dec_sep));
		$this->ss->assign('getNumberJs', $locale->getNumberJs());

		//// Name display format
		$this->ss->assign('default_locale_name_format', $locale->getLocaleFormatMacro($current_user));
		$this->ss->assign('getNameJs', $locale->getNameJs());

		$this->ss->assign('TIMEOPTIONS', get_select_options_with_id($sugar_config['time_formats'], $current_user->_userPreferenceFocus->getDefaultPreference('default_time_format')));
		$this->ss->assign('DATEOPTIONS', get_select_options_with_id($sugar_config['date_formats'], $current_user->_userPreferenceFocus->getDefaultPreference('default_date_format')));
		$this->ss->assign("MAIL_SENDTYPE", get_select_options_with_id($app_list_strings['notifymail_sendtype'], $current_user->getPreference('mail_sendtype')));
		$this->ss->assign("NEW_EMAIL", $current_user->emailAddress->getEmailAddressWidgetEditView($current_user->id, $current_user->module_dir));
		$this->ss->assign('EMAIL_LINK_TYPE', get_select_options_with_id($app_list_strings['dom_email_link_type'], $current_user->getPreference('email_link_type')));

		$this->ss->assign('IS_ERRORS', $this->bError);
		$this->ss->assign('ERROR_MESSAGES', $this->arErrorMessages);
		#######################################
		###### VALIDATE PACAKAGE DETAILS ######
		//Get Package details for this istance and set limit for user
		$arPackageData = $this->getInstancePackageDetail();
		//get how many active users exists in the system		
		$allActiveusers = $this->bean->get_full_list('',' users.status="Active" ');		
		
		$this->ss->assign('MAX_USER_ALLOWED', $arPackageData['no_of_users']);
		$this->ss->assign('NUM_INSTANCE_USERS',$arPackageData['no_of_users'] - count($allActiveusers));
		
		$bCritical=	false;
		$arCriticalErorr = array();
		//check for user limits
		if(count($allActiveusers) >= $arPackageData['no_of_users']){
			$bCritical= true;
			$arCriticalErorr[] = $mod_strings['ERROR_USER_LIMIT_EXCEED'];		
		}
		//check for expiry date
		if(isset($arPackageData['expiry_date']) && strtotime(date("Y-m-d")) > strtotime($arPackageData['expiry_date'])){
			$bCritical= true;
			$arCriticalErorr[] = $mod_strings['ERROR_PACKAGE_EXPIRED'];
		}
		
		//options for location
		$this->ss->assign('GEO_OPTION', array(
				'project_location' => 'Project Information',
				'client_location' => 'Client Information',
		));
		
		//if request for edit geo filter 
		if(isset($_REQUEST['geofilters'])){
			
			$admin=new Administration();
			$admin=$admin->retrieveSettings('instance',true);
			$this->ss->assign('GEO_OPTION_SELECTED',$admin->settings['instance_geo_filter']);
			$this->ss->assign('GEO_OPTION_SELECTED_FOR_CLIENTS',$admin->settings['instance_geo_filter_for_clients']);
		}

		
		$this->ss->assign('CRITICAL_ERROR', $bCritical);
		$this->ss->assign('CRITICAL_ERROR_MESSAGES', $arCriticalErorr);	
		
		#### END OF VALIDATE PACAKAGE DETAILS ####
		##########################################
		if($this->current_tpl == 'role_user'){

			echo $arResponse =  json_encode(array('status' =>($this->bError)?'failed':'success',
			'HTML'=>$this->ss->fetch('custom/modules/Users/tpls/role_users.tpl')
			));
			exit(0);
		}
		$this->ss->display('custom/modules/Users/tpls/bbwizard.tpl');
	}

	/*
	* function to crate users as per the selection
	*/
	function createInstanceUsers($arParams=array()) {
		
		//save users
		global $current_user,$app_list_strings,$db;
		$arUserSavedId = array();
		foreach($arParams as $stRole => $arUserDetails){

			if(in_array($stRole,$this->arRoleNames) || $stRole=='admin') {//pr($arUserDetails);
				//validate name is entered or not
				$iTotal = count($arUserDetails['first_name']);
				for($iCount = 0;$iCount < $iTotal;$iCount++)
				{
					$obNewUser = new User();
					$obNewUser->first_name = $arUserDetails['first_name'][$iCount];
					$obNewUser->last_name = $arUserDetails['last_name'][$iCount];
					$obNewUser->user_name = $arUserDetails['user_name'][$iCount];
					$obNewUser->email1 = $arUserDetails['email'][$iCount];
					$obNewUser->user_hash = md5($arUserDetails['password'][$iCount]);
					$obNewUser->pwd_last_changed = date("Y-m-d H:i:s");
					
					//new fields
					$obNewUser->company_name = $arUserDetails['company_name'][$iCount];
					$obNewUser->address_street = $arUserDetails['address'][$iCount];
					$obNewUser->address_city = $arUserDetails['city'][$iCount];
					$obNewUser->address_state = $arUserDetails['state'][$iCount];
					$obNewUser->address_postalcode = $arUserDetails['zip'][$iCount];
					$obNewUser->phone_work = str_replace('-','',$arUserDetails['phone'][$iCount]);
					$obNewUser->phone_fax = str_replace('-','',$arUserDetails['fax'][$iCount]);
					
					
					$obNewUser->status = 'Active';
					$obNewUser->employee_status = 'Active';
					
					if($stRole == 'admin'){
						$obNewUser->is_admin = '1';
					}
					
					//check if this email address is opted out
					$stEmailDetailsSQL = 'SELECT * FROM email_addresses WHERE email_address="'.$arUserDetails['email'][$iCount] .'"';
					$rsResult = $db->query($stEmailDetailsSQL);
					$arEmailDetails = $db->fetchByAssoc($rsResult);
					
					//as per the roles assign them roles
					$arUserSavedId[] = $obNewUser->save();
					$obNewUser->resetPreferences();
					
					$obOssTimeDate = new OssTimeDate();
					//get users timezone 
					$stSelecteState = $GLOBALS['app_list_strings']['state_dom'][$arUserDetails['state'][$iCount]];
					$stTimeZone = $GLOBALS['app_list_strings']['state_tz_dom'][$stSelecteState];
					$obTimeZone = $obOssTimeDate->getTimeZone($stTimeZone);
					$stTimeZone = $obTimeZone ->getName (); 
					$obNewUser->_userPreferenceFocus->setPreference('timezone', $stTimeZone, 'global');
					
					//reset to opt out this email
					if($arEmailDetails != false && $arEmailDetails['opt_out'] == '1'){
						$stUpdateEmailSQL = 'UPDATE email_addresses set opt_out =1 WHERE email_address="'.$arUserDetails['email'][$iCount].'"';					
						$db->query($stUpdateEmailSQL);
					}
					
					$obNewUser->set_relationship('acl_roles_users', array('role_id' => $this->arUserRolesDetails[$stRole], 'user_id' => $obNewUser->id));
					//set it has been displayed once
					$admin=new Administration();
 					$admin->saveSetting('instance','bbwizard_displayed',1); 
					//send registration email
					$this->sendRegistrationEmail($obNewUser->id,$arUserDetails['password'][$iCount]);
					
					
				}

			}

		}
		
		return $arUserSavedId;
		
	}

	/*
	*/
	function sendRegistrationEmail($usrId,$stPassword){
		
		global $app_strings,$sugar_config,$new_pwd;	

		$mod_strings=return_module_language('','Users');
		$res=$GLOBALS['sugar_config']['passwordsetting'];
		$regexmail = "/^\w+(['\.\-\+]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+\$/";

		//  Retrieve user
		$this->bean->retrieve($usrId);
		$user_array = array(
		'first_name'  => $this->bean->first_name,
		'last_name'   => $this->bean->last_name,
		'user_name'   => $this->bean->user_name,
		'email'       => $this->bean->email1,
		'password'    => $stPassword,
		);
		//pr($user_array);
		//die;
		$var  = $user_array['first_name'];
		$var1 = $user_array['last_name'];
		$var2 = $sugar_config['site_url'];
		$var3 = $user_array['password'];
		$var4 = $user_array['user_name'];
		
		// email related code .
		$emailTemp_id = $res['generatepasswordtmpl'];
		$emailTemp = new EmailTemplate();
		$emailTemp->disable_row_level_security = true;
		if ($emailTemp->retrieve($emailTemp_id) == '') {
			echo $mod_strings['LBL_EMAIL_TEMPLATE_MISSING'];
			$new_pwd='4';
			return;
		}
		$htmlBody = $emailTemp->body_html;
		$body = $emailTemp->body;		

		$htmlBody = str_replace('$contact_user_user_hash', $var3, $htmlBody);
		$body = str_replace('$contact_user_user_hash', $var3, $body);
		
		// EmailId.

		$htmlBody = str_replace('$contact_user_user_name', $var4, $htmlBody);
		$body = str_replace('$contact_user_user_name', $var4, $body);
		
		// siteURL.
		$htmlBody = str_replace('$config_site_url', $var2, $htmlBody);
		$body = str_replace('$config_site_url', $var2, $body);

		// lastname.
		/*
		$htmlBody = str_replace('$contact_user_user_name', $var1, $htmlBody);
		$body = str_replace('$contact_user_user_name', $var1, $body);
		*/

		// assigning these values to email templates.

		$emailTemp->body_html = $htmlBody;
		$emailTemp->body = $body;

		require_once('include/SugarPHPMailer.php');

		$itemail=$user_array['email'];
		$emailObj = new Email();
		// print_r($emailObj);die;
		$defaults = $emailObj->getSystemDefaultEmail();
		// print_r($defaults['email']);die;
		$mail = new SugarPHPMailer();
		// print_r($mail);die;
		$mail->setMailerForSystem();
		$mail->From = $defaults['email'];
		$mail->FromName = $defaults['name'];
		$mail->ClearAllRecipients();
		$mail->ClearReplyTos();
		$mail->Subject=from_html($emailTemp->subject);
		if($emailTemp->text_only != 1){
			$mail->IsHTML(true);
			$mail->Body=from_html($emailTemp->body_html);
			$mail->AltBody=from_html($emailTemp->body);
		}
		else {
			$mail->Body_html=from_html($emailTemp->body_html);
			$mail->Body=from_html($emailTemp->body);
		}
		
		/*
		if($mail->Body == '' && $current_user->is_admin){
		echo $app_strings['LBL_EMAIL_TEMPLATE_EDIT_PLAIN_TEXT'];
		$new_pwd='4';
		return;}
		if($mail->Mailer == 'smtp' && $mail->Host ==''&& $current_user->is_admin){
		echo $mod_strings['ERR_SERVER_SMTP_EMPTY'];
		$new_pwd='4';
		return;}
		*/
		$mail->prepForOutbound();
		$hasRecipients = false;
		// print_r($itemail);die;
		if (!empty($itemail)){
			if($hasRecipients){
				$mail->AddBCC($itemail);
			}else{
				$mail->AddAddress($itemail);
			}
			$hasRecipients = true;
		}
		$success = false;
		if($hasRecipients){
			$success = @$mail->Send();
			
		}
		
		//now create email
		if($success){

			$emailObj->team_id = 1;
			$emailObj->to_addrs= '';
			$emailObj->type= 'archived';
			$emailObj->deleted = '0';
			$emailObj->name = $mail->Subject ;
			$emailObj->description = $mail->Body;
			$emailObj->description_html =null;
			$emailObj->from_addr = $mail->From;
			$emailObj->parent_type = 'User';
			// $emailObj->date_sent =TimeDate::getInstance()->nowDb();
			// $emailObj->modified_user_id = '1';
			// $emailObj->created_by = '1';
			$emailObj->status='sent';
			$retId = $emailObj->save();
		}

	}
	/*
	* function to crate users as per the selection
	*/
	function validateUserDetails($arParams=array()) {

		GLOBAL $mod_strings,$app_strings;

		//pr($arParams);
		//set defult value for error flag
		$this->bError = false;
		if(count($arParams)>1){

			foreach($arParams as $stRole => $arUserDetails){
				
				if(in_array($stRole,$this->arRoleNames) || $stRole=='admin') {//pr($arUserDetails);
					
					//validate name is entered or not
					$iTotal = count($arUserDetails['first_name']);
					for($iCount = 0;$iCount < $iTotal;$iCount++)
					//$iCount =0;
					{
						//                     /  var_dump(is_array($arUserDetails['first_name'][$iCount]));
						//$stFirstName = $arUserDetails['first_name'][$iCount];
						if( isset($arUserDetails['first_name'][$iCount]) && trim((string)$arUserDetails['first_name'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['first_name'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_FIRST_NAME'];
						}
						if(isset($arUserDetails['last_name'][$iCount]) && trim($arUserDetails['last_name'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['last_name'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_LAST_NAME'];
						}
						//validate user name
						if(isset($arUserDetails['user_name'][$iCount]) && trim($arUserDetails['user_name'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['user_name'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_USER_NAME'];
						}
						//validate if user already exists
						if(isset($arUserDetails['user_name'][$iCount]) && trim($arUserDetails['user_name'][$iCount]) != ''){

							$obUsers = new User();
							$arUserDetail = $obUsers->get_detail('', ' users.user_name ="'.$arUserDetails['user_name'][$iCount].'"');
							if(isset($arUserDetail['bean']->id)){
								$this->bError = true;
								$this->arErrorMessages[$stRole]['user_name'][$iCount]= $mod_strings['LBL_ERROR_USER_NAME_EXISTS'];
							}
						}					
						
						if(isset($arUserDetails['email'][$iCount]) && !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $arUserDetails['email'][$iCount])){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['email'][$iCount]= $mod_strings['ERROR_INVALID_EMAIL'];
						}
						if(isset($arUserDetails['email'][$iCount]) && trim($arUserDetails['email'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['email'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_EMAIL'];
						}
						if(isset($arUserDetails['password'][$iCount]) && trim($arUserDetails['password'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['password'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_PASSWORD'];
						}

						if(isset($arUserDetails['con_password'][$iCount]) && trim($arUserDetails['con_password'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['con_password'][]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_CONFIRM_PASSWORD'];
						}

						//validate passwords must be same
						if(isset($arUserDetails['con_password'][$iCount]) && isset($arUserDetails['password'][$iCount])
						&& trim($arUserDetails['con_password'][$iCount]) != '' && trim($arUserDetails['password'][$iCount]) != ''
						&& trim($arUserDetails['con_password'][$iCount]) != trim($arUserDetails['password'][$iCount])
						){

							$this->bError = true;
							$this->arErrorMessages[$stRole]['con_password'][]= $mod_strings['ERR_REENTER_PASSWORDS'];

						}
						
						//new fields
						if( isset($arUserDetails['address'][$iCount]) && trim((string)$arUserDetails['address'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['address'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_ADDRESS'];
						}
						
						if( isset($arUserDetails['city'][$iCount]) && trim((string)$arUserDetails['city'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['city'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_CITY'];
						}
						
						if( isset($arUserDetails['state'][$iCount]) && trim((string)$arUserDetails['state'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['state'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_STATE'];
						}
						
						
						if( isset($arUserDetails['zip'][$iCount]) && trim((string)$arUserDetails['zip'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['zip'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_ZIP'];
						}
						
						if( isset($arUserDetails['phone'][$iCount]) && trim((string)$arUserDetails['phone'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['phone'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_PHONE'];
						}
						
						if( isset($arUserDetails['phone'][$iCount]) && trim((string)$arUserDetails['phone'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['phone'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_PHONE'];
						}
						
						$arPhoneValid = validatePhone($arUserDetails['phone'][$iCount], 'Phone');
					
						//validate phone format
						if(trim((string)$arUserDetails['phone'][$iCount]) != '' && !$arPhoneValid['bool']){
							$this->bError = true;
						 	$this->arErrorMessages[$stRole]['phone'][$iCount]= $arPhoneValid['error'];
						}
						
						if( isset($arUserDetails['fax'][$iCount]) && trim((string)$arUserDetails['fax'][$iCount]) == ''){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['fax'][$iCount]= $app_strings['ERR_MISSING_REQUIRED_FIELDS'].":".$mod_strings['LBL_FAX'];
						}
						//validate phone format
						$arPhoneValid = validatePhone($arUserDetails['fax'][$iCount],'Fax');
						
						if(trim((string)$arUserDetails['fax'][$iCount]) != '' && !$arPhoneValid['bool']){
							$this->bError = true;
							$this->arErrorMessages[$stRole]['fax'][$iCount]= $arPhoneValid['error'];
						}

					}
				}
			}

		}
		
		return $this->bError;
	}

	function prepareUserFilterParams()
	{
		//get all the created users and there role in this instance
		$obUsers = new User();
		$arAllUsers =  $obUsers->get_full_list(" concat(users.first_name,users.last_name)",' users.is_admin=0 AND users.status="Active"  and users.deleted = 0' );
		//get all the roles
		foreach($arAllUsers as $obUserData){
			$arUserDom[$obUserData->id] = $obUserData->name;
			$arUserRoles[$obUserData->id] = array_shift(ACLRole::getUserRoles($obUserData->id));
		}

		$this->ss->assign('AR_URER_ROLE_MAP',json_encode($arUserRoles));
		$this->ss->assign('DOM_USERS',$arUserDom);
		$this->ss->assign('DOM_STATE',$GLOBALS['app_list_strings']['state_dom']);
		$this->ss->assign('DOM_TYPE_PL',$GLOBALS['app_list_strings']['project_type_dom']);
		$this->ss->assign('DOM_LABOUR_OTIONS',array('Union','Non Union','Prevaling Wage','Undefined'));
		//get

	}

	/*
	Function to validate package detials
	*/
	function getInstancePackageDetail(){
		
		require_once 'include/nusoap/nusoap.php';
		require_once 'custom/modules/Users/filters/instancePackage.class.php';
        global $sugar_config;
        
        $obInstancePackage = new instancePackage();
        $arReturn = $obInstancePackage->getPacakgeDetails();
        return $arReturn;    
     	
	}


}


/**
 * function to validate phone number
 * @param string $stPhone
 * @param string $stLable
 * @return Ambigous <boolean, string>
 */
function validatePhone($stPhone,$stLable){

	$arReturn['bool']= true;

	if (trim($stPhone) != '' && strlen(str_replace("-","",$stPhone)) != 10  ) {
		$arReturn['bool'] = false;
		$arReturn['error'] = 'Invalid Value: '.$stLable.' (XXX-XXX-XXXX or XXXXXXXXXX)';
	}

	if( strstr($stPhone,'-') && !preg_match('/\d{3}-\d{3}-\d{4}/', $stPhone)){
		$arReturn['bool'] = false;
		$arReturn['error'] =  'Invalid Value: '.$stLable.' (XXX-XXX-XXXX or XXXXXXXXXX) ';
	}

	return  $arReturn;
}

?>
