<?php
		require_once('include/formbase.php');
		require_once 'custom/include/common_functions.php';

		global $beanFiles,$beanList,$db;
		
		$bean_name = $beanList[$_REQUEST['module']];
		require_once($beanFiles[$bean_name]);
		
		$focus = new $bean_name();
		
		$linked_id = $_REQUEST['linked_id'];
		
		
		$focus->retrieve($linked_id);
		
		if(isset($focus->parent_lead_id) && !empty($focus->parent_lead_id)){
			// cut it off:
			$parent_lead_id = $focus->parent_lead_id;
			$focus->parent_lead_id = $focus->id;
			$focus->save();
		
			
		}else{
			
			//get new lead as parent:
			$getLeadsGroupQuery = "SELECT id
					FROM leads WHERE parent_lead_id = '".$linked_id."' 
					ORDER BY date_entered ASC";
			$getLeadsGroupResult = $focus->db->query($getLeadsGroupQuery);
			
			$i = 0;
			while($getLeadsGroupRow = $focus->db->fetchByAssoc($getLeadsGroupResult)){
				$lead = new Lead();
				$lead->retrieve($getLeadsGroupRow['id']);
				/*if($i == 0){
					$parent_lead_id = $lead->id;
					$lead->parent_lead_id = '';
				}else{
					$lead->parent_lead_id = $parent_lead_id;
				}*/
				$parent_lead_id = $lead->id;
				$lead->parent_lead_id = $parent_lead_id;
					
				$lead->save();
				$i++;
			}
			
			// cut the prev lead:
			updateLeadVersionBidDueDate($linked_id);
			updateNewTotalBidderCount($linked_id);
			updateOnlineCount($linked_id);
			
		}
		
		//Get Child Change log flag by parent id
		$sql = "SELECT change_log_flag FROM leads WHERE parent_lead_id = '".$parent_lead_id."' AND deleted=0";
		$query = $db->query($sql);
		$child_change_log = false;
		while($row = $db->fetchByAssoc($query)){
			if($row['change_log_flag']==1){
				$child_change_log = true;
			}
		}
		
		if($child_change_log == false){
		    //Get Count from change log by parent id
		    $sql = "SELECT COUNT(1) cnt FROM leads_audit WHERE parent_id='".$parent_lead_id."'";
		    $query = $db->query($sql);
		    $result = $db->fetchByAssoc($query);
		    if($result['cnt'] == 0 ){
				$update_sql = "UPDATE leads SET change_log_flag=0 WHERE id='".$parent_lead_id."' AND deleted=0";
				$db->query($update_sql);
		    }
		}
	
		//update counts
		updateLeadVersionBidDueDate($parent_lead_id);
		updateNewTotalBidderCount($parent_lead_id);
		updateOnlineCount($parent_lead_id);
		
		//set update prev bid to flag
		setPreviousBidToUpdate();
		
		if(!empty($_REQUEST['return_url'])){
		//	$_REQUEST['return_url'] =urldecode($_REQUEST['return_url']);
		}
		
		$GLOBALS['log']->debug("deleted relationship: bean: $bean_name, linked_field: $linked_field, linked_id:$linked_id" );
		if(empty($_REQUEST['refresh_page'])){
			//handleRedirect();
		}
		exit;
