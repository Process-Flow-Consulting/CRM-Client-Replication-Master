<?php
if (! defined('sugarEntry') || ! sugarEntry)
    die('Not A Valid Entry Point');
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
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
 * *******************************************************************************
 *
 * Description: This file is used to override the default Meta-data EditView
 * behavior
 * to provide customization specific to the Contacts module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ******************************************************************************
 */
global $sugar_config;
require_once $sugar_config['master_config_path']; // '/vol/certificate/master_config.php';
require_once 'custom/include/master_db/mysql.class.php';
require_once 'custom/modules/AOS_Quotes/schedule_quotes/class.easylinkmessage.php';
require_once 'include/SugarDateTime.php';
require_once 'custom/include/common_functions.php';

/**
 * Update Proposal Scheduler method
 * @modified by : Ashutosh
 * @date : 5 May 2014
 * 
 * @return boolean true
 */
function updateProposals ()
{
    global $sugar_config, $timedate, $db;
    
    // failed status code Array
    $arFaildStatusCodes = array(
            '4',
            '5',
            '8'
    );
    $arInProgressCodes = array(
            '1',
            '2',
            '3'
    );
    
    // get instance account number
    $obAdmin = new Administration();
    $arAdminData = $obAdmin->retrieveSettings('instance', true);
    
    // get EasyLink API Object
    $obEasyLink = new easyLinkMessage();
    $obEasyLink->do_log(
            '<----- UPDATE CRON STARTED ' . date('Y-m-d H:i:s') . '------>');
    
    // get DB Object
    $cdb = $obEasyLink->__getCentralDB();
    
    // ####################################################
    // ## UPDATE STATUS OF JOBS ####
    // ####################################################
    // reset stat to 2 for pending proposals
    $updatePendingQuery = "UPDATE oss_proposalqueue SET proposal_schedule_status = 'scheduled', process_stat='2'" .
             " WHERE process_stat='3' AND TIME_TO_SEC(TIMEDIFF(NOW(),last_retry_date) ) >= 3600 ";
    $cdb->query($updatePendingQuery);
    
    /**
     * Run Update CRON through Customer Instance instead of Master Instance
     * Modified by Satish Gupta on 18 Feb 2013
     */
    
    // Get ALL Proposals for this instance that is Scheduled or Delivered.
    $sqlGetProposals = "SELECT * FROM oss_proposalqueue " .
             " WHERE instance_folder_name ='1064484'";
    $obEasyLink->do_log($sqlGetProposals);
    $queryGetProposals = $cdb->query($sqlGetProposals);
    $iTotalRecordCount = $cdb->num_rows($queryGetProposals);
    $obEasyLink->do_log('COUNT ---> ' . $iTotalRecordCount);
    
    if ($iTotalRecordCount == 0) {
        $obEasyLink->do_log(
                '<------No Proposal to Update Status from Proposal Queue.------>');
        return true;
    }
    
    while ($arResult = $cdb->fetch_assoc($queryGetProposals)) {
        // flag to change sales stage of Client Opportunity
        $bChangeSalesStage = true;
        
       /*  $updateQuery = "UPDATE oss_proposalqueue SET update_status_attempts = (COALESCE(update_status_attempts,0)+1) " .
                 " , last_retry_date = NOW(), process_stat='3' WHERE id='" .
                 $arResult['id'] . "'";
        $cdb->query($updateQuery); */
        
        // set this proposal to deleted if not exists in the system
        $stCheckQuoteExists = 'SELECT id FROM aos_quotes WHERE id = "' .
                 $arResult['proposal_id'] . '"';
        $rsResult = $db->query($stCheckQuoteExists);
        /* if ($db->getRowCount($rsResult) == 0) {
            
            $obEasyLink->do_log(
                    '#### RECORD ID MISSING ENDED UPDATE STATUS	  ####');
            
            $stMarkDeletedSQL = " UPDATE oss_proposalqueue set deleted='1'  WHERE id='" .
                     $arResult['id'] . "'";
            $cdb->query($stMarkDeletedSQL);
            
            $obEasyLink->do_log(
                    '#### UPDATE QUEUE :: RECORD ID[' . $arResult['proposal_id'] .
                             '] MARKED DELETED ####');
            
            // skip update process for this proposal
            continue;
        }
         */
        // set params for job Delivery status for Email
        $obEasyLinkResult = '';
        if ($arResult['easy_email_mrn'] != '' &&
                 $arResult['easy_email_xdn'] != '') {
            
            $obEasyLink->do_log(
                    '### Check STATUS FOR EMAIL MRN[jobID] ' .
                     $arResult['easy_email_mrn']);
            
            $arJobIDHostEmail = array(
                    'MRN' => $arResult['easy_email_mrn'],
                    'XDN' => $arResult['easy_email_xdn']
            );
            try {
                
                $obEasyLink->do_log(
                        '#----- SENDING STATUS REQUEST FOR proposal_id[' .
                                 $arResult['proposal_id'] . '],easy_email_mrn [' .
                                 $arResult['easy_email_mrn'] . '] ------#');
                
                $obEasyLinkResult = $obEasyLink->proposalDeliveryStatus(
                        $arJobIDHostEmail);
						print_r($obEasyLinkResult);
            } catch (SoapFault $obFault) {
                
                $stError = " SoapFault Error Message for Delivery Status Email :" .
                         $obFault->getMessage() . ' [ERROR CODE = ' .
                         $obFault->getCode() . ' ] ';
                $obEasyLink->do_log($stError, 'fatal');
                
                // can not get the delivery status for this proposal
                $GLOBALS['log']->fatal($stError);
            }
        }
        
        // Set params for job delivery status for Fax
        $obEasyLinkResultFax = '';
        if ($arResult['easy_fax_mrn'] != '' && $arResult['easy_fax_xdn'] != '') {
            
            $obEasyLink->do_log(
                    '### Check STATUS FOR FAX MRN[jobID] ' .
                             $arResult['easy_fax_mrn']);
            
            $arJobIDHostFax = array(
                    'MRN' => $arResult['easy_fax_mrn'],
                    'XDN' => $arResult['easy_fax_xdn']
            );
            try {
                
                $obEasyLinkResultFax = $obEasyLink->proposalDeliveryStatus(
                        $arJobIDHostFax);
            } catch (SoapFault $obFault) {
                
                // can not get the delivery status for this proposal
                $stError = " SoapFault Error Message for Delivery Status Fax:" .
                         $obFault->getMessage() . ' [ERROR CODE = ' .
                         $obFault->getCode() . ' ] ';
                
                $obEasyLink->do_log($stError, 'fatal');
                
                // can not get the delivery status for this proposal
                $GLOBALS['log']->fatal($stError);
            }
        }
        
        $completed = false;
        if (isset($obEasyLinkResult)) {
            
            if ($obEasyLinkResult->Status->StatusCode == 0) {
                
                if (is_array(
                        $obEasyLinkResult->JobDeliveryStatus->JobData->DeliveryGroup->DeliveryDetail)) {
                    $arDelivertyDetials = $obEasyLinkResult->JobDeliveryStatus->JobData->DeliveryGroup->DeliveryDetail;
                } else {
                    $arDelivertyDetials = array(
                            $obEasyLinkResult->JobDeliveryStatus->JobData->DeliveryGroup->DeliveryDetail
                    );
                }
                
                // itrate through each delivery status
                foreach ($arDelivertyDetials as $obDelivertyDetials) {
                  
                   print_r('joo'.$obDelivertyDetials);
                
                    if (in_array($obDelivertyDetials->State->code, 
                            $arFaildStatusCodes)) {
								 
                        $obEasyLink->do_log(
                                '#### EMAIL STATUS IS  ERRORS/CANCELLED/EXPIRED ####');
                        $obAdmin = new Administration();
                        $arAdminData = $obAdmin->retrieveSettings('instance', 
                                true);
                        
                        // send details to admin
                        $stNotificationEmail = PROPOSAL_SCHEDULE_ERROR_NOTIFICATION_EMAIL_ADDRESS;
                        
                        // get scheduled time
                        $obScheduledDate = new SugarDateTime(
                                $obEasyLinkResult->JobDeliveryStatus->JobData->StartTime);
                        
                        // get proposal error template
                        $obEmailTemplate = new EmailTemplate();
                        
                        $obEmailTemplate->retrieve_by_string_fields(
                                array(
                                        'name' => 'Proposal Schedule Error'
                                ));
                        // set instance name
                        $stSubject = str_replace('_INSTANCE_', 
                                $arAdminData->settings['instance_account_name'], 
                                $obEmailTemplate->subject);
                        $stEmailMessage = $obEmailTemplate->body_html;
                        
                        $arFindVars = array(
                                '_INSTANCE_',
                                '_MRN_',
                                '_XDN_',
                                '_DELIVERY_DATE_',
                                '_ATTEMPTS_',
                                '_STATE_'
                        );
                        $arReplaceVars = array(
                                $arAdminData->settings['instance_account_name'],
                                $obEasyLinkResult->JobDeliveryStatus->JobData->JobId->MRN,
                                $obEasyLinkResult->JobDeliveryStatus->JobData->JobId->XDN,
                                $obScheduledDate,
                                $obEasyLinkResult->Attempts,
                                $obEasyLinkResult->State->{'_'}
                        );
                        
                        $stEmailMessage = str_replace($arFindVars, 
                                $arReplaceVars, $stEmailMessage);
                        
                        $obEasyLink->sendNotificationEmail($stNotificationEmail, 
                                $stSubject, $stEmailMessage);
                        
                        $obEasyLink->do_log(
                                '#### SENDING NOTIFICATION TO :' .
                                         $stNotificationEmail);
                        
                        // update status for this proposal queue record
                        $stEmailTextDescription = '\n Email status code : ' .
                                 $obEasyLinkResult->State->code;
                        $stEmailTextDescription .= '\n Email status at EasyLink : ' .
                                 $obEasyLinkResult->State->{'_'};
                        $stUpdateStatusResult = 'UPDATE  oss_proposalqueue SET proposal_schedule_status="error", description =CONCAT(COALESCE(description,""),"' .
                                 $stEmailTextDescription . '")	WHERE id = "' .
                                 $arResult['id'] . '"';
                        $obEasyLink->do_log(
                                '#### UPDATED ERROR STATUS :' .
                                         $stUpdateStatusResult);
                        $cdb->query($stUpdateStatusResult);
                        // die ();
                        //stop execution now 
                        return true;
                    } else {
                        
                        if ($obDelivertyDetials->State->code == '7') {
                            
                            // get delivery date in GMT
                            $stDeliveryDate = $obDelivertyDetials->LastAttemptTime;
                            $obDate = new SugarDateTime($stDeliveryDate);
                            
                            // Check if email is delivered and open by the end
                            // user
                            if (isset($obDelivertyDetials->Events) &&
                                     count(
                                            $obDelivertyDetials->Events->PullEvent) >
                                     0) {
                                
                                // get latest data
                                $iTotalOpenCount = count(
                                        $obDelivertyDetials->Events->PullEvent);
                                
                                if (is_array(
                                        $obDelivertyDetials->Events->PullEvent)) {
                                    $obDateOpenReport = $obDelivertyDetials->Events->PullEvent[$iTotalOpenCount -
                                             1];
                                } else {
                                    $obDateOpenReport = $obDelivertyDetials->Events->PullEvent;
                                }
                                
                                // echo
                                // '<pre>';print_r($obDelivertyDetials);die;
                                
                                $obDateOpened = new SugarDateTime(
                                        $obDateOpenReport->PullTime, 
                                        new DateTimeZone('UTC'));
                                $stDateOpenUpdate = ',date_time_opened ="' .
                                         $obDateOpened->format('Y-m-d H:i:s') .
                                         '" ';
                                // process set 5 specifies proposal is sent and
                                // opened
                                $iProcessState = '5';
                            } else {
                                // if not opened by the end user do not update
                                $stDateOpenUpdate = '';
                                // process set 4 specifies proposal is sent but
                                // not opened
                                $iProcessState = '4';
                            }
                            
                            // update this date in porposal
                            $stUpdateSentDate = 'UPDATE aos_quotes SET date_time_sent = "' .
                                     $obDate->format('Y-m-d H:i:s') . '"' .
                                     $stDateOpenUpdate . ' WHERE  id = "' .
                                     $arResult['proposal_id'] . '"';
                            
                            $obEasyLink->do_log(
                                    '#### UPDATING Proposal [' .
                                             $stUpdateSentDate . '] ####');
                            
                            $db->query($stUpdateSentDate);
                            
                            $completed = true;
                        } elseif (in_array($obDelivertyDetials->State->code, 
                                $arInProgressCodes)) {
                            // check if the state is in 1,2,3 then set flag
                            // value to 2
                            // in ProposalQueue module
                            // this will make this record available for next
                            // itration
                            $stChangeStatusSQL = "UPDATE oss_proposalqueue set process_stat='2' WHERE id='" .
                                     $arResult['id'] . "'";
                            $cdb->query($stChangeStatusSQL);
                        }
                    }
                }
                
                // for multiple emails there will be array of objects
                $arEmailDeliveryDetails = $arDelivertyDetials;
                
                // update tracker information for email
                foreach ($arEmailDeliveryDetails as $obEmailDeliveryDetails) {
                    
                    $obProposalTracker = new oss_ProposalTracker();
					  print_r('joo'.$obProposalTracker);
                    $obProposalTracker->name = $obEmailDeliveryDetails->Destination->{'_'};
                    $obProposalTracker->email_subject = $obEasyLinkResult->JobDeliveryStatus->JobData->Subject->{'_'};
                    $obDateFirstAtmpt = new SugarDateTime(
                            $obEmailDeliveryDetails->FirstAttemptTime, 
                            new DateTimeZone('UTC'));
                    $obProposalTracker->date_firstattempt = $obDateFirstAtmpt->format(
                            'Y-m-d H:i:s');
                    
                    $obDateLastAtmpt = new SugarDateTime(
                            $obEmailDeliveryDetails->LastAttemptTime, 
                            new DateTimeZone('UTC'));
                    $obProposalTracker->date_lastattempt = $obDateLastAtmpt->format(
                            'Y-m-d H:i:s');
                    
                    $obProposalTracker->attempts = $obEmailDeliveryDetails->Attempts;
                    $obProposalTracker->proposal_id = $arResult['proposal_id'];
                    
                    // if there are more then one pull event
                    if (is_array($obEmailDeliveryDetails->Events->PullEvent)) {
                        
                        $iTotalOpenCount = count(
                                $obEmailDeliveryDetails->Events->PullEvent);
                        $obDateOpenReportFirst = $obEmailDeliveryDetails->Events->PullEvent[0]->PullTime;
                        
                        $obDateOpenReportLast = $obEmailDeliveryDetails->Events->PullEvent[$iTotalOpenCount -
                                 1]->PullTime;
                        
                        $obDateOpened = new SugarDateTime($obDateOpenReportFirst, 
                                new DateTimeZone('UTC'));
                        $stDateOpenUpdate = $obDateOpened->format('Y-m-d H:i:s');
                        
                        $obDateOpenedLast = new SugarDateTime(
                                $obDateOpenReportLast, new DateTimeZone('UTC'));
                        $stDateLastOpenUpdate = $obDateOpenedLast->format(
                                'Y-m-d H:i:s');
                    } else 
                        if (isset(
                                $obEmailDeliveryDetails->Events->PullEvent->PullTime)) {
                            
                            $obDateOpened = new SugarDateTime(
                                    $obEmailDeliveryDetails->Events->PullEvent->PullTime, 
                                    new DateTimeZone('UTC'));
                            $stDateOpenUpdate = $obDateOpened->format(
                                    'Y-m-d H:i:s');
                            $stDateLastOpenUpdate = $obDateOpened->format(
                                    'Y-m-d H:i:s');
                        } else {
                            // set null to first viewed and last viewed
                            $stDateOpenUpdate = null;
                            $stDateLastOpenUpdate = null;
                        }
                    
                    // if date open is defined
                    $obProposalTracker->status = ($stDateOpenUpdate != '') ? "Open" : $obEmailDeliveryDetails->State->{'_'};
                    
                    $obProposalTracker->first_viewed = $stDateOpenUpdate;
                    $obProposalTracker->last_viewed = $stDateLastOpenUpdate;
                    $obProposalTracker->hits = count(
                            $obEmailDeliveryDetails->Events->PullEvent);
                    
                    // new field - job id
                    $job_id = '';
                    if (! empty($arResult['easy_email_mrn'])) {
                        $job_id .= $arResult['easy_email_mrn'];
                    }
                    if (! empty($arResult['easy_fax_mrn'])) {
                        if (! empty($job_id)) {
                            $job_id .= '-';
                        }
                        $job_id .= $arResult['easy_fax_mrn'];
                    }
                    
                    $obProposalTracker->job_id = $job_id;
                    
                    /*
                     * $arDupCheck = array ( 'status' =>
                     * $obProposalTracker->status, 'date_lastattempt' =>
                     * $obProposalTracker->date_firstattempt, 'date_lastattempt'
                     * => $obProposalTracker->date_lastattempt ); if (trim (
                     * $stDateOpenUpdate ) != '') { $arSentOpenDates = array (
                     * 'first_viewed' => $stDateOpenUpdate, 'last_viewed' =>
                     * $stDateLastOpenUpdate ); $arDupCheck = array_merge (
                     * $arDupCheck, $arSentOpenDates ); }
                     */
                    
                    $arDupCheck = array(
                            //'status' => $obProposalTracker->status,
                            'name' => $obEmailDeliveryDetails->Destination->{'_'},
                            'job_id' => $obProposalTracker->job_id
                    );
                    
                    $obProposalTracker->retrieve_by_string_fields($arDupCheck);
                    $obEasyLink->do_log(
                            'Here is the id ' . $obProposalTracker->id);
                    // if the proposal tracker is already updated then do not
                    // change the sales stage
                    if (isset($obProposalTracker->id) &&
                             trim($obProposalTracker->id) != '') {
                        // The sales stage is already changed to "Proposal-Sent"
                        // do touch it again
                        // User might have changed the sales stage to some other
                        // value
                        $bChangeSalesStage = false;
                        
                        //@modified by Mohit Kumar Gupta 01-04-2015
                        //Logical bug resolved, = sign is missing and update proposal schedular giving fatal error
                        $obProposalTracker->status = ($stDateOpenUpdate != '') ? "Open" : $obEmailDeliveryDetails->State->{'_'};
                    }
                    // there must be a status for this tracker info
                    if (isset($obProposalTracker->status) &&
                             trim($obProposalTracker->status) != '') {
                        $obProposalTracker->save();
                    }
                    
                    if ($obProposalTracker->status == 'Open') {
                        
                        $obProposalTrackerSent = clone $obProposalTracker;
                        unset($obProposalTrackerSent->id);
                        $obProposalTrackerSent->status = $obEmailDeliveryDetails->State->{'_'};
                        
                        $arDupCheck = array(
                                'status' => $obProposalTracker->status,
                                'name' => $obEmailDeliveryDetails->Destination->{'_'},
                                'job_id' => $obProposalTrackerSent->job_id
                        );
                        
                        $obProposalTrackerSent->retrieve_by_string_fields(
                                $arDupCheck);
                        $obEasyLink->do_log(
                                'here is the id ' . $obProposalTrackerSent->id);
                        
                        // there must be a status for this tracker info
                        if (isset($obProposalTracker->status) &&
                                 trim($obProposalTracker->status) != '') {
                            $obProposalTracker->save();
                        }
                    }
                    
                    // email has preference to set sent time and open time
                }
            }
        }
        
        if ($obEasyLinkResultFax != '') {
            if ($obEasyLinkResultFax->Status->StatusCode == 0) {
                $obDelivertyDetialsFax = $obEasyLinkResultFax->JobDeliveryStatus->JobData->DeliveryGroup->DeliveryDetail;
                
                if (in_array($obDelivertyDetialsFax->State->code, 
                        $arFaildStatusCodes)) {
                    $obEasyLink->do_log(
                            '#### FAX STATUS IS  ERRORS/CANCELLED/EXPIRED ####');
                    $obAdmin = new Administration();
                    $arAdminData = $obAdmin->retrieveSettings('instance', true);
                    
                    // send details to admin
                    $stNotificationEmail = PROPOSAL_SCHEDULE_ERROR_NOTIFICATION_EMAIL_ADDRESS;
                    
                    // get scheduled time
                    $obScheduledDate = new SugarDateTime(
                            $obEasyLinkResultFax->JobDeliveryStatus->JobData->StartTime);
                    
                    // get proposal error template
                    $obEmailTemplate = new EmailTemplate();
                    
                    $obEmailTemplate->retrieve_by_string_fields(
                            array(
                                    'name' => 'Proposal Schedule Error'
                            ));
                    // set instance name
                    $stSubject = str_replace('_INSTANCE_', 
                            $arAdminData->settings['instance_account_name'], 
                            $obEmailTemplate->subject);
                    $stEmailMessage = $obEmailTemplate->body_html;
                    
                    $arFindVars = array(
                            '_INSTANCE_',
                            '_MRN_',
                            '_XDN_',
                            '_DELIVERY_DATE_',
                            '_ATTEMPTS_',
                            '_STATE_'
                    );
                    $arReplaceVars = array(
                            $arAdminData->settings['instance_account_name'],
                            $obEasyLinkResultFax->JobDeliveryStatus->JobData->JobId->MRN,
                            $obEasyLinkResultFax->JobDeliveryStatus->JobData->JobId->XDN,
                            $obScheduledDate,
                            $obDelivertyDetialsFax->Attempts,
                            $obDelivertyDetialsFax->State->{'_'}
                    );
                    
                    $stEmailMessage = str_replace($arFindVars, $arReplaceVars, 
                            $stEmailMessage);
                    
                    $obEasyLink->do_log(
                            '#### SENDING NOTIFICATION TO :' .
                                     $stNotificationEmail);
                    $obEasyLink->sendNotificationEmail($stNotificationEmail, 
                            $stSubject, $stEmailMessage);
                    
                    // update status for this proposal queue record
                    $stTextDescription = '\n Fax status code : ' .
                             $obDelivertyDetialsFax->State->code;
                    $stTextDescription .= '\n Fax status at EasyLink : ' .
                             $obDelivertyDetialsFax->State->{'_'};
                    $stUpdateStatusResult = 'UPDATE  oss_proposalqueue SET proposal_schedule_status="error", description =CONCAT(COALESCE(description,""),"' .
                             $stTextDescription . '")	WHERE id = "' .
                             $arResult['id'] . '"';
                    $obEasyLink->do_log($stUpdateStatusResult);
                    $cdb->query($stUpdateStatusResult);
                    //stop execution now 
                    return true;
                } else {
                    if ($obDelivertyDetialsFax->State->code == '7') {
                        $completed = true;
                    }
                    
                    // check if the state is in 1,2,3 then set flag value to 2
                    // in ProposalQueue module
                    if (in_array($obDelivertyDetialsFax->State->code, 
                            $arInProgressCodes)) {
                        
                        // this will make this record available for next
                        // itration
                        $stChangeStatusSQL = "UPDATE oss_proposalqueue set process_stat='2' WHERE id='" .
                                 $arResult['id'] . "'";
                        $cdb->query($stChangeStatusSQL);
                    }
                }
                
                // add traker information for fax
                if ($arResult['proposal_schedule_status'] == 'scheduled' && ! in_array(
                        $obDelivertyDetialsFax->State->code, $arInProgressCodes)) {
                    $obEasyLink->do_log('#### ADDING TRACKER INFO ####');
                    $obFaxDeliveryDetails = $obEasyLinkResultFax->JobDeliveryStatus->JobData->DeliveryGroup->DeliveryDetail;
                    
                    $obProposalTracker = new oss_ProposalTracker();
                    
                    $obProposalTracker->name = $obFaxDeliveryDetails->Destination->{'_'};
                    $obProposalTracker->status = $obFaxDeliveryDetails->State->{'_'};
                    $obProposalTracker->first_viewed = $obFaxDeliveryDetails->Events->PullEvent->PullTime;
                    $obProposalTracker->email_subject = $obFaxDeliveryDetails->JobDeliveryStatus->JobData->Subject->{'_'};
                    
                    $obDateFirstAtmpt = new SugarDateTime(
                            $obFaxDeliveryDetails->FirstAttemptTime, 
                            new DateTimeZone('UTC'));
                    $obProposalTracker->date_firstattempt = $obDateFirstAtmpt->format(
                            'Y-m-d H:i:s');
                    
                    $obDateLastAtmpt = new SugarDateTime(
                            $obFaxDeliveryDetails->LastAttemptTime, 
                            new DateTimeZone('UTC'));
                    $obProposalTracker->date_lastattempt = $obDateLastAtmpt->format(
                            'Y-m-d H:i:s');
                    
                    $obProposalTracker->attempts = $obFaxDeliveryDetails->Attempts;
                    $obProposalTracker->proposal_id = $arResult['proposal_id'];
                    
                    // new field - job id
                    $job_id = '';
                    if (! empty($arResult['easy_email_mrn'])) {
                        $job_id .= $arResult['easy_email_mrn'];
                    }
                    if (! empty($arResult['easy_fax_mrn'])) {
                        if (! empty($job_id)) {
                            $job_id .= '-';
                        }
                        $job_id .= $arResult['easy_fax_mrn'];
                    }
                    
                    $obProposalTracker->job_id = $job_id;
                    
                    /*
                     * $obProposalTracker->retrieve_by_string_fields ( array (
                     * 'status' => $obProposalTracker->status,
                     * 'date_firstattempt' =>
                     * $obProposalTracker->date_firstattempt, 'date_lastattempt'
                     * => $obProposalTracker->date_lastattempt ) );/
                     */
                    
                    $arDupCheck = array(
                            'status' => $obProposalTracker->status,
                            'job_id' => $obProposalTracker->job_id
                    );
                    
                    if (isset($obProposalTracker->status) &&
                             trim($obProposalTracker->status) != '') {
                        $obProposalTracker->save();
                    }
                    
                    $obEasyLink->do_log('#### TRACKER INFO ADDED ####');
                }
                
                // update proposal for Fax sent
                
                // set fax received time for proposal
                if (isset($obFaxDeliveryDetails->State->code) &&
                         $obFaxDeliveryDetails->State->code == '7') {
                    
                    $obDateFaxSent = new SugarDateTime(
                            $obFaxDeliveryDetails->LastAttemptTime, 
                            new DateTimeZone('UTC'));
                    $stDateOpenUpdate = 'date_time_received ="' .
                             $obDateFaxSent->format('Y-m-d H:i:s') . '" ';
                    
                    // update this date in porposal
                    $stUpdateSentDate = 'UPDATE aos_quotes SET "' . $stDateOpenUpdate .
                             ' WHERE  id = "' . $arResult['proposal_id'] . '"';
                    
                    $obEasyLink->do_log(
                            '#### UPDATING Proposal [' . $stUpdateSentDate .
                                     '] ####');
                    
                    $db->query($stUpdateSentDate);
                }
                
                if (! isset($obEasyLinkResult) &&
                         $obFaxDeliveryDetails->State->code == '7') {
                    $iProcessState = 5;
                    $bChangeSalesStage = true;
                }
            }
        }
        
        /*
         * if ($obEasyLinkResult != '' && $obEasyLinkResultFax != '') {
         * $completed = false; if ($obEasyLinkResult->Status->StatusCode == 0 &&
         * $obEasyLinkResultFax->Status->StatusCode == 0) { if
         * ($obDelivertyDetials->State->code == '7' &&
         * $obDelivertyDetialsFax->State->code == '7') { $completed = true; } }
         * }
         */
        
        /**
         * Easy link Job States
         * 1 Pending
         * 2	Submitted
         * 3	InProcess
         * 4	Error
         * 5	Cancelled
         * 6	Held
         * 7	Sent
         * 8	Expired
         */
        $obEasyLink->do_log(' STATUS OF ' . $bChangeSalesStage);
        // if the proposal has been sent
        if ($completed == true) {
            /**
             * proposal verisoning
             * Hirak - 07.02.2013
             */
            // update proposal sent count
            /*
             * $stProposalStatusQuery = 'SELECT is_proposal_modified FROM quotes
             * WHERE id = "' . $arResult ['proposal_id'] . '"';
             * $stProposalStatusResult = $db->query ( $stProposalStatusQuery );
             * $stProposalStatusRow = $db->fetchByAssoc (
             * $stProposalStatusResult ); if ($stProposalStatusRow
             * ['is_proposal_modified'] == 1) { $stUpdateSentData = 'UPDATE
             * quotes SET is_proposal_modified = 0, proposal_sent_count =
             * proposal_sent_count+1 WHERE id = "' . $arResult ['proposal_id'] .
             * '"'; $db->query ( $stUpdateSentData ); }
             */
            
            // update this entry on central database
            $stUpdateSentDateCentral = 'UPDATE oss_proposalqueue SET proposal_schedule_status = "delivered",
								 date_delivered = "' . $obDate->format('Y-m-d H:i:s') .
                     '",process_stat="' . $iProcessState . '"  
								 WHERE  id = "' . $arResult['id'] . '"';
            
            $obEasyLink->do_log(
                    '#### UPDATING Proposal Queue[' . $stUpdateSentDateCentral .
                             '] ####');
            
            $cdb->query($stUpdateSentDateCentral);
            // do not touch the sales stage again once updated for same job id
            if ($bChangeSalesStage) {
                $opportunity_sql = " SELECT opportunity_id FROM aos_quotes
				WHERE id = '" .
                         $arResult['proposal_id'] . "'
						AND aos_quotes.deleted = 0 ";
                $opportunity_result = $db->query($opportunity_sql);
                $opportunity_row = $db->fetchByAssoc($opportunity_result);
                $opprtunity_id = $opportunity_row['opportunity_id'];
                
                $stUpdateProposalSent = "UPDATE opportunities SET sales_stage = 'Proposal - Sent'
										WHERE id = '" . $opprtunity_id . "' ";
                $obEasyLink->do_log($stUpdateProposalSent . __LINE__);
                /**
                 * Maintain Change Log for Opportunity
                 * Added By Satish Gupta on 29th Jan 2013
                 */
                
                // Get Previous Value of Opportunity Sales Stage
                $opp_ss_sql = "SELECT sales_stage FROM opportunities WHERE id='" .
                         $opprtunity_id . "' AND deleted=0";
                $opp_ss_query = $db->query($opp_ss_sql);
                $opp_ss_result = $db->fetchByAssoc($opp_ss_query);
                $old_sales_stage = $opp_ss_result['sales_stage'];
                
                $db->query($stUpdateProposalSent);
                
                // Insert Change Log on opportunity audit table.
                insertChangeLog($db, 'opportunities', $opprtunity_id, 
                        $old_sales_stage, 'Proposal - Sent', 'sales_stage', 
                        'enum', '1');
            }
        } else {
            
            // this will make this record available for next itration
            $stChangeStatusSQL = "UPDATE oss_proposalqueue set process_stat='2' WHERE id='" .
                     $arResult['id'] . "'";
            $cdb->query($stChangeStatusSQL);
        }
        
        $obEasyLink->do_log('#### UPDATE STATUS COMPLETED ####');
    } // End while loop for get proposals
    /*
     * }else{ $obEasyLink->do_log('#### RECORD ID MISSING ENDED UPDATE STATUS
     * ####'); $stMarkDeletedSQL = "UPDATE oss_proposalqueue set deleted='1'
     * WHERE id='".$_REQUEST['schedule_record']."'";
     * $cdb->query($stMarkDeletedSQL); $obEasyLink->do_log('#### UPDATE QUEUE ::
     * RECORD ID['.$_REQUEST['schedule_record'].'] MARKED DELETED ####'); }
     */
    // ####################################################
    // ## UPDATE STATUS OF JOBS ####
    // ####################################################
}
updateProposals ();
?>

