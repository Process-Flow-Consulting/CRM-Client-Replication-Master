<?php
require_once 'modules/EmailTemplates/EmailTemplate.php';
require_once 'custom/include/common_functions.php';

class CustomEmailTemplate extends EmailTemplate{
	
	function CustomEmailTemplate(){
		parent::EmailTemplate();
	}
	
	
	/**
	 * Generates the extended field_defs for creating macros
	 * @param object $bean SugarBean
	 * @param string $prefix "contact_", "user_" etc.
	 * @return
	 */
	function generateFieldDefsJS() {
		global $current_user;

		$contact = new Contact();
		$account = new Account();
		$lead = new Lead();
		$prospect = new Prospect();
		$quote = new AOS_Quotes();
		$opportunity = new Opportunity();
	
		$loopControl = array(
				'Contacts' => array(
						'Contacts' => $contact,
						'Leads' => $lead,
						'Prospects' => $prospect,
				),
				'Accounts' => array(
						'Accounts' => $account,
				),
				'Users' => array(
						'Users' => $current_user,
				),
				'AOS_Quotes' => array(
						'AOS_Quotes' => $quote,
				),
				'Opportunities' => array(
						'Opportunities' => $opportunity,
				),
		);
	
		$prefixes = array(
				'Contacts' => 'contact_',
				'Accounts' => 'account_',
				'Users'	=> 'contact_user_',
				'AOS_Quotes' => 'aos_quotes_',
				'Opportunities' => 'opportunity_'
		);
	
		$collection = array();
		foreach($loopControl as $collectionKey => $beans) {
			$collection[$collectionKey] = array();
			foreach($beans as $beankey => $bean) {
	
				foreach($bean->field_defs as $key => $field_def) {
					if(	($field_def['type'] == 'relate' && empty($field_def['custom_type'])) ||
							($field_def['type'] == 'assigned_user_name' || $field_def['type'] =='link') ||
							($field_def['type'] == 'bool') ||
							(in_array($field_def['name'], $this->badFields)) ) {
						continue;
					}
					if(!isset($field_def['vname'])) {
						//echo $key;
					}
					// valid def found, process
					$optionKey = strtolower("{$prefixes[$collectionKey]}{$key}");
					$optionLabel = preg_replace('/:$/', "", translate($field_def['vname'], $beankey));
					$dup=1;
					foreach ($collection[$collectionKey] as $value){
						if($value['name']==$optionKey){
							$dup=0;
							break;
						}
					}
					if($dup)
						$collection[$collectionKey][] = array("name" => $optionKey, "value" => $optionLabel);
				}
			}
		}
		
		$collection['Instance'][] = array(
				"name" => 'instance_client', "value" => 'instance_client',
		);
		$collection['Instance'][] = array(
				"name" => 'instance_client_contact', "value" => 'instance_client_contact',
		);
		$collection['Instance'][] = array(
				"name" => 'instance_client_contact_address', "value" => 'instance_client_contact_address',
		);
	
		$json = getJSONobj();
		$ret = "var field_defs = ";
		$ret .= $json->encode($collection, false);
		$ret .= ";";
		return $ret;
	}
	
	function parse_template_bean($string, $bean_name, &$focus) {
		global $current_user, $sugar_config;
		global $beanFiles, $beanList;
		global $app_list_strings;
		$repl_arr = array();
	
		// cn: bug 9277 - create a replace array with empty strings to blank-out invalid vars
		if(!class_exists('Account'))
			if(!class_exists('Contact'))
			if(!class_exists('Leads'))
			if(!class_exists('Opportunity'))

	
		require_once('modules/Accounts/Account.php');
		$acct = new Account();
		$acct->disable_row_level_security = true;
		$contact = new Contact();
		$contact->disable_row_level_security = true;
		$lead = new Lead();
		$lead->disable_row_level_security = true;
		$prospect = new Prospect();
		$prospect->disable_row_level_security= true;
		$opportunity = new Opportunity();
		$opportunity->disable_row_level_security = true;
		
	
		foreach($lead->field_defs as $field_def) {
			if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
				continue;
			}
			$repl_arr["contact_".$field_def['name']] = '';
			$repl_arr["contact_account_".$field_def['name']] = '';
		}
		foreach($prospect->field_defs as $field_def) {
			if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
				continue;
			}
			$repl_arr["contact_".$field_def['name']] = '';
			$repl_arr["contact_account_".$field_def['name']] = '';
		}
		foreach($contact->field_defs as $field_def) {
			if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
				continue;
			}
			$repl_arr["contact_".$field_def['name']] = '';
			$repl_arr["contact_account_".$field_def['name']] = '';
		}
		foreach($acct->field_defs as $field_def) {
			if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
				continue;
			}
			$repl_arr["account_".$field_def['name']] = '';
			$repl_arr["account_contact_".$field_def['name']] = '';
		}
		
		foreach($opportunity->field_defs as $field_def) {
			if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
				continue;
			}
			$repl_arr["opportunity_".$field_def['name']] = '';
			$repl_arr["opportunity_contact_".$field_def['name']] = '';
			$repl_arr["opportunity_account_".$field_def['name']] = '';
		}
		// cn: end bug 9277 fix
	
	
		// feel for Parent account, only for Contacts traditionally, but written for future expansion
		if(isset($focus->account_id) && !empty($focus->account_id)) {
			$acct->retrieve($focus->account_id);
		}
		
		if(isset($focus->billing_account_id) && !empty($focus->billing_account_id)) {
			$acct->retrieve($focus->billing_account_id);
		}
		
		if(isset($focus->billing_contact_id) && !empty($focus->billing_contact_id)) {
			$contact->retrieve($focus->billing_contact_id);
		}
		
		if(isset($focus->opportunity_id) && !empty($focus->opportunity_id)){
			$opportunity->retrieve($focus->opportunity_id);
		}
	
		if($bean_name == 'Contacts') {
			// cn: bug 9277 - email templates not loading account/opp info for templates
			if(!empty($acct->id)) {
				foreach($acct->field_defs as $field_def) {
					if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
						continue;
					}
	
					if($field_def['type'] == 'enum') {
						$translated = translate($field_def['options'], 'Accounts' ,$acct->$field_def['name']);
	
						if(isset($translated) && ! is_array($translated)) {
							$repl_arr["account_".$field_def['name']] = $translated;
							$repl_arr["contact_account_".$field_def['name']] = $translated;
						} else { // unset enum field, make sure we have a match string to replace with ""
							$repl_arr["account_".$field_def['name']] = '';
							$repl_arr["contact_account_".$field_def['name']] = '';
						}
					} else {
						$repl_arr["account_".$field_def['name']] = $acct->$field_def['name'];
						$repl_arr["contact_account_".$field_def['name']] = $acct->$field_def['name'];
					}
				}
			}
	
			if(!empty($focus->assigned_user_id)) {
				$user = new User();
				$user->retrieve($focus->assigned_user_id);
				$repl_arr = EmailTemplate::_parseUserValues($repl_arr, $user);
			}
		} elseif($bean_name == 'Users') {
			/**
			 * This section of code will on do work when a blank Contact, Lead,
			 * etc. is passed in to parse the contact_* vars.  At this point,
			 * $current_user will be used to fill in the blanks.
			 */
			$repl_arr = EmailTemplate::_parseUserValues($repl_arr, $current_user);
			
		}elseif($bean_name == 'AOS_Quotes') {
			
			if(!empty($acct->id)) {
				foreach($acct->field_defs as $field_def) {
					if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
						continue;
					}
			
					if($field_def['type'] == 'enum') {
						$translated = translate($field_def['options'], 'Accounts' ,$acct->$field_def['name']);
			
						if(isset($translated) && ! is_array($translated)) {
							$repl_arr["account_".$field_def['name']] = $translated;
							$repl_arr["contact_account_".$field_def['name']] = $translated;
						} else { // unset enum field, make sure we have a match string to replace with ""
							$repl_arr["account_".$field_def['name']] = '';
							$repl_arr["contact_account_".$field_def['name']] = '';
						}
					} else {
						$repl_arr["account_".$field_def['name']] = $acct->$field_def['name'];
						$repl_arr["contact_account_".$field_def['name']] = $acct->$field_def['name'];
					}
				}
				
			}
			if(!empty($contact->id)) {
				foreach($contact->field_defs as $field_def) {
					if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name' || $field_def['type'] == 'link') {
						continue;
					}
				
					if($field_def['type'] == 'enum') {
						$translated = translate($field_def['options'], 'Accounts' ,$contact->$field_def['name']);
				
						if(isset($translated) && ! is_array($translated)) {
							$repl_arr["contact_".$field_def['name']] = $translated;
							$repl_arr["contact_account_".$field_def['name']] = $translated;
						} else { // unset enum field, make sure we have a match string to replace with ""
							$repl_arr["contact_".$field_def['name']] = '';
							$repl_arr["contact_account_".$field_def['name']] = '';
						}
					} else {
						if (isset($contact->$field_def['name'])) {
							$repl_arr["contact_".$field_def['name']] = $contact->$field_def['name'];
							$repl_arr["contact_account_".$field_def['name']] = $contact->$field_def['name'];
						} // if
					}
				}
			}
			
			if(!empty($opportunity->id)) {
				foreach($opportunity->field_defs as $field_def) {
					if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name' || $field_def['type'] == 'link') {
						continue;
					}
					
					if($field_def['type'] == 'enum') {
						$translated = translate($field_def['options'], 'Opportunity' ,$opportunity->$field_def['name']);
					
						if(isset($translated) && ! is_array($translated)) {
							$repl_arr["opportunity_".$field_def['name']] = $translated;
							$repl_arr["opportunity_account_".$field_def['name']] = $translated;
							$repl_arr["opportunity_contact_".$field_def['name']] = $translated;
						} else { // unset enum field, make sure we have a match string to replace with ""
							$repl_arr["opportunity_".$field_def['name']] = '';
							$repl_arr["opportunity_account_".$field_def['name']] = '';
							$repl_arr["opportunity_contact_".$field_def['name']] = '';
						}
					} else {
						if (isset($opportunity->$field_def['name'])) {
							$repl_arr["opportunity_".$field_def['name']] = $opportunity->$field_def['name'];
							$repl_arr["opportunity_account_".$field_def['name']] = $opportunity->$field_def['name'];
							$repl_arr["opportunity_contact_".$field_def['name']] = $opportunity->$field_def['name'];
						} // if
					}
				}
			}
		} 
		/**
		 * HOT FIX - change the Client opportunity name to Project Opportunity Name
		 */
		else if($bean_name == 'Opportunities'){
		    if($focus->parent_opportunity_id != ''){
		        $obParentOpp = BeanFactory::getBean('Opportunities',$focus->parent_opportunity_id);
		        $focus->name = $obParentOpp->name;
		    }
		    
		    //Changes related to BSI-763, add 2 non db field for client opportunity email template creation
		    //Mohit Kumar Gupta 30-09-2015		  
		    $focus->related_account_name = $focus->account_name;
		    $focus->related_contact_name = $focus->contact_name;
		}
		else {
			// assumed we have an Account in focus
			foreach($contact->field_defs as $field_def) {
				if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name' || $field_def['type'] == 'link') {
					continue;
				}
	
				if($field_def['type'] == 'enum') {
					$translated = translate($field_def['options'], 'Accounts' ,$contact->$field_def['name']);
	
					if(isset($translated) && ! is_array($translated)) {
						$repl_arr["contact_".$field_def['name']] = $translated;
						$repl_arr["contact_account_".$field_def['name']] = $translated;
					} else { // unset enum field, make sure we have a match string to replace with ""
						$repl_arr["contact_".$field_def['name']] = '';
						$repl_arr["contact_account_".$field_def['name']] = '';
					}
				} else {
					if (isset($contact->$field_def['name'])) {
						$repl_arr["contact_".$field_def['name']] = $contact->$field_def['name'];
						$repl_arr["contact_account_".$field_def['name']] = $contact->$field_def['name'];
					} // if
				}
			}
		}
	
		///////////////////////////////////////////////////////////////////////
		////	LOAD FOCUS DATA INTO REPL_ARR
		foreach($focus->field_defs as $field_def) {
			if(isset($focus->$field_def['name'])) {
				if(($field_def['type'] == 'relate' && empty($field_def['custom_type'])) || $field_def['type'] == 'assigned_user_name') {
					continue;
				}
	
				if($field_def['type'] == 'enum' && isset($field_def['options'])) {
					$translated = translate($field_def['options'],$bean_name,$focus->$field_def['name']);
	
					if(isset($translated) && ! is_array($translated)) {
						$repl_arr[strtolower($beanList[$bean_name])."_".$field_def['name']] = $translated;
					} else { // unset enum field, make sure we have a match string to replace with ""
						$repl_arr[strtolower($beanList[$bean_name])."_".$field_def['name']] = '';
					}
				} else {
					$repl_arr[strtolower($beanList[$bean_name])."_".$field_def['name']] = $focus->$field_def['name'];
				}
			} else {
				if($field_def['name'] == 'full_name') {
					$repl_arr[strtolower($beanList[$bean_name]).'_full_name'] = $focus->get_summary_text();
				} else {
					$repl_arr[strtolower($beanList[$bean_name])."_".$field_def['name']] = '';
				}
			}
		} // end foreach()
	
		krsort($repl_arr);
		reset($repl_arr);
		//20595 add nl2br() to respect the multi-lines formatting
		if(isset($repl_arr['contact_primary_address_street'])){
			$repl_arr['contact_primary_address_street'] = nl2br($repl_arr['contact_primary_address_street']);
		}
		if(isset($repl_arr['contact_alt_address_street'])){
			$repl_arr['contact_alt_address_street'] = nl2br($repl_arr['contact_alt_address_street']);
		}
		
		/*$string = str_replace("\$instance_client_contact_address",nl2br($sugar_config['instance_client_contact_address']),$string);
		$string = str_replace("\$instance_client_contact",$sugar_config['instance_client_contact'],$string);
		$string = str_replace("\$instance_client",$sugar_config['instance_client'] ,$string);*/
		$obUser = new User();
		$obUser->disable_row_level_security = 1; 
		$obUser->retrieve( $focus->assigned_user_id);
		
		$string = str_replace("\$assigned_user_name",$obUser->name,$string);
		$string = str_replace("\$assigned_user_address",nl2br($obUser->address_street).' <br/> '.$obUser->address_city.' '.$obUser->address_state.' '.$obUser->address_postalcode,$string);		
		$string = str_replace("\$assigned_user_phone",formatPhoneNumber($obUser->phone_work) ,$string);
		$string = str_replace("\$instance_client",$obUser->company_name.'<br/>'.$obUser->email1 ,$string);

		//formatting the proposal email template.
		if(isset($focus->billing_contact_id) && !empty($focus->billing_contact_id)){
			
			$contact_primary_address_group = '';
			
			if(!empty($contact->primary_address_street)){
				$contact_primary_address_group .= $contact->primary_address_street."<br>";
			}
			
			$contact_primary_address_array = array();
			
			if(!empty($contact->primary_address_city)){
				$contact_primary_address_array[] = $contact->primary_address_city;
			}
			
			if(!empty($contact->primary_address_state)){
				$contact_primary_address_array[] = $app_list_strings['state_dom'][$contact->primary_address_state];
			}
			
			if(!empty($contact_primary_address_array)){
				
				if(!empty($contact->primary_address_street)){
					// $contact_primary_address_group .= "&nbsp; &nbsp; &nbsp; &nbsp;";
					$contact_primary_address_group .= "";
				}
				
				$contact_primary_address_group .=  implode(", ", $contact_primary_address_array);
				$contact_primary_address_group .= " ";
			}
			
			if(!empty($contact->primary_address_postalcode)){
				
				if(empty($contact_primary_address_array) && !empty($contact->primary_address_street)){
					$contact_primary_address_group .= "&nbsp; &nbsp; &nbsp; &nbsp;";
				}
				$contact_primary_address_group .= $contact->primary_address_postalcode;
			}
			
			$string = str_replace("\$contact_primary_address_group",$contact_primary_address_group, $string);
			
		}else
		{
			//no contact exists 
			
			$string = str_replace("\$contact_primary_address_group",'', $string);
		}
		//end
		
		//$obAdministration = new Administration();
		//$obAdministration->disable_row_level_security =1;
		
		//$arAdminData = $obAdministration->retrieveSettings ( 'instance', true );
		//$string = str_replace("\$instance_client",$arAdminData->settings['instance_company_name'],$string);
		
		
		foreach ($repl_arr as $name=>$value) {
			if($value != '' && is_string($value)) {
				$string = str_replace("\$$name", $value, $string);
			} else {
				$string = str_replace("\$$name", ' ', $string);
			}
		}
			
		return $string;
	}
	
	/**
	 * HOT FIX - change the Client opportunity name to Project Opportunity Name
	 */
 	function parse_template($string, &$bean_arr) {
	    global $beanFiles, $beanList;
	
	    foreach($bean_arr as $bean_name => $bean_id) {
	        require_once($beanFiles[$beanList[$bean_name]]);
	
	        $focus = new $beanList[$bean_name];
	        $result = $focus->retrieve($bean_id);
	        if($bean_name == 'Leads' || $bean_name == 'Prospects') {
	            $bean_name = 'Contacts';
	        }
	
	        if(isset($this) && isset($this->module_dir) && $this->module_dir == 'EmailTemplates') {
	            $string = $this->parse_template_bean($string, $bean_name, $focus);
	        } else {
	            $string = self::parse_template_bean($string, $bean_name, $focus);
	        }
	    }
	    return $string;
	} 
	
} 