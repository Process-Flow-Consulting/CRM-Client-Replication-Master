<?php

/*
* @Desc   : Class to handle the xml data from onvia source xml
* @author : Basudeba Rath.
* @org    : Osscube Solutions Pvt. Ltd.
* @modified date : 05 Dec 2012.
*/
//ini_set('display_error', 1);
class Onvia_Handler {
	var $indexVal =0;
	var $indexBidderVal =0;
	var $isValidXML =false;
    var $parseVals = array();
    
	function Onvia_Handler(){}

   	function start_element($parser, $name, $attrs) {
	   
   		$this->name = $name;
   		
   		if($name == 'Proposals'){
   			//if there is a reports tag then its onvia
   			$this->isValidXML = true;
   		}
	   
   		//increment for root index
   		if($name == 'Proposal'){
   			$this->indexVal ++;
   		}
   	}

    function end_element($parser, $name) {
  
    }

    function characters($parser, $chars) {
    	$arLeadInfoTags = array('PublicationDate'
	   					,'BidNumber'
	   					,'BuyerAddress1'
	   					,'BuyerAddress2'
						,'BuyerBusinessPhone'
						,'BuyerCity'
	   					,'BuyerCounty'
						,'BuyerDepartment'
						,'BuyerEmail'
	   					,'BuyerFax'
						,'BuyerFirstName'
						,'BuyerID'
						,'BuyerJobTitle'
						,'BuyerLastName'
						,'BuyerPostalCode'
						,'BuyerState'
						,'CategoryID'
						,'CategoryName'
						,'ContractAwardAmount'
						,'ContractTerm'						
						,'ExternalDocuments'
						,'InternalDocuments'
						,'ProjectID'
						,'OwnerID'
						,'OwnerLastUpdatedDate'
	   					,'OwnerName'
	   					,'OwnerPrimaryFunctionID'
	   					,'OwnerPrimaryFunctionName'
	   					,'OwnerWebsite'
	   					,'LevelOfGovernment'
	   					,'PreBidMandatory'
	   					,'PreBidMeetingDate'
	   					,'ProcurementType'
	   					,'ProjectCity'
	   					,'ProjectCounty'
	   					,'ProjectState'
	   					,'ProjectDescription'
	   					,'SubmittalDate'
	   					,'ProjectTitle'
	   					,'ProjectCountyFipsCode'
	   					,'IsSpecAvailable'
	   					,'InDbProjectID'
    			        ,'PlanPrice'
    			        ,'OwnerAnnualExpenditure'
    			        ,'OwnerEmployeeCount'
    			        ,'MaximumContractValue'
    			        ,'MinimumContractValue'
    			        ,'OwnerEnrollment'
    			        ,'OwnerPopulation'
    			        ,'BondingRequirements'
    			        ,'AwardNumber'
    			        ,'SetAsidePercentage'
    			        ,'SetAsideRequirements'
    			        ,'InFileProjectID'
	   				);
    	
    	/**
    	 * @author : Basudeba Rath.
    	 * @modified date : 27 Dec 2012. 
    	 * @desc : Resolved issues in projectdescription and category name field 
    	 * and also resolved notice error.
    	 **/
    	
	   	if(in_array(trim($this->name),$arLeadInfoTags) && trim($chars) != ''){   		
	   		
	   		 if(!empty($this->name)){
	   		 	if(!isset($this->parseVals[$this->indexVal][$this->name])){
	   		 		$this->parseVals[$this->indexVal][$this->name] = "";
	   		 	}
	   		 	$this->parseVals[$this->indexVal][$this->name] .= $chars;
		       
	   		}
	   	}
    }

}

?>