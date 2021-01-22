<?php
require_once 'include/MVC/View/SugarView.php';
class AOS_QuotesViewDatediff extends SugarView{

	function __construct(){
		parent::SugarView();
	}

	function display(){	
		
		$date_time = $_REQUEST['date_time'];
		$timezone = $_REQUEST['timezone'];
		 
		require_once 'custom/include/OssTimeDate.php';
		$oss_timedate = new OssTimeDate();
		 
		$db_date_time_delivery = $oss_timedate->convertDateForDB($date_time, $timezone);
		$delivery_time = strtotime($db_date_time_delivery);
		 
		$now_date_time = $oss_timedate->nowDb();
		$now_time = strtotime($now_date_time);

		$delivery_time - $now_time;
		
		if( ($delivery_time - $now_time) < 7200 ){
			$arResponse['datePrevious']= '1';
		}else{
			$arResponse['datePrevious']='0';
		}	

		$stSql = 'SELECT * FROM email_addresses WHERE email_address ="'.trim($_REQUEST['contact_email']).'" AND opt_out = 1  ';
		
		$rsReulst = $GLOBALS['db']->query($stSql);
		$arEmailData =  $GLOBALS['db']->fetchByAssoc($rsReulst);
		$arResponse['emailOptout']=(isset($arEmailData['id']))?1:'0';
		//echo '<pre>';print_r($arResponse);
		echo json_encode($arResponse);die;
	}
}