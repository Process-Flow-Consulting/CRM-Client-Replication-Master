<?php 
require_once('modules/AOR_Reports/SavedReport.php');
require_once('modules/AOR_Reports/schedule/ReportSchedule.php');
require_once('modules/AOR_Reports/templates/templates_pdf.php');
require_once('include/modules.php');
require_once('config.php');
require_once('custom/modules/AOR_Reports/sugarpdf/sugarpdf.listview.php');
require_once 'modules/AOR_Reports/sugarpdf/sugarpdf.reports.php';

class AOR_ReportsViewUsers_all_activities extends SugarView{
	
	function AOR_ReportsViewUsers_all_activities(){
		parent::SugarView();
	}
	
	function display(){
		
		global $db, $current_user;		
		
		$isPdf = isset($_POST['pdf'])? 1 : 0;
		
		
		$fromDashlet = (isset($_REQUEST['dashlet']) && $_REQUEST['dashlet'] == 1)? 1:0; 
		
		if($isPdf == 1){
			$this->getReportData(1,0,0);			
		}
		
		else if($fromDashlet == 1){
			//Since i get only name for assigned user id from dashlet so find it id & modify request parameters
			$sqlDashlet = "SELECT id From users where user_name = '{$_REQUEST['assigned_user_id'][0]}'";
			$dashletResult = $db->query($sqlDashlet);
			if($row = $GLOBALS['db']->fetchByAssoc($dashletResult)){
				$_REQUEST['assigned_user_id_search'][0] =  $row['id'];
				$activtyType = explode(' ',$_REQUEST['module_status_name']);
				$_REQUEST['activity_type_search'][0] = $activtyType[0];
				switch($activtyType[0]){
					case 'Meeting': $_REQUEST['activity_type_search'][0] = 'Meetings';
					break;
					case 'Call': $_REQUEST['activity_type_search'][0] = 'Calls';
					break;
					case 'Email': $_REQUEST['activity_type_search'][0] = 'Emails';
					break;
					case 'Task': $_REQUEST['activity_type_search'][0] = 'Tasks';
					break;					
				} 
				$this->getReportData(0,0,0);
			}
		}
		else{
		    //check request is comming from scheduler or from user interface
		    //and set the assigned user preferences in case of user interface only
		    //Modifed By Mohit Kumar Gupta 04-12-2014
		    if (isset($_POST) && !empty($_POST)) {
		        $userPrefArr = array();
		        if(isset($_REQUEST['assigned_user_id_search']) && !empty($_REQUEST['assigned_user_id_search'])){
		            $userPrefArr['assigned_user_id_search'] = $_REQUEST['assigned_user_id_search'];
		        }
		    
		        if(isset($_REQUEST['activity_type_search']) && !empty($_REQUEST['activity_type_search'])){
		            $userPrefArr['activity_type_search'] = $_REQUEST['activity_type_search'];
		        }
		        
		        if(isset($_REQUEST['hide_summary']) && !empty($_REQUEST['hide_summary'])){
		            $userPrefArr['hide_summary'] = $_REQUEST['hide_summary'];
		        }
		        
		        if(isset($_REQUEST['hide_activity']) && !empty($_REQUEST['hide_activity'])){
		            $userPrefArr['hide_activity'] = $_REQUEST['hide_activity'];
		        }
		        
		        if(isset($_REQUEST['activityDateRange']) && !empty($_REQUEST['activityDateRange'])){
		            $userPrefArr['activityDateRange'] = $_REQUEST['activityDateRange'];
		        }
		        
		        if(isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])){
		            $userPrefArr['date_from'] = $_REQUEST['date_from'];
		        }
		        
		        if(isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])){
		            $userPrefArr['date_to'] = $_REQUEST['date_to'];
		        }
		        
		        $current_user->setPreference('user_activity_report_preferences',$userPrefArr);
		    }
			$this->getReportData(0,0,0);
		}
	}	
	
	/*
	 * Author : Shashank Verma
	 * Date : 22-Aug-2014
	 * Method Params : Whether to download pdf 
	 * Method Params : Call from Scheduler 
	 * Method Params : Call from Dashlet
	 */
	function getReportData($isPdf,$fromScheduler,$fromDashlet) {		
		global $db,$timedate,$app_list_strings,$timedate,$mod_strings, $current_user;
		
		$limit = "0,20";
		$hideActivity = 0;
		$hideSummary = 0;
		$selectedAssignedUserId = array();
		$activitesQuery = '';
		$selectedActivitySearch = array();
		$activityTypeOptions = array(
				'Meetings' => 'Meetings',
				'Calls' => 'Calls',
				'Emails' =>'Emails',
				'Tasks' => 'Tasks',
				'Notes' => 'Notes',
		);
		$assignedUserSearch = array();
		$allActivities = array();
		$assignedUserPrefData = $current_user->getPreference('user_activity_report_preferences');
		
		//Sort By Default on Name Ascending
		$orderSeq = !empty($_REQUEST['ordersequence']) ? $_REQUEST['ordersequence'] : '';
		$orderMet = !empty($_REQUEST['ordermethod'])?$_REQUEST['ordermethod']:'';
		
		//Set Pagination for 20 records by default
		if(!empty($_REQUEST['limit'])) {
		$limit = $_REQUEST['limit'].",20";
		}
		
		//Check if to hide Activity Panel
		if(isset($_REQUEST['hide_activity']) && !empty($_REQUEST['hide_activity'])){
		  $hideActivity = $_REQUEST['hide_activity'];
		} else if (isset($assignedUserPrefData['hide_activity']) && !empty($assignedUserPrefData['hide_activity'])) {
		  $hideActivity = $assignedUserPrefData['hide_activity'];
		}
		
		//Check if to hide Summary Panel
		if(isset($_REQUEST['hide_summary']) && !empty($_REQUEST['hide_summary'])){
		  $hideSummary = $_REQUEST['hide_summary'];
		} else if(isset($assignedUserPrefData['hide_summary']) && !empty($assignedUserPrefData['hide_summary'])){
		  $hideSummary = $assignedUserPrefData['hide_summary'];
		}
		
		if(isset($_REQUEST['activity_type_search']) && !empty($_REQUEST['activity_type_search'])){
			
			$selectedActivitySearch = $_REQUEST['activity_type_search'];
		} else if(isset($assignedUserPrefData['activity_type_search']) && !empty($assignedUserPrefData['activity_type_search'])){
			
			$selectedActivitySearch = $assignedUserPrefData['activity_type_search'];
		}
		else{
			$selectedActivitySearch = array ('Meetings','Calls','Emails','Tasks','Notes');
		}				
		
		//Create options for User Field
		$usersQuery = "SELECT id,user_name FROM users where deleted = 0";
		$usersResult = $GLOBALS['db']->query($usersQuery);
		
		while($row = $GLOBALS['db']->fetchByAssoc($usersResult)){
			
			$usersArray[$row['id']] = $row['user_name'];
		}
		
		//Make Where condition for all Query set assigned users
		if(isset($_REQUEST['assigned_user_id_search']) && !empty($_REQUEST['assigned_user_id_search'])){
		
			$assignedUserSearch = $_REQUEST['assigned_user_id_search'];
			$selectedAssignedUserId = $_REQUEST['assigned_user_id_search'];
			$assignedWhere = implode(',',$_REQUEST['assigned_user_id_search']);
			$commaSepratedWhere = str_replace(",", "','", $assignedWhere);
			$finalAssignedCondition =  "'".$commaSepratedWhere."'";
		}
		else{
		    if (!empty($assignedUserPrefData['assigned_user_id_search'])) {
    		    //Create Assigned User Search with User Keys
    		    foreach($usersArray as $userId => $userVal){
    		        //select only selected assigned user in user preferences
    		        //Modifed By Mohit Kumar Gupta 04-12-2014
    		        if (in_array((string)$userId,$assignedUserPrefData['assigned_user_id_search'],true)) {
    		            $assignedUserSearch[] = $userId;
    		            $selectedAssignedUserId [] =$userId;
    		        }
    		    }
		    } else {
		        //Create Assigned User Search with User Keys
		        foreach($usersArray as $userId => $userVal){
		          $assignedUserSearch[] = $userId;
		          $selectedAssignedUserId [] =$userId;
		        }
		    }
		    
			$assignedWhere = implode(',',$assignedUserSearch);
			$commaSepratedWhere = str_replace(",", "','", $assignedWhere);
			$finalAssignedCondition =  "'".$commaSepratedWhere."'";
		}
		
		//Create Module Object
		$meetingObj = new Meeting();
		$noteObj = new Note();
		$taskObj = new Task();
		$callObj = new Call();
		$emailObj = new Email();

		/*
		* Filter records acc the teams assigned
		*/
		/* $meetingTeamsQuery = '';
		$meetingObj->add_team_security_where_clause($meetingTeamsQuery);

		$noteTeamsQuery = '';
		$noteObj->add_team_security_where_clause($noteTeamsQuery);

		$taskTeamsQuery = '';
		$taskObj->add_team_security_where_clause($taskTeamsQuery);

		$callTeamsQuery = '';
		$callObj->add_team_security_where_clause($callTeamsQuery);

		$emailTeamsQuery = '';
		$emailObj->add_team_security_where_clause($emailTeamsQuery); */
		
	    $activityDateRange = '';
	    $dateFrom = '';
	    $dateTo = '';
	    if(isset($_REQUEST['activityDateRange']) && !empty($_REQUEST['activityDateRange'])) {
	        $activityDateRange = $_REQUEST['activityDateRange'];
	    } elseif (isset($assignedUserPrefData['activityDateRange']) && !empty($assignedUserPrefData['activityDateRange'])) {
	        $activityDateRange = $assignedUserPrefData['activityDateRange'];
	    }
	    
	    if(isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
	        $dateFrom = $_REQUEST['date_from'];
	    } elseif (isset($assignedUserPrefData['date_from']) && !empty($assignedUserPrefData['date_from'])) {
	        $dateFrom = $assignedUserPrefData['date_from'];
	    }
	    
	    if(isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
	        $dateTo = $_REQUEST['date_to'];
	    } elseif (isset($assignedUserPrefData['date_to']) && !empty($assignedUserPrefData['date_to'])) {
	        $dateTo = $assignedUserPrefData['date_to'];
	    }
	    
	    
		if(in_array('Meetings', $selectedActivitySearch)){
			//Fetch Report Data - All Activities Module
			$activitesQuery = " SELECT  meetings.id,meetings.name,meetings.status,meetings.date_start as Date,meetings.date_entered,meetings.date_modified,meetings.assigned_user_id,meetings.parent_id,meetings.parent_type,u.user_name,'Meetings' as Activity from meetings {$meetingTeamsQuery} INNER JOIN users u on u.id=meetings.assigned_user_id where meetings.DELETED=0";
			//Append Assigned User Search in meetings query
			if((isset($_REQUEST['assigned_user_id_search']) || !empty($assignedUserPrefData['assigned_user_id_search'])) && !empty($finalAssignedCondition)){
				$activitesQuery .= " AND meetings.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'meetings','date_start');
				$activitesQuery .= $customWhere;
			}
			if(in_array('Calls',$selectedActivitySearch) || in_array('Tasks', $selectedActivitySearch) || in_array('Notes',$selectedActivitySearch) || in_array('Emails', $selectedActivitySearch)){
				$activitesQuery .= " UNION ALL ";
			}
		}
		if(in_array('Calls', $selectedActivitySearch)){
							
			//UNION calls query in meeting query
			$activitesQuery .=" select calls.id,calls.name,calls.status,calls.date_start as Date,calls.date_entered,calls.date_modified,calls.assigned_user_id,calls.parent_id,calls.parent_type,u.user_name,'Calls' as Activity from calls {$callTeamsQuery} INNER JOIN users u on u.id=calls.assigned_user_id where calls.DELETED=0";
			//Append Assigned User Search in calls query
			if((isset($_REQUEST['assigned_user_id_search']) || !empty($assignedUserPrefData['assigned_user_id_search'])) && !empty($finalAssignedCondition)){
				$activitesQuery .= " AND calls.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'calls','date_start');
				$activitesQuery .= $customWhere;
			}
			if(in_array('Tasks', $selectedActivitySearch) || in_array('Notes',$selectedActivitySearch) || in_array('Emails', $selectedActivitySearch)){
				$activitesQuery .= " UNION ALL ";
			}			
		}
		
		if(in_array('Tasks', $selectedActivitySearch)){
		
			$activitesQuery .= " select tasks.id,tasks.name,tasks.status,tasks.date_start as Date,tasks.date_entered,tasks.date_modified,tasks.assigned_user_id,tasks.parent_id,tasks.parent_type,u.user_name,'Tasks' as Activity from tasks {$taskTeamsQuery} INNER JOIN users u on u.id=tasks.assigned_user_id where tasks.DELETED=0";
			//Append Assigned User Search in tasks query
			if((isset($_REQUEST['assigned_user_id_search']) || !empty($assignedUserPrefData['assigned_user_id_search'])) && !empty($finalAssignedCondition)){
				$activitesQuery .= " AND tasks.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'tasks','date_start');
				$activitesQuery .= $customWhere;
			}
			if(in_array('Notes',$selectedActivitySearch) || in_array('Emails', $selectedActivitySearch)){
					$activitesQuery .= " UNION ALL ";
			}
		}
		
		if(in_array('Notes',$selectedActivitySearch)){
			
			//Append Assigned User Search in Notes query
			$activitesQuery .= " select notes.id,notes.name,NULL AS status,NULL AS Date,notes.date_entered,notes.date_modified,notes.assigned_user_id,notes.parent_id,notes.parent_type,u.user_name,'Notes' as Activity from notes {$noteTeamsQuery} INNER JOIN users u on u.id=notes.assigned_user_id where notes.DELETED=0";
			if((isset($_REQUEST['assigned_user_id_search']) || !empty($assignedUserPrefData['assigned_user_id_search'])) && !empty($finalAssignedCondition)){
				$activitesQuery .= " AND notes.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'notes','date_modified');
				$activitesQuery .= $customWhere;
			}
			if(in_array('Emails', $selectedActivitySearch)){
				$activitesQuery .= " UNION ALL ";
			}
		}
		
		if(in_array('Emails', $selectedActivitySearch)){			
			$activitesQuery .= " select emails.id,emails.name,emails.status,date_sent as Date,emails.date_entered,emails.date_modified,emails.assigned_user_id,emails.parent_id,emails.parent_type,u.user_name,'Emails' as Activity from emails {$emailTeamsQuery} INNER JOIN users u on u.id=emails.assigned_user_id where emails.DELETED=0";
			//Append Assigned User Search in Emails Query
			if((isset($_REQUEST['assigned_user_id_search']) || !empty($assignedUserPrefData['assigned_user_id_search'])) && !empty($finalAssignedCondition)){
				$activitesQuery .= " AND emails.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'emails','date_sent');
				$activitesQuery .= $customWhere;
			}
		}

		if(!empty($orderSeq)){
			
			$activitesQuery .= " ORDER BY {$orderMet} {$orderSeq}";
		}
		else{
			$activitesQuery .= " ORDER BY name ASC";
		}
		if($isPdf == 0){
			$activitesQuery .= " limit {$limit}";
		}
		//Commented for Future Use
//		echo $activitesQuery;

		//Insert Count in each module query
		//return Query of Count for every module
		if($fromScheduler == 0){
			$testQuery = $this->bean->create_list_count_query($activitesQuery);
		}

		//Return Total Count of Query executed above
		$countTotal = $this->bean->_get_num_rows_in_query($testQuery);
		$totalRecords = $countTotal;
		$usersActivityResult = $GLOBALS['db']->query($activitesQuery);
		while($row= $GLOBALS['db']->fetchByAssoc($usersActivityResult)){
				
			$allActivities[$row['id']] = $row;
			if(!empty($row['parent_id'])){

				$parentType = strtolower($row['parent_type']);
				switch($parentType)
				{
					case 'accounts': $customSelect = "SELECT IFNULL(name,'') as relatedTo ";
					break;
					case 'leads' : $customSelect = "SELECT CONCAT(IFNULL(first_name,''),IFNULL(last_name,'')) as relatedTo ";
					break;
					case 'opportunities' : $customSelect = "SELECT IFNULL(name,'') as relatedTo ";
					break;
					case 'contacts' : $customSelect = "SELECT CONCAT(IFNULL(first_name,''),IFNULL(last_name,'')) as relatedTo ";
					break;
					case 'quotes' : $customSelect = "SELECT IFNULL(name,'') as relatedTo ";
					break;
					case 'emails' : $customSelect = "SELECT IFNULL(name,'') as relatedTo ";
					break;
					default: $customSelect = "SELECT IFNULL(name,'') as relatedTo ";
				}
		
				$relatedToQuery = $customSelect." FROM {$parentType} WHERE id = '{$row['parent_id']}' AND DELETED=0 ";
				$relatedToResult = $GLOBALS['db']->query($relatedToQuery);
		
				//Assign Related to the original data
				if($relatedData = $GLOBALS['db']->fetchByAssoc($relatedToResult)){
					$allActivities[$row['id']]['relatedTo'] = $relatedData['relatedTo'];
				}
			}
		}

		$summaryFinal = array();
		/*
		 * Summary Report Query for Meetings
		 */				 			

		if(in_array('Meetings', $selectedActivitySearch)){
		    //Create Meeting Default Arrray for each user so that on tpl it will be generated for every user
		    foreach($assignedUserSearch as $userKey => $userValue){
		    
		        $userName = $this->getUserName($userValue);
		        foreach ($app_list_strings['meeting_status_dom'] as $meetingKey=>$meetingValue){
		            $summaryFinal[$userName]['Meeting'][$meetingKey] = 0;
		        }
		        $summaryFinal[$userName]['Meeting']['user_name'] = $userName;
		    }
			//GENERATE CASES FROM APP LIST STRINGS
			$summaryMeetingQuery = " SELECT ";
			foreach ($app_list_strings['meeting_status_dom'] as $key=>$value){
				$summaryMeetingQuery .= " SUM(CASE WHEN meetings.status = '{$key}' THEN 1 ELSE 0 END) As '{$key}',";
			}
			$summaryMeetingQuery .= "user_name FROM meetings {$meetingTeamsQuery} INNER JOIN users u on u.id=meetings.assigned_user_id where meetings.DELETED=0 ";
		
			//Check if Assigned user is set
			if(isset($assignedUserSearch) && !empty($assignedUserSearch)){
				$summaryMeetingQuery .= " AND meetings.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'meetings','date_start');
				$summaryMeetingQuery .= $customWhere;
			}
			$summaryMeetingQuery .=" GROUP BY meetings.assigned_user_id ";
			$summaryMeetingResult = $GLOBALS['db']->query($summaryMeetingQuery);
			while($row = $GLOBALS['db']->fetchByAssoc($summaryMeetingResult)){
				$summaryFinal[$row['user_name']]['Meeting'] = $row;
			}
		}
				
		/*
		 * Summary Report Query for Calls
		*/
		 if(in_array('Calls', $selectedActivitySearch)){
		     //Create Calls Default Arrray for each user so that on tpl it will be generated for every user
		     foreach($assignedUserSearch as $userKey => $userValue) {
		         $userName = $this->getUserName($userValue);
		         foreach ($app_list_strings['call_status_dom'] as $callKey=>$callValue){
		             $summaryFinal[$userName]['Call'][$callKey] = 0;
		         }
		         $summaryFinal[$userName]['Call']['user_name'] = $userName;
		     }
			$summaryCallsQuery = " SELECT ";
			foreach ($app_list_strings['call_status_dom'] as $key=>$value){
				$summaryCallsQuery .= "SUM(CASE WHEN calls.status = '{$key}' THEN 1 ELSE 0 END) As '{$key}',";
			}
			$summaryCallsQuery .= "user_name FROM calls {$callTeamsQuery} INNER JOIN users u on u.id=calls.assigned_user_id where calls.DELETED=0 ";
			//Append Assigned User Search in calls query
			if(isset($assignedUserSearch) && !empty($assignedUserSearch)){
				$summaryCallsQuery .= " AND calls.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'calls','date_start');
				$summaryCallsQuery .= $customWhere;
			}
			$summaryCallsQuery .= " GROUP BY calls.assigned_user_id ";
			$summaryCallResult = $GLOBALS['db']->query($summaryCallsQuery);
			while($row = $GLOBALS['db']->fetchByAssoc($summaryCallResult)){
				$summaryFinal[$row['user_name']]['Call'] = $row;
			}
		}
		/*
		* Summary Report Query for Emails
		*/		
		if(in_array('Emails', $selectedActivitySearch)){
		    //Create Email Default Arrray for each user so that on tpl it will be generated for every user
		    foreach($assignedUserSearch as $userKey => $userValue) {
		        $userName = $this->getUserName($userValue);
		        $summaryFinal[$userName]['Email'] = Array (
		                'Inbound' => '0',
		                'Outbound' => '0',
		                'Archived' => '0',
		                'Draft' => '0',
		                'user_name' => $userName,
		        );
		    }
			$summaryEmailQuery = "SELECT SUM(CASE WHEN emails.type = 'inbound' THEN 1 ELSE 0 END) As 'Inbound',
				SUM(CASE WHEN emails.type = 'out' THEN 1 ELSE 0 END) As 'Outbound',
				SUM(CASE WHEN emails.type = 'archived' THEN 1 ELSE 0 END) As 'Archived',
				SUM(CASE WHEN emails.type = 'draft' THEN 1 ELSE 0 END) As 'Draft',
				u.user_name
				FROM emails {$emailTeamsQuery} INNER JOIN users u on u.id=emails.assigned_user_id where emails.DELETED=0 ";
			
				//Append Assigned User Search in Emails Query
			if(isset($assignedUserSearch) && !empty($assignedUserSearch)){
				$summaryEmailQuery .= " AND emails.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'emails','date_sent');
				$summaryEmailQuery .= $customWhere;
			}
			$summaryEmailQuery  .= " GROUP BY emails.assigned_user_id ";
			$summaryEmailResult = $GLOBALS['db']->query($summaryEmailQuery);
			while($row = $GLOBALS['db']->fetchByAssoc($summaryEmailResult)){
				$summaryFinal[$row['user_name']]['Email'] = $row;
			}
		}
		/*
		* Summary Report Query for Task
		*/		
		if(in_array('Tasks', $selectedActivitySearch)){
		    //Create Tasks Default Arrray for each user so that on tpl it will be generated for every user
		    foreach($assignedUserSearch as $userKey => $userValue) {
		        $userName = $this->getUserName($userValue);
		        foreach ($app_list_strings['task_status_dom'] as $taskKey=>$taskValue){
		            $summaryFinal[$userName]['Task'][$taskKey] = 0;
		        }
		        $summaryFinal[$userName]['Task']['user_name'] = $userName;
		    }
			$summaryTaskQuery = "SELECT";
			foreach ($app_list_strings['task_status_dom'] as $key=>$value){
				$summaryTaskQuery .= " SUM(CASE WHEN tasks.status = '{$key}' THEN 1 ELSE 0 END) As '{$key}',";
			}
			$summaryTaskQuery .= " u.user_name FROM tasks {$taskTeamsQuery} INNER JOIN users u on u.id=tasks.assigned_user_id where tasks.DELETED=0 ";
									
			//Append Assigned User Search in tasks query
			if(isset($assignedUserSearch) && !empty($assignedUserSearch)){
				$summaryTaskQuery .= " AND tasks.assigned_user_id IN({$finalAssignedCondition})";
			}
			
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'tasks','date_start');
				$summaryTaskQuery .= $customWhere;
			}
			$summaryTaskQuery .= " GROUP BY tasks.assigned_user_id ";
			$summaryTaskResult = $GLOBALS['db']->query($summaryTaskQuery);
			while($row = $GLOBALS['db']->fetchByAssoc($summaryTaskResult)){
				$summaryFinal[$row['user_name']]['Task'] = $row;
			}
		}
													
		/*
		* Summary Report Query for Notes
		*/
				
		if(in_array('Notes', $selectedActivitySearch)){
		    foreach($assignedUserSearch as $key => $value) {
		        $userName = $this->getUserName($value);
		        $summaryFinal[$userName]['Notes'] = Array (
		                'Total Count' => '0',
		                'user_name' => $userName
		        );
		    }
			$summaryNoteQuery = "SELECT count(notes.id) as 'Total Count',u.user_name
								FROM notes {$noteTeamsQuery} INNER JOIN users u on u.id=notes.assigned_user_id where notes.DELETED=0 ";
		
			//Append Users search to notes
			if(isset($assignedUserSearch) && !empty($assignedUserSearch)){
				$summaryNoteQuery .= " AND notes.assigned_user_id IN({$finalAssignedCondition})";
			}
			//Generate Where query with date filter
			if(!empty($activityDateRange)) {
				$customWhere = $this->getDateWhere($activityDateRange,$dateFrom,$dateTo,'notes','date_modified');
				$summaryNoteQuery .= $customWhere;
			}
			$summaryNoteQuery .= " GROUP BY notes.assigned_user_id ";
			$summaryNoteResult = $GLOBALS['db']->query($summaryNoteQuery);
		
			while($row = $GLOBALS['db']->fetchByAssoc($summaryNoteResult)){			
					$summaryFinal[$row['user_name']]['Notes'] = $row;
			}
		}
		
		//Make Sorting whether ascending or descending
		if(!isset($_REQUEST['limit'])){
			if(empty($_REQUEST['limit']))
				$limit = 0;
		}
		//Get Selected Date Search to make it selected on tpl
		$selectedDateSearch = '';
		if($activityDateRange != ''){
			$selectedDateSearch = $activityDateRange;
		}
		
		//Call From Scheduler
		if(($isPdf == 1) && ($fromScheduler == 1)){
			
			$finalReportArray = $this->createReportPdf($allActivities,$summaryFinal,$fromScheduler);
			return 	$finalReportArray;		
		}
		//Create Pdf From List View
		else if ($isPdf == 1 && $fromScheduler == 0){
			$this->createReportPdf($allActivities,$summaryFinal,$fromScheduler);
		}
		//Send Summary Data for Dashlet Processing
		else if ($fromDashlet == 1){
			return $summaryFinal;
		}
		
		$this->ss->assign('selectedActivityWidth',intval(85/count($selectedActivitySearch)));
		$this->ss->assign("limit",$limit);
		$this->ss->assign('userArray',$usersArray);
		$this->ss->assign('allActivities',$allActivities);
		$this->ss->assign('totalRecords',$totalRecords);
		$this->ss->assign("lastLimit",($totalRecords % 20));
		$this->ss->assign("ordersequence",$orderSeq);
		$this->ss->assign("ordermethod",$orderMet);
		$this->ss->assign('summaryFinal',$summaryFinal);
		$this->ss->assign('hide_activity',$hideActivity);
		$this->ss->assign('hide_summary',$hideSummary);
		$this->ss->assign('selectedAssignedUserId',$selectedAssignedUserId);
		$this->ss->assign('timeDate',$timedate);
		$this->ss->assign('selectedActivitySearch',$selectedActivitySearch);
		$this->ss->assign('activityTypeOptions',$activityTypeOptions);
		$this->ss->assign('MOD', $mod_strings);
		$this->ss->assign("activityDateRange",$GLOBALS['app_list_strings']['date_range_search_dom']);
		$this->ss->assign("selectedDateSearch",$selectedDateSearch);
		$this->ss->assign("dateFrom",$dateFrom);
		$this->ss->assign("dateTo",$dateTo);
		$this->ss->assign("isPdf",$isPdf=0);
		$this->ss->display('custom/modules/AOR_Reports/tpls/users_all_activities.tpl');	
	}
	
	/*
	 * Author : Shashank Verma
	 * function : Create Report Pdf
	 * Method Params : Report Data to be displayed in pdf
	*/
	function createReportPdf($allActivities,$summaryFinalPdf,$fromScheduler){
		
		global $timedate;
		$pdfReportData = array();
		$colSpanMod = array();
		$counter = 0;
		$statusCounter = 0;
	
		foreach($summaryFinalPdf as $userName => $userVal){
			foreach($userVal as $moduleKey => $moduleVal){
				$statusCounter = count($moduleVal);
				$statusCounter --;
				$colSpanMod[$moduleKey] = $statusCounter;
			}
			break;
		}
		
		$pdfListViewObj = new AOR_ReportSugarpdfListview();
		$this->ss->assign('summaryFinalPdf',$summaryFinalPdf);
		$this->ss->assign('options',$pdfListViewObj->getProperty('options'));
		$this->ss->assign('colSpanMod',$colSpanMod);
		$pdfHtml = $this->ss->fetch('custom/modules/AOR_Reports/tpls/user_all_activity_summary_pdf.tpl');
	
		foreach($allActivities as $recordKey => $recordVal){
			$pdfReportData[$counter]['Name'] = $recordVal['name'];
			$pdfReportData[$counter]['Activity'] = $recordVal['Activity'];
			$pdfReportData[$counter]['RelatedTo'] = $recordVal['relatedTo'];
			$pdfReportData[$counter]['Status'] = $recordVal['status'];
			$pdfReportData[$counter]['Date'] = $timedate->to_display_date_time($recordVal['Date']);
			$pdfReportData[$counter]['Date Modified'] = $timedate->to_display_date_time($recordVal['date_modified']);
			$pdfReportData[$counter]['Date Entered'] = $timedate->to_display_date_time($recordVal['date_entered']);
			$pdfReportData[$counter]['Assigned User'] = $recordVal['user_name'];
			$counter++;
		}
		unset($allActivities);
		
		if($fromScheduler == 1){
			$finalReportArray = array();
			$finalReportArray['customReportData'] =  $pdfReportData;
			$finalReportArray['summaryReportData'] =  $pdfHtml;
			
			return $finalReportArray;
		}
		
		if($fromScheduler != 1){
			$saved_report = new SavedReport();
			$saved_report->retrieve('9752e959-f5c7-64ff-b0fa-53cf53cdcd87');
			
			$jsonContent = json_decode(html_entity_decode($saved_report->content));
			$jsonContent->reportIdCustom = '9752e959-f5c7-64ff-b0fa-53cf53cdcd87';
			
			$reporter = new AOR_Reports(json_encode($jsonContent));
			$reporter->layout_manager->setAttribute("no_sort",1);
			$reporter->customReportData = $pdfReportData;
			$reporter->summaryReportData = $pdfHtml;
			$reporter->DetailView = 1;
			
			template_handle_pdf($reporter);
		}
	}

	/*
	 * Author : Shashank Verma
	 * function : To fetch User name of user for particular id
	 * Method Params : id (User id) 
	 */
	function getUserName($id)
	{
		$userQuery = "SELECT user_name from users where id ='{$id}' AND DELETED=0";
		$result = $GLOBALS['db']->query($userQuery);
		if($row = $GLOBALS['db']->fetchByAssoc($result)){
			$userName = $row['user_name'];
		}
		return $userName;
	}
	
	/*
	* Author : Shashank Verma
	* function : To fetch User name of user for particular id
	* Method Params : filterCriteria = which case has to be run i.e equal,not_equal
	* dateStart = Start Date of Filter
	* dateEnd = End Date of Filter
	* Module Name = Which Module where has to be generated
	* $fieldName = Apply Date Filter on column in where clause
	*/
	function getDateWhere($filterCriteria,$dateStart='',$dateEnd='',$moduleName='',$fieldName1='')
	{
		global $timedate;
		$date_Start = $timedate->getDayStartEndGMT($dateStart);
		$date_End = $timedate->getDayStartEndGMT($dateEnd);
		$where = '';
			
			switch ($filterCriteria){
				
			case '=' : if(!empty($date_Start)){
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$date_Start['start']}' AND {$moduleName}.{$fieldName1} <= '{$date_Start['end']}' ";
			}
			break;
			case 'not_equal' : if(!empty($date_Start)) {
				$where .= " AND {$moduleName}.{$fieldName1} NOT BETWEEN '{$date_Start['start']}' AND '{$date_Start['end']}' ";
			}
			break;
			case 'less_than' : if(!empty($date_Start)) {
				$where .= " AND {$moduleName}.{$fieldName1} < '{$date_Start['start']}' ";
			}
			break;
			case 'greater_than' : if(!empty($date_Start)) {
				$where .= " AND {$moduleName}.{$fieldName1} > '{$date_Start['end']}' ";
			}
			break;
			case 'last_7_days' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('last_7_days', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'next_7_days' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('next_7_days', null, true);
				$endDate = $dates[1]->asDb();
				$startDate = $dates[0]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'last_30_days' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('last_30_days', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'next_30_days' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('next_30_days', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'last_month' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('last_month', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'this_month' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('this_month', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'next_month' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('next_month', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'last_year' : if(!empty($date_Start)) {
				
				$dates = TimeDate::getInstance()->parseDateRange('last_year', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'this_year' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('this_year', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'next_year' : if(!empty($date_Start)) {
				$dates = TimeDate::getInstance()->parseDateRange('next_year', null, true);
				$startDate = $dates[0]->asDb();
				$endDate = $dates[1]->asDb();
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$startDate}' AND {$moduleName}.{$fieldName1} <= '{$endDate}' ";
			}
			break;
			case 'between' : if(!empty($date_Start) && !empty($date_End)){
				$where .= " AND {$moduleName}.{$fieldName1} >= '{$date_Start['start']}' AND {$moduleName}.{$fieldName1} <= '{$date_End['end']}' ";
			}
			break;
		}
		return $where;
		}
}