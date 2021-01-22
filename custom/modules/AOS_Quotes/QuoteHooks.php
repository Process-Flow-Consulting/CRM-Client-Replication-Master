<?php

if (! defined ( 'sugarEntry' ) || ! sugarEntry)
	die ( 'Not A Valid Entry Point' );
	/*
 *
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
 * remove SugarCRM copyrights from the source code or user interface. All copies
 * of the Covered Code must include on each user interface screen: (i) the
 * "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice in the same
 * form as they appear in the distribution. See full license for requirements.
 * Your Warranty, Limitations of liability and Indemnity are expressly stated in
 * the License. Please refer to the License for the specific language governing
 * these rights and limitations under the License. Portions created by SugarCRM
 * are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */

class QuoteHooks {
	
	function setAccountProviewLink(&$focus) {
		if ($_REQUEST ['action'] == 'EditView') {
			return;
		}
		
		require_once 'custom/include/common_functions.php';
		
		$focus->account_proview_url = proview_url(array('url'=>$focus->account_proview_url));
		
	
	}
	
	function ResetVerifyProposal(&$focus) {
		global $db, $current_user, $sugar_config;
		
		require_once 'custom/include/common_functions.php';
		/*$audit_sql = "SELECT MAX(distinct(date_created)) last_modified_date FROM quotes_audit UNION SELECT MAX(distinct(date_created)) last_modified_date FROM products_audit";
		$audit_query = $db->query ( $audit_sql );
		$audit_result_array = array ();
		while ( $audit_result = $db->fetchByAssoc ( $audit_query ) ) {
			$audit_result_array [] = $audit_result ['last_modified_date'];
		}
		$proposal_audit_date_new = $audit_result_array [0];
		$product_audit_date_new = $audit_result_array [1];
		*/
		//hirak :  date : 12-10-2012
		if ($_REQUEST ['is_form_updated'] == '1' || $_REQUEST['proposal_delivery_method'] == 'M'){
			
			$updateQuery = "UPDATE quotes SET verify_email_sent=0, proposal_verified='2' WHERE id='" . $focus->id . "'";
			$db->query ( $updateQuery );
			
			//must be set to 2  
			$focus->proposal_verified= '2';
			
			
			/**
			 * Maintain Change Log for Opportunity
			 * Added By Satish Gupta on 24th Jan 2013
			 */
			
			//Get Previous Value of Opportunity Sales Stage
			$opp_ss_sql = "SELECT sales_stage FROM opportunities WHERE id='".$focus->opportunity_id."' AND deleted=0";
			$opp_ss_query = $db->query($opp_ss_sql);
			$opp_ss_result = $db->fetchByAssoc($opp_ss_query);
			$old_sales_stage = $opp_ss_result['sales_stage'];
			
			// Added by Basudeba Rath, Date : 19/Oct/2012.
			$sqlUpdateSalesStage = "UPDATE opportunities SET sales_stage = 'Proposal - Unverified' WHERE id = '".$focus->opportunity_id."'";
			$db->query( $sqlUpdateSalesStage );			
			
			//Insert Change Log on opportunity audit table.
			insertChangeLog($db, 'opportunities', $focus->opportunity_id, $old_sales_stage, 'Proposal - Unverified', 'sales_stage', 'enum', $current_user->id);		
			
		}
		
		//if proposal oid manual always set the proposall verified
		if( $_REQUEST['proposal_delivery_method'] == 'M' ){
			
			$updateQuery = "UPDATE quotes SET verify_email_sent = 0, proposal_verified='1' WHERE id='" . $focus->id . "'";
			$db->query ( $updateQuery );
			
		}
		
		if( empty($_REQUEST['record']) || ($_REQUEST ['is_form_updated'] == '1') )
		{
			/**
			 * proposal verisoning
			 * Hirak - 07.02.2013
			 */
			
			/*if($focus->proposal_sent_count > 0){
				
				require_once('include/Sugarpdf/SugarpdfFactory.php');
			
			
				$stFileName = $focus->name .' '.$focus->proposal_version.'.pdf';
			
				$note = new Note();
				$note->disable_row_level_security = true;
				$note->id = create_guid();
			
				$object_map = array ();
				$pdf = SugarpdfFactory::loadSugarpdf ( 'Standard', 'Quotes', $focus, $object_map );
				$pdf->process ();
				$stTmpPdf = $pdf->Output ( "{$sugar_config['upload_dir']}{$note->id}", 'F' );
			
				if(file_exists("{$sugar_config['upload_dir']}{$note->id}")){
					
					$note->new_with_id = true; // duplicating the note with files
					$note->parent_id = $focus->id;
					$note->parent_type = $focus->module_dir;
					$note->name = $stFileName;
					$note->filename = $stFileName;
					$note->file_mime_type = Email::email2GetMime("{$sugar_config['upload_dir']}{$note->id}");
					$note->team_id = $focus->team_id;
					$note->team_set_id = $focus->team_set_id;
					$note->assigned_user_id = $focus->assigned_user_id;
					$note->save();
				
					// Fetch all documents related with proposal
					$focus->load_relationship ( 'notes' );
					$focus->notes->add($note->id);
				}
			
			}*/
				
			$proposal_sent_count = $focus->proposal_sent_count;
			$proposal_version = create_proposal_version($proposal_sent_count);
			$updateQuery = "UPDATE quotes SET is_proposal_modified = 1, proposal_version = '".$proposal_version."' WHERE id='" . $focus->id . "'";
			$db->query ( $updateQuery );
				
		}
		/**
		 * Added By : Ashutosh 
		 * mark proposal verified date
		 */		
		if($focus->proposal_verified ==1 || $_REQUEST['proposal_delivery_method'] == 'M'){
		    $updateQuery = "UPDATE quotes SET verified_date=UTC_TIMESTAMP() WHERE id='" . $focus->id . "'";
		    $db->query ( $updateQuery );
		}
		
	}
	
	/**
	 * Function to schedule a proposal
	 * 
	 */
	function scheduleProposal(&$focus){	

		
		ini_set('zend_optimizerplus.dups_fix',1);
		
		
		
		global $sugar_config,$timedate,$mod_strings,$db;
		require_once $sugar_config['master_config_path'] ;// '/vol/certificate/master_config.php';
		require_once 'custom/include/master_db/mysql.class.php';
		require_once 'custom/modules/AOS_Quotes/schedule_quotes/class.easylinkmessage.php';
		require_once 'include/SugarDateTime.php';
		
		
		
		//add email address to email_addresses table
		$stContactEmail = trim($focus->contact_email);
		if( $stContactEmail != '')
		{
			$stAddUpdateEmail = '';
			$stGetEmail = 'SELECT id,deleted ,email_address from email_addresses WHERE email_address = "'.$stContactEmail.'"';
			$rsGetEmail = $db->query($stGetEmail);
			$arGetEmail = $db->fetchByAssoc($rsGetEmail);
					
			//if email not exists or marked as deleted
			if(!$arGetEmail ){
				$stAddUpdateEmail = 'INSERT INTO email_addresses VALUES(UUID()
										,"'.$stContactEmail.'"
										,"'. strtoupper( $stContactEmail).'"
										,0,0,UTC_TIMESTAMP(),UTC_TIMESTAMP(),0)';
						}elseif(isset($arGetEmail['deleted']) && $arGetEmail['deleted']  !=0){
				
				$stAddUpdateEmail = 'UPDATE email_addresses SET deleted =0 WHERE id="'.$arGetEmail['deleted'].'"';
			}
			if( $stAddUpdateEmail != ''){
				$db->query($stAddUpdateEmail);
			}			
		}
		
		
		$obEasyLink = new easyLinkMessage($sugar_config['EASY_LINK_USER_NAME'], $sugar_config ['EASY_LINK_USER_PASS'] );
		
		$cdb = $obEasyLink->__getCentralDB();
		
		//check if this proposal exists at central cron		
		$stDbName = $sugar_config['dbconfig']['db_name'];
		$stCheckSql = 'SELECT id
						,instance_db_name
						,proposal_id
						,easy_email_mrn
						,easy_email_xdn
						,easy_fax_mrn
						,easy_fax_xdn
						,proposal_schedule_status
						FROM oss_proposalqueue  
						WHERE instance_db_name="'.$stDbName.'"
						AND proposal_id ="'.$focus->id.'"
						AND proposal_schedule_status IN ("inprogress","scheduled")' ;
		
		
		//check if there is any entry correspond to this proposal
		$rsResult = $cdb->query($stCheckSql);
		$arResult = $cdb->num_rows ($rsResult) ;
		
		
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
							AND proposal_id ="' . $focus->id . '"
							AND process_state IN ("pending","inprogress")';
		
		$rsCancel = $cdb->query ( $stInCancelQueueSQL );
		// Cancel queue row count
		$iCancelQueueCount = $cdb->num_rows ( $rsCancel );
		
		// check if the porposal in schedule queue
		$stInScheduleQueueSQL = 'SELECT id
								,  instance_folder
								,  instance_db_name
								,  proposal_id
								,  proposal_delivery_date
								,  process_state
								FROM  oss_schedulequeue
								WHERE instance_db_name="' . $stDbName . '"
								AND proposal_id ="' . $focus->id . '"
								AND process_state IN ("pending","inprogress")';
		
		$rsScheduleQueue = $cdb->query ( $stInScheduleQueueSQL );
		// Cancel queue row count
		$iScheduleQueueCount = $cdb->num_rows ( $rsScheduleQueue );
		
		//log counts values 
		$obEasyLink->do_log(' PROPOSAL COUNTS [ '.$focus->id.'] Pending counts ['.$arResult
							.'] Cancel Queue ['.$iCancelQueueCount
							.'] Schedule Queue ['.$iScheduleQueueCount.']');
		
		/**
		 * if the proposal was already scheduled and user has removed the delivery
		 * method then cancel the scheduled delivery 
		 * 
		 */
		//hirak : date: 12-10-2012
		if( ($focus->proposal_verified == '2' 
			&& ($arResult>0 || $iCancelQueueCount >0 || $iScheduleQueueCount >0)) 
				|| $focus->proposal_delivery_method == 'M'){
			//proposal is scheduled and now user has removed the delivery method	
			$obEasyLink->do_log(' Proposal[ '.$focus->id.']  already scheduled at easylink send cancel ');
			
			$obEasyLink->cancelScheduledProposal($focus);
		}
		
		
		
		/**
		 * A proposal will be scheduled on following conditions 
		 * 1) Proposal is verified		 
		 */	
		//hirak : date: 12-10-2012		
		if( $focus->proposal_verified == '1' && $focus->date_time_delivery != '' 
				&& $focus->proposal_delivery_method != 'M'){
							
				$obEasyLink->do_log(' Proposal[ '.$focus->id.']  verified, started scheduling.');
				
				//create schedule/stop date				
				$obScheduleDate = new SugarDateTime($focus->date_time_delivery,new DateTimeZone('UTC'));
				
				$obScheduleStopDate = new SugarDateTime($focus->date_time_delivery,new DateTimeZone('UTC'));
				$obScheduleStopDate->modify('+1 day');
				
				//$obScheduleDate->format('Y-m-d').'T'.$obScheduleDate->format('H:i:sP').'<br/>'.$obScheduleStopDate;
				
				$dtScheduledGmtTime = $obScheduleDate->format('Y-m-d').'T'.$obScheduleDate->format('H:i:sP');
				$dtScheduledStopGmtTime = $obScheduleStopDate->format('Y-m-d').'T'.$obScheduleDate->format('H:i:sP');
			
				//get difference in days
				$iDaysDiff =  ceil((strtotime($focus->date_time_delivery)- strtotime($timedate->nowDb()))/(60*60*24));
				
				$arScheduleDetails = $cdb->fetch_assoc($rsResult) ;
				
				$obEasyLink->do_log(' Proposal[ '.$focus->id.']  day difference .'.$iDaysDiff);
				
				//check if the delivery time is in next two days 	 			
				if($iDaysDiff <= 2 ) {
					
					//if there is a record correspond to this proposal
					//proposal is in pending queue
					if($arResult && $arResult >0)
					{
						$obEasyLink->do_log(' PROPOSAL '.$focus->id.' in pending queue.');
						//cancel this proposal from Pending,schedule Queue and 
						// update delivery date on cancel queue
						$bIsCancelled = $obEasyLink->cancelScheduledProposal($focus);
							
						//if proposal is cancelled sucessfully
						if($bIsCancelled){
							
							//proposal cancelled successfully send proposal schedule request
							$bIsProposalScheduled = $obEasyLink->scheduleProposalAtEasyLink($focus);
							
							//if proposal doesn't get scheduled show error
							if(!$bIsProposalScheduled){

								if(!isset($focus->do_not_redirect))
								{
									$obEasyLink->do_log(' Proposal[ '.$focus->id.'] Schedule error.','fatal');
								
									SugarApplication::appendErrorMessage($mod_strings['EROOR_SCHEDULE_PROPOSAL']);
									SugarApplication::redirect('index.php?module=Quotes&action=DetailView&record='.$focus->id);
								}
								//set retrun value
								if(isset($focus->do_not_redirect))
								{
								    $bReturn = false;
								}
								
								//sugar_die($mod_strings['EROOR_SCHEDULE_PROPOSAL']);
							}
							
							$obEasyLink->do_log(' Proposal[ '.$focus->id.'] Scheduled .');
						}
						else
						{
								
							//add this scheduled email proposal to cancel queue
							$obEasyLink->addTocancelQueue($focus,array( 'easy_email_mrn' => $arScheduleDetails['easy_email_mrn']
																		,'easy_email_xdn' => $arScheduleDetails['easy_email_xdn']
																		,'easy_fax_mrn' => $arScheduleDetails['easy_fax_mrn']
																		,'easy_fax_xdn' => $arScheduleDetails['easy_fax_xdn'])
																			);
							//since the job is in cancel queue due to an error set this proposal
							// proposal_schedule_status as error so that it will be excluded by the corn
							$stUpdateSheduleErrorSQL = 'UPDATE oss_proposalqueue  SET proposal_schedule_status ="error" WHERE id = "'.$arScheduleDetails['id'].'"';
							$cdb->query($stUpdateSheduleErrorSQL);			    	
							
							if(!isset($focus->do_not_redirect))
							{
								SugarApplication::appendErrorMessage($mod_strings['EROOR_CANCEL_PROPOSAL']);
								SugarApplication::redirect('index.php?module=Quotes&action=DetailView&record='.$focus->id);
							}
							
							//set retrun value
							if(isset($focus->do_not_redirect))
							{
								$bReturn = false;
							}
							
							//proposal cancel error display this error to end user							
							//sugar_die($mod_strings['EROOR_CANCEL_PROPOSAL']);
						}
					
					}//if proposal is in cancel queue
					elseif($iCancelQueueCount>0){
						
						$obEasyLink->do_log(' PROPOSAL '.$focus->id.' in cancel queue');
						//just update the delivery date in cancel queue
						$arCancelQueue = $cdb->fetch_assoc ( $rsCancel );
							
						// set delivery date[delivery datetime] and schedule after
						// cancel[skip_delivery_method] flag
						$stUpdateCancelQueueSQL = 'UPDATE oss_cancelqueue SET proposal_delivery_date = "' . $obProposal->date_time_delivery . '",skip_delivery_method= "' . $obProposal->skip_delivery_method . '" WHERE id="' . $arCancelQueue ['id'] . '"';
							
						$cdb->query ( $stUpdateCancelQueueSQL );
						
						
					}//if this proposal is in schedule queue
					elseif($iScheduleQueueCount>0){
						
						$obEasyLink->do_log(' PROPOSAL '.$focus->id.' in schedule queue');
						//just update the dliverty date
						$arScheduleQueue = $cdb->fetch_assoc ( $rsScheduleQueue );
							
						// set status as cancelled
						$stUpdateScheduleQueueSQL = 'UPDATE  oss_schedulequeue SET process_state = "cancelled"
						,date_cancelled= NOW() WHERE id="' . $arScheduleQueue ['id'] . '"';
							
						$cdb->query ( $stUpdateScheduleQueueSQL );
					}
					else
					{
						$obEasyLink->do_log(' PROPOSAL '.$focus->id.' Schedule new.');
						//new proposal to schedule
					 	$bIsProposalScheduled = $obEasyLink->scheduleProposalAtEasyLink($focus);

					 	//if proposal doesn't get scheduled show error
					 	if(!$bIsProposalScheduled){
					 	
					 		$obEasyLink->do_log(' Proposal[ '.$focus->id.'] Schedule error.','fatal');
					 		
					 		if(!isset($focus->do_not_redirect))
							{
								SugarApplication::appendErrorMessage($mod_strings['EROOR_SCHEDULE_PROPOSAL']);
								SugarApplication::redirect('index.php?module=Quotes&action=DetailView&record='.$focus->id);
							}
							//set retrun value
							if(isset($focus->do_not_redirect))
							{
							    $bReturn = false;
							}
					 		//sugar_die($mod_strings['EROOR_SCHEDULE_PROPOSAL']);
					 	}
					}
					
				}else{
					
					//make sure proposal is not in any other queue
					if($arResult>0 || $iCancelQueueCount >0 || $iScheduleQueueCount >0) {
						//proposal is scheduled and now user has removed the delivery method
						$bStatusCancel = $obEasyLink->cancelScheduledProposal($focus);
						
						if(!$bStatusCancel){

							//add this scheduled email proposal to cancel queue
							$obEasyLink->addTocancelQueue($focus,array( 'easy_email_mrn' => $arScheduleDetails['easy_email_mrn']
									,'easy_email_xdn' => $arScheduleDetails['easy_email_xdn']
									,'easy_fax_mrn' => $arScheduleDetails['easy_fax_mrn']
									,'easy_fax_xdn' => $arScheduleDetails['easy_fax_xdn'])
							);
								
							//since the job is in cancel queue due to an error set this proposal
							// proposal_schedule_status as error so that it will be excluded by the corn
							$stUpdateSheduleErrorSQL = 'UPDATE oss_proposalqueue  SET proposal_schedule_status ="error" WHERE id = "'.$arScheduleDetails['id'].'"';
							$cdb->query($stUpdateSheduleErrorSQL);
								
							//proposal cancel ERRORR
							if(!isset($focus->do_not_redirect))
							{
								SugarApplication::appendErrorMessage($mod_strings['EROOR_CANCEL_PROPOSAL']);
								SugarApplication::redirect('index.php?module=Quotes&action=DetailView&record='.$focus->id);
								//sugar_die($mod_strings['EROOR_CANCEL_PROPOSAL']);
							}
							//set retrun value
							if(isset($focus->do_not_redirect))
							{
							    $bReturn = false;
							}
						}
					}
					$arFieldMap['name']= $stDbName;
					$arFieldMap['date_schedule']=  $obScheduleDate->format('Y-m-d h:i:s') ;
					$arFieldMap['proposal_id']= $focus->id ;
					$arFieldMap['instance_folder_name']= $obEasyLink->instanceFolderName;
					$arFieldMap['instance_db_name']= $stDbName;
					$arFieldMap['process_stat']= '0';
					
					//proposal is verified but not in next two days let CRON do its work
					$stScheduleSql = 'INSERT INTO oss_proposalqueue(id, date_entered,date_modified, ' . implode ( ",", array_keys ( $arFieldMap ) ) . ')
					VALUES(UUID(),NOW(),NOW(),"' . implode ( '","', array_values ( $arFieldMap ) ) . '" )';
					
					// add new row for schedule
					$cdb->query ( $stScheduleSql );
					
					
					
				}
					
										
			}
			
			//set retrun value
			if(isset($focus->do_not_redirect))
			{
			    return $bReturn; 
			}
		
	}
	
	/**
	 * Added by Ashutosh to modify the
	 * layout option
	 */
	function setLayoutOptions(&$focus){
	    /**
	     * radio button named  description_placement will also be available on the 
	     * Edit view we have to change these options from edit view only 
	     * hence the condition added below
	     */
	    if(isset($_REQUEST['description_placement']) &&  $_REQUEST['description_placement'] != ''){
	        
	        $arLayoutOptions =  array( 
	            'line_items' => isset($_REQUEST['line_items'])?1:0,
		        'line_itmes_subtotal' => isset($_REQUEST['line_itmes_subtotal'])?1:0,
		        'inclusions' =>isset($_REQUEST['inclusions'])?1:0,
		        'inclusion_subtotal' => isset($_REQUEST['inclusion_subtotal'])?1:0,
		        'exclusions' => isset($_REQUEST['exclusions'])?1:0,
		        'exclusions_total' => isset($_REQUEST['exclusions_total'])?1:0,
		        'exclusions_tax' => isset($_REQUEST['exclusions_tax'])?1:0,
		        'exclusions_shipping' => isset($_REQUEST['exclusions_shipping'])?1:0,
		        'exclusions_grand_total' => isset($_REQUEST['exclusions_grand_total'])?1:0,
		        'alternates' => isset($_REQUEST['alternates'])?1:0,
		        'description_panel' => isset($_REQUEST['description_panel'])?1:0,
		        'description_placement' => isset($_REQUEST['description_placement'])?$_REQUEST['description_placement']:'bottom',
		    );
	    
	        $focus->layout_options = base64_encode( serialize($arLayoutOptions));
	    }
	}
	
	/**
     * Delete Quickbooks Id from Quotes
     * @author Shashank Verma
     * @date 14-10-2014
     */ 
	function deleteQuickbooksId(&$bean, $event, $arguments){
		
		if($bean->quickbooks_id != '' || $bean->quickbooks_invoice_id != ''){
			$bean->quickbooks_id = '';
			$bean->quickbooks_invoice_id = '';
			$bean->save();
		}
	}
}

?>
