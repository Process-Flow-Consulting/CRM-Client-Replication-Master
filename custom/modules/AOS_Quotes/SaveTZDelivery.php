<?php
class SaveTZDelivery{
    
    function TZDelivery(&$focus){
        
        global $timedate, $current_user;
        
        $date = $timedate->to_db_date($_REQUEST['date_time_delivery'],false);
        $time = date('H:i:s',strtotime($_REQUEST['date_time_delivery']));

        $focus->tz_date_time_delivery = $date.' '.$time;
    }
    
    function convertDueDateToTimeZone(&$focus){
		
    	if($_REQUEST['action'] == 'Save'){		
			
    		require_once 'custom/include/OssTimeDate.php';
    		$oss_timedate = new OssTimeDate();    		
    		$focus->date_time_delivery = $oss_timedate->convertDateForDB($_REQUEST['date_time_delivery'], $focus->delivery_timezone);
			
			/*$due_date_arr = explode(" ",$_REQUEST['date_time_delivery']);
			echo $focus->date_time_delivery;
			print_r($due_date_arr);
			
			die;
			$db_date_time_arr = $timedate->to_db_date_time($due_date_arr[0], $due_date_arr[1]);
			$db_date_time = strtotime(implode(" ",$db_date_time_arr));
			$time_zone = $_REQUEST['delivery_timezone'];

			switch($time_zone){
				case 'Eastern';
				$gmt_time = date('Y-m-d H:i:s',strtotime('+5 hour',$db_date_time));
				break;
				case 'Central';
				$gmt_time = date('Y-m-d H:i:s',strtotime('+6 hour',$db_date_time));
				break;
				case 'Mountain';
				$gmt_time = date('Y-m-d H:i:s',strtotime('+7 hour',$db_date_time));
				break;
				case 'Pacific';
				$gmt_time = date('Y-m-d H:i:s',strtotime('+8 hour',$db_date_time));
				break;
				default:
					$gmt_time = date('Y-m-d H:i:s',$db_date_time);
			}
			$focus->date_time_delivery = $gmt_time; */
		}
	}

}
?>
