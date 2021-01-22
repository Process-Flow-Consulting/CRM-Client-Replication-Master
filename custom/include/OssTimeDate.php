<?php
require_once 'include/TimeDate.php';

class OssTimeDate extends TimeDate {
	
	/**
	 * Convert Date to Database date based on TimeZone
	 * if date comes from bean then it will be convert from GMT to User
	 * TimeZone.
	 *
	 * @param $date datetime       	
	 * @param $fromTZ string       	
	 * @param $is_bean_date boolean       	
	 *
	 */
	
	public function convertDateForDB($date, $fromTZ, $is_bean_date = false, $isDbFormat=false) {		
		
		if ($is_bean_date) {
			$date = $this->_convert ( $date, $this->get_date_time_format (), $this->_getUserTZ(), $this->get_db_date_time_format (), self::$gmtTimezone );
			return $date;
		}
		
		// convert timezone based on bluebook timezone
		if (empty ( $fromTZ )) {
			$fromTZ = $this->_getUserTZ ();
		} else {
			$fromTZ = $this->getTimeZone ( $fromTZ );
		}
		
		$fromFormat = $this->get_date_time_format();
		if($isDbFormat){
			$fromFormat = $this->get_db_date_time_format();
		}
		
		$date = $this->_convert ( $date, $fromFormat, $fromTZ, $this->get_db_date_time_format (), self::$gmtTimezone );
		return $date;
	}
	
	/**
	 * Convert DB date to display date based on TimeZone
	 *
	 * @param $date datetime       	
	 * @param $toTZ string       	
	 */
	
	public function convertDBDateForDisplay($date, $toTZ, $is_bean_date=false) {
		if($is_bean_date){
			$date = $this->_convert($date, $this->get_date_time_format(), $this->_getUserTZ(), $this->get_db_date_time_format(), self::$gmtTimezone);
		}
				
		// convert timezone based on bluebook timezone
		if (empty ( $toTZ )) {
			$toTZ = $this->_getUserTZ ();
		} else {
			$toTZ = $this->getTimeZone ( $toTZ );
		}
		
		$date = $this->_convert ( $date, $this->get_db_date_time_format (), self::$gmtTimezone, $this->get_date_time_format (), $toTZ );
		return $date;
	}
	
	/**
	 * Get Timezone based on Defined by bluebook
	 * if timezone not defined by bluebook it will return same timezone
	 *
	 * $EST = 'America/New_York';
	 * $CST = 'America/Chicago';
	 * $MST = 'America/Denver';
	 * $PST = 'America/Los_Angeles';
	 *
	 * @param $timezone string       	
	 *
	 */
	public function getTimeZone($timeZone) {
		
		switch ($timeZone) {
			case 'Eastern' :
				$newTimeZone = 'America/New_York';
				break;
			case 'Central' :
				$newTimeZone = 'America/Chicago';
				break;
			case 'Mountain' :
				$newTimeZone = 'America/Denver';
				break;
			case 'Pacific' :
				$newTimeZone = 'America/Los_Angeles';
				break;
			default :
				$newTimeZone = $timeZone;
		}
		
		return new DateTimeZone ( $newTimeZone );
	}
}
?>