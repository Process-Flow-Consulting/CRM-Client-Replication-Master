<?php

global $sugar_config;
require_once 'include/MVC/View/SugarView.php';
include_once "custom/modules/AOS_Quotes/schedule_quotes/class.easylinkmessage.php";	
require_once $sugar_config['master_config_path'] ;//  '/vol/certificate/master_config.php';
ini_set('zend_optimizerplus.dups_fix',1);
class AOS_QuotesViewSend_verify_email extends SugarView {
	
	var $arMessage = array ();
	var $isSent = false;
	
	function __construct() {
		parent::SugarView ();
	}
	/**
	 *
	 * @see SugarView::display()
	 */
	function display() {
		
		global $current_user, $db, $sugar_config;
		
		$object_map = array ();
		
		require_once ('include/Sugarpdf/SugarpdfFactory.php');
		
		$stProposalId = $_REQUEST ['proposal_id'];
		
		// get Proposal data
		$obProposal = loadBean ( 'AOS_Quotes' );
		$obProposal->retrieve ( $stProposalId );
		
		$stUserEmailAddress = $current_user->email1;
		
		$return = 'Email Not Found';
		
		//hirak : date: 11-10-2012
		if($current_user->phone_fax == '' && ($obProposal->proposal_delivery_method == 'F' || $obProposal->proposal_delivery_method == 'EF')){
			$this->bSentStatus = false;
			$this->arMessage [] = 'Please specify your fax number.';
		}
		
		// if email is not set
		if (trim ( $stUserEmailAddress ) == '') {
			
			$this->bSentStatus = false;
			$this->arMessage [] = 'Please specify your email address.';
			
			
		}
		
		if(isset($this->bSentStatus) && $this->bSentStatus === false){
			$this->ss->assign ( 'AR_STATUS', json_encode ( array (
					'status' => $this->bSentStatus,
					'messages' => $this->arMessage 
			) ) );
			echo json_encode ( array (
									'status' => $this->bSentStatus,
									'messages' => $this->arMessage
							 		) 
							 );
			return ;
		}
		
		$stSql = 'SELECT * FROM email_addresses WHERE email_address ="'.trim($current_user->email1).'" AND opt_out = 1  ';
		
		$rsReulst = $GLOBALS['db']->query($stSql);
		$arEmailData =  $GLOBALS['db']->fetchByAssoc($rsReulst);
		$bIsEmailOptOut=(isset($arEmailData['id']))?1:'0';
		
		if($bIsEmailOptOut){
			echo json_encode ( array (
					'status' => 0,
					'messages' => array('You can not verify this proposal as '.$current_user->email1.' is opted out.')
			)
			);
			return ;
		}
		if($obProposal->date_time_delivery != ''){
			
		$date_time = $obProposal->date_time_delivery;
		
		$timezone = $obProposal->delivery_timezone;
		
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();
			
		$db_date_time_delivery = $oss_timedate->convertDateForDB($date_time, $timezone,true);
		$delivery_time = strtotime($db_date_time_delivery);
			
		$now_date_time = $oss_timedate->nowDb();
		$now_time = strtotime($now_date_time);
		
		//echo $delivery_time - $now_time;
		
		if( ($delivery_time - $now_time ) < 3600){
			
			$this->bSentStatus = false;
			$this->arMessage [] = 'Proposal can not be verified after one hour prior to delivery time.';
			
			echo json_encode ( array (
					'status' => $this->bSentStatus,
					'messages' => $this->arMessage
			)
			);
			die;
		}
	}
		
		
		#################################################
		###         RETRIEVE PROPOSAL DETAILS       #####
		#################################################
		//retrive assigned User detials
		$obUser = new User();
		$obUser->disable_row_level_security = 1;
		$obUser->retrieve( $obProposal->assigned_user_id);
		
		include_once "custom/modules/EmailTemplates/CustomEmailTemplate.php";			
			
		$obEmailTemplate = new CustomEmailTemplate ();
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Template'
		) );
			
		$arHTMLEmailData = $obEmailTemplate->parse_template_bean ( array (
				'subject' => $obEmailTemplate->subject,
				'body_html' => '<html><title></title><head></head><body>'.cleanSpecialChars($obEmailTemplate->body_html).'</body></html>',
				'body' => $obEmailTemplate->body
		), 'AOS_Quotes', $obProposal );

		//get fax template
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Fax Template'
		) );
		
		
		$arHTMLFaxCoverData = $obEmailTemplate->parse_template_bean ( array (
				'subject' => $obEmailTemplate->subject,
				'body_html' => '<html><title></title><head></head><body>'.cleanSpecialChars($obEmailTemplate->body_html).'</body></html>',
				'body' => $obEmailTemplate->body
		), 'AOS_Quotes', $obProposal );
		
		//check if there are lineitems
		$product = new AOS_Products();
			
		$where_lineitems = " aos_products.quote_id='".$stProposalId
		."' AND  (aos_products.product_type='line_items'	OR aos_products.product_type='inclusions'
		OR aos_products.product_type='exclusions' OR aos_products.product_type='alternates') ";
		$line_items = $product->get_full_list('',$where_lineitems);
		
		if(count($line_items) > 0){
			// get Proposal PDF
			
			/**
			 * proposal verisoning
			 * Hirak - 07.02.2013
			 */
			//$stFileName = 'Proposal_' . $obProposal->quote_num . '.pdf';
			$stFileName = $obProposal->name .' '.$obProposal->proposal_version.'.pdf';
			
				
			$pdf = SugarpdfFactory::loadSugarpdf ( 'Standard', 'AOS_Quotes', $obProposal, $object_map );
			$pdf->process ();
			$bean = $obProposal;
				
			$stTmpPdf = $pdf->Output ( '', 'S' );
				
			$arAttachedFiles [] = array (
					'fileName' => $stFileName,
					'content' => $stTmpPdf,
					'type' => 'PDF'
			)
			;
		}	
		// Fetch all documents related with proposal
		$obProposal->load_relationship ( 'documents' );
		$arDocs = $obProposal->documents->get ();
		// Attach documents to email
		foreach ( $arDocs as $stDoc ) {
			$obDocument = loadBean ( 'Documents' );
			$obDocument->disable_row_level_security = true;
			$obDocument->retrieve ( $stDoc );
			
			//get Type of document
			// $stType = getDocumentType($obDocument->last_rev_mime_type);
			
			$arAttachedFiles [] = array (
					'fileName' => $obDocument->filename,
					'content' => file_get_contents ( $GLOBALS ['sugar_config'] ['upload_dir'] . $obDocument->document_revision_id ),
					'type' =>  'PDF'
			)
			;
		}
		
		
		
		######################################################
		###         RETRIEVE PROPOSAL DETAILS ENDS       #####
		######################################################
		
		// Prepare Easy Linkparam
		$obEasyLink = new easyLinkMessage ( $sugar_config ['EASY_LINK_USER_NAME'], $sugar_config ['EASY_LINK_USER_PASS'] );
		// set WSDL for JobSubmit 
		$obEasyLink->wsdl = $sugar_config ['EASY_LINK_JOBSUBMIT_WSDL'];
		
		//hirak: date: 11-10-2012
		// Check if send email/Both is set
		if (($obProposal->proposal_delivery_method == 'E' || $obProposal->proposal_delivery_method == 'EF')) {
			
				
			
			// create JobSubmit Params
			$arEasyLinkJobSubmitEmailParams = array (
					'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $this->bean->id . '-' . strtotime ( date ( 'Y-m-d h:i:s' ) ),
					'BillingCode' => 'Verify Proposal Email ' . $obProposal->number,
					'CustomerReference' => $obEasyLink->instanceFolderName,
					'emailSubject' => ''.$arHTMLEmailData ['subject'].' '.$obProposal->name,
					'email' => array (
							'toEmail' => $stUserEmailAddress,
							'attachments' => $arAttachedFiles,
							'emailBody' => $arHTMLEmailData ['body_html'],	
					        //'FromAddress' => $obUser->email1,
							'ReplyTo' => $obUser->email1,
							'FromDisplayName'=>$obUser->company_name //$obProposal->assigned_user_name, 
									));
			
			
			try {
				
				// send JobSubmit Request for Email
				$obResult= $obEasyLink->jobSubmitEmail($arEasyLinkJobSubmitEmailParams,false);
				
				// track this response
				if ($obResult->Status->StatusCode == '0') {
					// set proposal email verify MRN
					$obProposal->easy_email_verify_mrn = $obResult->MessageResult->JobId->MRN;
					
					$this->bIsProposalEmailSent = true;
				}
			
			} catch ( SoapFault $obFault ) {
				
				$this->bIsProposalEmailSent = false;
				$GLOBALS ['log']->fatal ( " SoapFault Error Message for Email Verify :" . $obFault->getMessage () . ' [ERROR CODE = ' . $obFault->getCode () . ' ] ' );
			
			}
		
		} 
		//hirak: date: 11-10-2012
		// send fax
		if (($obProposal->proposal_delivery_method == 'F' || $obProposal->proposal_delivery_method == 'EF')) {
			
			//check if fax is specified for this Quote			
			/*if (trim($obProposal->contact_fax) == '') {				
				$this->bSentStatus= false;
				$this->arMessage[] = 'Please specify a fax number.';					
				$this->ss->assign('AR_STATUS',json_decode(array('status'=>$this->bSentStatus,'messages' =>$this->arMessage )));
				
				echo json_decode(array('status'=>$this->bSentStatus,'messages' =>$this->arMessage ));
				return ;	
			}*/
			
			// create JobSubmit Params
			$arEasyLinkJobSubmitFaxParams = array (
					'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $obProposal->id . '-' . strtotime ( date ( 'Y-m-d h:i:s' ) ),
					'BillingCode' => 'Verify Proposal fax ' . $obProposal->number,
					'Phone' => $current_user->phone_fax,// $obProposal->contact_fax,
					'CustomerReference' => $obEasyLink->instanceFolderName,					
					'Fax' => array ('Phone' => $current_user->phone_fax,  //$obProposal->contact_fax,						
									'attachments' => $arAttachedFiles ,
								    'CoverBody' => $arHTMLFaxCoverData ['body_html']						
			
					));
			
			try{
			
				$obResult = $obEasyLink->jobSubmitFAX($arEasyLinkJobSubmitFaxParams);
				
				// track this response
				if ($obResult->Status->StatusCode == '0') {
					// set proposal email verify MRN
					$obProposal->easy_fax_verify_mrn = $obResult->MessageResult->JobId->MRN;
					
					$this->bIsProposalFaxSent = true;
				}
			
			}
			catch(SoapFault $obFaxFault){
				
				$this->bIsProposalFaxSent = false;
				// LOG THIS		
				$GLOBALS ['log']->fatal ( " SoapFault Error Message for FAX verify :" . $obFault->getMessage () . ' [ERROR CODE = ' . $obFault->getCode () . ' ] ' );
				
			}	
		
		}
		
		//check if email need to sent 
		if(isset($this->bIsProposalEmailSent) ){
			
			//is it sent by the API
			if($this->bIsProposalEmailSent  === true){
								
				$this->isSent = true;
				$this->arMessage[]= 'Verification email has been sent.';
				
			}else
			{
				$this->arMessage[]= 'Error while sending Verification email.';
			}
		}
		
		//check if Fax need to sent 
		if(isset($this->bIsProposalFaxSent) ){
			//is it sent by the API
			if($this->bIsProposalFaxSent  === true){
				
				$this->isSent = true;
				$this->arMessage[]= 'Verification fax has been sent.';				
				
			}else
			{
				$this->arMessage[]= 'Error while sending verification fax.';
			}
		}	
		
		//check status and up date MRN numbers
		if($this->isSent === true){
			
			/*
			 * Saving proposal with this creates a mess from logic hooks 
			 * hence writing SQL
			 * $obProposal->verify_email_sent = '1';				
				$obProposal->save();*/
			
			$updateSql = "UPDATE aos_quotes SET verify_email_sent='1'
											,easy_email_verify_mrn  = '".$obProposal->easy_email_verify_mrn."'
											,easy_fax_verify_mrn = '".$obProposal->easy_fax_verify_mrn."'
						 WHERE id='" . $obProposal->id . "'";
			$db->query ( $updateSql );
						
		}
		
		$arResult = array('status'=>$this->isSent,
						 'messages' =>$this->arMessage );
		//var_dump(json_encode($arResult));
		echo json_encode($arResult);
		die();
		
	}
	/**
	 *
	 * @see SugarView::display()
	 */
	function old_display() {
		global $current_user, $db;
		$object_map = array ();
		require_once ('include/SugarPHPMailer.php');
		require_once ('include/Sugarpdf/SugarpdfFactory.php');
		
		$proposal_id = $_REQUEST ['proposal_id'];
		
		$proposal = loadBean ( 'AOS_Quotes' );
		$proposal->retrieve ( $proposal_id );
		
		$user_email = $current_user->email1;
		$return = 'Email Not Found';
		//hirak: date: 11-10-2012
		if (! empty ( $user_email ) && ($proposal->proposal_delivery_method == 'E' || $proposal->proposal_delivery_method == 'EF')) {
			// Get Proposal Template.
			include_once "custom/modules/EmailTemplates/CustomEmailTemplate.php";
			$email_template = new CustomEmailTemplate ();
			$email_template->retrieve_by_string_fields ( array (
					'name' => 'Proposal Template' 
			) );
			$email_template_body = $email_template->parse_template_bean ( array (
					'subject' => $email_template->subject,
					'body_html' => $email_template->body_html,
					'body' => $email_template->body 
			), 'AOS_Quotes', $proposal );
			
			$mail = new SugarPHPMailer ();
			$mail->setMailerForSystem ();
			$mail->IsHTML ( true );
			$mail->From = $user_email;
			$mail->FromName = $current_user->name;
			$mail->ClearAllRecipients ();
			$mail->ClearReplyTos ();
			
			$subject = "Proposal for: " . $proposal->name;
			
			if (! empty ( $email_template_body ['subject'] )) {
				$subject = $email_template_body ['subject'];
			}
			
			$mail->Subject = from_html ( $subject );
			
			$mail->Body = from_html ( $email_template_body ['body_html'] );
			$mail->AltBody = from_html ( $email_template->description );
			
			$mail->prepForOutbound ();
			
			/**
			 * proposal verisoning
			 * Hirak - 07.02.2013
			 */
			//$fileName = 'Proposal_' . $proposal->quote_num . '.pdf';
			$fileName = $proposal->name .' '.$proposal->proposal_version.'.pdf';
			
			
			$pdf = SugarpdfFactory::loadSugarpdf ( 'Standard', 'AOS_Quotes', $proposal, $object_map );
			$pdf->process ();
			$bean = $proposal;
			
			$tmp = $pdf->Output ( '', 'S' );
			
			$badoutput = ob_get_contents ();
			if (strlen ( $badoutput ) > 0) {
				ob_end_clean ();
			}
			
			$fp = sugar_fopen ( 'custom/modules/AOS_Quotes/send_pdf/' . $fileName, 'w' );
			fwrite ( $fp, ltrim ( $tmp ) );
			fclose ( $fp );
			
			$mail->AddAttachment ( 'custom/modules/AOS_Quotes/send_pdf/' . $fileName, $fileName );
			
			// Fetch all documents related with proposal
			$proposal->load_relationship ( 'documents' );
			$docs = $proposal->documents->get ();
			// Attach documents to email
			foreach ( $docs as $doc ) {
				$document = loadBean ( 'Documents' );
				$document->disable_row_level_security = true;
				$document->retrieve ( $doc );
				$mail->AddAttachment ( $GLOBALS ['sugar_config'] ['upload_dir'] . $document->document_revision_id, $document->filename );
			}
			
			$mail->AddAddress ( $user_email );
			$success = @$mail->Send ();
			$return = 'Not Sent';
			if ($success) {
				// Set verify_email_sent flag as 1
				$updateSql = "UPDATE aos_quotes SET verify_email_sent='1' WHERE id='" . $proposal->id . "'";
				$db->query ( $updateSql );
				$return = 'Sent';
				unlink ( 'custom/modules/AOS_Quotes/send_pdf/' . $fileName );
			}
		}
		
		echo json_encode ( $return );
	}
}
