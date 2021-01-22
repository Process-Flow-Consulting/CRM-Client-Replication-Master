<?php

/*
* Class to handle the xml data from reed source xml
*
*/
class Reed_Handler {
var $indesVal =0;
var $indexBidderVal =0;
var $addAddress=0;
var $isPhone=0;
var $isValidXML =false;
var $arLeadInfoTags = array(
 'Project'
,'Valuation'
,'Parameter'
,'SubCategory'
,'StageComment'
,'AddressLine1'
,'AddressLine2'
,'City'
,'County'
,'StateProvince'
,'ZipPostalCode'
,'County'
,'CountryRegion'
,'PlanSpec'
,'Detail'
);   
 var $arClinetInfoTags = array(
'Company',
'Contact',
'AddressLine1',
'AddressLine2',
'City',
'StateProvince',
'ZipPostalCode',
'County',
'CountryRegion',
'PhoneNumber',
'FaxNumber',
);
  
var $arCommon = array(
     'AddressLine1',
     'AddressLine2',
     'City',
     'StateProvince',
     'ZipPostalCode',
     'County',
     'CountryRegion',
 );
  
function Reed_Handler(){}

   function start_element($parser, $name, $attrs) {
       
       if($name == 'Projects'){
       	 $this->isValidXML = true;
       }
       if($name == 'Phone')
       {
           if($attrs['PhoneType'] == 'Fax Phone Number'){
                $this->isPhone = '1';
           }else{
               $this->isPhone = '0';
           }
       }
       
       if($name == 'PhoneNumber' && $this->isPhone==1){
           $name = 'FaxNumber';
       }
       
       $this->name = $name;
       
       if($name == 'Project'){
           $this->addAddress = 1;
         $this->indexVal ++;
         $this->indexBidderVal= 0;
        
          if (count($attrs)>0) {
              if($attrs['URL']!='')
                $this->parseVals[$this->indexVal]['ProjectURL'] = $attrs['URL'];
            }
        }
        
        
      //increment for bidders index
      if($name == 'Company'){
          $this->addAddress = 0;
         $this->indexBidderVal ++;
         
         if (count($attrs)>0) {
              if($attrs['Name']!='')
                 $this->parseVals[$this->indexVal]['bidders'][$this->indexBidderVal]['CompanyName'] = $attrs['Name'];
            }
      }
      
      if($name == 'Contact'){
          if (count($attrs)>0) {
              if($attrs['Name']!='')
                 $this->parseVals[$this->indexVal]['bidders'][$this->indexBidderVal]['ContactName'] = $attrs['Name'];
            }
      }
      
       if(in_array(trim($this->name),$this->arLeadInfoTags)){
           if (count($attrs)>0) {
                foreach($attrs as $key=>$value){
                    if(trim($value) != '' && $key!='URL')
                    $this->parseVals[$this->indexVal][$key] = $value;
                }
            }
       }
       
       if(in_array(trim($this->name),$this->arClinetInfoTags)){
           if (count($attrs)>0) {
                foreach($attrs as $key=>$value){
                    if(trim($value) != '' && $key!='URL' && $key!='Name'){
                        $this->parseVals[$this->indexVal]['bidders'][$this->indexBidderVal][$key] = $value;
                    }
                }
            }
       }
       
   }

   function end_element($parser, $name) {
  
   }

   function characters($parser, $chars) {
  
  
       if(in_array(trim($this->name),$this->arLeadInfoTags) && trim($chars) != ''){
            if(in_array(trim($this->name),$this->arCommon) && $this->addAddress==1){   
                $this->parseVals[$this->indexVal][$this->name] .= trim($chars);
            }else if(!in_array(trim($this->name),$this->arCommon)){
                $this->parseVals[$this->indexVal][$this->name] .= trim($chars);
            }
       }
       
       if(in_array(trim($this->name),$this->arClinetInfoTags) && trim($chars) != '')
       {
            if(in_array(trim($this->name),$this->arCommon) && $this->addAddress!=1){
                $this->parseVals[$this->indexVal]['bidders'][$this->indexBidderVal][$this->name] .= trim($chars);
           }else if(!in_array(trim($this->name),$this->arCommon)){
                $this->parseVals[$this->indexVal]['bidders'][$this->indexBidderVal][$this->name] .= trim($chars);
            }
       }
   }
}

?>