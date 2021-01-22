<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ****************************************************************************** */

/* * *******************************************************************************

 * Description:
 * ****************************************************************************** */

/**
 * Scan the bounced email searching for a valid target identifier.
 *
 * @param string Email Description
 * @return array Results including matches and identifier
 */
function checkBouncedEmailForIdentifier($email_description) {
    $matches = array();
    $identifiers = array();
    $found = FALSE;
    //Check if the identifier is present in the header.
    if (preg_match('/X-OpprTrackID: [a-z0-9\-]*/', $email_description, $matches)) {
        $identifiers = preg_split('/X-OpprTrackID: /', $matches[0], -1, PREG_SPLIT_NO_EMPTY);
        $found = TRUE;
        $GLOBALS['log']->debug("Found Opportunity identifier in header of email" . $matches[0]);
    }

    return array('found' => $found, 'matches' => $matches, 'identifiers' => $identifiers);
}

function opportunity_process_bounced_emails(&$email, &$email_header) {
    global $sugar_config;
    $emailFromAddress = $email_header->fromaddress;
    $email_description = $email->description;
    //$email_description .= retrieveErrorReportAttachment($email);

    $GLOBALS['log']->debug("opportunity_process_bounced_emails" . $email_header->fromaddress);

    //if (preg_match('/MAILER-DAEMON|POSTMASTER/i', $emailFromAddress)) {
    //$email_description = quoted_printable_decode($email_description);    
    $matches = array();

    //do we have the identifier tag in the email?
    $identifierScanResults = checkBouncedEmailForIdentifier($email_description);

    if ($identifierScanResults['found']) {
        $matches = $identifierScanResults['matches'];
        $identifiers = $identifierScanResults['identifiers'][0];

        if (!empty($identifiers)) {

            $GLOBALS['log']->debug("Identifiers: " . $identifiers);
            global $timedate;
            $pt = new oss_ProposalTracker();
            $pt->retrieve_by_string_fields(array('email_module_id' => $identifiers));
            if (empty($pt->id)) {
                global $db;
                $emailSql = "SELECT `to_addrs` FROM emails_text WHERE `email_id` = '" . $identifiers . "' AND deleted=0";
                $emailQuery = $db->query($emailSql);
                $emailResult = $db->fetchByAssoc($emailQuery);
                $emailSql1 = "SELECT `name`, `parent_id` FROM emails WHERE `id` = '" . $identifiers . "' AND deleted=0";
                $emailQuery1 = $db->query($emailSql1);
                $emailResult1 = $db->fetchByAssoc($emailQuery1);
                $pt->email_module_id = $identifiers;
                $pt->name = $emailResult['to_addrs'];
                $pt->email_subject = $emailResult1['name'];
                $pt->first_viewed = $timedate->nowDb();
                $pt->last_viewed = $timedate->nowDb();
                $pt->hits = 1;
                $pt->status = 'Bounce';
                $pt->save();
                $pt->load_relationship('opportunitiroposaltracker');
                $pt->opportunitiroposaltracker->add($emailResult1['parent_id']);
                $pt->load_relationship('emails_proposaltracker');
                $pt->emails_proposaltracker->add($identifiers);
            }else{
                $pt->retrieve();
                $pt->last_viewed = $timedate->nowDb();
                $pt->hits = $pt->hits + 1;
                $pt->status = 'Bounce';
                $pt->save();
            }
        }
    }
}

?>
