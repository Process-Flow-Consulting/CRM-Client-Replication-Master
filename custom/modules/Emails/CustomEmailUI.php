<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomEmailUI
 *
 * @author satish
 */
require_once 'modules/Emails/EmailUI.php';
class CustomEmailUI extends EmailUI{
    
    function CustomEmailUI(){
        parent::EmailUI();
    }
    
    function displayEmailFrame() {

		require_once("include/OutboundEmail/OutboundEmail.php");

		global $app_strings, $app_list_strings;
		global $mod_strings;
		global $sugar_config;
		global $current_user;
		global $locale;
		global $timedate;
		global $theme;
		global $sugar_version;
		global $sugar_flavor;
		global $current_language;
		global $server_unique_key;

		$this->preflightUserCache();
		$ie = new InboundEmail();

		// focus listView
		$list = array(
			'mbox' => 'Home',
			'ieId' => '',
			'name' => 'Home',
			'unreadChecked' => 0,
			'out' => array(),
		);

		$this->_generateComposeConfigData('email_compose');


        //Check quick create module access
        $QCAvailableModules = $this->_loadQuickCreateModules();

        //Get the quickSearch js needed for assigned user id on Search Tab
        require_once('include/QuickSearchDefaults.php');
        $qsd = new QuickSearchDefaults();
        $qsd->setFormName('advancedSearchForm');
        $quicksearchAssignedUser = "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}";
        $quicksearchAssignedUser .= "sqs_objects['advancedSearchForm_assigned_user_name']=" . json_encode($qsd->getQSUser()) . ";";
        $qsd->setFormName('Distribute');
        $quicksearchAssignedUser .= "sqs_objects['Distribute_assigned_user_name']=" . json_encode($qsd->getQSUser()) . ";";
        $this->smarty->assign('quickSearchForAssignedUser', $quicksearchAssignedUser);


		///////////////////////////////////////////////////////////////////////
		////	BASIC ASSIGNS
		$this->smarty->assign("currentUserId",$current_user->id);
		$this->smarty->assign("CURRENT_USER_EMAIL",$current_user->email1);
        $this->smarty->assign("currentUserName",$current_user->name);
		$this->smarty->assign('yuiPath', 'modules/Emails/javascript/yui-ext/');
		$this->smarty->assign('app_strings', $app_strings);
		$this->smarty->assign('mod_strings', $mod_strings);
		$this->smarty->assign('theme', $theme);
		$this->smarty->assign('sugar_config', $sugar_config);
		$this->smarty->assign('is_admin', $current_user->is_admin);
		$this->smarty->assign('sugar_version', $sugar_version);
		$this->smarty->assign('sugar_flavor', $sugar_flavor);
		$this->smarty->assign('current_language', $current_language);
		$this->smarty->assign('server_unique_key', $server_unique_key);
		$this->smarty->assign('qcModules', json_encode($QCAvailableModules));
		$extAllDebugValue = "ext-all.js";
		$this->smarty->assign('extFileName', $extAllDebugValue);

		// settings: general
		$e2UserPreferences = $this->getUserPrefsJS();
		$emailSettings = $e2UserPreferences['emailSettings'];

		///////////////////////////////////////////////////////////////////////
		////	USER SETTINGS
		// settings: accounts
		$this->smarty->assign("pro", 1);
		// $this->smarty->assign("ie_team", $current_user->getPrivateTeam());

		// $teams = array(
			// 'options' => $current_user->get_my_teams(),
			// 'selected' => empty($current_user->default_team) ? $current_user->getPrivateTeam() : $current_user->default_team
		// );

		// $this->retrieveTeamInfoForSettingsUI();

		$cuDatePref = $current_user->getUserDateTimePreferences();
		$this->smarty->assign('dateFormat', $cuDatePref['date']);
		$this->smarty->assign('dateFormatExample', str_replace(array("Y", "m", "d"), array("yyyy", "mm", "dd"), $cuDatePref['date']));
		$this->smarty->assign('calFormat', $timedate->get_cal_date_format());
        $this->smarty->assign('TIME_FORMAT', $timedate->get_user_time_format());
		
		$ieAccounts = $ie->retrieveByGroupId($current_user->id);
		$ieAccountsOptions = "<option value=''>{$app_strings['LBL_NONE']}</option>\n";

		foreach($ieAccounts as $k => $v) {
			$disabled = (!$v->is_personal) ? "DISABLED" : "";
			$group = (!$v->is_personal) ? $app_strings['LBL_EMAIL_GROUP']."." : "";
			$ieAccountsOptions .= "<option value='{$v->id}' $disabled>{$group}{$v->name}</option>\n";
		}

		$this->smarty->assign('ieAccounts', $ieAccountsOptions);
		$this->smarty->assign('rollover', $this->rolloverStyle);

		$protocol = filterInboundEmailPopSelection($app_list_strings['dom_email_server_type']);
		$this->smarty->assign('PROTOCOL', get_select_options_with_id($protocol, ''));
		$this->smarty->assign('MAIL_SSL_OPTIONS', get_select_options_with_id($app_list_strings['email_settings_for_ssl'], ''));
		$this->smarty->assign('ie_mod_strings', return_module_language($current_language, 'InboundEmail'));

		$charsetSelectedValue = isset($emailSettings['defaultOutboundCharset']) ? $emailSettings['defaultOutboundCharset'] : false;
		if (!$charsetSelectedValue) {
			$charsetSelectedValue = $current_user->getPreference('default_export_charset', 'global');
			if (!$charsetSelectedValue) {
				$charsetSelectedValue = $locale->getPrecedentPreference('default_email_charset');
			}
		}
		$charset = array(
			'options' => $locale->getCharsetSelect(),
			'selected' => $charsetSelectedValue,
		);
		$this->smarty->assign('charset', $charset);

		$emailCheckInterval = array('options' => $app_strings['LBL_EMAIL_CHECK_INTERVAL_DOM'], 'selected' => $emailSettings['emailCheckInterval']);
		$this->smarty->assign('emailCheckInterval', $emailCheckInterval);
		$this->smarty->assign('attachmentsSearchOptions', $app_list_strings['checkbox_dom']);
		$this->smarty->assign('sendPlainTextChecked', ($emailSettings['sendPlainText'] == 1) ? 'CHECKED' : '');
		$this->smarty->assign('showNumInList', get_select_options_with_id($app_list_strings['email_settings_num_dom'], $emailSettings['showNumInList']));

		////	END USER SETTINGS
		///////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////
		////	SIGNATURES
		$prependSignature = ($current_user->getPreference('signature_prepend')) ? 'true' : 'false';
		$defsigID = $current_user->getPreference('signature_default');
		$this->smarty->assign('signatures', $current_user->getSignatures(false, $defsigID));
		$this->smarty->assign('signaturesSettings', $current_user->getSignatures(false, $defsigID, false));
		$signatureButtons = $current_user->getSignatureButtons('SUGAR.email2.settings.createSignature', !empty($defsigID));
		if (!empty($defsigID)) {
			$signatureButtons = $signatureButtons . '<span name="delete_sig" id="delete_sig" style="visibility:inherit;"><input class="button" onclick="javascript:SUGAR.email2.settings.deleteSignature();" value="'.$app_strings['LBL_EMAIL_DELETE'].'" type="button" tabindex="392">&nbsp;
					</span>';
		} else {
			$signatureButtons = $signatureButtons . '<span name="delete_sig" id="delete_sig" style="visibility:hidden;"><input class="button" onclick="javascript:SUGAR.email2.settings.deleteSignature();" value="'.$app_strings['LBL_EMAIL_DELETE'].'" type="button" tabindex="392">&nbsp;
					</span>';
		}
		$this->smarty->assign('signatureButtons', $signatureButtons);
		$this->smarty->assign('signaturePrepend', $prependSignature == 'true' ? 'CHECKED' : '');
		////	END SIGNATURES
		///////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////
		////	EMAIL TEMPLATES
		$email_templates_arr = $this->getEmailTemplatesArray();
		natcasesort($email_templates_arr);
		$this->smarty->assign('EMAIL_TEMPLATE_OPTIONS', get_select_options_with_id($email_templates_arr, ''));
		////	END EMAIL TEMPLATES
		///////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////
		////	FOLDERS & TreeView
		$this->smarty->assign('groupUserOptions', $ie->getGroupsWithSelectOptions(array('' => $app_strings['LBL_EMAIL_CREATE_NEW'])));

		$tree = $this->getMailboxNodes();

		// preloaded folder
		$preloadFolder = 'lazyLoadFolder = ';
		$focusFolderSerial = $current_user->getPreference('focusFolder', 'Emails');
		if(!empty($focusFolderSerial)) {
			$focusFolder = unserialize($focusFolderSerial);
			//$focusFolder['ieId'], $focusFolder['folder']
			$preloadFolder .= json_encode($focusFolder).";";
		} else {
			$preloadFolder .= "new Object();";
		}
		////	END FOLDERS
		///////////////////////////////////////////////////////////////////////

		$out = "";
		$out .= $this->smarty->fetch("custom/modules/Emails/templates/_baseEmail.tpl");
		$out .= $tree->generate_header();
		$out .= $tree->generateNodesNoInit(true, 'email2treeinit');
		$out .=<<<eoq
			<script type="text/javascript" language="javascript">

				var loader = new YAHOO.util.YUILoader({
				    require : [
				    	"layout", "element", "tabview", "menu",
				    	"cookie", "sugarwidgets"
				    ],
				    loadOptional: true,
				    skin: { base: 'blank', defaultSkin: '' },
				    onSuccess: email2init,
				    allowRollup: true,
				    base: "include/javascript/yui/build/"
				});
				loader.addModule({
				    name :"sugarwidgets",
				    type : "js",
				    fullpath: "include/javascript/sugarwidgets/SugarYUIWidgets.js",
				    varName: "YAHOO.SUGAR",
				    requires: ["datatable", "dragdrop", "treeview", "tabview", "calendar"]
				});
				loader.insert();

				{$preloadFolder};
	
			</script>
eoq;


		return $out;
	}
	
	/**
	 * Renders the QuickCreate form from Smarty and returns HTML
	 * @param array $vars request variable global
	 * @param object $email Fetched email object
	 * @param bool $addToAddressBook
	 * @return array
	 */
	function getQuickCreateForm($vars, $email, $addToAddressBookButton=false) {
		require_once("include/EditView/EditView2.php");
		global $app_strings;
		global $app_list_strings;
		global $mod_strings;
		global $current_user;
		global $beanList;
		global $beanFiles;
		global $current_language;
	
		//Setup the current module languge
		$mod_strings = return_module_language($current_language, $_REQUEST['qc_module']);
	
		$bean = $beanList[$_REQUEST['qc_module']];
		$class = $beanFiles[$bean];
		require_once($class);
	
		$focus = new $bean();
	
		$people = array(
				'Contact'
				,'Lead'
		);
		$emailAddress = array();
	
		// people
		if(in_array($bean, $people)) {
			// lead specific
			$focus->lead_source = 'Email';
			$focus->lead_source_description = trim($email->name);
	
			$from = (isset($email->from_name) && !empty($email->from_name)) ? $email->from_name : $email->from_addr;
	
			if(isset($_REQUEST['sugarEmail']) && !empty($_REQUEST['sugarEmail']))
				$from = (isset($email->to_addrs_names) && !empty($email->to_addrs_names)) ? $email->to_addrs_names : $email->to_addrs;
	
	
			$name = explode(" ", trim($from));
	
			$address = trim(array_pop($name));
			$address = str_replace(array("<",">","&lt;","&gt;"), "", $address);
	
			$emailAddress[] = array(
					'email_address'		=> $address,
					'primary_address'	=> 1,
					'invalid_email'		=> 0,
					'opt_out'			=> 0,
					'reply_to_address'	=> 1
			);
	
			$focus->email1 = $address;
	
			if(!empty($name)) {
				$focus->last_name = trim(array_pop($name));
	
				foreach($name as $first) {
					if(!empty($focus->first_name)) {
						$focus->first_name .= " ";
					}
					$focus->first_name .= trim($first);
				}
			}
		} else {
			// bugs, cases, tasks
			$focus->name = trim($email->name);
		}
	
		$focus->description = trim(strip_tags($email->description));
		$focus->assigned_user_id = $current_user->id;
	
		// $focus->team_id = $current_user->default_team;
	
		$EditView = new EditView();
		$EditView->ss = new Sugar_Smarty();
		
		if( $_REQUEST['qc_module'] == 'Opportunities' ){
			//delete cache template
			require_once('include/TemplateHandler/TemplateHandler.php');
			$th = new TemplateHandler();
			$th->clearCache($_REQUEST['qc_module']);
		}
		
		//MFH BUG#20283 - checks for custom quickcreate fields
		//separeate client and project opportunity -- hirak
		
		if( $_REQUEST['qc_module'] == 'Opportunities' && isset($_REQUEST['target_view']) &&
					$_REQUEST['target_view'] == 'QuickCreateSub'){
			$EditView->setup($_REQUEST['qc_module'], $focus, 'custom/modules/'.$focus->module_dir.'/metadata/editviewdefs_sub.php', 'include/EditView/EditView.tpl');
		}else if( $_REQUEST['qc_module'] == 'Opportunities' && !isset($_REQUEST['target_view']) ){
			
		    $EditView->setup($_REQUEST['qc_module'], $focus, 'custom/modules/'.$focus->module_dir.'/metadata/editviewdefs_email.php', 'include/EditView/EditView.tpl');
		}
		else{
		    $EditView->setup($_REQUEST['qc_module'], $focus, 'custom/modules/'.$focus->module_dir.'/metadata/editviewdefs.php', 'include/EditView/EditView.tpl');
		}
		$EditView->process();
		$EditView->render();
	    //Modified by Mohit Kumar Gupta, Date 23/7/13
	    //Add lead_county for country listing
		if( $_REQUEST['qc_module'] == 'Opportunities' ){
			
				$structure = '';
			$structure .= '<select id="lead_structure" name="lead_structure">';
			$structure .= '<option value=""></option>';
			$structure .= '<optgroup style="background:#ececec" label="Residential Building"></optgroup>';
			foreach ($app_list_strings['structure_residential'] as $key => $value) {
				$selected = '';
				if ($selected_structure == $value) {
					$selected = 'selected';
				}
				$structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
			$structure .= '<optgroup style="background:#ececec" label="Non-Residential Building"></optgroup>';
			foreach ($app_list_strings['structure_non_residential'] as $key => $value) {
				$selected = '';
				if ($selected_structure == $value) {
					$selected = 'selected';
				}
				$structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
			$structure .= '<optgroup style="background:#ececec" label="Non-Building Construction"></optgroup>';
			foreach ($app_list_strings['structure_non_building'] as $key => $value) {
				$selected = '';
				if ($selected_structure == $value) {
					$selected = 'selected';
				}
				$structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
			$structure .= '</select>';
			$EditView->ss->assign('lead_structure', $structure);
			
			// change sales stage dropdown for project opportunity.
			if( isset($_REQUEST['target_view']) && ( $_REQUEST['target_view'] == 'QuickCreateSub') ){
				$EditView->fieldDefs['sales_stage']['options'] = $GLOBALS['app_list_strings']['client_sales_stage_dom'];
			}else{
			    $county = '';
			    $county .= '<div id="county_div">';
			    $county .= '<select title="County" id="lead_county" name="lead_county">';
			    $county .= '<option value="0" label=""></option>';
			    $county .= '</select>';
			    $county .= '</div>';
			    $EditView->ss->assign('lead_county',$county);
				$EditView->fieldDefs['sales_stage']['options'] = $GLOBALS['app_list_strings']['project_sales_stage_dom'];
			}
		}
		
		if( $_REQUEST['qc_module'] == 'Accounts'){
			//get state DOM values
			$EditView->ss->assign('STATE_DOM', $GLOBALS['app_list_strings']['state_dom']);
			
		}
		
		$EditView->defs['templateMeta']['form']['buttons'] = array(
				'email2save' => array(
						'id' => 'e2AjaxSave',
						'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_SAVE_BUTTON_LABEL']
						. '   " onclick="SUGAR.email2.detailView.saveQuickCreate(false);" />'
				),
				'email2saveandreply' => array(
						'id' => 'e2SaveAndReply',
						'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_EMAIL_SAVE_AND_REPLY']
						. '   " onclick="SUGAR.email2.detailView.saveQuickCreate(\'reply\');" />'
				),
				'email2cancel' => array(
						'id' => 'e2cancel',
						'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_EMAIL_CANCEL']
						. '   " onclick="SUGAR.email2.detailView.quickCreateDialog.hide();" />'
				)
		);
	
	
		if($addToAddressBookButton) {
			$EditView->defs['templateMeta']['form']['buttons']['email2saveAddToAddressBook'] = array(
					'id' => 'e2addToAddressBook',
					'customCode' => '<input type="button" class="button" value="   '.$app_strings['LBL_EMAIL_ADDRESS_BOOK_SAVE_AND_ADD']
					. '   " onclick="SUGAR.email2.detailView.saveQuickCreate(true);" />'
			);
		}
	
		//Get the module language for javascript
		if(!is_file(sugar_cached('jsLanguage/') . $_REQUEST['qc_module'] . '/' . $GLOBALS['current_language'] . '.js')) {
			require_once('include/language/jsLanguage.php');
			jsLanguage::createModuleStringsCache($_REQUEST['qc_module'], $GLOBALS['current_language']);
		}
		$jsLanguage = getVersionedScript("cache/jsLanguage/{$_REQUEST['qc_module']}/{$GLOBALS['current_language']}.js", $GLOBALS['sugar_config']['js_lang_version']);
		
		if( $_REQUEST['qc_module'] == 'Opportunities' ){
			
			$time_format = $GLOBALS['timedate']->get_user_time_format($current_user);
			
			$jsLanguage .= "
			<script src='custom/modules/Emails/oppQuickCompose.js' ></script>
			<script type='text/javascript'>
			//used to change the height to auto for opportunity module
			jQuery('.bd').css('height','auto');
		if(document.getElementById('btn_contact_name'))
		document.getElementById('btn_contact_name').onclick = function(){
			var client_id = document.getElementById('account_id').value;		
			var popup_request_data = {
				'call_back_function' : 'set_contact_returns',
				'form_name' : 'EditView',
				'field_to_name_array' : {
				'id' : 'id',
				'name' : 'name',
				'account_id' : 'account_id',
				'account_name' : 'account_name',
				},
			};
		open_popup('Contacts', 600, 400, '&account_id='+client_id, true, false, popup_request_data);
		}
		
		//Added by Mohit Kumar Gupta, Date 23/7/13
		//Used for populate country drop down values
		function getCounty(stateAbbr,selCounty){
            var callback = {
                success:function(o){
                    //alert(o.responseText);
                    document.getElementById(\"county_div\").innerHTML = o.responseText;
                }
            }
            var connectionObject = YAHOO.util.Connect.asyncRequest (\"GET\", \"index.php?entryPoint=CountyAjaxCall&state_abbr=\"+stateAbbr+\"&selected_county=\"+selCounty+\"&fieldname=lead_county\", callback);
        }
        
		function set_contact_returns(popup_reply_data){
			var name_to_value_array = popup_reply_data.name_to_value_array;
			var id = name_to_value_array['id'];
			var contact = name_to_value_array['name'];
			var account_id = name_to_value_array['account_id'];
			var account_name = name_to_value_array['account_name'];				
			document.getElementById('contact_name').value=contact;
			document.getElementById('contact_id').value = id;
			document.getElementById('account_name').value=account_name;
			document.getElementById('account_id').value = account_id;
			//suggest assigned user
			suggestAssignedUser();
			//getAccountsDefualtContact($('#account_id').val())
		}
				
		if(document.getElementById('btn_opportunity_name'))	
		document.getElementById('btn_opportunity_name').onclick = function(){
			var opportunity_id = document.getElementById('parent_opportunity_id').value;		
			var popup_request_data = {
				'call_back_function' : 'set_parent_opportunity_returns',
				'form_name' : 'EditView',
				'field_to_name_array' : {
				'id' : 'id',
				'name' : 'name',
				'date_closed' : 'date_closed',
				'bid_due_timezone' : 'bid_due_timezone',
				'amount' : 'amount',
				'sales_stage' : 'sales_stage',
				'lead_source' : 'lead_source',
				'date_closed_tz' : 'date_closed_tz',
				},
			};
					
			open_popup( 'Opportunities',  600,  400,  '&parent_opportunity_only=true',  true,  false, popup_request_data, 'single', true );	
		}
				
		function set_parent_opportunity_returns(popup_reply_data){
			
			var name_to_value_array = popup_reply_data.name_to_value_array;
			var id = name_to_value_array['id'];
			var name = name_to_value_array['name'];
			var date_closed = name_to_value_array['date_closed'];
			var bid_due_timezone = name_to_value_array['bid_due_timezone'];
			var amount = name_to_value_array['amount'];
			var sales_stage = name_to_value_array['sales_stage'];
			var lead_source = name_to_value_array['lead_source'];
			var date_closed_tz = name_to_value_array['date_closed_tz'];
								
			document.getElementById('parent_opportunity_id').value = id;
			document.getElementById('opportunity_name').value = name;
			
			/*
			if(confirm('Do you want to overwrite data with project opportunity data ?')) 
			*/{
				document.getElementById('name').value = name;
				document.getElementById('bid_due_timezone').value = bid_due_timezone;
				document.getElementById('amount').value = amount;
				
				var client_sales_stage_dom = SUGAR.language.languages['app_list_strings']['client_sales_stage_dom'];
				for (i in client_sales_stage_dom){
					if( client_sales_stage_dom[i] == sales_stage){
						document.getElementById('sales_stage').value = i;
					}
				}

				var lead_source_dom = SUGAR.language.languages['app_list_strings']['lead_source_dom'];
				for (i in lead_source_dom){
				   if(lead_source_dom[i] == lead_source){
				        document.getElementById('lead_source').value = i;
				   }
				}
				
				date_closed_tz = SUGAR.util.DateUtils.parse(date_closed_tz);
				date_closed_tz = SUGAR.util.DateUtils.formatDate(date_closed_tz, true);
				
				var combo_date_closed = new Datetimecombo(date_closed_tz, 'date_closed', '$time_format', 0, '', false, true);
				text = combo_date_closed.html();
				document.getElementById('date_closed_time_section').innerHTML = text;
				
				document.getElementById('date_closed').value = date_closed_tz;
				
			}
		}
		
		sqs_objects['form_EmailQCView_Opportunities_opportunity_name'] = '';
		
		YAHOO.util.Event.onContentReady('parent_opportunity_id', function(){

		        var container1 = document.createElement('div');
		        container1.innerHTML = '';  
		        container1.id = 'parentOpportunityContainer';
				
				YAHOO.util.Dom.insertAfter(container1 ,YAHOO.util.Dom.get('opportunity_name'));                   
		            
		        YAHOO.example.classification = function() {
		        
		        // instantiate remote data source
		        var oDS = new YAHOO.util.XHRDataSource('index.php?'); 
		        oDS.connMethodPost = 1;
		        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON; 
		        oDS.responseSchema = {
							resultsList:'fields',
							total:'totalCount',
							fields:['name','id','date_closed','bid_due_timezone','amount','sales_stage','lead_source','date_closed_tz'],
							metaNode:'fields',
							metaFields:{total:'totalCount',fields:'fields'}
				};
		        oDS.maxCacheEntries = 10;         
		    
		        var oAC = new YAHOO.widget.AutoComplete('opportunity_name', 'parentOpportunityContainer', oDS);
				oAC.resultTypeList = false;                
		        oAC.useShadow = true;
				
				var myHiddenField = YAHOO.util.Dom.get('parent_opportunity_id');
				var myHandler = function(sType, aArgs) {
						
						var myAC = aArgs[0]; // reference back to the AC instance 
						var elLI = aArgs[1]; // reference to the selected LI element
						var oData = aArgs[2]; // object literal of selected item's result data 
						// update hidden form field with the selected item's ID
						myHiddenField.value = oData.id;
						//filll additional fields
						YAHOO.util.Dom.get('name').value = oData.name;
						YAHOO.util.Dom.get('bid_due_timezone').value = oData.bid_due_timezone;
						YAHOO.util.Dom.get('amount').value = oData.amount;
						YAHOO.util.Dom.get('sales_stage').value = oData.sales_stage;
						YAHOO.util.Dom.get('lead_source').value = oData.lead_source;
						
						var date_closed_tz = SUGAR.util.DateUtils.parse(oData.date_closed_tz);
						date_closed_tz = SUGAR.util.DateUtils.formatDate(date_closed_tz, true);
						
						var combo_date_closed = new Datetimecombo(date_closed_tz, 'date_closed', '$time_format', 0, '', false, true);
						text = combo_date_closed.html();
						document.getElementById('date_closed_time_section').innerHTML = text;
						
						YAHOO.util.Dom.get('date_closed').value = date_closed_tz;
				};
				
		        oAC.itemSelectEvent.subscribe(myHandler);
		
				var opportunity_search = document.getElementById('opportunity_name').value;
				
		
		        oAC.generateRequest = function(sQuery) {
		        	return  'to_pdf=true&module=Opportunities&action=parentoppautofill&q='+sQuery+'&order=name&parent_opportunity_only=true&limit=30';
			    };          
		            
		        return {
		            oDS: oDS,
		            oAC: oAC,                    
		        };
		    }();
		});
		</script>";

			if( isset($_REQUEST['target_view']) && ( $_REQUEST['target_view'] == 'QuickCreateSub') ){
				$jsLanguage .="<script type='text/javascript'>
					 document.getElementById('sales_stage').children[6].disabled='disabled';
					 document.getElementById('sales_stage').children[7].disabled='disabled';
					 document.getElementById('sales_stage').children[8].disabled='disabled';
				</script>";
			}
		}
	
		$EditView->view = 'EmailQCView';
		$EditView->defs['templateMeta']['form']['headerTpl'] = 'include/EditView/header.tpl';
		//Added By : Ashutosh : if it is a quick create for Project Opportunity change the footer tpl
		if( $_REQUEST['qc_module'] == 'Opportunities' && !isset($_REQUEST['target_view']) ){	    
		    
		    $EditView->ss->assign('OPT_SOURCE_SELECT', $GLOBALS['app_list_strings']['opp_source_dom']);
		    $EditView->defs['templateMeta']['form']['footerTpl'] = 'custom/include/EditView/footer_opp_email.tpl';
		}else{   
		    
		    $EditView->defs['templateMeta']['form']['footerTpl'] = 'include/EditView/footer.tpl';
		}
		$meta = array();
		$meta['html'] = $jsLanguage . $EditView->display(false, true);
		$meta['html'] = str_replace("src='".getVersionedPath('include/SugarEmailAddress/SugarEmailAddress.js')."'", '', $meta['html'])."<script>sqs_objects['form_EmailQCView_Opportunities_opportunity_name'] = '';</script>";
		$meta['emailAddress'] = $emailAddress;
	
		$mod_strings = return_module_language($current_language, 'Emails');
	
		return $meta;
	}
	
	/**
	 * Renders the Import form from Smarty and returns HTML
	 * @param array $vars request variable global
	 * @param object $email Fetched email object
	 * @param bool $addToAddressBook
	 * @return array
	 */
	function getImportForm($vars, $email, $formName = 'ImportEditView') {
		require_once("include/EditView/EditView2.php");
		require_once("include/TemplateHandler/TemplateHandler.php");
		require_once('include/QuickSearchDefaults.php');
		$qsd = QuickSearchDefaults::getQuickSearchDefaults();
		$qsd->setFormName($formName);
	
		global $app_strings;
		global $current_user;
		global $app_list_strings;
		$sqs_objects = array(
				"{$formName}_parent_name" => $qsd->getQSParent(),
		);
		$smarty = new Sugar_Smarty();
		$smarty->assign("APP",$app_strings);
		$smarty->assign('formName',$formName);
	
		// $showTeam = false;
				// if (!isset($vars['showTeam']) || $vars['showTeam'] == true) {
				// $showTeam = true;
	// } // if
	// if ($showTeam) {
		// $smarty->assign("teamId", $current_user->default_team);
		// $email->team_id = $current_user->default_team;
		// $email->team_set_id = $current_user->team_set_id;
		// }
		// $smarty->assign("showTeam",$showTeam);
	
		// require_once('include/SugarFields/Fields/Teamset/EmailSugarFieldTeamsetCollection.php');
		// $teamSetField = new EmailSugarFieldTeamsetCollection($email, $email->field_defs, '', $formName);
		// $code = $teamSetField->get_code();
		// $sqs_objects1 = $teamSetField->createQuickSearchCode(true);
		//$sqs_objects = array_merge($sqs_objects, $sqs_objects1);
		// $smarty->assign("TEAM_SET_FIELD", $code . $sqs_objects1);
	
		$showAssignTo = false;
		if (!isset($vars['showAssignTo']) || $vars['showAssignTo'] == true) {
		$showAssignTo = true;
	} // if
	if ($showAssignTo) {
	if(empty($email->assigned_user_id) && empty($email->id))
	$email->assigned_user_id = $current_user->id;
		if(empty($email->assigned_name) && empty($email->id))
			$email->assigned_user_name = $current_user->user_name;
			$sqs_objects["{$formName}_assigned_user_name"] = $qsd->getQSUser();
		}
		$smarty->assign("showAssignedTo",$showAssignTo);
	
		$showDelete = false;
		if (!isset($vars['showDelete']) || $vars['showDelete'] == true) {
		$showDelete = true;
		}
		$smarty->assign("showDelete",$showDelete);
	
				$smarty->assign("userId",$email->assigned_user_id);
		$smarty->assign("userName",$email->assigned_user_name);
		//@modified by Mohit Kumar Gupta
		//@date 19-dec-2013
		//add none option to drop down
		$parent_types = $app_list_strings['compose_email_record_type_display']; //separeate client and project opportunity -- hirak
		$smarty->assign('parentOptions', get_select_options_with_id($parent_types, $email->parent_type));
	
		$quicksearch_js = '<script type="text/javascript" language="javascript">sqs_objects = ' . json_encode($sqs_objects) . '</script>';
		$smarty->assign('SQS', $quicksearch_js);
	
				$meta = array();
				$meta['html'] = $smarty->fetch("custom/modules/Emails/templates/importRelate.tpl"); //separeate client and project opportunity -- hirak
        return $meta;
    }
    
    /**
     * Generate the frame needed for the quick compose email UI.  This frame is loaded dynamically
     * by an ajax call.
     *
     * @return JSON An object containing html markup and js script variables.
     */
    function displayQuickComposeEmailFrame()
    {
        $this->preflightUserCache();
    
        $this->_generateComposeConfigData('email_compose_light');
        $javascriptOut = $this->smarty->fetch("custom/modules/Emails/templates/_baseConfigData.tpl");
    
        $divOut = $this->smarty->fetch("modules/Emails/templates/overlay.tpl");
        $divOut .= $this->smarty->fetch("modules/Emails/templates/addressSearchContent.tpl");
    
        $outData = array('jsData' => $javascriptOut,'divData'=> $divOut);
        $out = json_encode($outData);
        return $out;
    }
    
    
    /**
     * Generate the config data needed for the Full Compose UI and the Quick Compose UI.  The set of config data
     * returned is the minimum set needed by the quick compose UI.
     *
     * @param String $type Drives which tinyMCE options will be included.
     */
    function _generateComposeConfigData($type = "email_compose_light" )
    {
    	global $app_list_strings,$current_user, $app_strings, $mod_strings,$current_language,$locale;
    
    	//Link drop-downs
    	//@modified by Mohit Kumar Gupta
    	//@date 19-dec-2013
    	//add none option to drop down
    	$parent_types = $app_list_strings['compose_email_record_type_display']; //separeate client and project opportunity -- hirak
    	$disabled_parent_types = ACLController::disabledModuleList($parent_types, false, 'list');
    
    	foreach($disabled_parent_types as $disabled_parent_type) {
    		unset($parent_types[$disabled_parent_type]);
    	}
    	asort($parent_types);
    	$linkBeans = json_encode(get_select_options_with_id($parent_types, ''));
    
    	//TinyMCE Config
    	require_once("include/SugarTinyMCE.php");
    	$tiny = new SugarTinyMCE();
    	$tinyConf = $tiny->getConfig($type);
    
    	//Generate Language Packs
    	$lang = "var app_strings = new Object();\n";
    	foreach($app_strings as $k => $v) {
    		if(strpos($k, 'LBL_EMAIL_') !== false) {
    			$lang .= "app_strings.{$k} = '{$v}';\n";
    		}
    	}
    	//Get the email mod strings but don't use the global variable as this may be overridden by
    	//other modules when the quick create is rendered.
    	$email_mod_strings = return_module_language($current_language,'Emails');
    	$modStrings = "var mod_strings = new Object();\n";
    	foreach($email_mod_strings as $k => $v) {
    		$v = str_replace("'", "\'", $v);
    		$modStrings .= "mod_strings.{$k} = '{$v}';\n";
    	}
    	$lang .= "\n\n{$modStrings}\n";
    
    	//Grab the Inboundemail language pack
    	$ieModStrings = "var ie_mod_strings = new Object();\n";
    	$ie_mod_strings = return_module_language($current_language,'InboundEmail');
    	foreach($ie_mod_strings as $k => $v) {
    		$v = str_replace("'", "\'", $v);
    		$ieModStrings .= "ie_mod_strings.{$k} = '{$v}';\n";
    	}
    	$lang .= "\n\n{$ieModStrings}\n";
    
    	$this->smarty->assign('linkBeans', $linkBeans);
    	$this->smarty->assign('linkBeansOptions', $parent_types);
    	$this->smarty->assign('tinyMCE', $tinyConf);
    	$this->smarty->assign('lang', $lang);
    	$this->smarty->assign('app_strings', $app_strings);
    	$this->smarty->assign('mod_strings', $email_mod_strings);
    	$ie1 = new InboundEmail();
    	// $ie1->team_id = empty($current_user->default_team) ? $current_user->team_id : $current_user->default_team;
    	// $ie1->team_set_id = $current_user->team_set_id;
    
    	// require_once('include/SugarFields/Fields/Teamset/EmailSugarFieldTeamsetCollection.php');
    	// $teamSetField = new EmailSugarFieldTeamsetCollection($ie1, $ie1->field_defs, '', 'composeEmailForm');
    	// $code1 = $teamSetField->get_code();
    	// $sqs_objects1 = $teamSetField->createQuickSearchCode(true);
    	// $this->smarty->assign('teamsdata', json_encode($code1 . $sqs_objects1));
    
    	//Signatures
    	$defsigID = $current_user->getPreference('signature_default');
    	$defaultSignature = $current_user->getDefaultSignature();
    	$sigJson = !empty($defaultSignature) ? json_encode(array($defaultSignature['id'] => from_html($defaultSignature['signature_html']))) : "new Object()";
    	$this->smarty->assign('defaultSignature', $sigJson);
    	$this->smarty->assign('signatureDefaultId', (isset($defaultSignature['id'])) ? $defaultSignature['id'] : "");
    	//User Preferences
    	$this->smarty->assign('userPrefs', json_encode($this->getUserPrefsJS()));
    
    	//Get the users default outbound id
    	$defaultOutID = $ie1->getUsersDefaultOutboundServerId($current_user);
    	$this->smarty->assign('defaultOutID', $defaultOutID);
    
    	//Character Set
    	$charsets = json_encode($locale->getCharsetSelect());
    	$this->smarty->assign('emailCharsets', $charsets);
    
    	//Relateable List of People for address book search
    	//#20776 jchi
    	$peopleTables = array("users",
    			"contacts",
    			"leads",
    			"prospects",
    			"accounts");
    	$filterPeopleTables = array();
    	global $app_list_strings, $app_strings;
    	$filterPeopleTables['LBL_DROPDOWN_LIST_ALL'] = $app_strings['LBL_DROPDOWN_LIST_ALL'];
    	foreach($peopleTables as $table) {
    		$module = ucfirst($table);
    		$class = substr($module, 0, strlen($module) - 1);
    		require_once("modules/{$module}/{$class}.php");
    		$person = new $class();
    
    		if (!$person->ACLAccess('list')) continue;
    		$filterPeopleTables[$person->table_name] = $app_list_strings['moduleList'][$person->module_dir];
    	}
    	$this->smarty->assign('listOfPersons' , get_select_options_with_id($filterPeopleTables,''));
    
    }
}

?>
