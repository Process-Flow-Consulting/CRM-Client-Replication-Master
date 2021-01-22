<?php
require_once('include/MVC/View/views/view.detail.php');

class LeadsViewDetail extends ViewDetail{
	function LeadsViewDetail(){
		parent::ViewDetail();
	}

	function display(){
		
		global $current_user, $app_list_strings, $timedate,$db;		

		//condition for Related leads
		if($this->bean->id == $this->bean->parent_lead_id){
			unset($this->bean->lead_name);
		}
		#####################
		### ACCESS FILTER ###
		if(isset($_SESSION['message'])){
			echo '<span style="color:#FF0000;">'.$_SESSION['message'].'</span>';
			unset($_SESSION['message']);
		}		
		
		if(!$current_user->is_admin){
			require_once('custom/modules/Users/filters/userAccessFilters.php');
			userAccessFilters::isLeadAccessable($this->bean->id);
		}
		### END OF ACCESS FILTER ###
		############################
		
		//Convert Date Time according to Time Zone
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();		
		$bid_due_date = $oss_timedate->convertDBDateForDisplay($this->bean->bids_due, $this->bean->bid_due_timezone, true); 
		$this->bean->bids_due = $bid_due_date;
		
		//$bid_due_date = convertDbDateToTimeZone($this->bean->bids_due, $this->bean->bid_due_timezone);
		//$this->bean->bids_due = $bid_due_date;

		$add = explode('^',$this->bean->project_url);
		$add_l = explode('^',$this->bean->project_url_l);
		//echo "<pre>"; print_r($add_l); echo "</pre>"; exit("ccdas");

        $this->ss->assign('type', $app_list_strings['project_type_dom'][$this->bean->type]);
                
		$field = "";
		foreach($add as $key=>$val){
			$field .= "<b>Description: </b>".$add_l[$key]."<b>  URL: </b>"."<a href=".$val." target='_blank' >".$val."</a> <br>";
		}
		
		/**
		 * Display Date Time without timezone conversion
		 */
		
		if(trim($this->bean->mi_lead_id) != '' || trim($this->bean->onvia_id)!= ''){
			//Pre Bid Meeting
			$db_pbm = $timedate->to_db($this->bean->pre_bid_meeting);	
			$this->bean->pre_bid_meeting = $timedate->to_display_date_time($db_pbm,true,false);
	
			//Start Date
			$db_sd = $timedate->to_db($this->bean->start_date);
			$this->bean->start_date = $timedate->to_display_date($db_sd,false);
	
			//End Date
			$db_ed = $timedate->to_db($this->bean->end_date);
			$this->bean->end_date = $timedate->to_display_date($db_ed,false);
		}	
		
		//Get County by County id
		$sql = "SELECT `name` FROM oss_county WHERE `id` = '".$this->bean->county_id."' AND deleted = 0";
		$query = $db->query($sql);
		$result = $db->fetchByAssoc($query);
		$this->bean->county = $result['name'];
		
		
		$this->ss->assign('project_url',$field);	

		
		
		
		parent::display();
		
		if($this->bean->status == 'New' ){
			$this->bean->status = 'Viewed';
			//$this->bean->date_modfied = $timedate->to_display_date_time($timedate->nowDb());
			/**
			 * Save function is commented due to it is also updating the bids due date 
			 * and some other fields automatically even here we just need to update 
			 * only status of lead, so we are now going to update lead status directly from query
			 * @modified By Mohit Kumar Gupta
			 * @date 28-nov-2013
			 */
			//$this->bean->save();
			$updateSql = "UPDATE leads SET status='Viewed' WHERE id='".$this->bean->id."'";
			$updateQuery = $db->query($updateSql);
		}
		
echo '<script type="text/javascript"  >$(document).ready(function() 
{$("a.no_proview").each(function(indexVal,elm){$(elm).tipTip({maxWidth: "auto",edgeOffset: 10,content: "No proview available.",defaultPosition: "bottom"})});
		})
		
		</script>';
		
		if($this->bean->status == 'Converted' ){
			echo '<script type="text/javascript">
			$(document).ready(function(){
				$("#delete_button").click(function(){
					var _form = document.getElementById(\'formDetailView\');
					_form.return_module.value=\'Leads\';
					_form.return_action.value=\'ListView\';
					_form.action.value=\'Delete\';
					if(confirm(\'You are attempting to delete a Project Lead that has been converted into an opportunity, if you continue to delete this Project Lead you will no longer be able to access information on that opportunity related to this Project Lead.\'))
						SUGAR.ajaxUI.submitForm(_form);
				});
			});
			</script>';
		}else{
			echo '<script type="text/javascript">
			$(document).ready(function(){
				$("#delete_button").click(function(){
					var _form = document.getElementById(\'formDetailView\');
					_form.return_module.value=\'Leads\';
					_form.return_action.value=\'ListView\';
					_form.action.value=\'Delete\';
					if(confirm(\'Are you sure you want to delete this record ?\'))
						SUGAR.ajaxUI.submitForm(_form);
				});
			});
			</script>';
		}

	}
}
?>
