<?php 

require_once('include/MVC/View/views/view.edit.php');


class AOS_QuotesViewCancel_proposal extends ViewEdit
{

	function __construct(){
				
		parent::ViewEdit();	
	}
	
	function display(){
		
		//echo '<pre>';print_r($this->bean);die;
		global $mod_strings,$db;
		//proposal must be verified and delivery method must not be manual
		if( $this->bean->proposal_verified == 1 && $this->bean->proposal_delivery_method != 'M')
		{
			require_once 'custom/modules/AOS_Quotes/schedule_quotes/class.easylinkmessage.php';
			$obEasyLink = new easyLinkMessage();
			$obProposal = new AOS_Quotes();
			$obProposal->retrieve( $this->bean->id);
			
			/**
			 * check if proposal is scheduled at easylink
			 * if the proposal in pending queue as scheduled
			 * check status of proposal
			 */
			$stInPendingQueueSQL = 'SELECT id
									,instance_db_name
									,proposal_id
									,COALESCE(easy_email_mrn,easy_fax_mrn ) MRN
									,COALESCE(easy_email_xdn,easy_fax_xdn) XDN
									,proposal_schedule_status
								FROM oss_proposalqueue
								WHERE instance_db_name="'.$sugar_config['dbconfig']['db_name'].'"
								AND proposal_id ="' . $this->bean->id . '"
								AND proposal_schedule_status = "scheduled"';
			
			$obEasyLink->__getCentralDB();
			$rsPending = $obEasyLink->cdb->query ( $stInPendingQueueSQL );
			if($obEasyLink->cdb->num_rows($rsPending) > 0){
				
				$arPending = $obEasyLink->cdb->fetch_assoc ( $rsPending );				
				//echo '<pre>';print_r($arPending);
				if($arPending['MRN'] != '' && $arPending['XDN'] !=''){
					//check status of this job
					$arJobIDHost = array('MRN'=> $arPending['MRN']
										 ,'XDN' => $arPending['XDN']);
					$arJobStatus = $obEasyLink->proposalDeliveryStatus($arJobIDHost);
					
					
					/**
					 * 
					 * Easy link Job States
					 * 1 	Pending
					 * 2	Submitted
					 * 3	InProcess
					 * 4	Error
					 * 5	Cancelled
					 * 6	Held
					 * 7	Sent
					 * 8	Expired
					 */
					$obDeliveryDetails = $arJobStatus->JobDeliveryStatus->JobData->DeliveryGroup->DeliveryDetail;
					//if there is some issue with API call or status code is in 4,5,6,7,8
					if(!isset($obDeliveryDetails->State->code) ||in_array($obDeliveryDetails->State->code,array(4,5,6,7,8))){
						
						echo $stReturnMsg  = $mod_strings['MSG_ERR_CONFIRM_CANCEL'] ;
						exit(0);
					}
				}
			
			}
			
			
			
			
			
			if($obEasyLink->cancelScheduledProposal($obProposal)){				
				
				$stReturnMsg  = $mod_strings['MSG_STATUS_CONFIRM_CANCEL'] ;
				
				//since proposal is cancelled successfully mark it unverified
				$updateQuery = "UPDATE quotes SET verify_email_sent=0, proposal_verified='2' WHERE id='" . $this->bean->id . "'";
				$db->query ( $updateQuery );
				
				// change the proposal sales stage status to unverified.
				// Date : 19/10/2012.
				// Author : Basudeba Rath.
			
				//$sqlOppId = "SELECT * FROM quotes LEFT JOIN quotes_opportunities";
				$sqlUpdateSalesStage = "UPDATE opportunities SET sales_stage = 'Proposal - Unverified' WHERE id = '".$this->bean->opportunity_id."'";
				$db->query( $sqlUpdateSalesStage );
				
			}else{
				
				$stReturnMsg  = $mod_strings['MSG_ERR_CONFIRM_CANCEL'] ;
			}

			echo $stReturnMsg;
			
		}else{
			
			echo $mod_strings['MSG_ERR_IN_CANCEL'];
		}
	}
}

?>