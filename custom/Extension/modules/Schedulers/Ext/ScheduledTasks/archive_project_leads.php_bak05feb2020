<?php
$job_strings[] = 'archiveProjectLeads';

function archiveProjectLeads(){
    
    $GLOBALS['log']->fatal('----->Scheduler fired job of type archiveProjectLeads()');
    global $db;

    $dbTime = $GLOBALS['timedate']->nowDb();
    $dbTimeLastMonth = date('Y-m-d H:i:s',(strtotime($dbTime)- 30*24*60*60 ) );
    //Mohit Kumar Gupta
    //date 25-oct-2013
    //Added project status array
    $projectStatusArr = array(
    		'Awarded - Reported',
    		'General Contract Negotiating',
    		'General Contract Award Pending',
    		'General Contract Awarded',
    		'Prime Contract Awarded',
    		'Sub Contract Negotiating',
    		'Sub Contract Awarded',
    		'Negotiating-Owner',
    		'Awarded-General Contractor',
    		'Negotiating-General Contractor',
    		'Award Pending-General Contractor'
    );
    //find the leads whose bids due was one month back
    $sqlLeads = " SELECT leads.id,leads.project_status,leads.date_modified FROM leads  WHERE ( ( leads.bids_due < '".$dbTimeLastMonth."' AND leads.bids_due != '0000-00-00 00:00:00' )  OR ( (leads.bids_due IS NULL OR leads.bids_due = '0000-00-00 00:00:00') AND leads.date_modified < '".$dbTimeLastMonth."' ) ) AND leads.deleted = 0 AND leads.parent_lead_id = leads.id AND leads.status IN ('New', 'Viewed', 'Dead') ";
    //$GLOBALS['log']->fatal($sqlLeads);
    $resultLeads = $db->query($sqlLeads);
    $count = 0;
    $GLOBALS['log']->fatal('Delete Lead process started');
    while( $rowLeads = $db->fetchByAssoc($resultLeads) ){
    	//Mohit Kumar Gupta
    	//date 25-oct-2013
    	//Added if condition that record should not deleted 
    	//when date_modified is with in 30 days and project status is in project status array.
    	//i.e. both condition must be true
        if (!($rowLeads['date_modified'] >= $dbTimeLastMonth && in_array($rowLeads['project_status'],$projectStatusArr))) {
	        //found if the linked leads has any opportunity
	        $sqlChildOpportunities = "SELECT COUNT(leads.id)c FROM leads INNER JOIN opportunities ON opportunities.project_lead_id = leads.id AND opportunities.deleted=0 WHERE leads.parent_lead_id = '".$rowLeads['id']."' AND leads.deleted = 0";
	        //$GLOBALS['log']->fatal($sqlChildOpportunities);
	        
	        $resultChildOpportunities = $db->query($sqlChildOpportunities);
	        $rowChildOpportunities = $db->fetchByAssoc($resultChildOpportunities);
	        $countChildLead = $rowChildOpportunities['c'];
	        
	        //if no opportunity
	        if($countChildLead < 1){
	            $sqlChildLead = " SELECT id FROM leads WHERE parent_lead_id ='".$rowLeads['id']."' ";
	            //$GLOBALS['log']->fatal($sqlChildLead);
	            $resultChildLead = $db->query($sqlChildLead);
	            while( $rowChildLead = $db->fetchByAssoc($resultChildLead) ){
	                //$GLOBALS['log']->fatal($rowChildLead);
	                deleteLeadsRelatedData($rowChildLead['id']);
	                $deleteLead = "DELETE FROM leads WHERE id ='".$rowChildLead['id']."' ";
	               // $GLOBALS['log']->fatal($deleteLead);
	                $db->query($deleteLead);
	                $count++;
			//$GLOBALS['log']->fatal('Lead deleted = '.$count);
	            }
	        }
        }        
    }
    
    //find the leads deleted by User
    $sqlDeletedLeads = " SELECT id FROM leads WHERE deleted = 1 ";
    $resultDeletedLeads = $db->query($sqlDeletedLeads);
    while($rowDeletedLeads = $db->fetchByAssoc($resultDeletedLeads) ){

        $sqlChildLead = " SELECT id FROM leads WHERE parent_lead_id ='".$rowDeletedLeads['id']."' ";
        $resultChildLead = $db->query($sqlChildLead);
        
        while( $rowChildLead = $db->fetchByAssoc($resultChildLead) ){
            deleteLeadsRelatedData($rowChildLead['id']);
            $deleteLead = "DELETE FROM leads WHERE id ='".$rowChildLead['id']."' ";
            $db->query($deleteLead);
            $count++;
	    //$GLOBALS['log']->fatal('Lead deleted = '.$count);
        }
    }
    deleteAccountsContactsAndRelations();
    $GLOBALS['log']->fatal('Total lead deleted = '.$count);
    $GLOBALS['log']->fatal('Delete Lead process Ended');    
    return true;
}

/**
 * Delete all related activities of a lead
 * @param string $leadId -- parent id
 */
function deleteLeadsRelatedData($leadId){

    if(empty($leadId))
        return true;
    
    global $db;
    
    //delete related Tasks
    $deleteRelatedTasks = " DELETE FROM tasks WHERE parent_id = '".$leadId."'  ";
    $db->query($deleteRelatedTasks);
    
    //delete related Calls
    $deleteRelatedCalls = " DELETE FROM calls WHERE parent_id = '".$leadId."' ";
    $db->query($deleteRelatedCalls);
    
    //delete related Meetings
    $deleteRelatedMeetings = " DELETE FROM meetings WHERE parent_id = '".$leadId."' ";
    $db->query($deleteRelatedMeetings);
    
    //delete related Notes
    $deleteRelatedNotes = " DELETE FROM notes WHERE parent_id = '".$leadId."'  ";
    $db->query($deleteRelatedNotes);
    
    //delete related Emails
    $deleteRelatedEmails = " DELETE FROM emails_beans WHERE bean_id = '".$leadId."' ";
    $db->query($deleteRelatedEmails);
    
    //delete related Online Plans
    $deleteRelatedOnlinePlans = " DELETE FROM oss_onlineplans WHERE lead_id = '".$leadId."' ";
    $db->query($deleteRelatedOnlinePlans);
    
    //delete classification reslationship
    $deleteRelatedClassifications = " DELETE FROM oss_classifcation_leads_c WHERE oss_classi7103dsleads_idb = '".$leadId."' ";
    $db->query($deleteRelatedClassifications);
    
    //delete Related Bidders
    $deleteRelatedBidders = " DELETE FROM oss_leadclientdetail WHERE lead_id ='".$leadId."' ";
    $db->query($deleteRelatedBidders);
    
    return true;
}

/**
 * Delete all invisible clients and client contacts those are not related to any bidder
 * @return true
 */

function deleteAccountsContactsAndRelations() {
    global $db;
    
    //delete invisible clients those are not related to any bidder
    $clientQuery = "DELETE ac FROM accounts ac LEFT JOIN "
            . "oss_leadclientdetail bidder ON "
            . "bidder.account_id=ac.id AND bidder.deleted='0' "
            . "where bidder.id IS NULL AND ac.visibility='0' AND ac.deleted='0'";
    $db->query($clientQuery);
    $GLOBALS['log']->fatal($clientQuery);
    
    //Delete invisible client contacts those are not related to any bidder
    $clientContactQuery = "DELETE cc FROM contacts cc LEFT JOIN "
            . "oss_leadclientdetail bidder ON "
            . "bidder.contact_id=cc.id AND bidder.deleted='0' "
            . "where bidder.id IS NULL AND cc.visibility='0' AND cc.deleted='0'";
    $db->query($clientContactQuery);
    $GLOBALS['log']->fatal($clientContactQuery);
    
    //Delete invisible clients and client contacts relationship those are not related to any bidder
    $relationQuery = "DELETE acc FROM accounts_contacts acc "
        . "LEFT JOIN accounts ac ON ac.id=acc.account_id "
        . "LEFT JOIN contacts cc ON  cc.id=acc.contact_id "        
        . "where acc.deleted='0' AND ac.id IS NULL AND cc.id IS NULL ";
    $db->query($relationQuery);
    $GLOBALS['log']->fatal($relationQuery);
    
    return true;
}