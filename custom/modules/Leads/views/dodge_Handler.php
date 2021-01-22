<?php

/*
* Class to handle the xml data from dodge source xml
*
*/
class Dodge_Handler {
	var $indesVal =0;
	var $indexBidderVal =0;
	var $isValidXML =false;
   function Dodge_Handler(){}

   function start_element($parser, $name, $attrs) {
	   $this->name = $name;
       if($name == 'reports'){
       	 //if there is a reports tag then its dodge
       	 $this->isValidXML = true;
       }
	   //increment for root index       
       if($name == 'report'){       	        	  
		  $this->indexVal ++;
		  $this->indexBidderVal= 0;
	   }
	   //increment for bidders index
	   if($name == 'contact-information'){ 
			$this->indexBidderVal ++;
	   
		}	   
   }

   function end_element($parser, $name) {
  
   }

	/**
	* @modifed by Mohit kumar Gupta 09-04-2015
	* @desc : add one more key p-zip-code5 to array
	**/
   function characters($parser, $chars) {
	   $arLeadInfoTags = array('dr-nbr'
	   					,'publish-date'
						,'title'
						,'project-url'
	   					,'cn-project-url'
						,'p-addr-line1'
						,'p-county-name'
	   					,'p-fips-county'
						,'p-state-id'
						,'p-zip-code'
						,'p-zip-code5'
						,'p-city-name'
						,'est-low'
						,'bid-date'
						,'bid-time'
						,'bid-zone'
						,'contract-nbr'
						,'target-start-date'
						,'owner-class'
						,'proj-type'						
						,'available-from'
						,'status-text'
						,'proj-text'
						,'title-code'
						,'work-type'
						);	   
						
	   $arClinetInfoTags = array(
	   					's-contact-role',
						'firm-name',
						'c-addr-line1',
						'c-addr-line2',
						'c-city-name',
	   					'c-fips-county-id',
	   					'c-county-name',
	   					'c-fips-county',
						'c-state-id',
						'c-zip-code5',						
						's-contact-role',						
						'contact-name',
	   					'contact-title',					
						'area-code',						
						'phone-nbr',						
						'fax-area-code',						
						'fax-nbr',						
						'email-id',						
						'www-url',						
						'contact-name',						
						's-bid-category-desc',						
						);
	   
       if(in_array(trim($this->name),$arLeadInfoTags) && trim($chars) != ''){
		   $this->parseVals[$this->indexVal][$this->name] = trim($chars);
       }
       
       if(in_array(trim($this->name),$arClinetInfoTags) && trim($chars) != '')
       {
		   $this->parseVals[$this->indexVal]['bidders'][$this->indexBidderVal][$this->name] = trim($chars);
	   }
   }

}

?>
