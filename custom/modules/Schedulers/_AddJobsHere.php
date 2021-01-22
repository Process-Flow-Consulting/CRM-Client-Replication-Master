<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of _AddJobsHere
 */
$job_strings[] = 'pollMonitoredInboxesForBouncedOpportunityEmails';
$job_strings[] = 'pollMonitoredInboxesUpdated';
$job_strings[] = 'getClientUpdate';
$job_strings[] = 'updateProposalScheduler';
//$job_strings[] = 'updateNonBBClient';
$job_strings[] = 'updateNonBBClientContact';
// $job_strings[] = 'customProcessQueue';
// $job_strings[] = 'pruneDatabaseCustomized';
$job_strings[] = 'updateTrackerSessions';


function customProcessQueue() {
    include_once('custom/include/process_queue.php');
    return true;
}

function updateTrackerSessions() {
    global $sugar_config, $timedate;
	$GLOBALS['log']->info('----->Scheduler fired job of type updateTrackerSessions()');
	$db = DBManagerFactory::getInstance();
    require_once('include/utils/db_utils.php');
	//Update tracker_sessions to set active flag to false
	$sessionTimeout = db_convert("'".$timedate->getNow()->get("-6 hours")->asDb()."'" ,"datetime");
	$query = "UPDATE tracker_sessions set active = 0 where date_end < $sessionTimeout";
	$GLOBALS['log']->info("----->Scheduler is about to update tracker_sessions table by running the query $query");
	$db->query($query);
	return true;
}


/* Job 10
 *
 */
function pollMonitoredInboxesForBouncedOpportunityEmails() {
	$GLOBALS['log']->info('----->Scheduler job of type pollMonitoredInboxesForBouncedOpportunityEmails()');
	global $dictionary;


        require_once 'custom/modules/InboundEmail/CustomInboundEmail.php';

        $ie = new CustomInboundEmail();
	$r = $ie->db->query('SELECT id FROM inbound_email WHERE deleted=0 AND status=\'Active\' AND mailbox_type=\'bounce\'');

	while($a = $ie->db->fetchByAssoc($r)) {
		$ieX = new CustomInboundEmail();
		$ieX->retrieve($a['id']);
		$ieX->connectMailserver();
        $GLOBALS['log']->info("Bounced Opportunity scheduler connected to mail server id: {$a['id']} ");
		$newMsgs = array();
		if ($ieX->isPop3Protocol()) {
			$newMsgs = $ieX->getPop3NewMessagesToDownload();
		} else {
			$newMsgs = $ieX->getNewMessageIds();
		}
		//$newMsgs = $ieX->getNewMessageIds();
		if(is_array($newMsgs)) {
			foreach($newMsgs as $k => $msgNo) {
				$uid = $msgNo;
				if ($ieX->isPop3Protocol()) {
					$uid = $ieX->getUIDLForMessage($msgNo);
				} else {
					$uid = imap_uid($ieX->conn, $msgNo);
				} // else
                 $GLOBALS['log']->info("Bounced Opportunity scheduler will import message no: $msgNo");
				$ieX->importOneEmail($msgNo, $uid, false,false);
			}
		}
		imap_expunge($ieX->conn);
		imap_close($ieX->conn);
	}

	return true;
}


function pollMonitoredInboxesUpdated() {

	$GLOBALS['log']->info('----->Scheduler fired job of type pollMonitoredInboxes()');
	global $dictionary;
	global $app_strings;


	require_once('modules/Emails/EmailUI.php');
        require_once 'custom/modules/InboundEmail/CustomInboundEmail.php';

	$ie = new CustomInboundEmail();
	$emailUI = new EmailUI();
	$r = $ie->db->query('SELECT id, name FROM inbound_email WHERE  deleted=0 AND status=\'Active\' AND mailbox_type != \'bounce\'');
	$GLOBALS['log']->debug('Just got Result from get all Inbounds of Inbound Emails');

	while($a = $ie->db->fetchByAssoc($r)) {
		$GLOBALS['log']->debug('In while loop of Inbound Emails');
		$ieX = new CustomInboundEmail();
		$ieX->retrieve($a['id']);
		$mailboxes = $ieX->mailboxarray;
		foreach($mailboxes as $mbox) {
			$ieX->mailbox = $mbox;
			$newMsgs = array();
			$msgNoToUIDL = array();
			$connectToMailServer = false;
			if ($ieX->isPop3Protocol()) {
				$msgNoToUIDL = $ieX->getPop3NewMessagesToDownloadForCron();
				// get all the keys which are msgnos;
				$newMsgs = array_keys($msgNoToUIDL);
			}
			if($ieX->connectMailserver() == 'true') {
				$connectToMailServer = true;
			} // if

			$GLOBALS['log']->debug('Trying to connect to mailserver for [ '.$a['name'].' ]');
			if($connectToMailServer) {
				$GLOBALS['log']->debug('Connected to mailserver');
				if (!$ieX->isPop3Protocol()) {
					$newMsgs = $ieX->getNewMessageIds();
				}
				if(is_array($newMsgs)) {
					$current = 1;
					$total = count($newMsgs);
					require_once("include/SugarFolders/SugarFolders.php");
					$sugarFolder = new SugarFolder();
					$groupFolderId = $ieX->groupfolder_id;
					$isGroupFolderExists = false;
					$users = array();
					if ($groupFolderId != null && $groupFolderId != "") {
						$sugarFolder->retrieve($groupFolderId);
						$isGroupFolderExists = true;
						// $_REQUEST['team_id'] = $sugarFolder->team_id;
						// $_REQUEST['team_set_id'] = $sugarFolder->team_set_id;
					} // if
					$messagesToDelete = array();
					if ($ieX->isMailBoxTypeCreateCase()) {
						$users[] = $sugarFolder->assign_to_id;
						/* require_once("modules/Teams/TeamSet.php");
						require_once("modules/Teams/Team.php");
						$GLOBALS['log']->debug('Getting users for teamset');
						$teamSet = new TeamSet();
						$usersList = $teamSet->getTeamSetUsers($sugarFolder->team_set_id, true);
						$GLOBALS['log']->debug('Done Getting users for teamset');
						$users = array();
						foreach($usersList as $userObject) {
							if ($userObject->is_group) {
								continue;
							} // if
							$users[] = $userObject->id;
						} // foreach */

						$distributionMethod = $ieX->get_stored_options("distrib_method", "");
						if ($distributionMethod != 'roundRobin') {
							$counts = $emailUI->getAssignedEmailsCountForUsers($users);
						} else {
							$lastRobin = $emailUI->getLastRobin($ieX);
						}
						$GLOBALS['log']->debug('distribution method id [ '.$distributionMethod.' ]');
					}
					foreach($newMsgs as $k => $msgNo) {
						$uid = $msgNo;
						if ($ieX->isPop3Protocol()) {
							$uid = $msgNoToUIDL[$msgNo];
						} else {
							$uid = imap_uid($ieX->conn, $msgNo);
						} // else
						if ($isGroupFolderExists) {
							// $_REQUEST['team_id'] = $sugarFolder->team_id;
							// $_REQUEST['team_set_id'] = $sugarFolder->team_set_id;
							if ($ieX->importOneEmail($msgNo, $uid)) {
								// add to folder
								$sugarFolder->addBean($ieX->email);
								if ($ieX->isPop3Protocol()) {
									$messagesToDelete[] = $msgNo;
								} else {
									$messagesToDelete[] = $uid;
								}
								if ($ieX->isMailBoxTypeCreateCase()) {
									$userId = "";
									if ($distributionMethod == 'roundRobin') {
										if (sizeof($users) == 1) {
											$userId = $users[0];
											$lastRobin = $users[0];
										} else {
											$userIdsKeys = array_flip($users); // now keys are values
											$thisRobinKey = $userIdsKeys[$lastRobin] + 1;
											if(!empty($users[$thisRobinKey])) {
												$userId = $users[$thisRobinKey];
												$lastRobin = $users[$thisRobinKey];
											} else {
												$userId = $users[0];
												$lastRobin = $users[0];
											}
										} // else
									} else {
										if (sizeof($users) == 1) {
											foreach($users as $k => $value) {
												$userId = $value;
											} // foreach
										} else {
											asort($counts); // lowest to highest
											$countsKeys = array_flip($counts); // keys now the 'count of items'
											$leastBusy = array_shift($countsKeys); // user id of lowest item count
											$userId = $leastBusy;
											$counts[$leastBusy] = $counts[$leastBusy] + 1;
										}
									} // else
									$GLOBALS['log']->debug('userId [ '.$userId.' ]');
									$ieX->handleCreateCase($ieX->email, $userId);
								} // if
							} // if
						} else {
								if($ieX->isAutoImport()) {
									$ieX->importOneEmail($msgNo, $uid);
								} else {
									/*If the group folder doesn't exist then download only those messages
									 which has caseid in message*/
									$ieX->getMessagesInEmailCache($msgNo, $uid);
									$email = new Email();
									$header = imap_headerinfo($ieX->conn, $msgNo);
									$email->name = $ieX->handleMimeHeaderDecode($header->subject);
									$email->from_addr = $ieX->convertImapToSugarEmailAddress($header->from);
									$email->reply_to_email  = $ieX->convertImapToSugarEmailAddress($header->reply_to);
									if(!empty($email->reply_to_email)) {
										$contactAddr = $email->reply_to_email;
									} else {
										$contactAddr = $email->from_addr;
									}
									$mailBoxType = $ieX->mailbox_type;
									if (($mailBoxType == 'support') || ($mailBoxType == 'pick')) {
										if(!class_exists('aCase')) {

										}
										$c = new aCase();
										$GLOBALS['log']->debug('looking for a case for '.$email->name);
										if ($ieX->getCaseIdFromCaseNumber($email->name, $c)) {
											$ieX->importOneEmail($msgNo, $uid);
										} else {
											$ieX->handleAutoresponse($email, $contactAddr);
										} // else
									} else {
										$ieX->handleAutoresponse($email, $contactAddr);
									} // else
								} // else
						} // else
						$GLOBALS['log']->debug('***** On message [ '.$current.' of '.$total.' ] *****');
						$current++;
					} // foreach
					// update Inbound Account with last robin
					if ($ieX->isMailBoxTypeCreateCase() && $distributionMethod == 'roundRobin') {
						$emailUI->setLastRobin($ieX, $lastRobin);
					} // if

				} // if
				if ($isGroupFolderExists)	 {
					$leaveMessagesOnMailServer = $ieX->get_stored_options("leaveMessagesOnMailServer", 0);
					if (!$leaveMessagesOnMailServer) {
						if ($ieX->isPop3Protocol()) {
							$ieX->deleteMessageOnMailServerForPop3(implode(",", $messagesToDelete));
						} else {
							$ieX->deleteMessageOnMailServer(implode($app_strings['LBL_EMAIL_DELIMITER'], $messagesToDelete));
						}
					}
				}
			} else {
				$GLOBALS['log']->fatal("SCHEDULERS: could not get an IMAP connection resource for ID [ {$a['id']} ]. Skipping mailbox [ {$a['name']} ].");
				// cn: bug 9171 - continue while
			} // else
		} // foreach
		imap_expunge($ieX->conn);
		imap_close($ieX->conn, CL_EXPUNGE);
	} // while

	return true;
}


/*
 * Get Client Update from BB HUb
 * 
 */

function getClientUpdate(){
	
	$GLOBALS['log']->info('----->Scheduler job of type getClientUpdate()');
	
	global $timedate, $db;
	
	$api_url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetmyvendor_update.p?sugarcrm_account=";
	
	require_once 'modules/Administration/Administration.php';
	$obAdmin = new Administration ();
	$obAdmin->disable_row_level_security = true;
	$arAdminData = $obAdmin->retrieveSettings ( 'instance', true );
	$account_no = $arAdminData->settings['instance_account_name'];
	
	$api_url .= $account_no; 
	
	$GLOBALS['log']->fatal('----->Client Update URL: '.$api_url);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000000000);
	curl_setopt($ch, CURLOPT_TIMEOUT ,3000000000);
	$output = curl_exec($ch);
	curl_close($ch);
	
	//to test from file
	//$file = "/var/www/bluebook/customer/test.txt";
	//$output = file_get_contents($file);
	
	//$GLOBALS['log']->fatal('----->Client Update Json: '.$output);
	
	$output = json_decode($output);
	
	if($output->response->status == 'success'){
	    
	    $clientBbIdArr = array();
		foreach($output->response->Clients as $client){		

			/**
			 * Set the flag for update comes from bluebook instead of update the client.
			 * change the logic of single record update to one time update for all records by Mohit Kumar Gupta 05-10-2015
			 * Updated by Satish Gupta on 16-01-2013
			 */
			if(isset($client->client_bb_id) && trim($client->client_bb_id)!= ''){
			    $clientBbIdArr [] = $client->client_bb_id;
				/*$client_bb_id = $client->client_bb_id;
				$update_query = "UPDATE accounts SET is_bb_update=1 WHERE mi_account_id='".$client_bb_id."' AND visibility='1' AND deleted=0 AND is_bb_update=0";
				$update = $db->query($update_query);
				$effected_row = $db->getAffectedRowCount($update);				
				if($effected_row){
					$GLOBALS['log']->fatal('----->'.$client_bb_id.' Updated Flag Set On '.$timedate->now());
				}*/
				
			}//end if
			
			//$sql = " SELECT id, visibility FROM  accounts WHERE mi_account_id = '".$client_bb_id."' AND deleted = 0";
			//$result = $db->query($sql);				
			//while($row = $db->fetchByAssoc($result)){
	
				//if($row['visibility'] == 1){					
					
					//require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';
					
					//$pullObj = new PullBBH($client_bb_id);
					//$pullObj->insertUpdateClients();
					//$GLOBALS['log']->fatal('----->'.$client_bb_id.' Updated on '.$timedate->now());
					
					//$update_query = "";
						
				//}
	
			//}
				
		}//end foreach
		
		//change the logic of single record update to one time update for all records by Mohit Kumar Gupta 05-10-2015
	    if (count(array_filter($clientBbIdArr)) > 0) {
			$clientBbIdStr = "('".implode("','", $clientBbIdArr)."')";
			$update_query = "UPDATE accounts SET is_bb_update=1 WHERE mi_account_id IN ".$clientBbIdStr." AND visibility='1' AND deleted=0 AND is_bb_update=0";
			$update = $db->query($update_query);
			$effected_row = $db->getAffectedRowCount($update);
			$GLOBALS['log']->fatal('Updated Flag Query',$update_query);
			if($effected_row){
			    $GLOBALS['log']->fatal('Rows effected by Updated Flag Query = '. $effected_row);
			}
		}
	
	}//end if
	
	return true;
}
/**
 * 
 * @return boolean
 */
function updateProposalScheduler(){
	$GLOBALS['log']->info('----->Scheduler job of type UpdateProposals()');
	require_once 'custom/modules/AOS_Quotes/schedule_quotes/updateProposals.php';
	updateProposals();
	$GLOBALS['log']->info('----->End Scheduler job of type UpdateProposals()');
	return true;
}

/**
 * update if client matches to the bb hub client
 * 
 */
function updateNonBBClient(){
	$GLOBALS['log']->info('----->Scheduler job of type updateNonBBClient()');
	
	require_once 'custom/include/common_functions.php';
	require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';
	
	global $db, $current_user;

	$sugacrm_account = getCurrentInstanceAccountNo();
	
	$getNonBBClientsQuery = " SELECT accounts.name, accounts.id, accounts.phone_office,
				accounts.phone_fax, ea.email_address 	 
				FROM accounts 
				LEFT JOIN email_addr_bean_rel ear 
				ON  ear.bean_id = accounts.id AND  ear.bean_module = 'Accounts'
				AND ear.deleted = 0 AND ear.primary_address = 1
				LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id 
				AND ea.deleted = 0 
				WHERE mi_account_id IS NULL
				ORDER BY accounts.date_entered ";

	
	$getNonBBClientsResult = $db->query($getNonBBClientsQuery);
	
	while ($getNonBBClientsRow =  $db->fetchByAssoc($getNonBBClientsResult)){
		
		$count = 0;
		$api_url = 'http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blsearch_pfe_json.p?searchtype=client';
		$api_url .= '&sugarcrm_account='.$sugacrm_account;
		
		
		if(!empty($getNonBBClientsRow['email_address'])){
			$count = 1;
			$api_url .= '&email='.$getNonBBClientsRow['email_address'];
		}
		if(!empty($getNonBBClientsRow['phone_office'])){
			$count = 1;
			$api_url .= '&phone='.clean_ph_no($getNonBBClientsRow['phone_office']);
		}
		if(!empty($getNonBBClientsRow['phone_fax'])){
			$count = 1;
			$api_url .= '&fax='.clean_ph_no($getNonBBClientsRow['phone_fax']);
		}
		if(!empty($getNonBBClientsRow['name'])){
			$api_url .= '&company='.urlencode(html_entity_decode($getNonBBClientsRow['name']));
		}
				
		//echo '<br>'; echo $api_url; echo '<br>';
		if($count > 0 )
		{
			$GLOBALS['log']->info($api_url);
			
			$api_data_json =  getRemoteData($api_url);
			
			$api_data_array = json_decode($api_data_json);
			
			if( $api_data_array->response->status == 'success' ){
				
				$pullBBH = new PullBBH($sugacrm_account);
				
				$pullBBH->updateExistingNonBBClient($api_data_json, $getNonBBClientsRow['id']);
				
			}
		}
		//print_r($api_data_array); 
		//echo '<br>'; echo '---------------------------------------'; echo '<br>';
		
	}
	
	return true;
}


/**
 * update if client matches to the bb hub client
 *
 */
function updateNonBBClientContact(){
	
	$GLOBALS['log']->info('----->Scheduler job of type updateNonBBClientContact()');

	require_once 'custom/include/common_functions.php';
	require_once 'custom/modules/Leads/pull_project_lead/PullBBH.class.php';

	global $db, $current_user;
	
	$process_path = 'upload/process/';
	$lock_file = $process_path."update_non_bb_lock";
	
	// Check lock file is exists
	if (file_exists($lock_file)) {
		$content = file_get_contents($lock_file);
		if(!empty($content)){
			$current_time = time();
			if( $current_time > ($content+24*60*60)){
				unlink($lock_file);
			}else{
				$GLOBALS['log']->fatal('----->Skip updateNonBBClientContact(). already running.');
				return true;
			}
		}else{
			unlink($lock_file);
		}
	}
	
	$file_pointer = fopen ( $lock_file, "w" );
	fwrite($file_pointer, time());
	fclose($file_pointer);
	

	$sugacrm_account = getCurrentInstanceAccountNo();

	$getNonBBClientContactQuery = " SELECT TRIM(CONCAT_WS(' ', contacts.first_name, contacts.last_name)) name, contacts.id, 
				contacts.phone_work,contacts.phone_fax, ea.email_address
				FROM contacts
				LEFT JOIN email_addr_bean_rel ear
				ON  ear.bean_id = contacts.id AND  ear.bean_module = 'Contacts'
				AND ear.deleted = 0  AND ear.primary_address = 1
				LEFT JOIN email_addresses ea ON ea.id = ear.email_address_id
				AND ea.deleted = 0
				WHERE mi_contact_id IS NULL
				ORDER BY contacts.date_entered";
	

	$getNonBBClientContactResult = $db->query($getNonBBClientContactQuery);

	while ($getNonBBClientContactRow =  $db->fetchByAssoc($getNonBBClientContactResult)){

		$api_url = 'http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blsearch_pfe_json.p?searchtype=contact';
		$api_url .= '&sugarcrm_account='.$sugacrm_account;
		
		$count = 0;
		if(!empty($getNonBBClientContactRow['email_address'])){
			$count = 1;
			$api_url .= '&email='.$getNonBBClientContactRow['email_address'];
		}
		if(!empty($getNonBBClientContactRow['phone_work'])){
			$count = 1;
			$api_url .= '&phone='.clean_ph_no($getNonBBClientContactRow['phone_work']);
		}
		if(!empty($getNonBBClientContactRow['phone_fax'])){
			$count = 1;
			$api_url .= '&fax='.clean_ph_no($getNonBBClientContactRow['phone_fax']);
		}
		if(!empty($getNonBBClientContactRow['name'])){
			$api_url .= '&name='.urlencode(html_entity_decode($getNonBBClientContactRow['name']));
		}

		//echo '<br>'; echo $api_url; echo '<br>';
		
		if($count > 0)
		{
			$GLOBALS['log']->info($api_url);
			
			$api_data_json =  getRemoteData($api_url);
	
			$api_data_array = json_decode($api_data_json);
	
			if( $api_data_array->response->status == 'success' ){
					
				$pullBBH = new PullBBH($sugacrm_account);
					
				$pullBBH->updateExistingNonBBClientContact($api_data_json, $getNonBBClientContactRow['id']);
					
			}

		}
		
		//print_r($api_data_array);
		//echo '<br>'; echo '---------------------------------------'; echo '<br>';
	}
	
	updateNonBBClient();
	
	if (file_exists($lock_file)) {
		unlink($lock_file);
	}
	
	return true;
}

/**
 * @author Mohit kUmar Gupta
 * @date 26-11-2015
 * customize prune database scheduler
 * @return boolean
 */
function pruneDatabaseCustomized() {
    $GLOBALS['log']->info('----->Scheduler fired job of type pruneDatabaseCustomized()');
    $backupDir	= sugar_cached('backups');
    $backupFile	= 'backup-pruneDatabase-GMT0_'.gmdate('Y_m_d-H_i_s', strtotime('now')).'.php';

    $db = DBManagerFactory::getInstance();
    $tables = $db->getTablesArray();

    //_ppd($tables);
    if(!empty($tables)) {
        foreach($tables as $kTable => $table) {
                        
            // find tables with deleted=1
            $columns = $db->get_columns($table);
            // no deleted - won't delete
            if(empty($columns['deleted'])) continue;

            $custom_columns = array();
            if(array_search($table.'_cstm', $tables)) {
                $custom_columns = $db->get_columns($table.'_cstm');
                if(empty($custom_columns['id_c'])) {
                    $custom_columns = array();
                }
            }

            $qDel = "SELECT * FROM $table WHERE deleted = 1";
            
            //Modified by Mohit Kumar Gupta 26-11-2015
            //don't hard delete the data of opportunity module those are pulled from BBHub or created using BBHub lead
            //for the change request of client BSI-793
            if ($table == 'opportunities') {
                    $qDel .= " AND (initial_pulled_user_email IS NULL OR initial_pulled_user_email ='')";
            }
            
            $rDel = $db->query($qDel);
            $queryString = array();
            // make a backup INSERT query if we are deleting.
            while($aDel = $db->fetchByAssoc($rDel, false)) {
                // build column names
                
                //Modified by Mohit Kumar Gupta
                //change the $rDel to $aDel from core file, it should be an array instaed of data set
                $queryString[] = $db->insertParams($table, $columns, $aDel, null, false);

                if(!empty($custom_columns) && !empty($aDel['id'])) {
                    $qDelCstm = 'SELECT * FROM '.$table.'_cstm WHERE id_c = '.$db->quoted($aDel['id']);
                    $rDelCstm = $db->query($qDelCstm);

                    // make a backup INSERT query if we are deleting.
                    while($aDelCstm = $db->fetchByAssoc($rDelCstm)) {
                        $queryString[] = $db->insertParams($table, $custom_columns, $aDelCstm, null, false);
                    } // end aDel while()

                    $db->query('DELETE FROM '.$table.'_cstm WHERE id_c = '.$db->quoted($aDel['id']));
                }
            } // end aDel while()
            // now do the actual delete
            $deleteQuery = 'DELETE FROM '.$table.' WHERE deleted = 1';
            //Modified by Mohit Kumar Gupta 26-11-2015
            //don't hard delete the data of opportunity module those are pulled from BBHub or created using BBHub lead
            //for the change request of client BSI-793
            if ($table == 'opportunities') {
                $deleteQuery .= " AND (initial_pulled_user_email IS NULL OR initial_pulled_user_email ='')";
            }
            $db->query($deleteQuery);
        } // foreach() tables

        if(!file_exists($backupDir) || !file_exists($backupDir.'/'.$backupFile)) {
            // create directory if not existent
            mkdir_recursive($backupDir, false);
        }
        // write cache file

        write_array_to_file('pruneDatabase', $queryString, $backupDir.'/'.$backupFile);
        return true;
    }
    return false;
}
?>
