<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

if (!empty($_REQUEST['identifier'])) {
    global $timedate;
    $pt = new oss_ProposalTracker();
    $pt->retrieve_by_string_fields(array('email_module_id' => $_REQUEST['identifier']));
    if (empty($pt->id)) {
        global $db;
        $emailSql = "SELECT `to_addrs` FROM emails_text WHERE `email_id` = '" . $_REQUEST['identifier'] . "' AND deleted=0";
        $emailQuery = $db->query($emailSql);
        $emailResult = $db->fetchByAssoc($emailQuery);
        $emailSql1 = "SELECT `name`, `parent_id` FROM emails WHERE `id` = '".$_REQUEST['identifier']."' AND deleted=0";
        $emailQuery1 = $db->query($emailSql1);
        $emailResult1 = $db->fetchByAssoc($emailQuery1);
        $pt->email_module_id = $_REQUEST['identifier'];
        $pt->name = $emailResult['to_addrs'];
        $pt->email_subject = $emailResult1['name'];
        $pt->first_viewed = $timedate->nowDb();
        $pt->last_viewed = $timedate->nowDb();
        $pt->hits = 1;
        $pt->status = 'Viewed';
        $pt->proposal_id = $emailResult1['parent_id'];
        $pt->save();
        //$pt->load_relationship('opportunitiroposaltracker');
        //$pt->opportunitiroposaltracker->add($emailResult1['parent_id']);
        $pt->load_relationship('emails_proposaltracker');
        $pt->emails_proposaltracker->add($_REQUEST['identifier']);
    } else {
        $pt->retrieve();
        $pt->last_viewed = $timedate->nowDb();
        $pt->hits = $pt->hits + 1;
        $pt->save();
    }
}

sugar_cleanup();
Header("Content-Type: image/gif");
$fn = sugar_fopen(SugarThemeRegistry::current()->getImageURL("blank.gif", false), "r");
fpassthru($fn);
?>
