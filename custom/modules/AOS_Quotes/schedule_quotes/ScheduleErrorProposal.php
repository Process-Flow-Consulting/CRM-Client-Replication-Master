<?php


ini_set('zend_optimizerplus.dups_fix',1);

global $sugar_config, $timedate,$current_user;

require_once  $sugar_config['master_config_path'] ;//'/vol/certificate/master_config.php';
require_once 'custom/include/master_db/mysql.class.php';
require_once 'custom/include/common_functions.php';
require_once 'custom/modules/AOS_Quotes/schedule_quotes/class.easylinkmessage.php';
require_once 'include/SugarDateTime.php';
require_once('include/modules.php');
require_once('config.php');
require_once 'include/utils.php';

global $sugar_config, $current_language, $app_list_strings, $app_strings, $locale,$mod_strings;
$language = $sugar_config['default_language'];//here we'd better use English, because pdf coding problem.

$app_list_strings = return_app_list_strings_language($language);
$app_strings = return_application_language($language);
//$mod_strings =return_mod_list_strings_language($language,'AOS_Quotes');

include("modules/AOS_Quotes/language/$language.lang.php");
include("custom/modules/AOS_Quotes/Ext/Language/$language.lang.ext.php");

/*	$user = new User();
	$user->retrieve('1');
	
	$current_user = $user;
*/
//get instance account number 
$obAdmin = new Administration ();
$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );


//validate if this request is from a valid source
if(!isset($_REQUEST['salt']) || trim($_REQUEST['salt'])=='' || $_REQUEST['salt'] != md5($sugar_config['dbconfig']['db_name']) ){
	echo $stUnathorized = '###########   Unautherorized access to [HOST :'.$_SERVER['HTTP_HOST'].'[ QUERY_STRING : '.$_SERVER['QUERY_STRING'].']';
	$GLOBALS['log']->fatal($stUnathorized);
	die;	
}

if (isset ( $_REQUEST ['record'] ) && trim ( $_REQUEST ['record'] ) != '') {
	$focus = new AOS_Quotes ();
	$focus->disable_row_level_security = 1;
	$focus->retrieve ( $_REQUEST ['record'] );
}
else{
	die('NOTHING TO SCHEDULE');
}

$queue_id = $_REQUEST ['queue_id'];

$obEasyLink = new easyLinkMessage ( $sugar_config ['EASY_LINK_USER_NAME'], $sugar_config ['EASY_LINK_USER_PASS'] );
//get DB Object
$cdb = $obEasyLink->__getCentralDB();
//$cdb = new MasterMySQL ( MASTER_HOST, MASTER_USER, MASTER_PASS, MASTER_CRON_DB, true );

/**
 * A proposal will be scheduled on following conditions
 *  1) Proposal is verified
 */
 if ( $focus->proposal_verified == '1') {
 	
	
	// check if entry already exists
	$stDbName = $sugar_config ['dbconfig'] ['db_name'];
	
	// create schedule/stop date
	$obScheduleDate = new SugarDateTime ( $focus->date_time_delivery, new DateTimeZone ( 'UTC' ) );
	
	$obScheduleStopDate = new SugarDateTime ( $focus->date_time_delivery, new DateTimeZone ( 'UTC' ) );
	$obScheduleStopDate->modify ( '+1 day' );
	
	$dtScheduledGmtTime = $obScheduleDate->format ( 'Y-m-d' ) . 'T' . $obScheduleDate->format ( 'H:i:sP' );
	$dtScheduledStopGmtTime = $obScheduleStopDate->format ( 'Y-m-d' ) . 'T' . $obScheduleDate->format ( 'H:i:sP' );
	
	// get difference in days
	$iDaysDiff = ceil ( (strtotime ( $focus->date_time_delivery ) - strtotime ( $timedate->nowDb () )) / (60 * 60 * 25) );
	
	

		//echo '<pre>';print_r($dtScheduledGmtTime);die;
		// schedule this proposal now
		// ###############################################
		// # RETRIEVE PROPOSAL DETAILS #####
		// ###############################################
		require_once ('include/Sugarpdf/SugarpdfFactory.php');
		include_once "custom/modules/EmailTemplates/CustomEmailTemplate.php";
		$object_map = array ();
		$obEmailTemplate = new CustomEmailTemplate ();
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Template' 
		) );
		
		$arHTMLEmailData = $obEmailTemplate->parse_template_bean ( array (
				'subject' => $obEmailTemplate->subject,
				'body_html' => '<html><title></title><head></head><body>'.cleanSpecialChars($obEmailTemplate->body_html).'</body></html>',
				'body' => $obEmailTemplate->body 
		), 'AOS_Quotes', $focus );
		
		
		//get fax template
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Fax Template'
		) );
		
		
		$arHTMLFaxCoverData = $obEmailTemplate->parse_template_bean ( array (
				'subject' => $obEmailTemplate->subject,
				'body_html' => '<html><title></title><head></head><body>'.cleanSpecialChars($obEmailTemplate->body_html).'</body></html>',
				'body' => $obEmailTemplate->body
		), 'AOS_Quotes', $focus );
		
		
		
		
		//check if there are lineitems
		$product = new AOS_Products();
		$product->disable_row_level_security = 1;
		$where_lineitems = " aos_products.quote_id='".$focus->id
		."' AND  (aos_products.product_type='line_items'	OR aos_products.product_type='inclusions'
		OR aos_products.product_type='exclusions' OR aos_products.product_type='alternates') ";
		$line_items = $product->get_full_list('',$where_lineitems);
		
		if(count($line_items) > 0){
			// get Proposal PDF
			/**
			 * proposal verisoning
			 * Hirak - 07.02.2013
			 */
			//$stFileName = 'Proposal_' . $focus->quote_num . '.pdf';
			$stFileName = $focus->name .' '.$focus->proposal_version.'.pdf';
			
			$pdf = SugarpdfFactory::loadSugarpdf ( 'Standard', 'AOS_Quotes', $focus, $object_map );
			$pdf->process ();
			$bean = $focus;
			
			$stTmpPdf = $pdf->Output ( '', 'S' );
			
			$arAttachedFiles [] = array (
					'fileName' => $stFileName,
					'content' => $stTmpPdf,
					'type' => 'PDF' 
			);
		}
			
		// Fetch all documents related with proposal
		$focus->load_relationship ( 'documents' );
		$arDocs = $focus->documents->get ();
		
		// Attach documents to email
		foreach ( $arDocs as $stDoc ) {
			$obDocument = loadBean ( 'Documents' );
			$obDocument->disable_row_level_security = true;
			$obDocument->retrieve ( $stDoc );
				
			//get Type of document	
			$stType = getDocumentType($obDocument->last_rev_mime_type);			
			
			$arAttachedFiles [] = array (
					'fileName' => $obDocument->filename,
					'content' => file_get_contents ( $GLOBALS ['sugar_config'] ['upload_dir'] . $obDocument->document_revision_id ),
					'type' => $stType 
			);
		}
		
		
		 ###################################################
		 # RETRIEVE PROPOSAL DETAILS ENDS #####
		 ###################################################
		 //hirak: date: 12-10-2012
		// if email need to sent schedule an email
		if (($focus->proposal_delivery_method == 'E' || $focus->proposal_delivery_method == 'EF') && ($_REQUEST['error_type'] != 'fax_error')) {
			
			$arEasyLinkJobSubmitEmailParams = array ();
			$arEasyLinkJobSubmitEmailParams = array (
					'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $this->bean->id . '-' . strtotime ( date ( 'Y-m-d h:i:s' ) ),
					'BillingCode' => 'Proposal Email ' . $focus->quote_num,
					'CustomerReference' => 'The Bluebook',
					'emailSubject' => '' . $arHTMLEmailData ['subject'] . ' ' . $focus->name,
					'StartTime' => $dtScheduledGmtTime,
					'StopTime' => $dtScheduledStopGmtTime,
					'email' => array (
							'toEmail' => $focus->contact_email,
							'attachments' => $arAttachedFiles,
							'emailBody' => $arHTMLEmailData ['body_html'],
							//'FromAddress' => '',
							'FromDisplayName' => ''  // $obProposal->assigned_user_name,
							)
					 
			);
			
			try {
				
				$obEasyLink->wsdl = $sugar_config ['EASY_LINK_JOBSUBMIT_WSDL'];
				$obEmailScheduleResult = $obEasyLink->jobSubmitEmail ( $arEasyLinkJobSubmitEmailParams );
				
			
			} catch ( SoapFault $obFault ) {
				
				$obEasyLink->do_log( " SoapFault Error Message for Email Schedule :" . $obFault->getMessage () . ' [ERROR CODE = ' . $obFault->getCode () . ' ] ' );
				
				$stUpdateScheduleQueueSQL = "UPDATE  oss_schedulequeue SET process_state = 'pending',date_modified= NOW() WHERE id= '" . $queue_id."'";
				//$GLOBALS['log']->fatal($stUpdateScheduleQueueSQL);
				$cdb->query ( $stUpdateScheduleQueueSQL );
				
				// GOT AN ERROR SEND NOTIFICATION EMAIL
				$obErrorTemplate = $obEasyLink->getEmailTemplate($focus);
						
				$stSubject = $obErrorTemplate->subject;
				$stNotificationContent = $obErrorTemplate->body_html;					 				 
						 
				$obUser = new User();
				$obUser->retrieve($focus->assigned_user_id);					 
						
				$arEmailIds[] =  $focus->email1 ;
				$obEasyLink->sendNotificationEmail($arEmailIds, $stSubject,$stNotificationContent);
				//stop executing this script
				exit(0);
				
				
			}
		}
		
		$arFieldMap = array();
		if (isset ( $obEmailScheduleResult->MessageResult->JobId->MRN )) {
				
			$arFieldMap ['easy_email_mrn'] = $obEmailScheduleResult->MessageResult->JobId->MRN;
			$arFieldMap ['easy_email_xdn'] = $obEmailScheduleResult->MessageResult->JobId->XDN;
			$arFieldMap ['proposal_schedule_status'] = 'scheduled';
		}
		
		//hirak : date : 12-10-2012
		// if fax need to sentschedule Fax
		if ($focus->proposal_delivery_method == 'F' || $focus->proposal_delivery_method == 'EF') {
			
			// create JobSubmit Params
			$arEasyLinkJobSubmitFaxParams = array (
					'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $focus->id . '-' . strtotime ( date ( 'Y-m-d h:i:s' ) ),
					'BillingCode' => 'Proposal fax ' . $focus->quote_num,
					'Phone' => $focus->contact_fax,
					'CustomerReference' => 'The Bluebook',
					'StartTime' => $dtScheduledGmtTime,
					'StopTime' => $dtScheduledStopGmtTime,
					'Fax' => array (
							'Phone' => $focus->contact_fax,
							'attachments' => $arAttachedFiles,
							'CoverBody' => $arHTMLFaxCoverData ['body_html'] 
					) 
			);
			
			try {
				
				$obEasyLink->wsdl = $sugar_config ['EASY_LINK_JOBSUBMIT_WSDL'];
				$obFaxScheduleResult = $obEasyLink->jobSubmitFAX ( $arEasyLinkJobSubmitFaxParams );
				
			} catch ( SoapFault $obFault ) {
				
				if (isset ( $obEmailScheduleResult->MessageResult->JobId->MRN )) {
					
					$arFieldMap ['proposal_schedule_status'] = 'fax_error';
					
				}else{
					
					$arFieldMap ['proposal_schedule_status'] = 'pending';
					
				}
				//got an error to process API request / send emails and log this / add this request to schedule queue
				$obEasyLink->do_log( " SoapFault Error Message for fax Schedule :" . $obFault->getMessage () . ' [ERROR CODE = ' . $obFault->getCode () . ' ] ' );
				
				$stUpdateScheduleQueueSQL = "UPDATE  oss_schedulequeue SET process_state = '".$arFieldMap ['proposal_schedule_status']."',date_modified= NOW() WHERE id= '" . $queue_id."'";
					
				$cdb->query ( $stUpdateScheduleQueueSQL );
				
				// GOT AN ERROR SEND NOTIFICATION EMAIL
				$obErrorTemplate = $obEasyLink->getEmailTemplate($focus);
						
				$stSubject = $obErrorTemplate->subject;
				$stNotificationContent = $obErrorTemplate->body_html;					 				 
						 
				$obUser = new User();
				$obUser->retrieve($focus->assigned_user_id);					 
						
				$arEmailIds[] =  $focus->email1 ;
				$obEasyLink->sendNotificationEmail($arEmailIds, $stSubject,$stNotificationContent);
				
				
			}
		}
		
		
		if (isset ( $obFaxScheduleResult->MessageResult->JobId->MRN )) {
			
			$arFieldMap ['easy_fax_mrn'] = $obFaxScheduleResult->MessageResult->JobId->MRN;
			$arFieldMap ['easy_fax_xdn'] = $obFaxScheduleResult->MessageResult->JobId->XDN;
			$arFieldMap ['proposal_schedule_status'] = 'scheduled';
		}
		
		
		if (isset ( $obEmailScheduleResult->MessageResult->JobId->MRN ) || isset ( $obFaxScheduleResult->MessageResult->JobId->MRN ))
		{
			
			$obEasyLink->do_log( " Proposal Scheduled:" . $focus->name );
			
			$obScheduleDate = new SugarDateTime($focus->date_time_delivery,new DateTimeZone('UTC'));
			$arFieldMap['name']= $sugar_config['dbconfig']['db_name'];
			$arFieldMap['date_schedule']=  $obScheduleDate->format('Y-m-d h:i:s') ;
			$arFieldMap['proposal_id']= $focus->id ;
			$arFieldMap['instance_folder_name']= $obEasyLink->instanceFolderName;
			$arFieldMap['instance_db_name']= $stDbName;
			$arFieldMap['schedule_queue_id'] = $queue_id;
			$arFieldMap['process_stat'] = '2';
			
			
			$columns =  implode (',',array_keys ( $arFieldMap ) );
			$values =  implode ( "','", array_values ( $arFieldMap ) );
			
			if($_REQUEST['error_type'] == 'fax_error'){
				
				$stScheduleSql = "UPDATE  oss_proposalqueue 
						SET easy_fax_mrn = '".$arFieldMap ['easy_fax_mrn']."', 
						easy_fax_xdn = '".$arFieldMap ['easy_fax_xdn']."', 
						proposal_schedule_status = 'scheduled'
						date_schedule = '".$arFieldMap['date_schedule']."'
						WHERE schedule_queue_id = '".$queue_id."' ";
				$cdb->query ( $stScheduleSql );
					
			}else{
				
				$stScheduleSql = "INSERT INTO oss_proposalqueue(id, date_entered,date_modified,$columns) VALUES(UUID(),NOW(),NOW(),'$values')";
				$cdb->query ( $stScheduleSql );
			}
			
			$GLOBALS['log']->fatal($stScheduleSql);
			
			$stUpdateScheduleQueueSQL = "UPDATE  oss_schedulequeue SET process_state = 'scheduled',date_modified= NOW(),date_completed = NOW() WHERE id='" . $queue_id ."'";
			$cdb->query ( $stUpdateScheduleQueueSQL );
		}
	
	
	
	
}

