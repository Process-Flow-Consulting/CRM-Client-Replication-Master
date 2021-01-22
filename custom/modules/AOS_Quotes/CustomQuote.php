<?php
require_once 'modules/AOS_Quotes/AOS_Quotes.php';
class CustomAOS_Quotes extends AOS_Quotes {
	
	function __construct() {
		parent::AOS_Quotes ();
	}
	
	/**
	 * Override this function for list view customization
	 * @see Quote::fill_in_additional_list_fields()
	 */
	
	function fill_in_additional_list_fields() {
		
		global $timedate, $current_user;
		
		//Display Proposal Verified icon.
		if($this->proposal_delivery_method == 'M'){
			
			$proposal_verified_icon = '<img src="custom/themes/default/images/manuel.png" height="20" width="60" alt="yes" border="0">
			<input type="hidden" name="'.$this->id.'_status" id="'.$this->id.'_status" value="m">';
		}else if($this->proposal_verified == '1'){
			$proposal_verified_icon = '<img src="custom/themes/default/images/yes-icon.png" alt="yes" border="0">
		    <input type="hidden" name="'.$this->id.'_status" id="'.$this->id.'_status" value="v">';
		}else if($this->proposal_verified == '2' && $this->verify_email_sent == '1'){
		    
			$proposal_verified_icon = '<a href="javascript:void(0);" onClick="verifyProposal(\''.$this->id.'\')" id="'.$this->id.'" value="'.$this->id.'">
			 <img src="custom/themes/default/images/pending-icon.png" alt="pending" border="0">
			 </a>
			 <input type="hidden" name="'.$this->id.'_status" id="'.$this->id.'_status" value="p">';
		}else{
		    
			$proposal_verified_icon = '<a href="javascript:void(0);" onClick="verifyProposal(\''.$this->id.'\')" id="'.$this->id.'" value="'.$this->id.'">
			 <img src="custom/themes/default/images/no-icon.png" alt="no" border="0">
			 </a>
			 <input type="hidden" name="'.$this->id.'_status" id="'.$this->id.'_status" value="u">';
		}
		
		$this->proposal_verified = $proposal_verified_icon;
		
		$this->date_time_delivery_db = $timedate->to_db($this->date_time_delivery);
		$this->date_time_delivery_ahead_db = date( 'Y-m-d H:i:s', ( strtotime($this->date_time_delivery_db) -3600 ) ); 
		
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();
		
		//Display Delivery Schedule
		/**
         * @Modified By Mohit Kumar Gupta
         * condition of > changed to >= because seconds is missing in date_time_opened,date_time_received,date_time_sent
         * and date_time_delivery fields and we are comparing here date_time_delivery with other dates.
		 */
		$date_time_delivery_str = '';
		if($this->date_time_opened != '' && ($timedate->to_db($this->date_time_opened) >= $this->date_time_delivery_ahead_db) ){
			$date_time_delivery_str .= 'Opened ';
			
			if($this->proposal_delivery_method == 'M'){
				$date_time_delivery_str .= '- Manual ';
			}
			
			$date_time_delivery_str .= '<br>';
			
			$date_time_delivery_str .= $oss_timedate->convertDBDateForDisplay($this->date_time_opened,$this->delivery_timezone,true);;
		}elseif($this->date_time_received != '' && ($timedate->to_db($this->date_time_received) >= $this->date_time_delivery_ahead_db) ){
				$date_time_delivery_str .= 'Received ';
				
				if($this->proposal_delivery_method == 'M'){
					$date_time_delivery_str .= '- Manual ';
				}
				
				$date_time_delivery_str .= '<br>';
				
				$date_time_delivery_str .= $oss_timedate->convertDBDateForDisplay($this->date_time_received,$this->delivery_timezone,true);;
		}elseif($this->date_time_sent != ''  && ($timedate->to_db($this->date_time_sent) >= $this->date_time_delivery_ahead_db) ){
			$date_time_delivery_str .= 'Sent ';
			
			if($this->proposal_delivery_method == 'M'){
				$date_time_delivery_str .= '- Manual ';
			}
			
			$date_time_delivery_str .= '<br>';
			
			$date_time_delivery_str .= $oss_timedate->convertDBDateForDisplay($this->date_time_sent,$this->delivery_timezone,true);;
		}else{
			$date_time_delivery_str .= 'Scheduled ';

			if($this->proposal_delivery_method == 'M'){
				$date_time_delivery_str .= '- Manual ';
			}
			
			$date_time_delivery_str .= '<br>';
			
			$date_time_delivery_str .= $oss_timedate->convertDBDateForDisplay($this->date_time_delivery,$this->delivery_timezone,true);
		}
		
		$tz_array = array('');
		$date_time_delivery_str .= ' '.$this->delivery_timezone;		
		$this->date_time_delivery = $date_time_delivery_str;
	}
	
	/**
	 * overwrite parent notification body
	 * @see Quote::set_notification_body()
	 */
	function set_notification_body($xtpl, $quote) {
		
		global $timedate;
		
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();
		$date_time_delivery = $oss_timedate->convertDBDateForDisplay($quote->date_time_delivery, $quote->delivery_timezone, false);
	
		$xtpl->assign("QUOTE_SUBJECT", $quote->name);
		$xtpl->assign("QUOTE_STATUS", $quote->quote_stage);
		$xtpl->assign("QUOTE_DELIVERY_DATETIME", $date_time_delivery);
		$xtpl->assign("QUOTE_DELIVERY_TIMEZONE", $quote->delivery_timezone);
		$xtpl->assign("QUOTE_DESCRIPTION", $quote->description);
	
		return $xtpl;
	}
	
	/**
	 * @author Mohit Kumar Gupta
	 * @date 05-03-2015
	 * issue resolved of select all not working on export
	 */
	function create_new_list_query($order_by, $where, $filter=array(), $params=array(), $show_deleted = 0, $join_type='', $return_array = false, $parentbean=null, $singleSelect = false) {
				
		$ret_array = parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, true, $parentbean, $singleSelect);
		
		//If retrun array is set to true then return array else return string
        //Modified by Mohit Kumar Gupta
        //@date 27-01-2014
        if($return_array) {
        	return $ret_array;
        } else {
        	return $ret_array['select'] . ' ' . $ret_array['from'] . ' ' . $ret_array['where'] .' '.$ret_array['group_by']. ' ' . $ret_array['order_by'];
        }       
	}
}
