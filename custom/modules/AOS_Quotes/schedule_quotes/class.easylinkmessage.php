<?php
/**
 * Class for interacting with EasyLink Message API 
 * @author ashutosh
 * 
 */

global $sugar_config;

require_once 'include/SugarDateTime.php';
require_once  $sugar_config['master_config_path'] ;//'/vol/certificate/master_config.php';
require_once 'custom/include/master_db/mysql.class.php';

define ( 'NS_CONFIG', $sugar_config ['EASY_LINK_REQUEST_RESPONSE_NAMESPACE'] );
define ( 'RECEIVER_KEY_CONFIG', $sugar_config ['EASY_LINK_RECEIVER_KEY'] );

class easyLinkMessage {
	
	const NS = NS_CONFIG;
	const RECEIVER_KEY = RECEIVER_KEY_CONFIG;
	var $wsdl = '';
	
	var $CLIENT;
	var $USER_ID;
	var $USER_PWD;
	var $ERROR_CODE;
	var $ERROR_MESSAGE;
	var $instanceFolderName ;
	/**
	 * Constructeur
	 *
	 * @param
	 *        	s: USERID
	 *        	PASSWORD
	 */
	function easyLinkMessage($userID = null, $userPwd = null) {
		global $sugar_config;
		$this->USER_ID = (trim ( $userID ) == '') ? $sugar_config ['EASY_LINK_USER_NAME'] : $userID;
		$this->USER_PWD = (trim ( $userPwd ) == '') ? $sugar_config ['EASY_LINK_USER_PASS'] : $userPwd;
		$this->wsdl = $sugar_config ['EASY_LINK_JOBSUBMIT_WSDL'];
		
		//set instance folder name / accountname
		$obAdmin = new Administration ();
		$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
		$this->instanceFolderName  = $arAdminData->settings['instance_account_name'];
		
	}
	
	/**
	 * Function to send jobSubmit Request for
	 * Fax
	 */
	public function jobSubmitFAX($arFaxParams) {
		
		$arJobSubmitParams = array (
				'JobOptions' => array (
						'BillingCode' => $arFaxParams ['BillingCode'],
						'CustomerReference' => $arFaxParams ['CustomerReference'],
						'FaxOptions' => array (
								'BannerFX' => array (
										'UseBannerFx' => 'no' 
								)
								 
						) 
				),
				'Destinations' => array (
						'Fax' => array (
								'Phone' => $arFaxParams ['Phone']  // '011911204275763'
						 
									)));
		
		// if startTime is set the fax has to be scheduled
		if (isset ( $arFaxParams ['StartTime'] )) {
			//move this date to 1hour back
			$obUTCDate = new SugarDateTime($arFaxParams['StartTime'], new DateTimeZone('UTC'));
			$obUTCDate->sub(new DateInterval('PT1H'));
			$arFaxParams ['StartTime'] = $obUTCDate->format ( 'Y-m-d' ) . 'T' . $obUTCDate->format ( 'H:i:sP' );
				
			$arJobSubmitParams ['JobOptions'] ['Delivery'] = array (
					'Schedule' => 'scheduled',
					'StartTime' => $arFaxParams ['StartTime'], // '2012-05-18T12:00:00-05:00',
					'StopTime' => $arFaxParams ['StopTime']  // '2012-05-18T15:00:00-05:00',
					);
		}
		
		$arJobSubmitParams ['Contents'] ['Part'] [] = array (
				'Treatment' => 'body' 
		) + array (
				'Document' => array (
						'DocType' => 'HTML',
						'DocData' => array (
								'_' => base64_encode ( html_entity_decode ( $arFaxParams ['Fax'] ['CoverBody'] ) ),
								'format' => 'base64' 
						) 
				) 
		);
		
		// add attachements if present
		if (count ( $arFaxParams ['Fax'] ['attachments'] ) > 0) {
			
			foreach ( $arFaxParams ['Fax'] ['attachments'] as $arDocDetail ) {
				// echo '<pre>' ;print_r($arDocDetail);
				if (trim ( $arDocDetail ['content'] ) != '') {
					$arAttachFilDetail = array (
							'Document' => array (
									'DocType' => $arDocDetail ['type'],
									'DocData' => array (
											'_' => base64_encode ( $arDocDetail ['content'] ),
											'format' => 'base64' 
									),
									'Filename' => $arDocDetail ['fileName'] 
							) 
					);
					
					$arJobSubmitParams ['Contents'] ['Part'] [] = array (
							'Treatment' => 'attachment' 
					) + $arAttachFilDetail;
				}
			}
		}
		$arJobSubmitParams ['Reports'] = array (
				'DeliveryReport' => array (
						'DeliveryReportType' => 'none' 
				) 
		) + array (
				'ProgressReport' => array (
						'ProgressReportType' => 'none' 
				) 
		);
		
		$params = array (
				'JobSubmitRequest' => array (
						'Message' => $arJobSubmitParams 
				) 
		);
		
		// echo '<pre>';print_r ( $params );die ();
		return $this->_soapCall ( 'JobSubmit', $params );
	}
	
	/**
	 * Function to send jobSubmit Request for
	 * Email
	 */
	public function jobSubmitEmail($arEmailDetails) {
		
		if (isset ( $arEmailDetails ['MessageId'] )) {
			
			$arJobSubmitParams ['MessageId'] = $arEmailDetails ['MessageId'];
		}
		$stMyEmailSubject = $arEmailDetails ['emailSubject'];
		
		$arDestinationEmail= array();
		/**
		 * Modifiction - Ability to send/Schedule emails to multiple email addresses
		 * Date : 10 March, 2014
		 */
		//check if there are comma seperated emails
		if (strpos($arEmailDetails ['email'] ['toEmail'],',') !== false) {						
			$arAllEmails = explode ( ',', $arEmailDetails ['email'] ['toEmail'] );
			//create nodes for multiple emails
			foreach ( $arAllEmails as $stDestinationEmail ) {
				
				$arDestinationEmailGroup[] = array (
						'Email' => $stDestinationEmail,
						'Subject' => ( string ) $stMyEmailSubject,
						'Eformat' => 'html' 
				);
			}
		}else{
			//
			$arDestinationEmailGroup = array (
			        'Email' => $arEmailDetails ['email'] ['toEmail'],
			        'Subject' => ( string ) $stMyEmailSubject,
			        'Eformat' => 'html'
			);
		}
		
		$arJobSubmitParams = array (
				'JobOptions' => array (
						'BillingCode' => $arEmailDetails ['BillingCode'],
						'CustomerReference' => $arEmailDetails ['CustomerReference'],
						'EnhancedEmailOptions' => array (
								'Subject' => ( string ) $stMyEmailSubject,
								//'FromAddress' => $arEmailDetails ['email'] ['FromAddress'],
								'ReplyTo' => $arEmailDetails ['email'] ['ReplyTo'],
								'FromDisplayName' => $arEmailDetails ['email'] ['FromDisplayName'],
								'HTMLOpenTracking' => 'bottom' 
						) 
				),
				'Destinations' => array (
						'Internet' =>  $arDestinationEmailGroup
				) 
		);
		
		// if this email need to schedule
		if (isset ( $arEmailDetails ['StartTime'] ) && trim ( $arEmailDetails ['StartTime'] ) != '') {
			//move this date to 1hour back			
			$obUTCDate = new SugarDateTime($arEmailDetails['StartTime'], new DateTimeZone('UTC'));			
			$obUTCDate->sub(new DateInterval('PT1H'));			
			$arEmailDetails ['StartTime'] = $obUTCDate->format ( 'Y-m-d' ) . 'T' . $obUTCDate->format ( 'H:i:sP' );
			
			
			$arJobSubmitParams ['JobOptions'] ['Delivery'] = array (
					'Schedule' => 'scheduled',
					'StartTime' => $arEmailDetails ['StartTime'], // 2012-05-22T12:00:00-05:00',
					'StopTime' => $arEmailDetails ['StopTime'] 
			);
		} else {
			$arJobSubmitParams ['JobOptions'] ['Delivery'] = array (
					'Schedule' => 'express' 
			);
		}
		/*
		 * Get this instance footer from config and set unsubscribe link
		 */
		$obAdministration = new Administration();
		$arAdminData = $obAdministration->retrieveSettings ( 'instance', true );		
		$arUnsubscribeUrlParams = array('email'=>$arEmailDetails ['email'] ['toEmail']
										,'account' =>$arAdminData->settings['instance_account_name']
										,'client'=>''	 
										);
		$stUnsubsCribeLink = base64_encode( json_encode($arUnsubscribeUrlParams));
		$stFooter = str_replace('UNSUBS_LINK',$stUnsubsCribeLink,$arAdminData->settings['instance_email_footer']);
		
		
		
		
		$arJobSubmitParams ['Contents'] ['Part'] [] = array (
				'Treatment' => 'body' 
		) + array (
				'Document' => array (
						'DocType' => 'HTML',
						'DocData' => array (
								'_' => base64_encode ( html_entity_decode ( cleanSpecialChars( $arEmailDetails ['email'] ['emailBody'].$stFooter) ) ),
								'format' => 'base64' 
						) 
				) 
		);
		
		// add attachements if present
		if (count ( $arEmailDetails ['email'] ['attachments'] ) > 0) {
			
			foreach ( $arEmailDetails ['email'] ['attachments'] as $arDocDetail ) {
				if (trim ( $arDocDetail ['content'] ) != '') {
					$arAttachFilDetail = array (
							'Document' => array (
									'DocType' => $arDocDetail ['type'],
									'DocData' => array (
											'_' => base64_encode ( $arDocDetail ['content'] ),
											'format' => 'base64' 
									),
									'Filename' => $arDocDetail ['fileName'] 
							) 
					);
					
					$arJobSubmitParams ['Contents'] ['Part'] [] = array (
							'Treatment' => 'attachment' 
					) + $arAttachFilDetail;
				}
			}
		}
		
		$arJobSubmitParams ['Reports'] = array (
				'FriendReport' => array (
						'FriendReportType' => 'detail',
						'ReportAddress' => array (
								'Internet' => array (
										'Email' => 'mmoyers@mail.thebluebook.com' 
								) 
						) 
				),
				'DeliveryReport' => array (
						'DeliveryReportType' => 'none' 
				) 
		) + array (
				'ProgressReport' => array (
						'ProgressReportType' => 'none' 
				) 
		);
		
		$params = array (
				'JobSubmitRequest' => array (
						'Message' => $arJobSubmitParams 
				) 
		);
		
		return $this->_soapCall ( 'JobSubmit', $params );
	}
	
	/**
	 * Function to get Job Status
	 *
	 * @param
	 *        	s : Mix array ('XDN' => XDN NAME ,MRN => MESSAGE REQUEST
	 *        	NUMBER)
	 */
	public function proposalDeliveryStatus($arJobIDHost) {
		
		global $sugar_config;
		
		$this->wsdl = $sugar_config ['EASY_LINK_JOB_DELOVERY_STATUS_WSDL'];
		$arJobDeliveryRequest = array (
				'JobDeliveryStatusRequest' => array (
						'JobId' => $arJobIDHost,
						'StatusOptions' => array(								
								'IncludeExtendedDeliveryData' => array(
										'ExtSegmentFilter' => array (
												'ExtPropFilter' => array(
														'name'=>'progresspoint') 
										)
								 	),
								'AllDeliveryGroups' =>true,
								'AllDeliveryGroupsSpecified' => true
								) 
				) 
		);
		
		return $this->_soapCall ( 'JobDeliveryStatus', $arJobDeliveryRequest );
	}
	/**
	 * To Cancel a Scheduled Fax
	 */
	public function JobCancel($arJobIdDetail) {
		
		$arJobCancelData = array (
				'JobCancelRequest' => array (
						'CancelItem' => array (
								'JobId' => array (
										'XDN' => $arJobIdDetail ['XDN'],
										'MRN' => $arJobIdDetail ['MRN'] 
								) 
						) 
				) 
		);
		
		return $this->_soapCall ( 'JobCancel', $arJobCancelData );
	}
	/**
	 * Set SOAP header
	 */
	private function _getSoapHeader($userID, $pwd) {
		
		$soapHeader = new SoapHeader ( self::NS, 'Request', array (
				"ReceiverKey" => self::RECEIVER_KEY,
				"Authentication" => array (
						"XDDSAuth" => array (
								"RequesterID" => $userID,
								"Password" => $pwd 
						) 
				) 
		) );
		return $soapHeader;
	}
	/**
	 * Method to call API methods
	 *
	 * @param methodName $method        	
	 * @param mix $params        	
	 * @throws SoapFault
	 * @return void mixed
	 */
	private function _soapCall($method, $params = array()) {
		try {
			$this->CLIENT = new SoapClient ( $this->wsdl, array (
					'location' => self::RECEIVER_KEY,
					'trace' => true 
			) );
			
			$result = $this->CLIENT->__soapCall ( $method, $params, null, $this->_getSoapHeader ( $this->USER_ID, $this->USER_PWD ) );
			
			return $result;
		} catch ( SoapFault $fault ) {
			
			/**
			 * var_dump($this->CLIENT->__getLastRequestHeaders());
			 * var_dump($this->CLIENT->__getLastRequest());
			 * var_dump($this->CLIENT->__getLastResponseHeaders());
			 * var_dump($this->CLIENT->__getLastResponse());
			 */
			throw $fault;
			return;
		}
	}
	
	/**
	 * function to get DB object for central Database
	 */
	function __getCentralDB() {		
			
		// Connect to Central Schedule DB
		$this->cdb = new MasterMySQL ( MASTER_HOST, MASTER_USER, MASTER_PASS, MASTER_DB, true );
		return $this->cdb;
	}
	
	/**
	 *
	 *
	 *
	 *
	 * Cancel a scheduled proposal from Pending queue, Cancel queue,
	 * schedule queue
	 *
	 * @param unknown_type $stProposalId        	
	 * @return boolean
	 */
	function cancelScheduledProposal($obProposal) {
		
		global $sugar_config;
		$bReturn = false;
		// get connection to central Schedule DB
		$this->__getCentralDB ();
		
		$stDbName = $sugar_config ['dbconfig'] ['db_name'];
		
		// check if the proposal in pending queue
		$stInPendingQueueSQL = 'SELECT id
									,instance_db_name
									,proposal_id
									,easy_email_mrn
									,easy_email_xdn
									,easy_fax_mrn
									,easy_fax_xdn
									,proposal_schedule_status
								FROM oss_proposalqueue
								WHERE instance_db_name="' . $stDbName . '"
								AND proposal_id ="' . $obProposal->id . '"
								AND proposal_schedule_status IN ("inprogress","scheduled")';
		
		$rsPending = $this->cdb->query ( $stInPendingQueueSQL );
		// Total Count row count
		$iTotalInPendingQueue = $this->cdb->num_rows ( $rsPending );
		
		// check if the porposal in cancel queue
		$stInCancelQueueSQL = 'SELECT id 
							    	,  instance_folder 
							    	,  instance_db_name 
							    	,  proposal_id 
							    	,  proposal_delivery_date 
							    	,  easy_email_mrn 
							    	,  easy_email_xdn 
							    	,  easy_fax_mrn 
							    	,  easy_fax_xdn 
							    	,  process_state 
							    	, schedule_after_cancel
						    	FROM  oss_cancelqueue
						    	WHERE instance_db_name="' . $stDbName . '"
						    	AND proposal_id ="' . $obProposal->id . '"
						    	AND process_state IN ("pending","inprogress")';
		
		$rsCancel = $this->cdb->query ( $stInPendingQueueSQL );
		// Cancel queue row count
		$iCancelQueueCount = $this->cdb->num_rows ( $rsCancel );
		
		// check if the porposal in schedule queue
		$stInScheduleQueueSQL = 'SELECT id
						    	,  instance_folder
						    	,  instance_db_name
						    	,  proposal_id
						    	,  proposal_delivery_date
						    	,  process_state						    	
						    	FROM  oss_schedulequeue
						    	WHERE instance_db_name="' . $stDbName . '"
						    	AND proposal_id ="' . $obProposal->id . '"
						    	AND process_state IN ("pending","inprogress")';
		
		$rsScheduleQueue = $this->cdb->query ( $stInScheduleQueueSQL );
		// Cancel queue row count
		$iScheduleQueueCount = $this->cdb->num_rows ( $rsScheduleQueue );
		
		// if proposal exists in pending Queue
		if ($iTotalInPendingQueue > 0) {
			
			// ###################################
			// # PENDING QUEUE ####
			// ###################################
			
			// get Scheduled proposal details
			$arPending = $this->cdb->fetch_assoc ( $rsPending );
			
			if ($arPending ['proposal_schedule_status'] == 'inprogress') {
				
				// if proposal is saved as in progress then cancel the proposal
				$stCancelPendingQueue = 'UPDATE oss_proposalqueue SET  proposal_schedule_status = "cancelled",date_cancelled = NOW() 
    									WHERE id = "' . $arPending ['id'] . '"';
				$this->cdb->query ( $stCancelPendingQueue );
				
				// set return value
				$bReturn = true;
			
			} elseif ($arPending ['proposal_schedule_status'] == 'scheduled') {
				
				// if proposal is scheduled at Easylink send cancel request
				if (trim ( $arPending ['easy_email_mrn'] ) != '') {
					// cancel this email schedule
					try {
						
						 $arJobData = array (
								'MRN' => $arPending ['easy_email_mrn'],
								'XDN' => $arPending ['easy_email_xdn'] 
						); 
						
						$this->wsdl = $sugar_config ['EASY_LINK_JOBCANCEL_WSDL'];
						$obResponse = $this->JobCancel ( $arJobData );
						
						// if status of cancel request is ok
						if (isset ( $obResponse->Status->StatusMessage ) && strtolower ( $obResponse->Status->StatusMessage ) == 'ok') {
							// mark this proposal cancelled
							$stCancelPendingQueue = 'UPDATE oss_proposalqueue SET  proposal_schedule_status = "cancelled",date_cancelled = NOW()
	    					WHERE id = "' . $arPending ['id'] . '"';
							$this->cdb->query ( $stCancelPendingQueue );
							
							// set return value
							$bReturn = true;
						}
					
					} catch ( SoapFault $obEmailFault ) {
						
						// GOT an error while canceling the scheduled email
						// GOT AN ERROR SEND NOTIFICATION EMAIL
						 $obErrorTemplate = $this->getEmailTemplate($obProposal);
						
						 $stSubject = $obErrorTemplate->subject;
						 $stNotificationContent = $obErrorTemplate->body_html;					 				 
						 
						 $obUser = new User();
						 $obUser->retrieve($obProposal->assigned_user_id);					 
						
						 $arEmailIds[] =  $obUser->email1 ;
						 $this->sendNotificationEmail($arEmailIds, $stSubject,$stNotificationContent);
						
						// log this error
						$this->do_log ( " SoapFault Error Message for Email Cancel request :" . $obEmailFault->getMessage () . ' [ERROR CODE = ' . $obEmailFault->getCode () . ' ] ','fatal' );
						// caught an error
						$bReturn = false;
					}
				
				}
				
				// if any fax already scheduled at Easy Link
				if (trim ( $arPending ['easy_fax_mrn'] ) != '') {
					// cancel this fax schedule
					try {
						
						$arJobData = array (
								'MRN' => $arPending ['easy_fax_mrn'],
								'XDN' => $arPending ['easy_fax_xdn'] 
						);
						$this->wsdl = $sugar_config ['EASY_LINK_JOBCANCEL_WSDL'];
						$obFaxResponse = $this->JobCancel ( $arJobData );
						
						if (isset ( $obFaxResponse->Status->StatusMessage ) && strtolower ( $obFaxResponse->Status->StatusMessage ) == 'ok') {
							// set return value
							$bReturn = true;
						} 
						
					} catch ( SoapFault $obFaxFault ) {
						// GOT an error while canceling the scheduled email
						// notify
						// GOT AN ERROR SEND NOTIFICATION EMAIL
						 $obErrorTemplate = $this->getEmailTemplate($obProposal);
						 
						 $stSubject = $obErrorTemplate->subject;
						 $stNotificationContent = $obErrorTemplate->body_html;					 
						 				 
						 
						  $obUser = new User();
						  $obUser->retrieve($obProposal->assigned_user_id);					 
						
						  $arEmailIds[] =  $obUser->email1 ;
						  $this->sendNotificationEmail($arEmailIds, $stSubject,$stNotificationContent);
						
						// log this error
						$this->do_log ( " SoapFault Error Message for Fax Cancel request:" . $obFaxFault->getMessage () . ' [ERROR CODE = ' . $obFaxFault->getCode () . ' ] ' ,'fatal');
						// caught an error
						$bReturn = false;
					}
				}
			
			}
			
			// ##########################################
			// # END OF PENDING QUEUE ####
			// ##########################################
		} elseif ($iCancelQueueCount > 0) {
			
			// ##########################################
			// # CANCEL QUEUE ####
			// ##########################################
			// if proposal exists in cancel queue then mark this as cancelled
			$arCancelQueue = $this->cdb->fetch_assoc ( $rsCancel );
			
			// set delivery date[delivery datetime] and schedule after
			// cancel[skip_delivery_method] flag
			$stUpdateCancelQueueSQL = 'UPDATE   oss_cancelqueue SET proposal_delivery_date = "' . $obProposal->date_time_delivery . '",skip_delivery_method= "' . $obProposal->skip_delivery_method . '" WHERE id="' . $arCancelQueue ['id'] . '"';
			
			$this->cdb->query ( $stUpdateCancelQueueSQL );
			
			$bReturn = true;
			
			// ##########################################
			// # END OF CANCEL QUEUE ####
			// ##########################################
		
		} elseif ($iScheduleQueueCount > 0) {
			
			// ##########################################
			// # SCHEDULE QUEUE ####
			// ##########################################
			$arScheduleQueue = $this->cdb->fetch_assoc ( $rsScheduleQueue );
			
			// set status as cancelled
			$stUpdateScheduleQueueSQL = 'UPDATE  oss_schedulequeue SET process_state = "cancelled"
	    								,date_cancelled= NOW() WHERE id="' . $arScheduleQueue ['id'] . '"';
			
			$this->cdb->query ( $stUpdateScheduleQueueSQL );
			
			$bReturn = true;
			// ##########################################
			// # END OF SCHEDULE QUEUE ####
			// ##########################################
		
		}
		
		return $bReturn;
	}
	
	/** 
	 * GET EMAIL TEMPLATE
	 * 
	 */	
	function getEmailTemplate($obProposal){
			
		
		
		//get proposal error template
		$obEmailTemplate = new EmailTemplate();
		
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Error Notification'
		) );
		
		$arEmailTemplateBody = $obEmailTemplate->parse_template_bean(array(
											'subject'=>$obEmailTemplate->subject,
                                        'body_html'=>$obEmailTemplate->body_html,
                                        'body'=>$obEmailTemplate->body				
											), 'AOS_Quotes', $obProposal);
		
		//set instance name
		$obEmailTemplate->subject =  from_html(str_replace('_INSTANCE_', $this->instanceFolderName, $arEmailTemplateBody['subject']));
		$stEmailMessage = from_html($arEmailTemplateBody['body_html']);
		$stEmailMessage = str_replace('_INSTANCE_', $this->instanceFolderName, $stEmailMessage);
		
		
		
		$obEmailTemplate->body_html = $stEmailMessage;
		
		return $obEmailTemplate;
	}
	/**
	 * Function to send notification emails
	 *
	 * @param array $arEmailIds        	
	 * @param string $stSubject        	
	 * @param stfing $stEmailHTML        	
	 */
	function sendNotificationEmail($arEmailIds, $stSubject, $stEmailHTML) {
		
			
		//send details to admin
		$stNotificationEmail = PROPOSAL_SCHEDULE_ERROR_NOTIFICATION_EMAIL_ADDRESS;
		
		// email related code .
		$htmlBody = $stEmailHTML;
		$body = strip_tags ( $stEmailHTML );
		
		require_once ('include/SugarPHPMailer.php');
		
		
		$emailObj = new Email ();
		
		$defaults = $emailObj->getSystemDefaultEmail ();
		
		$mail = new SugarPHPMailer ();
		
		$mail->setMailerForSystem ();
		$mail->From = $defaults ['email'];
		$mail->FromName = $defaults ['name'];
		$mail->ClearAllRecipients ();
		$mail->ClearReplyTos ();
		$mail->Subject = from_html ( $stSubject );
		
		$mail->Body_html = from_html ( $stEmailHTML );
		$mail->Body = from_html ( $stEmailHTML );
		$mail->IsHTML ( true );
		
		$mail->prepForOutbound ();
		$hasRecipients = false;
		
		//this email should go to a common email
		$mail->AddBCC($stNotificationEmail);
		
		foreach($arEmailIds as $itemail){
			$mail->AddAddress ( $itemail );
		}
		
		$hasRecipients = true;
	
		$success = false;
		if ($hasRecipients) {
			$success = @$mail->Send ();
		
		}
	}
	
	/**
	 * Function to add a proposal to schedule queue
	 * the schedule queue will hit easylink frequent
	 * intervals then tries to schedule this job
	 *
	 * @param
	 *        	AOS_Quotes Object $obProposal
	 * @param
	 *        	cancel queue fields array $arParams
	 */
	function addToScheduleQueue($obProposal){
		
		global $sugar_config;
		// get central DB connection
		$this->__getCentralDB ();
		
		// check if this proposal already in cancel queue
		$stChcekSQL = 'SELECT *
		FROM oss_schedulequeue
		WHERE process_state IN ("pending","inprogress")
		AND proposal_id = "' . $obProposal->id . '"
		AND instance_db_name ="' . $sugar_config ['dbconfig'] ['db_name'] . '"
		';
		
		$obScheduleDeliveryDate = new SugarDateTime($obProposal->date_time_delivery,new DateTimeZone('UTC'));
		
		$arScheduleQueueData = array (
				'name' => $sugar_config ['dbconfig'] ['db_name'],
				'instance_folder' => $this->instanceFolderName,
				'instance_db_name' => $sugar_config ['dbconfig'] ['db_name'],
				'proposal_id' => $obProposal->id,
				'proposal_delivery_date' => $obScheduleDeliveryDate->format('Y-m-d h:i:s'),
				
		) ;
		
		$rsScheduleData = $this->cdb->query($stChcekSQL);
		$iScheduleQueueCount = $this->cdb->num_rows($rsScheduleData);
		
		if($iScheduleQueueCount >0 ){
			
			$arScheduleQueueResult = $this->cdb->fetch_assoc($rsScheduleData);
			
			$stUpdateFields = '';
			$stSeperator = ' ';
			foreach ( $arScheduleQueueData as $stFieldName => $stFieldValue ) {
			
				$stUpdateFields .= $stSeperator . $stFieldName . '= "' . $stFieldValue . '"';
				$stSeperator = ', ';
			}
			
			$stAddScheduleQueue = "UPDATE oss_schedulequeue SET ".$stUpdateFields." WHERE id = '". $arScheduleQueueResult ['id'] . "'";
			
		} else {
			$stAddScheduleQueue = "INSERT INTO oss_schedulequeue ( id,date_entered,date_modified," . implode ( ',', array_keys ( $arScheduleQueueData ) ) . ")
				VALUES (UUID(),NOW(),NOW(),'" . implode ( "','", array_values ( $arScheduleQueueData ) ) . "')";
			
		}
		
		$this->do_log(' Add/update Schedule QUEUE : '.$stAddScheduleQueue);
		
		//add to schedule queue
		$this->cdb->query($stAddScheduleQueue);
	}
	
	/**
	 * Function to add a proposal to cancel queue
	 * the cancel queue will hit easylink frequent
	 * intervals then tries to cancel this job
	 *
	 * @param
	 *        	AOS_Quotes Object $obProposal
	 * @param
	 *        	cancel queue fields array $arParams
	 */
	function addTocancelQueue($obProposal, $arParams) {
		global $sugar_config;
		// get central DB connection
		$this->__getCentralDB ();
		
		// check if this proposal already in cancel queue
		$stChcekSQL = 'SELECT * 
    				   FROM  oss_cancelqueue 
    				   WHERE process_state IN ("pending","inprogress") 
    						 AND proposal_id = "' . $obProposal->id . '" 
    						 AND instance_db_name ="' . $sugar_config ['dbconfig'] ['db_name'] . '"
    						 ';
		
		$rsCancelQueue = $this->cdb->query ( $stChcekSQL );
		$iTotalInCancelQueue = $this->cdb->num_rows ( $rsCancelQueue );
		
		$obScheduleDeliveryDate = new SugarDateTime($obProposal->date_time_delivery,new DateTimeZone('UTC'));
		
		
		// set indices for new row
		$arCancelQueueData = array (
				'name' => $sugar_config ['dbconfig'] ['db_name'],
				'instance_folder' => $this->instanceFolderName,
				'instance_db_name' => $sugar_config ['dbconfig'] ['db_name'],
				'proposal_id' => $obProposal->id,
				'proposal_delivery_date' => $obScheduleDeliveryDate->format('Y-m-d h:i:s'),
				'schedule_after_cancel' => $obProposal->skip_delivery_method 
		) + $arParams;
		
		// if record already exists update the delivery date and
		if ($iTotalInCancelQueue > 0) {
			$arCancelQueueResult = $this->cdb->fetch_assoc ( $rsCancelQueue );
			$stUpdateFields = '';
			$stSeperator = ' ';
			foreach ( $arCancelQueueData as $stFieldName => $stFieldValue ) {
				
				$stUpdateFields .= $stSeperator . $stFieldName . '= "' . $stFieldValue . '"';
				$stSeperator = ', ';
			}
			
			$stAddCancelQueue = "UPDATE  oss_cancelqueue SET $stUpdateFields WHERE id = '" . $arCancelQueueResult ['id'] . "'";
		
		} else {
			$stAddCancelQueue = "INSERT INTO  oss_cancelqueue (id,date_entered,date_modified," . implode ( ',', array_keys ( $arCancelQueueData ) ) . ")
    							 VALUES (UUID(),NOW(),NOW(),'" . implode ( "','", array_values ( $arCancelQueueData ) ) . "')";
		
		}
		
		$this->cdb->query ( $stAddCancelQueue );
	
	}
	/**
	 * Function to Schedule a proposal at Easylink
	 *
	 * @param
	 *        	AOS_Quotes Object $focus
	 */
	function scheduleProposalAtEasyLink($focus) {
		
		// schedule this proposal now
		// ###############################################
		// # RETRIEVE PROPOSAL DETAILS #####
		// ###############################################
		require_once ('include/Sugarpdf/SugarpdfFactory.php');
		include_once "custom/modules/EmailTemplates/CustomEmailTemplate.php";
		$this->__getCentralDB();
		global $sugar_config;
		
		$bReturn = true;
		
		$obUser = new User();
		$obUser->disable_row_level_security =1;
		$obUser->retrieve($focus->assigned_user_id);
		
		$this->do_log('### GOT PROPOSAL ['.$focus->id.'] in method scheduleProposalAtEasyLink .');
		
		$stDbName = $sugar_config ['dbconfig'] ['db_name'];
		
		// create schedule/stop date
		$obScheduleDate = new SugarDateTime ( $focus->date_time_delivery, new DateTimeZone ( 'UTC' ) );
		
		$obScheduleStopDate = new SugarDateTime ( $focus->date_time_delivery, new DateTimeZone ( 'UTC' ) );
		$obScheduleStopDate->modify ( '+1 day' );
		
		// $obScheduleDate->format('Y-m-d').'T'.$obScheduleDate->format('H:i:sP').'<br/>'.$obScheduleStopDate;
		
		$dtScheduledGmtTime = $obScheduleDate->format ( 'Y-m-d' ) . 'T' . $obScheduleDate->format ( 'H:i:sP' );
		$dtScheduledStopGmtTime = $obScheduleStopDate->format ( 'Y-m-d' ) . 'T' . $obScheduleDate->format ( 'H:i:sP' );
		
		$object_map = array ();
		// Get proposal template
		$obEmailTemplate = new CustomEmailTemplate ();
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Template' 
		) );
		
		$arHTMLEmailData = $obEmailTemplate->parse_template_bean ( array (
				'subject' => $obEmailTemplate->subject,
				'body_html' => '<html><title></title><head></head><body>' . html_entity_decode ( $obEmailTemplate->body_html ) . '</body></html>',
				'body' => $obEmailTemplate->body 
		), 'AOS_Quotes', $focus );
		
		//get fax template
		$obEmailTemplate->retrieve_by_string_fields ( array (
				'name' => 'Proposal Fax Template'
		) );
		
		
		$arHTMLFaxCoverData = $obEmailTemplate->parse_template_bean ( array (
				'subject' => $obEmailTemplate->subject,
				'body_html' => '<html><title></title><head></head><body>'.html_entity_decode($obEmailTemplate->body_html).'</body></html>',
				'body' => $obEmailTemplate->body
		), 'AOS_Quotes', $focus );
		
		//check if there are lineitems
		$product = new Product();
			
		$where_lineitems = " products.quote_id='".$focus->id
		."' AND  (products.product_type='line_items'	OR products.product_type='inclusions'
		OR products.product_type='exclusions' OR products.product_type='alternates') ";
		$line_items = $product->get_full_list('',$where_lineitems);
		
		if(count($line_items) > 0){
			// set Proposal PDF name
			
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
			// get Type of document
			$stType = getDocumentType ( $obDocument->last_rev_mime_type );
			
			$arAttachedFiles [] = array (
					'fileName' => $obDocument->filename,
					'content' => file_get_contents ( $GLOBALS ['sugar_config'] ['upload_dir'] . $obDocument->document_revision_id ),
					'type' => $stType 
			);
		}
		
		// ###################################################
		// # RETRIEVE PROPOSAL DETAILS ENDS #####
		// ###################################################
		//hirak : date : 16-10-2012
		// if email need to sent schedule an email
		if ($focus->proposal_delivery_method == 'E' || $focus->proposal_delivery_method == 'EF') {
			
			$this->do_log('PROPOSAL ['.$focus->id.'] prepare schedule email request param.');
			
			$arEasyLinkJobSubmitEmailParams = array ();
			$arEasyLinkJobSubmitEmailParams = array (
					'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $focus->id . '-' . strtotime ( date ( 'Y-m-d h:i:s' ) ),
					'BillingCode' => 'Proposal Email ' . $focus->quote_num,
					'CustomerReference' => $this->instanceFolderName,
					'emailSubject' => '' . $arHTMLEmailData ['subject'] . ' ' . $focus->name,
					'StartTime' => $dtScheduledGmtTime,
					'StopTime' => $dtScheduledStopGmtTime,
					'email' => array (
							'toEmail' => $focus->contact_email,
							'attachments' => $arAttachedFiles,
							'emailBody' => $arHTMLEmailData ['body_html'],
							//'FromAddress' => $obUser->email1,
							'ReplyTo' => $obUser->email1,							
							'FromDisplayName' => $obUser->company_name
					) 
			);
			
			try {
				
				$this->do_log('PROPOSAL ['.$focus->id.'] sending schedule email request .');
				
				$this->wsdl = $sugar_config ['EASY_LINK_JOBSUBMIT_WSDL'];
				
				$obEmailScheduleResult = $this->jobSubmitEmail ( $arEasyLinkJobSubmitEmailParams );
				
				$bReturn = true;
			
			} catch ( SoapFault $obFault ) {
				
				
				 $this->do_log('PROPOSAL ['.$focus->id.'] Schedule email request failed.','error');
				 
				 //add this to schedule queue
				 $this->addToScheduleQueue($focus);
				
				 $bReturn = false;
				
				//set status as error for pending queue/oss_proposalqueue 
				$arFieldMap ['proposal_schedule_status'] = 'error';
				
				// GOT AN ERROR SEND NOTIFICATION EMAIL
				 $obErrorTemplate = $this->getEmailTemplate($focus);				 
				 
				 $stSubject = $obErrorTemplate->subject;
				 $stNotificationContent = $obErrorTemplate->body_html;				 				 
				 
				 
				 $arEmailIds[] =  $obUser->email1 ;
				 $this->sendNotificationEmail($arEmailIds, $stSubject,$stNotificationContent);
				
				 // LOG THIS ERROR
				 $this->do_log ( " SoapFault Error Message for Email Schedule :" . $obFault->getMessage () . ' [ERROR CODE = ' . $obFault->getCode () . ' ] ' ,'fatal');
				
				
			}
		}
		
		// if a Email has been scheduled set params for oss_proposalqueue table
		if (isset ( $obEmailScheduleResult->MessageResult->JobId->MRN )) {
				
			$arFieldMap ['easy_email_mrn'] = $obEmailScheduleResult->MessageResult->JobId->MRN;
			$arFieldMap ['easy_email_xdn'] = $obEmailScheduleResult->MessageResult->JobId->XDN;
			$arFieldMap ['proposal_schedule_status'] = 'scheduled';
		}
		
		// if fax need to sentsched
		if ($bReturn && ($focus->proposal_delivery_method == 'F' || $focus->proposal_delivery_method == 'EF')) {
			
			$this->do_log('PROPOSAL ['.$focus->id.'] prepare schedule fax request params.');
			
			// create JobSubmit Params
			$arEasyLinkJobSubmitFaxParams = array (
					'MessageId' => 'BLUE_POPOSAL_VERIFY-' . $focus->id . '-' . strtotime ( date ( 'Y-m-d h:i:s' ) ),
					'BillingCode' => 'Proposal fax ' . $focus->quote_num,
					'Phone' => $focus->contact_fax,
					'CustomerReference' => $this->instanceFolderName,
					'StartTime' => $dtScheduledGmtTime,
					'StopTime' => $dtScheduledStopGmtTime,
					'Fax' => array (
							'Phone' => $focus->contact_fax,
							'attachments' => $arAttachedFiles,
							'CoverBody' => $arHTMLFaxCoverData ['body_html'] 
					) 
			);
			
			 
			
			try {
				
				$this->do_log('PROPOSAL ['.$focus->id.'] sending schedule fax request .');
				
				$this->wsdl = $sugar_config ['EASY_LINK_JOBSUBMIT_WSDL'];
				$obFaxScheduleResult = $this->jobSubmitFAX ( $arEasyLinkJobSubmitFaxParams );
				$bReturn = true;
				
				
			} catch ( SoapFault $obFault ) {
				
				$this->do_log('PROPOSAL ['.$focus->id.'] schedule fax request failed.','error');
				
				$arScheduleError ['name'] = $sugar_config['dbconfig']['db_name'] ;
				$arScheduleError ['instance_db_name'] = $sugar_config['dbconfig']['db_name'];
				$arScheduleError ['proposal_id'] = $focus->id;
				$arScheduleError ['instance_folder']= $this->instanceFolderName;
				$arScheduleError ['proposal_delivery_date']= $dtScheduledGmtTime;
				$arScheduleError ['process_state'] = 'pending';
				
				//set status as fax_error for proposal queue
				$arFieldMap ['proposal_schedule_status'] = 'fax_error';
				
				//if email is sent but fax cought an error
				if (isset ( $obEmailScheduleResult->MessageResult->JobId->MRN )) {
				
					$arScheduleError ['process_state'] ='fax_error';
					
				}else{
				
					$arScheduleError ['process_state']  = 'pending';					
				
				}
				$columns =  implode (',',array_keys ( $arScheduleError ) );
				$values =  implode ( "','", array_values ( $arScheduleError ) );
				
				//add this proposal to schedule queue
				$stUpdateScheduleQueueSQL = "INSERT INTO oss_schedulequeue (id,date_entered,date_modified,$columns) VALUES  (
				UUID(),NOW(),NOW(),'{$values}') ";
				//$GLOBALS['log']->fatal($stUpdateScheduleQueueSQL);
				$this->cdb->query ( $stUpdateScheduleQueueSQL );
				
				
				
				$bReturn = false;
				// GOT AN ERROR SEND NOTIFICATION EMAIL
				$obErrorTemplate = $this->getEmailTemplate($focus);
				
				
				 $stSubject = $obErrorTemplate->subject;
				 $stNotificationContent = $obErrorTemplate->body_html;
				 $obUser = new User();
				 $obUser->retrieve($focus->assigned_user_id);
				 
				 $arEmailIds[] =  $obUser->email1 ;
				 
				 $this->sendNotificationEmail($arEmailIds, $stSubject,$stNotificationContent);
				
				// LOG THIS ERROR
				$this->do_log( " SoapFault Error Message for fax Schedule :" . $obFault->getMessage () . ' [ERROR CODE = ' . $obFault->getCode () . ' ] ','fatal' );
			
			}
		}
		
		
		
		
		// if a Fax has been scheduled set params for oss_proposalqueue table
		if (isset ( $obFaxScheduleResult->MessageResult->JobId->MRN )) {
			
			$arFieldMap ['easy_fax_mrn'] = $obFaxScheduleResult->MessageResult->JobId->MRN;
			$arFieldMap ['easy_fax_xdn'] = $obFaxScheduleResult->MessageResult->JobId->XDN;
			$arFieldMap ['proposal_schedule_status'] = 'scheduled';
			
			
		}
		
		$arFieldMap ['name'] = $stDbName;
		$arFieldMap ['instance_db_name'] = $stDbName;
		$arFieldMap['instance_folder_name']= $this->instanceFolderName;
		$arFieldMap ['proposal_id'] = $focus->id;
		$arFieldMap ['date_schedule'] = $focus->date_time_delivery;
		//since this proposal is already scheduled set status 2 so that update status cron	  	
		$arFieldMap ['process_stat'] = '2';
		
		
		// this will be a new proposal schedule request
		$stScheduleSql = 'INSERT INTO oss_proposalqueue(id, date_entered,date_modified, ' . implode ( ",", array_keys ( $arFieldMap ) ) . ')
    				VALUES(UUID(),NOW(),NOW(),"' . implode ( '","', array_values ( $arFieldMap ) ) . '" )';
		
		$this->do_log(' Save scheduled proposal details :: '.$stScheduleSql);
		
		// add new row for schedule
		$this->cdb->query ( $stScheduleSql );		
		
		
		return $bReturn ;	
	}
	
	
	/**
	 * function to log events
	 * 
	 * @param string $str
	 * @param string $mode error/fatal/log 
	 */
	function do_log($str,$mode='log')
	{ 
		//add to sugar log
		/*$GLOBALS['log']->fatal($str);
		switch ($mode){
			case 'error':
				$stLogMode = '[ERROR] ';
			break;
			case 'fatal':
				$stLogMode = '[FATAL] ';
			break;
				
			default:
				$stLogMode = '[INFO] ';
			break;			
		}		

		
		$str = "\n [".date('Y-m-d h:i:s').'] '.$stLogMode .$str;		
		$quoteLogPath = dirname(__FILE__).'/logs/';		
		error_log($str,3,$quoteLogPath.'quote'.date('d_m_Y').'.log');
		*/
		
	}

}

?>
