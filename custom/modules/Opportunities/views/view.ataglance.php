<?php
//ini_set('display_errors',1);
require_once 'include/MVC/View/SugarView.php';
require_once 'custom/include/OssTimeDate.php';

class OpportunitiesViewAtaglance extends SugarView{
	
	function __construct(){
		parent::SugarView();
	}
	
	function display(){		
		if(isset($_REQUEST['opportunity_id']) && !empty($_REQUEST['opportunity_id'])){
			global $db, $app_list_strings, $mod_strings;
			$oss_timedate = new OssTimeDate();
			$parent_opportunity_id = $_REQUEST['opportunity_id'];			
			$opportunitySQL = "SELECT 
			    opportunities.id,
			    opportunities.parent_opportunity_id,
			    opportunities.name,
			    opportunities.sales_stage,
			    opportunities.client_bid_status,
			    jt6.name account_name,
			    opportunities.contact_id,
				LTRIM(RTRIM(CONCAT(IFNULL(jt2.first_name, ''),' ',IFNULL(jt2.last_name, '')))) assigned_user_name,    
			    LTRIM(RTRIM(CONCAT(IFNULL(jt7.first_name, ''),' ',IFNULL(jt7.last_name, '')))) contact_name,
			    jt7.phone_work,
			    opportunities.project_lead_id,
			    LTRIM(RTRIM(CONCAT(IFNULL(jt9.first_name, ''),' ',IFNULL(jt9.last_name, '')))) project_lead_name,
				jt10.online_link_count online_link_count
			FROM
			    opportunities
					LEFT JOIN
			    users jt2 ON opportunities.assigned_user_id = jt2.id AND jt2.deleted = 0 AND jt2.deleted = 0
			        LEFT JOIN
			    accounts_opportunities jt5 ON jt5.opportunity_id = opportunities.id AND jt5.deleted = 0
			        LEFT JOIN
			    accounts jt6 ON jt6.id = jt5.account_id AND jt6.deleted = 0
			        LEFT JOIN
			    contacts jt7 ON opportunities.contact_id = jt7.id AND jt7.deleted = 0
			        LEFT JOIN
			    leads jt9 ON opportunities.project_lead_id = jt9.id AND jt9.deleted = 0
					LEFT JOIN
				project_lead_lookup jt10 ON jt10.project_lead_id = jt9.id			       
			where
			    (COALESCE(opportunities.parent_opportunity_id,opportunities.id) = '".$parent_opportunity_id."') AND opportunities.deleted = 0
			ORDER BY opportunities.parent_opportunity_id";
			//echo $opportunitySQL;
			
			$opportunityQuery = $db->query($opportunitySQL);
			
			$opportunities = array();
			$i=0;
			while($row = $db->fetchByAssoc($opportunityQuery)){				
				$opportunities[$i] = $row;								
				//Get proposal Details by Opportunity
				$opportunity = new Opportunity();
				$opportunity->retrieve($row['id']);
				//Get Proposals of client opportunity.
				$opportunity->load_relationships('aos_quotes');
				$quotes = $opportunity->aos_quotes->get();
				foreach($quotes as $quoteId){
					$quote = new AOS_Quotes();
					$quote->retrieve($quoteId);
					$opportunities[$i]['proposals'][] = $quote;					
				}

				//Get Activity Records
				$activitySQL = "SELECT 'Call' action, CONCAT(calls.direction, ' Call ', calls.status) status, calls.name subject, calls.date_start sort_date, calls.description, calls.assigned_user_id, LTRIM(RTRIM(CONCAT(IFNULL(users.first_name,''),' ',IFNULL(users.last_name,'')))) assigned_user_name FROM calls  LEFT JOIN users ON calls.assigned_user_id=users.id AND users.deleted=0  WHERE calls.parent_type = 'Opportunities' AND calls.parent_id='".$row['id']."' AND calls.deleted = 0
								UNION
								SELECT 'Meeting' action, CONCAT('Meeting ',meetings.status) status, meetings.name subject, meetings.date_start sort_date, meetings.description, meetings.assigned_user_id, LTRIM(RTRIM(CONCAT(IFNULL(users.first_name,''),' ',IFNULL(users.last_name,'')))) assigned_user_name FROM meetings LEFT JOIN users ON meetings.assigned_user_id=users.id AND users.deleted=0 WHERE meetings.parent_type = 'Opportunities' AND meetings.parent_id='".$row['id']."'  AND meetings.deleted = 0
								UNION
								SELECT 'Task' action, CONCAT('Task ',tasks.status) status, tasks.name subject, tasks.date_start sort_date, tasks.description, tasks.assigned_user_id, LTRIM(RTRIM(CONCAT(IFNULL(users.first_name,''),' ',IFNULL(users.last_name,'')))) assigned_user_name FROM tasks LEFT JOIN users ON tasks.assigned_user_id=users.id AND users.deleted=0 WHERE tasks.parent_type = 'Opportunities' AND tasks.parent_id='".$row['id']."'  AND tasks.deleted = 0
								UNION
								SELECT 'Note' action, 'Note:' status, notes.name subject, notes.date_modified sort_date, notes.description description, notes.assigned_user_id, LTRIM(RTRIM(CONCAT(IFNULL(users.first_name,''),' ',IFNULL(users.last_name,'')))) assigned_user_name  FROM notes LEFT JOIN users ON notes.assigned_user_id=users.id AND users.deleted=0  WHERE notes.parent_type = 'Opportunities' AND notes.parent_id='".$row['id']."' AND notes.deleted = 0
								UNION
								SELECT 'Email' action, 'Email' status, CONCAT(emails.name,'\nTo: ',emails_text.to_addrs,' \nFrom: ',REPLACE(SUBSTRING_INDEX(emails_text.from_addr,'<',-1),'>','')) subject, emails.date_sent sort_date,IFNULL(CONCAT('\n\n',emails_text.description),'') description, emails.assigned_user_id, LTRIM(RTRIM(CONCAT(IFNULL(jt0.first_name,''),' ',IFNULL(jt0.last_name,'')))) assigned_user_name  FROM emails LEFT JOIN users jt0 ON emails.assigned_user_id=jt0.id AND jt0.deleted=0 AND jt0.deleted=0 INNER JOIN (select eb.email_id, 'direct' source FROM emails_beans eb where eb.bean_module = 'Opportunities' AND eb.bean_id = '".$row['id']."' AND eb.deleted=0 UNION select DISTINCT eear.email_id, 'relate' source from emails_email_addr_rel eear INNER JOIN email_addr_bean_rel eabr ON eabr.bean_id ='".$row['id']."' AND eabr.bean_module = 'Opportunities' AND eabr.email_address_id = eear.email_address_id and eabr.deleted=0 where eear.deleted=0 ) email_ids ON emails.id=email_ids.email_id INNER JOIN emails_text ON emails_text.email_id=emails.id where ( emails.deleted=0 ) AND emails.deleted=0
								ORDER BY sort_date desc";
				$activityQuery = $db->query($activitySQL);
		
				while($activityRow = $db->fetchByAssoc($activityQuery)){
					$opportunities[$i]['activities'][] = $activityRow;
				}						
				
				/*
				//Get Meetings of client opportunity.
				$opportunity->load_relationship('meetings');
				$meetings = $opportunity->meetings->get();
				foreach($meetings as $meetingId){
					$meeting = new Meeting();
					$meeting->retrieve($meetingId);
					$opportunities[$i]['meetings'][] = $meeting;
				}
				
				//Get Tasks of client opportunity
				$opportunity->load_relationship('tasks');
				$tasks = $opportunity->tasks->get();
				foreach($tasks as $taskId){
					$task = new Task();
					$task->retrieve($taskId);
					$opportunities[$i]['tasks'][] = $task;
				}
				
				//Get Emails of client Opportunity
				$opportunity->load_relationship('emails');
				$emails = $opportunity->emails->get();
				foreach($emails as $emailId){
					$email = new Email();
					$email->retrieve($emailId);
					$opportunities[$i]['emails'][] = $email;
				}
				
				//Get Calls of client opportunity.
				$opportunity->load_relationship('calls');
				$calls = $opportunity->calls->get();
				foreach($calls as $callId){
					$call = new Call();
					$call->retrieve($callId);
					$opportunities[$i]['calls'][] = $call;					
				}
				
				//Get Notes of client Opportunity
				$opportunity->load_relationship('notes');
				$notes = $opportunity->notes->get();
				foreach($notes as $noteId){
					$note = new Note();
					$note->retrieve($noteId);
					$opportunities[$i]['notes'][] = $note;
				}			
				*/
				$i++;	
			}	
				//echo '<pre>';print_r($opportunities);echo '</pre>';
			$this->ss->assign('APP',$app_list_strings);
			$this->ss->assign('MOD',$mod_strings);
			$this->ss->assign('oss_timedate',$oss_timedate);
			$this->ss->assign('opportunities',$opportunities);
		}
		
		
		$this->ss->display('custom/modules/Opportunities/tpls/ataglance.tpl');
	}
}
?>