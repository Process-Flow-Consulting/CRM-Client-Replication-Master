<?php
global $sugar_config;
require_once 'include/MVC/View/SugarView.php';
include_once "custom/modules/Quotes/schedule_quotes/class.easylinkmessage.php";
require_once $sugar_config['master_config_path']; // '/vol/certificate/master_config.php';
require_once 'custom/include/common_functions.php';
ini_set('zend_optimizerplus.dups_fix', 1);
class OpportunitiesViewVerifyproposal extends SugarView
{
    function OpportunitiesViewVerifyproposal()
    {
        parent::SugarView();
    }
    function display()
    {
        global $db;
        
        $verificationError = 0;
        
        $pids = array();
        
        if ($_REQUEST['uid'] != '') {
            
            $pids = explode(',', $_REQUEST['uid']);
        
        }else if($_REQUEST['select_entire_list'] == '1'){

            $getOpportunitySql = " SELECT quotes.id  FROM quotes  WHERE quotes.deleted = 0 ";
            $getOpportunityResult = $db->query($getOpportunitySql);
            while( $getOpportunityRow = $db->fetchByAssoc($getOpportunityResult) ){
                $pids[] = $getOpportunityRow['id'];
            }
            
        }else {
            sugar_die('No Proposal Selected to Verify.');
        }
        
        !empty($_REQUEST['status']) ? $status = $_REQUEST['status'] : $status = 'u';
        
        foreach ($pids as $pid) {
            
            if ($status == 'u') {
                
                $send_status = $this->send_verify_email($pid);
                
                if ($send_status == '0') {
                    $verificationError = 1;
                }
            } elseif ($status == 'p') {
                
                // modified by Basudeba, Date : 17/Oct/2012.
                $stChildOppId = (isset($_REQUEST['rec_id']) && trim($_REQUEST['rec_id']) != '') ? $_REQUEST['rec_id'] : $_REQUEST['mass_opp'][$pid];
                $set_status = $this->set_status_verified($pid, $stChildOppId);
                if ($set_status != '1') {
                    $verificationError = 1;
                }
            }
        }
        
        $redirect_url = 'index.php?module=Opportunities&action=DetailView&record=' . $_REQUEST['record'] . '&ClubbedView=1';
       
        //if verified from proposal list view redirect back there
        if( !empty($_REQUEST['return_module'])  && ( $_REQUEST['return_module'] == 'Quotes') ){
            $redirect_url = 'index.php?module=Quotes&action=index';
        } 
        
        if ($verificationError == 1) {
            $redirect_url .= '&verification_error=1';
        }
        
        SugarApplication::redirect($redirect_url);
        exit();
    }
    
    /**
     * ********************************
     * @function set_status_verified().
     *
     * @author modified : Basudeba Rath.
     *         @date modified:18/10/2012.
     *        
     */
    function set_status_verified($proposal_id, $opportunity_id)
    {
        global $current_user, $db, $sugar_config, $timedate, $mod_strings;
        
        /*
         * $updateQuery = "UPDATE quotes SET proposal_verified='1' WHERE id='".
         * $proposal_id ."'"; $db->query ( $updateQuery ); $proposalObj = new
         * Quote(); $proposalObj->retrieve($proposal_id);
         * $proposalObj->proposal_verified = 1; $proposalObj->save();
         */
        
        require_once 'custom/modules/Quotes/QuoteHooks.php';
        
        $obQuoteLogic = new QuoteHooks();
        $obProposal = new Quote();
        $obProposal->retrieve($proposal_id);
        // Ashutosh on 16 Jan 2013
        if (trim($obProposal->contact_email) != '' && !$this->checkEmailOptedOut($obProposal->contact_email)) {
            return false;
        }

        /**
	*Added By : Ashutosh
        * Purpose : to add 2 hour validation on proposal schedule.
	*/	
        if (trim($obProposal->date_time_delivery) != '') {
            $date_time = $obProposal->date_time_delivery;
            $timezone = $obProposal->delivery_timezone;
        
            require_once 'custom/include/OssTimeDate.php';
            $oss_timedate = new OssTimeDate();
        
            $db_date_time_delivery = $oss_timedate->convertDateForDB($date_time, $timezone, true);
            $delivery_time = strtotime($db_date_time_delivery);

            $now_date_time = $oss_timedate->nowDb();
            $now_time = strtotime($now_date_time);
        
            // if delivery time is less than 1 hour
            if (($delivery_time - $now_time) <=7200) {
        
                $GLOBALS['log']->fatal("Proposal Verification Error: Delivery time less than 1 hour:" . $proposal_id);
        
                return false;
            }
        }
        //if empty opportunity get opportunity id from relationship
        if(empty($opportunity_id)){
            $obProposal->load_relationship('opportunities');
            $opportunities = $obProposal->opportunities->get(); 
            $opportunity_id = $opportunities[0];
        }
        
        // Added By Basudeba Rath, Date : 25/Oct/2012.
        // Load the Quotes languages.
        $mod_strings = return_module_language('en_us', 'Quotes');
        
        // set this propoerty to not to redirect
        $obProposal->do_not_redirect = true;
        
        //consider this proposal as verified 
        $obProposal->proposal_verified = 1;
        
        $obProposal->date_time_delivery = $timedate->to_db($obProposal->date_time_delivery);
        
        //check status and update the proposal 
        if ($obQuoteLogic->scheduleProposal($obProposal) !== false) {
            
            //to skip the logic hooks we are using direct SQL
            $updateQuery = "UPDATE quotes SET  proposal_verified='1'
                    			   , date_modified= UTC_TIMESTAMP()
                                    , verified_date=UTC_TIMESTAMP()
                                   ,modified_user_id= '".$current_user->id."' 
                    		WHERE id='" . $proposal_id . "'";
            
            $db->query($updateQuery);
            
            //to skip the logic hooks we are using direct SQL
            $updateQuery = "UPDATE opportunities SET  sales_stage ='Proposal - Verified', date_modified= UTC_TIMESTAMP()
                                   ,modified_user_id= '".$current_user->id."' 
                            WHERE id='" . $opportunity_id . "'";
            
            /**
             * Maintain Change Log For Opportunity
             */
            //Get Previous Value of Opportunity Sales Stage
            $opp_ss_sql = "SELECT sales_stage FROM opportunities WHERE id='".$opportunity_id."' AND deleted=0";
            $opp_ss_query = $db->query($opp_ss_sql);
            $opp_ss_result = $db->fetchByAssoc($opp_ss_query);
            $old_sales_stage = $opp_ss_result['sales_stage'];
            
            insertChangeLog($db, 'opportunities', $opportunity_id, $old_sales_stage, 'Proposal - Verified', 'sales_stage', 'enum', $current_user->id);
            $db->query($updateQuery);           
            
            $bReturnStatus =  true;
            
        } else {
            
            //cought an error 
            $bReturnStatus =   false;
        }
        
        return $bReturnStatus;
    }
    function send_verify_email($proposal_id)
    {
        global $current_user, $db, $sugar_config, $mod_strings;
        
        $mod_strings = return_module_language('en_us', 'Quotes');
        
        $object_map = array ();
        require_once ('include/Sugarpdf/SugarpdfFactory.php');
        
        // get Proposal data
        $obProposal = loadBean('Quotes');
        $obProposal->retrieve($proposal_id);
        
        $obUser = new User();
        $obUser->disable_row_level_security = 1;
        $obUser->retrieve($obProposal->assigned_user_id);
        $stUserEmailAddress = $current_user->email1;
        
        // Ashutosh : date: 16 Jan 2013
        // if the user has opted-out do not send verification email
        if (!$this->checkEmailOptedOut($current_user->email1)) {
            return false;
        }
        // hirak : date : 12-10-2012
        // if verification method is not set
        // if($current_user->phone_fax == ''
        // && ($obProposal->proposal_delivery_method == 'E' ||
        // $obProposal->proposal_delivery_method == 'EF')){
        if ($obProposal->proposal_delivery_method == '' || $obProposal->proposal_delivery_method == 'M') {
            
            $GLOBALS['log']->fatal("Proposal Verification Error: Verification method is not set:" . $proposal_id);
            
            return '0';
        }
        
        // if email is not set
        if (trim($stUserEmailAddress) == '') {
            
            $GLOBALS['log']->fatal("Proposal Verification Error: NO Email Address is set to send verification:" . $proposal_id);
            
            return '0';
        }
        if (trim($obProposal->date_time_delivery) != '') {
            $date_time = $obProposal->date_time_delivery;
            $timezone = $obProposal->delivery_timezone;
            
            require_once 'custom/include/OssTimeDate.php';
            $oss_timedate = new OssTimeDate();
            
            $db_date_time_delivery = $oss_timedate->convertDateForDB($date_time, $timezone, true);
            $delivery_time = strtotime($db_date_time_delivery);
            
            $now_date_time = $oss_timedate->nowDb();
            $now_time = strtotime($now_date_time);
            
            $delivery_time - $now_time;
            
            // if delivery time is less than 1 hour
            if (($delivery_time - $now_time) < 3600) {
                
                $GLOBALS['log']->fatal("Proposal Verification Error: Delivery time less than 1 hour:" . $proposal_id);
                
                return '0';
            }
        }
        
        include_once "custom/modules/EmailTemplates/CustomEmailTemplate.php";
        
        // get email template
        $obEmailTemplate = new CustomEmailTemplate();
        $obEmailTemplate->retrieve_by_string_fields(array (
                'name' => 'Proposal Template' 
        ));
        
        $arHTMLEmailData = $obEmailTemplate->parse_template_bean(array (
                'subject' => $obEmailTemplate->subject,
                'body_html' => '<html><title></title><head></head><body>' . html_entity_decode($obEmailTemplate->body_html) . '</body></html>',
                'body' => $obEmailTemplate->body 
        ), 'Quotes', $obProposal);
        
        // get fax template
        $obEmailTemplate->retrieve_by_string_fields(array (
                'name' => 'Proposal Fax Template' 
        ));
        
        $arHTMLFaxCoverData = $obEmailTemplate->parse_template_bean(array (
                'subject' => $obEmailTemplate->subject,
                'body_html' => '<html><title></title><head></head><body>' . html_entity_decode($obEmailTemplate->body_html) . '</body></html>',
                'body' => $obEmailTemplate->body 
        ), 'Quotes', $obProposal);
        
        // check if there are lineitems
        $product = new Product();
        
        $where_lineitems = " products.quote_id='" . $proposal_id . "' 
			AND  (products.product_type='line_items'	OR products.product_type='inclusions'
			OR products.product_type='exclusions' OR products.product_type='alternates') ";
        $line_items = $product->get_full_list('', $where_lineitems);
        
        if (count($line_items) > 0) {
            
            // get Proposal PDF
        	/**
        	 * proposal verisoning
        	 * Hirak - 07.02.2013
        	 */
           //$stFileName = 'Proposal_' . $obProposal->quote_num . '.pdf';
            $stFileName = $obProposal->name .' '.$obProposal->proposal_version.'.pdf';
            
            $pdf = SugarpdfFactory::loadSugarpdf('Standard', 'Quotes', $obProposal, $object_map);
            $pdf->process();
            $bean = $obProposal;
            
            $stTmpPdf = $pdf->Output('', 'S');
            
            $arAttachedFiles[] = array (
                    'fileName' => $stFileName,
                    'content' => $stTmpPdf,
                    'type' => 'PDF' 
            );
        }
        
        // Fetch all documents related with proposal
        $obProposal->load_relationship('documents');
        $arDocs = $obProposal->documents->get();
        
        $doc_count = 0;
        
        // Attach documents to email
        foreach ($arDocs as $stDoc) {
            $obDocument = loadBean('Documents');
            $obDocument->disable_row_level_security = true;
            $obDocument->retrieve($stDoc);
            
            // get Type of document
            $stType = getDocumentType($obDocument->last_rev_mime_type);
            
            $arAttachedFiles[] = array (
                    'fileName' => $obDocument->filename,
                    'content' => file_get_contents($GLOBALS['sugar_config']['upload_dir'] . $obDocument->document_revision_id),
                    'type' => $stType 
            );
            
            $doc_count++;
        }
        
        if ((count($line_items) < 1) && ($doc_count < 1)) {
            
            $GLOBALS['log']->fatal("Proposal Verification Error: No Line Item or Document Attached:" . $proposal_id);
            
            return '0';
        }
        
        // Prepare Easy Linkparam
        $obEasyLink = new easyLinkMessage($sugar_config['EASY_LINK_USER_NAME'], $sugar_config['EASY_LINK_USER_PASS']);
        // set WSDL for JobSubmit
        $obEasyLink->wsdl = $sugar_config['EASY_LINK_JOBSUBMIT_WSDL'];
        
        // hirak : date : 12-10-2012
        // Check if send email/Both is set
        if (($obProposal->proposal_delivery_method == 'E' || $obProposal->proposal_delivery_method == 'EF')) {
            
            // create JobSubmit Params
            $arEasyLinkJobSubmitEmailParams = array (
                    'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $this->bean->id . '-' . strtotime(date('Y-m-d h:i:s')),
                    'BillingCode' => 'Verify Proposal Email ' . $obProposal->quote_num,
                    'CustomerReference' => $obEasyLink->instanceFolderName,
                    'emailSubject' => '' . $arHTMLEmailData['subject'] . ' ' . $obProposal->name,
                    'email' => array (
                            'toEmail' => $stUserEmailAddress,
                            'attachments' => $arAttachedFiles,
                            'emailBody' => $arHTMLEmailData['body_html'],
                            'FromAddress' => $obUser->email1,
                            'ReplyTo' => $obUser->email1,
                            'FromDisplayName' => $obUser->company_name  // $obProposal->assigned_user_name,
                    
                     
            ))       
            ;
            
            try {
                
                // send JobSubmit Request for Email
                $obResult = $obEasyLink->jobSubmitEmail($arEasyLinkJobSubmitEmailParams, false);
                
                // track this response
                if ($obResult->Status->StatusCode == '0') {
                    
                    // set proposal email verify MRN
                    $obProposal->easy_email_verify_mrn = $obResult->MessageResult->JobId->MRN;
                    
                    $this->bIsProposalEmailSent = true;
                }
            } catch (SoapFault $obFault) {
                
                $this->bIsProposalEmailSent = false;
                $GLOBALS['log']->fatal(" Proposal Verification Error: SoapFault Error Message for Email Verify :" . $obFault->getMessage() . ' [ERROR CODE = ' . $obFault->getCode() . ' ] ');
            }
        }
        
        // hirak : date : 12-10-2012
        // send fax
        if (($obProposal->proposal_delivery_method == 'F' || $obProposal->proposal_delivery_method == 'EF')) {
            
            // create JobSubmit Params
            $arEasyLinkJobSubmitFaxParams = array (
                    'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $obProposal->id . '-' . strtotime(date('Y-m-d h:i:s')),
                    'BillingCode' => 'Verify Proposal fax ' . $obProposal->quote_num,
                    'Phone' => $current_user->phone_fax, // $obProposal->contact_fax,
                    'CustomerReference' => $obEasyLink->instanceFolderName,
                    'Fax' => array (
                            'Phone' => $current_user->phone_fax, // $obProposal->contact_fax,
                            'attachments' => $arAttachedFiles,
                            'CoverBody' => $arHTMLFaxCoverData['body_html'] 
                    ) 
            );
            
            try {
                
                $obResult = $obEasyLink->jobSubmitFAX($arEasyLinkJobSubmitFaxParams);
                
                // track this response
                if ($obResult->Status->StatusCode == '0') {
                    // set proposal email verify MRN
                    $obProposal->easy_fax_verify_mrn = $obResult->MessageResult->JobId->MRN;
                    
                    $this->bIsProposalFaxSent = true;
                }
            } catch (SoapFault $obFaxFault) {
                
                $this->bIsProposalFaxSent = false;
                // LOG THIS
                $GLOBALS['log']->fatal("Proposal Verification Error:  SoapFault Error Message for FAX verify :" . $obFault->getMessage() . ' [ERROR CODE = ' . $obFault->getCode() . ' ] ');
            }
        }
        
        // check if email need to sent
        if (isset($this->bIsProposalEmailSent)) {
            
            // is it sent by the API
            if ($this->bIsProposalEmailSent === true) {
                $this->isSent = true;
                $this->arMessage[] = 'Verification email has been sent.';
                $GLOBALS['log']->fatal("Proposal Verification Success: Verification email has been sent:" . $proposal_id);
            } else {
                $this->arMessage[] = 'Error while sending Verification email.';
                $GLOBALS['log']->fatal("Proposal Verification Error: Error while sending verification email:" . $proposal_id);
            }
        }
        
        // check if Fax need to sent
        if (isset($this->bIsProposalFaxSent)) {
            
            // is it sent by the API
            if ($this->bIsProposalFaxSent === true) {
                
                $this->isSent = true;
                $this->arMessage[] = 'Verification fax has been sent.';
                $GLOBALS['log']->fatal("Proposal Verification Success: Verification fax has been sent:" . $proposal_id);
            } else {
                $this->arMessage[] = 'Error while sending verification fax.';
                $GLOBALS['log']->fatal("Proposal Verification Error: Error while sending verification fax:" . $proposal_id);
            }
        }
        
        // check status and up date MRN numbers
        if ($this->isSent === true) {
            
            /*
             * Saving proposal with this creates a mess from logic hooks hence
             * writing SQL $obProposal->verify_email_sent = '1';
             * $obProposal->save();
             */
            $updateSql = "UPDATE quotes SET verify_email_sent='1'
												,easy_email_verify_mrn  = '" . $obProposal->easy_email_verify_mrn . "'
										,easy_fax_verify_mrn = '" . $obProposal->easy_fax_verify_mrn . "'
									 WHERE id='" . $obProposal->id . "'";
            $db->query($updateSql);
            
            return '1';
        }
    }
    
    /*
     *
     */
    function checkEmailOptedOut($stEmail)
    {
        $bReturn = true;
        $stSql = 'SELECT * FROM email_addresses WHERE email_address ="' . trim($stEmail) . '" AND opt_out = 1  ';
        
        $rsReulst = $GLOBALS['db']->query($stSql);
        $arEmailData = $GLOBALS['db']->fetchByAssoc($rsReulst);
        $bIsEmailOptOut = (isset($arEmailData['id'])) ? 1 : '0';
        // var_dump($bIsEmailOptOut);die;
        if ($bIsEmailOptOut) {
            
            $bReturn = false;
        }
        
        return $bReturn;
    }
}
