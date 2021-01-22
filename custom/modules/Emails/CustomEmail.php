<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomEmail
 *
 * @author satishkumargupta
 */
require_once 'modules/Emails/Email.php';

class CustomEmail extends Email {

    function Email() {
        parent::Email();
    }

    function email2init() {
        require_once('custom/modules/Emails/CustomEmailUI.php');
        $this->et = new CustomEmailUI();
    }

    
    /**
     * Presaves one attachment for new email 2.0 spec
     * DOES NOT CREATE A NOTE
     * @return string ID of note associated with the attachment
     */
    public function email2saveAttachment()
    {
    	$email_uploads = "modules/Emails/{$GLOBALS['current_user']->id}";
    	
    	
    	/**************File Upload Limit Check ***********************/
    	require_once ('custom/modules/Users/filters/instancePackage.class.php');
    	require_once ('custom/include/common_functions.php');
    	
    	
    	global $app_list_strings, $db, $current_user, $timedate, $sugar_config, $app_strings;
    	 
    	$admin=new Administration();
    	$admin_settings = $admin->retrieveSettings('instance', true);
    	$geo_filter = $admin->settings ['instance_geo_filter'];
    	 
    	$obPackage = new instancePackage ();
    	$pkgDetails = $obPackage->getPacakgeDetails();
    	
    	$upload_field = 'email_attachment';
    	 
    	$current_upload_directory_size = getDirectorySize('upload/');
    	$current_file_size = $_FILES[$upload_field]['size'];
    	
    	if( ($current_upload_directory_size + $current_file_size) > $pkgDetails['upload_limit'] ){
    		$GLOBALS['log']->fatal($app_strings['LBL_NOT_ENOUGH_SPACE']);
    		$error = $app_strings['LBL_NOT_ENOUGH_SPACE'];
    		echo "<script type='text/javascript'>alert('$error')</script>";
    		return array();
    	}
    	/**************File Upload Limit Check ***********************/
    	
    	$upload = new UploadFile('email_attachment');
    	if(!$upload->confirm_upload()) {
    		$err = $upload->get_upload_error();
    		if($err) {
    			$GLOBALS['log']->error("Email Attachment could not be attached due to error: $err");
    		}
    		return array();
    	}
    
    	$guid = create_guid();
    	$fileName = $upload->create_stored_filename();
    	$GLOBALS['log']->debug("Email Attachment [$fileName]");
    	if($upload->final_move($guid)) {
    		copy("upload://$guid", sugar_cached("$email_uploads/$guid"));
    		return array(
    				'guid' => $guid,
    				'name' => $GLOBALS['db']->quote($fileName),
    				'nameForDisplay' => $fileName
    		);
    	} else {
    		$GLOBALS['log']->debug("Email Attachment [$fileName] could not be moved to upload dir");
    		return array();
    	}
    }
    
    
    function email2Send($request) {

        global $mod_strings;
        global $app_strings;
        global $current_user;
        global $sugar_config;
        global $locale;
        global $timedate;
        global $beanList;
        global $beanFiles;
        $OBCharset = $locale->getPrecedentPreference('default_email_charset');

        /*         * ********************************************************************
         * Sugar Email PREP
         */
        /* preset GUID */

        $orignialId = "";
        if (!empty($this->id)) {
            $orignialId = $this->id;
        } // if

        if (empty($this->id)) {
            $this->id = create_guid();
            $this->new_with_id = true;
        }

        /* satisfy basic HTML email requirements */
        $this->name = $request['sendSubject'];
        $this->description_html = '&lt;html&gt;&lt;body&gt;' . $request['sendDescription'] . '&lt;/body&gt;&lt;/html&gt;';

        /*         * ********************************************************************
         * PHPMAILER PREP
         */
        $mail = new SugarPHPMailer();
        $mail = $this->setMailer($mail, '', $_REQUEST['fromAccount']);
        if (empty($mail->Host) && !$this->isDraftEmail($request)) {
            $this->status = 'send_error';

            if ($mail->oe->type == 'system')
                echo($app_strings['LBL_EMAIL_ERROR_PREPEND'] . $app_strings['LBL_EMAIL_INVALID_SYSTEM_OUTBOUND']);
            else
                echo($app_strings['LBL_EMAIL_ERROR_PREPEND'] . $app_strings['LBL_EMAIL_INVALID_PERSONAL_OUTBOUND']);

            return false;
        }

        $subject = $this->name;
        $mail->Subject = from_html($this->name);

        // work-around legacy code in SugarPHPMailer
        if ($_REQUEST['setEditor'] == 1) {
            $_REQUEST['description_html'] = $_REQUEST['sendDescription'];
            $this->description_html = $_REQUEST['description_html'];
        } else {
            $this->description_html = '';
            $this->description = $_REQUEST['sendDescription'];
        }
        // end work-around

        if ($this->isDraftEmail($request)) {
            if ($this->type != 'draft' && $this->status != 'draft') {
                $this->id = create_guid();
                $this->new_with_id = true;
                $this->date_entered = "";
            } // if
            $q1 = "update emails_email_addr_rel set deleted = 1 WHERE email_id = '{$this->id}'";
            $r1 = $this->db->query($q1);
        } // if

        if (isset($request['saveDraft'])) {
            $this->type = 'draft';
            $this->status = 'draft';
            $forceSave = true;
        } else {
            /* Apply Email Templates */
            // do not parse email templates if the email is being saved as draft....
            $toAddresses = $this->email2ParseAddresses($_REQUEST['sendTo']);
            $sea = new SugarEmailAddress();
            $object_arr = array();
            
            /**
             * add an extra parent type Opportunities for email template parsing
             * @modified by Mohit Kumar Gupta
             * @date 11-06-2014           
             */
            if (isset($_REQUEST['parent_type']) && !empty($_REQUEST['parent_type']) &&
                    isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id']) &&
                    ($_REQUEST['parent_type'] == 'Accounts' ||
                    $_REQUEST['parent_type'] == 'Contacts' ||
                    $_REQUEST['parent_type'] == 'Leads' ||
                    $_REQUEST['parent_type'] == 'Users' ||
                    $_REQUEST['parent_type'] == 'Opportunities' ||
                    $_REQUEST['parent_type'] == 'Quotes')) {
                if (isset($beanList[$_REQUEST['parent_type']]) && !empty($beanList[$_REQUEST['parent_type']])) {
                    $className = $beanList[$_REQUEST['parent_type']];
                    if (isset($beanFiles[$className]) && !empty($beanFiles[$className])) {
                        if (!class_exists($className)) {
                            require_once($beanFiles[$className]);
                        }
                        $bean = new $className();
                        $bean->retrieve($_REQUEST['parent_id']);
                        $object_arr[$bean->module_dir] = $bean->id;
                    } // if
                } // if
            }
            foreach ($toAddresses as $addrMeta) {
                $addr = $addrMeta['email'];
                $beans = $sea->getBeansByEmailAddress($addr);
                foreach ($beans as $bean) {
                    if (!isset($object_arr[$bean->module_dir])) {
                        $object_arr[$bean->module_dir] = $bean->id;
                    }
                }
            }

            /* template parsing */
            if (empty($object_arr)) {
                $object_arr = array('Contacts' => '123');
            }
            $object_arr['Users'] = $current_user->id;
            
            /**
             * this is hot fix only for Opportunity module, so require file directly
             * change the path of email template to custom email template
             * @modified by Mohit Kumar Gupta
             * @date 11-06-2014
             */
            if (isset($_REQUEST['parent_type']) && !empty($_REQUEST['parent_type']) &&
            isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id']) && $_REQUEST['parent_type'] == 'Opportunities' ){
                require_once 'custom/modules/EmailTemplates/CustomEmailTemplate.php';
                $this->description_html = CustomEmailTemplate::parse_template($this->description_html, $object_arr);
            } else {
                $this->description_html = EmailTemplate::parse_template($this->description_html, $object_arr);
            }            
            $this->name = EmailTemplate::parse_template($this->name, $object_arr);
            $this->description = EmailTemplate::parse_template($this->description, $object_arr);
            $this->description = html_entity_decode($this->description, ENT_COMPAT, 'UTF-8');
            if ($this->type != 'draft' && $this->status != 'draft') {
                $this->id = create_guid();
                $this->date_entered = "";
                $this->new_with_id = true;
                $this->type = 'out';
                $this->status = 'sent';
            }
        }

        if (isset($_REQUEST['parent_type']) && empty($_REQUEST['parent_type']) &&
                isset($_REQUEST['parent_id']) && empty($_REQUEST['parent_id'])) {
            $this->parent_id = "";
            $this->parent_type = "";
        } // if


        $mail->Subject = $this->name;
        $mail = $this->handleBody($mail);
        $mail->Subject = $this->name;
        $this->description_html = from_html($this->description_html);
        $this->description_html = $this->decodeDuringSend($this->description_html);
        $this->description = $this->decodeDuringSend($this->description);

        /* from account */
        $replyToAddress = $current_user->emailAddress->getReplyToAddress($current_user);
        $replyToName = "";
        if (empty($request['fromAccount'])) {
            $defaults = $current_user->getPreferredEmail();
            $mail->From = $defaults['email'];
            $mail->FromName = $defaults['name'];
            $replyToName = $mail->FromName;
            //$replyToAddress = $current_user->emailAddress->getReplyToAddress($current_user);
        } else {
            // passed -> user -> system default
            $ie = new InboundEmail();
            $ie->retrieve($request['fromAccount']);
            $storedOptions = unserialize(base64_decode($ie->stored_options));
            $fromName = "";
            $fromAddress = "";
            $replyToName = "";
            //$replyToAddress = "";
            if (!empty($storedOptions)) {
                $fromAddress = $storedOptions['from_addr'];
                $fromName = from_html($storedOptions['from_name']);
                $replyToAddress = (isset($storedOptions['reply_to_addr']) ? $storedOptions['reply_to_addr'] : "");
                $replyToName = (isset($storedOptions['reply_to_name']) ? from_html($storedOptions['reply_to_name']) : "");
            } // if
            $defaults = $current_user->getPreferredEmail();
            // Personal Account doesn't have reply To Name and Reply To Address. So add those columns on UI
            // After adding remove below code
            // code to remove
            if ($ie->is_personal) {
                if (empty($replyToAddress)) {
                    $replyToAddress = $current_user->emailAddress->getReplyToAddress($current_user);
                } // if
                if (empty($replyToName)) {
                    $replyToName = $defaults['name'];
                } // if
                //Personal accounts can have a reply_address, which should
                //overwrite the users set default.
                if (!empty($storedOptions['reply_to_addr']))
                    $replyToAddress = $storedOptions['reply_to_addr'];
            }
            // end of code to remove
            $mail->From = (!empty($fromAddress)) ? $fromAddress : $defaults['email'];
            $mail->FromName = (!empty($fromName)) ? $fromName : $defaults['name'];
            $replyToName = (!empty($replyToName)) ? $replyToName : $mail->FromName;
        }

        $mail->Sender = $mail->From; /* set Return-Path field in header to reduce spam score in emails sent via Sugar's Email module */


        if (!empty($replyToAddress)) {
            $mail->AddReplyTo($replyToAddress, $locale->translateCharsetMIME(trim($replyToName), 'UTF-8', $OBCharset));
        } else {
            $mail->AddReplyTo($mail->From, $locale->translateCharsetMIME(trim($mail->FromName), 'UTF-8', $OBCharset));
        } // else
        $emailAddressCollection = array(); // used in linking to beans below
        // handle to/cc/bcc
        foreach ($this->email2ParseAddresses($request['sendTo']) as $addr_arr) {
            if (empty($addr_arr['email']))
                continue;

            if (empty($addr_arr['display'])) {
                $mail->AddAddress($addr_arr['email'], "");
            } else {
                $mail->AddAddress($addr_arr['email'], $locale->translateCharsetMIME(trim($addr_arr['display']), 'UTF-8', $OBCharset));
            }
            $emailAddressCollection[] = $addr_arr['email'];
        }
        foreach ($this->email2ParseAddresses($request['sendCc']) as $addr_arr) {
            if (empty($addr_arr['email']))
                continue;

            if (empty($addr_arr['display'])) {
                $mail->AddCC($addr_arr['email'], "");
            } else {
                $mail->AddCC($addr_arr['email'], $locale->translateCharsetMIME(trim($addr_arr['display']), 'UTF-8', $OBCharset));
            }
            $emailAddressCollection[] = $addr_arr['email'];
        }

        foreach ($this->email2ParseAddresses($request['sendBcc']) as $addr_arr) {
            if (empty($addr_arr['email']))
                continue;

            if (empty($addr_arr['display'])) {
                $mail->AddBCC($addr_arr['email'], "");
            } else {
                $mail->AddBCC($addr_arr['email'], $locale->translateCharsetMIME(trim($addr_arr['display']), 'UTF-8', $OBCharset));
            }
            $emailAddressCollection[] = $addr_arr['email'];
        }


        /* parse remove attachments array */
        $removeAttachments = array();
        if (!empty($request['templateAttachmentsRemove'])) {
            $exRemove = explode("::", $request['templateAttachmentsRemove']);

            foreach ($exRemove as $file) {
                $removeAttachments = substr($file, 0, 36);
            }
        }

        /* handle attachments */
        if (!empty($request['attachments'])) {
            $exAttachments = explode("::", $request['attachments']);

            foreach ($exAttachments as $file) {
                $file = trim(from_html($file));
                $file = str_replace("\\", "", $file);
                if (!empty($file)) {
                    //$fileLocation = $this->et->userCacheDir."/{$file}";
                    $fileGUID = substr($file, 0, 36);
                    $fileLocation = $this->et->userCacheDir . "/{$fileGUID}";
                    $filename = substr($file, 36, strlen($file)); // strip GUID	for PHPMailer class to name outbound file

                    $mail->AddAttachment($fileLocation, $filename, 'base64', $this->email2GetMime($fileLocation));
                    //$mail->AddAttachment($fileLocation, $filename, 'base64');
                    // only save attachments if we're archiving or drafting
                    if ((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
                        $note = new Note();
                        $note->id = create_guid();
                        $note->new_with_id = true; // duplicating the note with files
                        $note->parent_id = $this->id;
                        $note->parent_type = $this->module_dir;
                        $note->name = $filename;
                        $note->filename = $filename;
                        $noteFile = "{$sugar_config['upload_dir']}{$note->id}";
                        $note->file_mime_type = $this->email2GetMime($fileLocation);
                        // $note->team_id = (isset($_REQUEST['primaryteam']) ? $_REQUEST['primaryteam'] : $current_user->getPrivateTeamID());
                        // $noteTeamSet = new TeamSet();
                        // $noteteamIdsArray = (isset($_REQUEST['teamIds']) ? explode(",", $_REQUEST['teamIds']) : array($current_user->getPrivateTeamID()));
                        // $note->team_set_id = $noteTeamSet->addTeams($noteteamIdsArray);

                        if (!copy($fileLocation, $noteFile)) {
                            $GLOBALS['log']->debug("EMAIL 2.0: could not copy attachment file to cache/upload [ {$fileLocation} ]");
                        }

                        $note->save();
                    }
                }
            }
        }

        /* handle sugar documents */
        if (!empty($request['documents'])) {
            $exDocs = explode("::", $request['documents']);




            foreach ($exDocs as $docId) {
                $docId = trim($docId);
                if (!empty($docId)) {
                    $doc = new Document();
                    $docRev = new DocumentRevision();
                    $doc->retrieve($docId);
                    $docRev->retrieve($doc->document_revision_id);

                    $filename = $docRev->filename;
                    $fileLocation = "{$sugar_config['upload_dir']}{$docRev->id}";
                    $mime_type = $docRev->file_mime_type;
                    $mail->AddAttachment($fileLocation, $locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $mime_type);

                    // only save attachments if we're archiving or drafting
                    if ((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
                        $note = new Note();
                        $note->id = create_guid();
                        $note->new_with_id = true; // duplicating the note with files
                        $note->parent_id = $this->id;
                        $note->parent_type = $this->module_dir;
                        $note->name = $filename;
                        $note->filename = $filename;
                        $note->file_mime_type = $mime_type;
                        // $note->team_id = $this->team_id;
                        // $note->team_set_id = $this->team_set_id;
                        $noteFile = "{$sugar_config['upload_dir']}{$note->id}";

                        if (!copy($fileLocation, $noteFile)) {
                            $GLOBALS['log']->debug("EMAIL 2.0: could not copy SugarDocument revision file to {$sugar_config['upload_dir']} [ {$fileLocation} ]");
                        }

                        $note->save();
                    }
                }
            }
        }

        /* handle template attachments */
        if (!empty($request['templateAttachments'])) {

            $exNotes = explode("::", $request['templateAttachments']);
            foreach ($exNotes as $noteId) {
                $noteId = trim($noteId);
                if (!empty($noteId)) {
                    $note = new Note();
                    $note->retrieve($noteId);
                    if (!empty($note->id)) {
                        $filename = $note->filename;
                        $fileLocation = "{$sugar_config['upload_dir']}{$note->id}";
                        $mime_type = $note->file_mime_type;
                        if (!$note->embed_flag) {
                            $mail->AddAttachment($fileLocation, $filename, 'base64', $mime_type);
                            // only save attachments if we're archiving or drafting
                            if ((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {

                                if ($note->parent_id != $this->id)
                                    $this->saveTempNoteAttachments($filename, $fileLocation, $mime_type);
                            } // if
                        } // if
                    } else {
                        //$fileLocation = $this->et->userCacheDir."/{$file}";
                        $fileGUID = substr($noteId, 0, 36);
                        $fileLocation = $this->et->userCacheDir . "/{$fileGUID}";
                        //$fileLocation = $this->et->userCacheDir."/{$noteId}";
                        $filename = substr($noteId, 36, strlen($noteId)); // strip GUID	for PHPMailer class to name outbound file

                        $mail->AddAttachment($fileLocation, $locale->translateCharsetMIME(trim($filename), 'UTF-8', $OBCharset), 'base64', $this->email2GetMime($fileLocation));

                        //If we are saving an email we were going to forward we need to save the attachments as well.
                        if ((($this->type == 'draft') && !empty($this->id))
                                || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
                            $mimeType = $this->email2GetMime($fileLocation);
                            $this->saveTempNoteAttachments($filename, $fileLocation, $mimeType);
                        } // if
                    }
                }
            }
        }



        /*         * ********************************************************************
         * Final Touches
         */
        /* save email to sugar? */
        $forceSave = false;

        if ($this->type == 'draft' && !isset($request['saveDraft'])) {
            // sending a draft email
            $this->type = 'out';
            $this->status = 'sent';
            $forceSave = true;
        } elseif (isset($request['saveDraft'])) {
            $this->type = 'draft';
            $this->status = 'draft';
            $forceSave = true;
        }

        /*         * ********************************************************************
         * SEND EMAIL (finally!)
         */
        $mailSent = false;
        if ($this->type != 'draft') {
            $mail->prepForOutbound();
            $mail->Body = $this->decodeDuringSend($mail->Body);
            $mail->AltBody = $this->decodeDuringSend($mail->AltBody);

            /**
             * @author Satish Gupta Updated on 24-11-2011
             * Send Blank image and proposal id for email tracking
             *
             */
            if ('Quotes' == $_REQUEST['parent_type']) {
                $mail->Body .= "<br><IMG HEIGHT='1' WIDTH='1' src='{$sugar_config['site_url']}/index.php?entryPoint=bimage&identifier={$this->id}'>";
                $mail->AddCustomHeader('X-OpprTrackID:' . $this->id);
            }
            /* Changes End */
            //print_r($mail->Body); die('Show Mail Body');
            if (!$mail->Send()) {
                $this->status = 'send_error';
                ob_clean();
                echo($app_strings['LBL_EMAIL_ERROR_PREPEND'] . $mail->ErrorInfo);
                return false;
            }
        }

        if ((!(empty($orignialId) || isset($request['saveDraft']) || ($this->type == 'draft' && $this->status == 'draft'))) &&
                (($_REQUEST['composeType'] == 'reply') || ($_REQUEST['composeType'] == 'replyAll') || ($_REQUEST['composeType'] == 'replyCase')) && ($orignialId != $this->id)) {
            $originalEmail = new Email();
            $originalEmail->retrieve($orignialId);
            $originalEmail->reply_to_status = 1;
            $originalEmail->save();
            $this->reply_to_status = 0;
        } // if

        if ($_REQUEST['composeType'] == 'reply' || $_REQUEST['composeType'] == 'replyCase') {
            if (isset($_REQUEST['ieId']) && isset($_REQUEST['mbox'])) {
                $emailFromIe = new InboundEmail();
                $emailFromIe->retrieve($_REQUEST['ieId']);
                $emailFromIe->mailbox = $_REQUEST['mbox'];
                if (isset($emailFromIe->id) && $emailFromIe->is_personal) {
                    if ($emailFromIe->isPop3Protocol()) {
                        $emailFromIe->mark_answered($this->uid, 'pop3');
                    } elseif ($emailFromIe->connectMailserver() == 'true') {
                        $emailFromIe->markEmails($this->uid, 'answered');
                        $emailFromIe->mark_answered($this->uid);
                    }
                }
            }
        }


        if ($forceSave ||
                $this->type == 'draft' ||
                (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {

            // saving a draft OR saving a sent email
            $decodedFromName = mb_decode_mimeheader($mail->FromName);
            $this->from_addr = "{$decodedFromName} <{$mail->From}>";
            $this->from_addr_name = $this->from_addr;
            $this->to_addrs = $_REQUEST['sendTo'];
            $this->to_addrs_names = $_REQUEST['sendTo'];
            $this->cc_addrs = $_REQUEST['sendCc'];
            $this->cc_addrs_names = $_REQUEST['sendCc'];
            $this->bcc_addrs = $_REQUEST['sendBcc'];
            $this->bcc_addrs_names = $_REQUEST['sendBcc'];
            // $this->team_id = (isset($_REQUEST['primaryteam']) ? $_REQUEST['primaryteam'] : $current_user->getPrivateTeamID());
            // $teamSet = new TeamSet();
            // $teamIdsArray = (isset($_REQUEST['teamIds']) ? explode(",", $_REQUEST['teamIds']) : array($current_user->getPrivateTeamID()));
            // $this->team_set_id = $teamSet->addTeams($teamIdsArray);
            $this->assigned_user_id = $current_user->id;

            $this->date_sent = $timedate->now();
            ///////////////////////////////////////////////////////////////////
            ////	LINK EMAIL TO SUGARBEANS BASED ON EMAIL ADDY

            if (isset($_REQUEST['parent_type']) && !empty($_REQUEST['parent_type']) &&
                    isset($_REQUEST['parent_id']) && !empty($_REQUEST['parent_id'])) {
                $this->parent_id = $_REQUEST['parent_id'];
                $this->parent_type = $_REQUEST['parent_type'];
                $q = "SELECT count(*) c FROM emails_beans WHERE  email_id = '{$this->id}' AND bean_id = '{$_REQUEST['parent_id']}' AND bean_module = '{$_REQUEST['parent_type']}'";
                $r = $this->db->query($q);
                $a = $this->db->fetchByAssoc($r);
                if ($a['c'] <= 0) {
                    if (isset($beanList[$_REQUEST['parent_type']]) && !empty($beanList[$_REQUEST['parent_type']])) {
                        $className = $beanList[$_REQUEST['parent_type']];
                        if (isset($beanFiles[$className]) && !empty($beanFiles[$className])) {
                            if (!class_exists($className)) {
                                require_once($beanFiles[$className]);
                            }
                            $bean = new $className();
                            $bean->retrieve($_REQUEST['parent_id']);
                            if ($bean->load_relationship('emails')) {
                                $bean->emails->add($this->id);
                            } // if
                        } // if
                    } // if
                } // if
            } else {
                if (!class_exists('aCase')) {
                    
                } else {
                    $c = new aCase();
                    if ($caseId = InboundEmail::getCaseIdFromCaseNumber($mail->Subject, $c)) {
                        $c->retrieve($caseId);
                        $c->load_relationship('emails');
                        $c->emails->add($this->id);
                        $this->parent_type = "Cases";
                        $this->parent_id = $caseId;
                    } // if
                }
            } // else
            ////	LINK EMAIL TO SUGARBEANS BASED ON EMAIL ADDY
            ///////////////////////////////////////////////////////////////////
            $this->save();
        }

        if (!empty($request['fromAccount'])) {
            if (isset($ie->id) && !$ie->isPop3Protocol()) {
                $sentFolder = $ie->get_stored_options("sentFolder");
                if (!empty($sentFolder)) {
                    $data = $mail->CreateHeader() . "\r\n" . $mail->CreateBody() . "\r\n";
                    $ie->mailbox = $sentFolder;
                    if ($ie->connectMailserver() == 'true') {
                        $connectString = $ie->getConnectString($ie->getServiceString(), $ie->mailbox);
                        $returnData = imap_append($ie->conn, $connectString, $data, "\\Seen");
                        if (!$returnData) {
                            $GLOBALS['log']->debug("could not copy email to {$ie->mailbox} for {$ie->name}");
                        } // if
                    } else {
                        $GLOBALS['log']->debug("could not connect to mail serve for folder {$ie->mailbox} for {$ie->name}");
                    } // else
                } else {
                    $GLOBALS['log']->debug("could not copy email to {$ie->mailbox} sent folder as its empty");
                } // else
            } // if
        } // if
        return true;
    }
    // end email2send
    
    /**
     * retrieve email data
     * @modifed By Mohit Kumar Gupta 22-08-2014
     * @param string $id
     * @param string $encoded
     * @param string $deleted
     * @return object
     */
    function retrieve($id, $encoded=true, $deleted=true) {
        // cn: bug 11915, return SugarBean's retrieve() call bean instead of $this
        $ret = SugarBean::retrieve($id, $encoded, $deleted);
        if($ret) {
            $ret->retrieveEmailText();
            $ret->raw_source = SugarCleaner::cleanHtml($ret->raw_source);
            $ret->description = to_html($ret->description);
            $ret->description_html = $ret->description_html;
            $ret->retrieveEmailAddresses();
    
            $ret->date_start = '';
            $ret->time_start = '';
            $dateSent = explode(' ', $ret->date_sent);
            if (!empty($dateSent)) {
                $ret->date_start = $dateSent[0];
                if ( isset($dateSent[1]) )
                    $ret->time_start = $dateSent[1];
            }
            // for Email 2.0
            foreach($ret as $k => $v) {
                $this->$k = $v;
            }
        }
        return $ret;
    }//end of retrieve
}

?>
