<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


require_once('custom/modules/AOS_Quotes/sugarpdf/sugarpdf.quotes.php');
require_once('custom/include/common_functions.php');

class AOS_QuotesSugarpdfStandard extends AOS_QuotesSugarpdfAOS_Quotes{
    /**
     * Options array for the header table.
     * @var Array
     */
    protected $headerOptions;
     /**
     * Options array for the addresses table.
     * @var Array
     */
    private $addressOptions;
     /**
     * Options array for the table containing all the items.
     * @var Array
     */
    private $itemOptions;
     /**
     * Options array for the total table.
     * @var Array
     */
    private $totalOptions;
     /**
     * Options array for the grand total table.
     * @var Array
     */
    private $grandTotalOptions;   
    
    /**
     *Options array for the inclusions table 
     * @var Array
     */
    private $inclusionOptions;
    
    /**
    *Options array for the exclusions table
    * @var Array
    */
    private $exclusionOptions;
    private $alternateOptions;

    /**
     * @author Mohit Kumar Gupta
     * @date 04-oct-2013
     * Options array for the footer table.
     * @var Array
     */
    private $footerOptions;
    
    /**
     * @author Mohit Kumar Gupta
     * @date 04-oct-2013
     * Options data for the footer table.
     * @var html
     */
    private $footerOptionsData;
        
    private function _initOptions(){
        global $mod_strings;
        $this->headerOptions = array(
                      "isheader"=>0,                        
                      "TD"=>array("bgcolor"=>"#DCDCDC","width"=>"200"),                      
                      "table"=>array("cellspacing"=>"2", "border"=>"0","width"=>'1000',"valign"=>"top"),
        );
        
        $this->footerOptions = array(
        		"isheader"=>false,
        		"table"=>array("cellspacing"=>"2", "border"=>"0"),        		
        );
        
        $this->addressOptions = array(
                      "isheader"=>true,
                      "header"=>array("tr"=>array("bgcolor"=>"#4B4B4B"), "td"=>array("style"=>"color:#FFFFFF; font-weight:bold")),
                      "table"=>array("cellspacing"=>"2", "border"=>"0", "width"=>"30%"),
        );
        
        $this->itemOptions = array(                        
                        "header"=>array("fill"=>"#4B4B4B", "fontStyle"=>"B", "textColor"=>"#FFFFFF","width"=>"100%"),
                        "width"=>array(                            
                            // for the next two vars, if you change the value, change the value below in the else block in the display() function
                            
                            $mod_strings["LBL_TITLE"] => "25%",
                            $mod_strings["LBL_DESCRIPTION"] => "40%",
                            $mod_strings["LBL_QUANTITY"] => "10%",
                            $mod_strings["LBL_PRICE"] => "10%",                            
                            $mod_strings["LBL_TOTAL"] => "15%",
                        ),
        );
        
        $this->inclusionOptions = array(
                                "header"=>array("fill"=>"#4B4B4B", "fontStyle"=>"B", "textColor"=>"#FFFFFF","width"=>"100%"),
                                "table"=>array("cellspacing"=>"2", "border"=>"1","width"=>'1000',"valign"=>"top"),
                                "width"=>array(
        
        // for the next two vars, if you change the value, change the value below in the else block in the display() function
        
		        $mod_strings["LBL_TITLE"] => "25%",
		        $mod_strings["LBL_DESCRIPTION"] => "60%",		        
		        $mod_strings["LBL_TOTAL"] => "15%",
		        ),
        );
        
        $this->exclusionOptions = array(
                                        "header"=>array("fill"=>"#4B4B4B", "fontStyle"=>"B", "textColor"=>"#FFFFFF"),
                                        "width"=>array(
        
        // for the next two vars, if you change the value, change the value below in the else block in the display() function
        
        $mod_strings["LBL_TITLE"] => "25%",
        $mod_strings["LBL_DESCRIPTION"] => "75%",        
        ),
        );         
        
        $this->alternateOptions = array(                        
                        "header"=>array("fill"=>"#4B4B4B", "fontStyle"=>"B", "textColor"=>"#FFFFFF","width"=>"100%"),
                        "width"=>array(                            
                            // for the next two vars, if you change the value, change the value below in the else block in the display() function
                            $mod_strings["LBL_TITLE"] => "25%",
                            $mod_strings["LBL_DESCRIPTION"] => "40%",
                            $mod_strings["LBL_QUANTITY"] => "10%",
                            $mod_strings["LBL_PRICE"] => "10%",                            
                            $mod_strings["LBL_TOTAL"] => "15%",
                        ),
        );
        $this->totalOptions = array(
                            "isheader"=>false,
                            "width"=>array(
                                "BLANK"=>"70%",
                                "TITLE"=>"15%",
                                "VALUE"=>"15%",
                            ),
        );
        $this->grandTotalOptions = array(
                "isheader"=>false,
                "width"=>array(
                    "BLANK"=>"40%",
                    "TITLE0"=>"15%",
                    "VALUE0"=>"15%",
                    "TITLE"=>"15%",
                    "VALUE"=>"15%",
                ),
        );
    }
    function preDisplay(){
        global $mod_strings, $timedate, $user, $db;
        parent::preDisplay();
        $this->_initOptions();
        //retrieve the sales person's first name
        global $beanFiles;
        require_once($beanFiles['User']);
        $rep = new User;
        $rep->retrieve($this->bean->assigned_user_id);
        
        //for header address update start
        //Modified by Mohit Kumar Gupta
        //@date 07-oct-2013
		$oppQuery = "SELECT op.id,op.lead_address,op.lead_state,op.lead_county,op.lead_city,op.lead_zip_code FROM quotes_opportunities AS q_op";
		$oppQuery .= " INNER JOIN opportunities AS op1 ON op1.id = q_op.opportunity_id";
		$oppQuery .= " INNER JOIN opportunities AS op ON op.id = COALESCE(op1.parent_opportunity_id,op1.id) WHERE q_op.quote_id = '".$this->bean->id."'";
		$oppResult = $db->query($oppQuery);
		$oppData = $db->fetchByAssoc($oppResult);
		$fullHeaderAddress = '';
		$headerMargin = 10;
		$gap = 1;
		$finalGap = 0;
		$divide = 20;
		$flag = 0;
		if (!empty($oppData['lead_address'])) {
			$fullHeaderAddress .= $oppData['lead_address']; 
			$finalGap += (intval(strlen($oppData['lead_address'])/$divide) + 1);
		}
		if (!empty($oppData['lead_city'])) {
			$fullHeaderAddress .= ($fullHeaderAddress!='')? "<br/>".$oppData['lead_city'] : $oppData['lead_city'];
			$finalGap += (intval(strlen($oppData['lead_city'])/$divide) + 1);
		}
		if (!empty($oppData['lead_state'])) {
			$fullHeaderAddress .= ($fullHeaderAddress!='')? "<br/>".$oppData['lead_state'] : $oppData['lead_state'];
			$flag = 1;
		}
		if (!empty($oppData['lead_zip_code'])) {
			$fullHeaderAddress .= (!empty($oppData['lead_state']))?(", ".$oppData['lead_zip_code']):("<br/>".$oppData['lead_zip_code']);
			$flag = 1;
		}		
		if ($flag) {
			$finalGap++;
		}
		if ($finalGap > 0) {
			$headerMargin += $finalGap*$gap;
		}
		$quote[0]['TITLE'] = $mod_strings['LBL_PDF_QUOTE_NUMBER'];
        $quote[1]['TITLE'] = $mod_strings['LBL_PDF_QUOTE_DATE'];
        $quote[2]['TITLE'] = $mod_strings['LBL_PDF_SALES_CONTACT'];
        $quote[3]['TITLE'] = $mod_strings['LBL_PDF_SALES_COMPANY'];
        //for header address update start
        //Modified by Mohit Kumar Gupta
        //@date 07-oct-2013
        if ($fullHeaderAddress != '') {
        	$quote[4]['TITLE'] = $mod_strings['LBL_PDF_SALES_ADDRESS'];
        }
                
        $quote_num = $this->bean->quote_num.".".$this->bean->proposal_version;
        //if proposal delivery method is manual no versioning
        if( $this->bean->proposal_delivery_method == 'M' ){
        	$quote_num = $this->bean->quote_num;
        }

        //$quote[0]['VALUE']['value'] = format_number_display($this->bean->quote_num.".".$this->bean->proposal_version,$this->bean->system_id);
        $quote[0]['VALUE']['value'] = $quote_num;
        $quote[1]['VALUE']['value'] = $timedate->nowDate();
        $quote[2]['VALUE']['value'] = $rep->first_name.' '.$rep->last_name;
        $quote[3]['VALUE']['value'] = $this->bean->name;
        //for header address update start
        //Modified by Mohit Kumar Gupta
        //@date 07-oct-2013
        if ($fullHeaderAddress != '') {
        	$quote[4]['VALUE']['value'] = $fullHeaderAddress;
        }	    

        // these options override the params of the $options array.
        $quote[0]['VALUE']['options'] = array("width"=>"400");
        $quote[1]['VALUE']['options'] = array("width"=>"400");
        $quote[2]['VALUE']['options'] = array("width"=>"400");
        $quote[3]['VALUE']['options'] = array("width"=>"400");
        //for header address update start
        //Modified by Mohit Kumar Gupta
        //@date 07-oct-2013
        if ($fullHeaderAddress != '') {
        	$quote[4]['VALUE']['options'] = array();
        }
        
        //for footer address update start
        //Modified by Mohit Kumar Gupta
        //@date 04-oct-2013        
        if (!empty($rep->company_name)) {
        	$footerQuotes[][] = $rep->company_name;
        }
        $address = '';
        $address1 = '';
        $address2 = '';
        $footerMargin = 10;
        $footerGap = 3;
        $footerFinalGap = 0;
        $footerDivide = 40;
        if (!empty($rep->address_street)) {
        	$address1 .= $rep->address_street; 
        	$footerFinalGap += (intval(strlen($rep->address_street)/$footerDivide) + 1);
        }
        if (!empty($rep->address_city)) {
        	$address1 .= ($address1 != '')? " ".$rep->address_city : $rep->address_city;
        	$footerFinalGap += (intval(strlen($rep->address_city)/$footerDivide) + 1);
        }
        if (!empty($rep->address_state)) {
        	$address2 .= $rep->address_state." ";
        }
        if (!empty($rep->address_postalcode)) {
        	$address2 .= $rep->address_postalcode;
        }
        if ($address2 != '') {
        	$footerFinalGap++;
        }
        $address = ($address1 != '') ? (($address2 != '') ? ($address1.", ".$address2) : $address1) : $address2;
        if ($address != '') {
        	$footerQuotes[][] = $address;
        }
        $phoneFax = '';
        if (!empty($rep->phone_work)) {
        	$phoneFax .= formatPhoneNumber($rep->phone_work)." (P) ";
        }
        if (!empty($rep->phone_fax)) {
        	$phoneFax .= formatPhoneNumber($rep->phone_fax). " (F)";
        }
        if ($phoneFax != '') {
        	$footerQuotes[][] = $phoneFax;
        	$footerFinalGap++;
        }
        $this->footerOptionsData = $footerQuotes;
        if ($footerFinalGap > 0) {
        	$footerMargin += $footerFinalGap*$footerGap;
        }
        $this->SetAutoPageBreak(true,22);
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+$headerMargin, PDF_MARGIN_RIGHT);
        
        $this->setFooterMargin($footerMargin);
        //for footer address update end
        
        $html = $this->writeHTMLTable($quote, true, $this->headerOptions);
        $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', $html);
               
    }
    
    /**
     * This method prints text from the current position.<br />
     * and override here for replacing back slash special character
     * @Modified By Mohit Kumar Gupta
     * @date 10-oct-2013
     * @param float $h Line height
     * @param string $txt String to print
     * @param mixed $link URL or identifier returned by AddLink()
     * @param int $fill Indicates if the background must be painted (1) or transparent (0). Default value: 0.
     * @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
     * @param boolean $ln if true set cursor at the bottom of the line, otherwise set cursor at the top of the line.
     * @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
     * @param boolean $firstline if true prints only the first line and return the remaining string.
     * @param boolean $firstblock if true the string is the starting of a line.
     * @param float $maxh maximum height. The remaining unprinted text will be returned. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature.
     * @return mixed Return the number of cells or the remaining string if $firstline = true.
     * @access public
     * @since 1.5
     */
    public function Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0) {
    	
    	$txt = $this->stringReplace('\\\\', '\\', $txt);
    	parent::Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
    }  
      
    /**
     * replace the string with the given string
     * @author Mohit Kumar Gupta
     * @date 12-oct-2013
     * @param string $values
     * @param string $replace
     * @param string $string
     * @return string
     */
    function stringReplace($values, $replace, $string) {
    	if (strpos($string, $values)) {
    		$string = str_replace($values, $replace, $string);
    		return $this->stringReplace($values, $replace, $string);
    	}
    	return $string;
    }
    
    /**
     * This method is used to render the page footer.
     * It is automatically called by AddPage() 
     * @author Mohit Kumar Gupta	
     * @date 04-oct-2013
     * @access public
     */
    public function Footer() {    	
    	$headerdata = $this->writeHTMLTable($this->footerOptionsData,true,$this->footerOptions);
    	$headerfont = $this->getFooterFont();
    	if (empty($this->pagegroups)) {
    		$pagenumtxt = $this->l['w_page'].' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
    	} else {
    		$pagenumtxt = $this->l['w_page'].' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
    	}
    	// This table split the header in 3 parts of equal width. The last part (on the right) contain the header text.
    	$table[0]["logo"]= "<div><font face=\"".$headerfont[0]."\" size=\"".($headerfont[2])."\"><b>".$pagenumtxt."</b></font></div>";
    	$table[0]["blank"]= "";
    	$table[0]["data"]= "<div><font face=\"".$headerfont[0]."\" size=\"".($headerfont[2])."\"><b>".$headerdata."</b></font></div>";
    	$options = array(
    			"isheader"=>false,
    	);
    	$this->SetTextColor(0, 0, 0);
    	$line_width = 0.85 / $this->getScaleFactor();
    	$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
    	$this->MultiCell(0, 0, '', 'T', 0, 'C');
    	$this->writeHTMLTable($table, 0, $this->footerOptions);   				
    }
    
    
    function display(){
        global $mod_strings, $app_strings, $app_list_strings;
        global $locale;

        require_once('modules/AOS_Quotes/AOS_Quotes.php');
        require('modules/AOS_Quotes/config.php');

        parent::display();

        $GLOBALS['log']->info("Quote layout view: Invoice");
		
		// BBSMP-107  -- Start
        //$addressBS[][$mod_strings['LBL_PDF_BILLING_ADDRESS']]  = $this->bean->billing_contact_name;
		//$addressBS[][$mod_strings['LBL_PDF_BILLING_ADDRESS']]  = $this->bean->billing_account_name;
        //for proposal address update start
        //Modified by Mohit Kumar Gupta
        //@date 07-oct-2013
		if (!empty($this->bean->billing_contact)) {
        	$addressBS[][$mod_strings['LBL_PDF_BILLING_ADDRESS']] = $this->bean->billing_contact;
        }
		// BBSMP-107  -- Start
        if (!empty($this->bean->contact_phone)) {
        	$addressBS[][$mod_strings['LBL_PDF_BILLING_ADDRESS']] = formatPhoneNumber($this->bean->contact_phone)." (P)";
        }
        if (!empty($this->bean->contact_fax)) {
        	$addressBS[][$mod_strings['LBL_PDF_BILLING_ADDRESS']] = formatPhoneNumber($this->bean->contact_fax)." (F)";
        }
        $fullAddress = '';
        if (!empty($this->bean->billing_address_street)) {
        	$fullAddress .= $this->bean->billing_address_street;
        }
        if (!empty($this->bean->billing_address_city)) {
        	$fullAddress .= ($fullAddress!='')? " ".$this->bean->billing_address_city : $this->bean->billing_address_city;
        }
        if (!empty($this->bean->billing_address_state)) {
        	$fullAddress .= ($fullAddress!='')? ", ".$this->bean->billing_address_state : $this->bean->billing_address_state;
        }
        if (!empty($this->bean->billing_address_postalcode)) {
        	$fullAddress .= (!empty($this->bean->billing_address_state))?(" ".$this->bean->billing_address_postalcode):(($fullAddress!='') ? ", ".$this->bean->billing_address_postalcode:$this->bean->billing_address_postalcode);
        }
        $addressBS[][$mod_strings['LBL_PDF_BILLING_ADDRESS']]  = $fullAddress; //print_r($fullAddress); exit;              
        //for proposal address update end
        
        //$addressBS[0][$mod_strings['LBL_PDF_SHIPPING_ADDRESS']]  = $this->bean->shipping_contact_name;
        //$addressBS[1][$mod_strings['LBL_PDF_SHIPPING_ADDRESS']]  = $this->bean->shipping_account_name;
        //$addressBS[2][$mod_strings['LBL_PDF_SHIPPING_ADDRESS']]  = $this->bean->shipping_address_street;
        //if(!empty($this->bean->shipping_address_city) || !empty($this->bean->shipping_address_state) || !empty($this->bean->shipping_address_postalcode)) {
        //    $addressBS[3][$mod_strings['LBL_PDF_SHIPPING_ADDRESS']]  = $this->bean->shipping_address_city.", ".$this->bean->shipping_address_state."  ".$this->bean->shipping_address_postalcode;
        //}else{
        //    $addressBS[3][$mod_strings['LBL_PDF_SHIPPING_ADDRESS']]  = '';
        //}
        //$addressBS[4][$mod_strings['LBL_PDF_SHIPPING_ADDRESS']]  = $this->bean->shipping_address_country;


        // Write the Billing/Shipping array
        $this->writeHTMLTable($addressBS, false, $this->addressOptions);


		$layout_options = array();
        if(!empty($this->bean->layout_options)){
			$layout_options = unserialize(base64_decode($this->bean->layout_options));
		}
        
                
        if(!empty($this->bean->description) && isset($layout_options['description_panel']) 
				&& ($layout_options['description_panel'] == '1') && isset($layout_options['description_placement'])
					&& ($layout_options['description_placement'] == 'top') ){
			$this->writeHTML(html_entity_decode($this->bean->description), true, 0, true, 0);
			$this->Ln();
			$this->Ln();
		}


        require_once('modules/Currencies/Currency.php');
        $currency = new Currency();
        ////    settings
        $format_number_array = array(
            'currency_symbol' => true,
            'type' => 'sugarpdf',
            'currency_id' => $this->bean->currency_id,
            'charset_convert' => true, /* UTF-8 uses different bytes for Euro and Pounds */
        );
        $currency->retrieve($this->bean->currency_id);
        
        $product = new AOS_Products();
		$product->disable_row_level_security = 1;
        $where = " aos_products.quote_id='".$this->bean->id."' ";
        $products = $product->get_full_list('',$where);
        
        if(( isset($layout_options['line_itmes_subtotal']) && ($layout_options['line_itmes_subtotal'] == '1') )
           || 
           (  isset($layout_options['line_items']) && ($layout_options['line_items'] == '1') )
           ){
            if($this->y >= 240){
                $this->AddPage();
            }
            
            //Display Line Items Table        
            $this->MultiCell(0,0, "<b>".$mod_strings['LBL_LINE_ITEM_INFORMATION']."</b>" ,0,'L',0,1,"","",true,0,true);
        }

        
        $count = 0;
        $item = array();
        $arShowCoumnsLineItems = array();
        
        //Mohit Kumar Gupta
        //unit measure dom array for key and value pair start
        //date 24-oct-2013
        $unitMeasureDom = getSavedUnitOfMeasure();
        //unit measure dom array for key and value pair end
        
        foreach($products as $line_item){
           if( ($line_item->product_type=='line_items') && isset($layout_options['line_items'])  && ($layout_options['line_items'] == '1') ){	
                
                //flag to check if any of the line item should be displayed
                $isLineItemExists = false;
               
                $item[$count][$mod_strings['LBL_TITLE']] = '';
        		if($line_item->title_show==1){
        		    $isLineItemExists = true;
        		    $arShowCoumnsLineItems[$mod_strings['LBL_TITLE']] = 1;
	        		$item[$count][$mod_strings['LBL_TITLE']] = stripslashes($line_item->name);
	        	}
	        	
	        	$item[$count][$mod_strings['LBL_DESCRIPTION']] = '';
	        	if($line_item->desc_show==1){
	        	    $isLineItemExists = true;
	        	    $arShowCoumnsLineItems[$mod_strings['LBL_DESCRIPTION']] = 1;
	        		$item[$count][$mod_strings['LBL_DESCRIPTION']] = stripslashes($line_item->description);
	        	}
	        	
	        	$item[$count][$mod_strings['LBL_QUANTITY']]['value'] = '';
	        	if($line_item->qty_show==1){
	        	    $isLineItemExists = true;
	        	    $arShowCoumnsLineItems[$mod_strings['LBL_QUANTITY']] = 1;
	        	    
	        	    //Mohit Kumar Gupta
	        	    //unit measure changes for quantity field start
	        	    //date 24-oct-2013
	        	    $quantityUnit = format_number_sugarpdf($line_item->quantity, 0, 0);
	        	    $quantityUnit .= isset($line_item->unit_measure)? " ".$unitMeasureDom[$line_item->unit_measure]:""; 
	        		$item[$count][$mod_strings['LBL_QUANTITY']]['value'] = $quantityUnit;
	        		//unit measure changes for quantity field end
	        		
	        		$item[$count][$mod_strings['LBL_QUANTITY']]['options'] = array("align"=>"C");
	        	}
	        	
	        	
	        	$item[$count][$mod_strings['LBL_PRICE']]['value'] = '';
	        	if($line_item->price_show==1){
	        	    
	        	    $isLineItemExists = true;
	        	    $arShowCoumnsLineItems[$mod_strings['LBL_PRICE']] = 1;
	        		$item[$count][$mod_strings['LBL_PRICE']]['value'] = format_number_sugarpdf($line_item->unit_price, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
	        		$item[$count][$mod_strings['LBL_PRICE']]['options'] = array("align"=>"R");
	        	}
	        	
	        	
	        	$item[$count][$mod_strings['LBL_TOTAL']]['value'] = '';
	        	if($line_item->total_show==1){
	        	    $isLineItemExists = true;
	        	    $arShowCoumnsLineItems[$mod_strings['LBL_TOTAL']] = 1;
	        		$item[$count][$mod_strings['LBL_TOTAL']]['value'] = format_number_sugarpdf($line_item->total, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
	        		$item[$count][$mod_strings['LBL_TOTAL']]['options'] = array("align"=>"R");
	        	}
	        	//unset if all items should not be displayed on proposal
	        	if(!$isLineItemExists ){
	        	   unset($item[$count]);	
	        	}
        	$count++;
        	}
        }
        
        //re-index the array
        $item = array_values($item);       
        
        //set line items if there is no line item then column should not be visible 
        $item = $this->setColumnsForTable($item,$arShowCoumnsLineItems,"itemOptions");
             
        
        if (count($item) > 0){
          $this->writeCellTable($item, $this->itemOptions);
          $this->drawLine();
        }
        
        
        if( isset($layout_options['line_itmes_subtotal']) && ($layout_options['line_itmes_subtotal'] == '1') ){
			$subtotal_li=array();
			$subtotal_li[0]['BLANK'] = ' ';
			$subtotal_li[0]['TITLE'] = $mod_strings['LBL_SUBTOTAL'].":";
			$subtotal_li[0]['VALUE']['value'] =  format_number_sugarpdf($this->bean->subtotal, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
			$subtotal_li[0]['VALUE']['options'] = array("align"=>"R");
			$this->writeCellTable($subtotal_li,$this->totalOptions);
		}
        
		
		
        //Display Inclusions Table
		if( isset($layout_options['inclusions']) && ($layout_options['inclusions'] == '1') 
		    ||  ( isset($layout_options['inclusion_subtotal']) &&  ($layout_options['inclusion_subtotal'] == '1') )
		   ){  
    	    if($this->y >= 240){
    	        $this->AddPage();
    	    }
    	    
           $this->MultiCell(0,0, "<b>".$mod_strings['LBL_INCLUSION_INFORMATION']."</b>" ,0,'L',0,1,"","",true,0,true);
		}
        
        $count_inc=0;
        $item_inc=array();
        foreach($products as $line_item){
            if( ($line_item->product_type=='inclusions') && isset($layout_options['inclusions']) && ($layout_options['inclusions'] == '1') ){
                //flag to check if any of the line item should be displayed
                $isLineItemExists = false;
                
        		$item_inc[$count_inc][$mod_strings['LBL_TITLE']]='';
        		if($line_item->title_show==1){
        		    $isLineItemExists = true;
        		    $arShowCoumnsinclusionItems[$mod_strings['LBL_TITLE']]= 1;
	        		$item_inc[$count_inc][$mod_strings['LBL_TITLE']] = stripslashes($line_item->name);
	        	}
	        	$item_inc[$count_inc][$mod_strings['LBL_DESCRIPTION']]='';
	        	if($line_item->desc_show==1){
	        	    $isLineItemExists = true;
	        	    $arShowCoumnsinclusionItems[$mod_strings['LBL_DESCRIPTION']]= 1;
	        		$item_inc[$count_inc][$mod_strings['LBL_DESCRIPTION']] = stripslashes($line_item->description);
	        	}
	        	$item_inc[$count_inc][$mod_strings['LBL_TOTAL']]['value']='';
        		if($line_item->total_show==1){
        		    $isLineItemExists = true;
        		    $arShowCoumnsinclusionItems[$mod_strings['LBL_TOTAL']]= 1;
		        	$item_inc[$count_inc][$mod_strings['LBL_TOTAL']]['value'] = format_number_sugarpdf($line_item->total,$locale->getPrecision(), $locale->getPrecision(), $format_number_array);
	        	}
        		$item_inc[$count_inc][$mod_strings['LBL_TOTAL']]['options']=array("align"=>"R");
        		
        		//unset if all items should not be displayed on proposal
        		if(!$isLineItemExists ){
        		    unset($item_inc[$count_inc]);
        		}
        		$count_inc++;
        	}
        }
        
        $item_inc = array_values($item_inc);
        //set line items if there is no line item then column should not be visible
        $item_inc = $this->setColumnsForTable($item_inc,$arShowCoumnsinclusionItems,"inclusionOptions");
         
        
        if (count($item_inc) > 0){
            
           $this->writeCellTable($item_inc, $this->inclusionOptions);
           $this->drawLine();
        }

		if( isset($layout_options['inclusion_subtotal']) &&  ($layout_options['inclusion_subtotal'] == '1') ){
			$subtotal_inc=array();
			$subtotal_inc[0]['BLANK'] = ' ';
			$subtotal_inc[0]['TITLE'] = $mod_strings['LBL_SUBTOTAL'].":";
			$subtotal_inc[0]['VALUE']['value'] =  format_number_sugarpdf($this->bean->subtotal_inclusion, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
			$subtotal_inc[0]['VALUE']['options'] = array("align"=>"R");
			$this->writeCellTable($subtotal_inc,$this->totalOptions);
		}
        
		
		
        //Display Exclusions Table
        
		if( isset($layout_options['exclusions']) 
		    &&  ($layout_options['exclusions'] == '1') ){   
			
		    if($this->y >= 240){
		    	$this->AddPage();
		    }     

		    $this->MultiCell(0,0, "<b>".$mod_strings['LBL_EXCLUSION_INFORMATION']."</b>" ,0,'L',0,1,"","",true,0,true);
		  
		}
		
        $count_exc=0;
        $item_exc=array();
        foreach($products as $line_item){
            if( ($line_item->product_type=='exclusions') && isset($layout_options['exclusions']) &&  ($layout_options['exclusions'] == '1') ){
                
                //flag to check if any of the line item should be displayed
                $isLineItemExists = false;

                
        		$item_exc[$count_exc][$mod_strings['LBL_TITLE']]='';
        		if($line_item->title_show==1){
        		    $isLineItemExists = true;
        		    $arShowCoumnsExclusionItems[$mod_strings['LBL_TITLE']] = 1;
        			$item_exc[$count_exc][$mod_strings['LBL_TITLE']] = stripslashes($line_item->name);
        		}
        		$item_exc[$count_exc][$mod_strings['LBL_DESCRIPTION']]='';
        		if($line_item->desc_show==1){
        		    $isLineItemExists = true;
        		    $arShowCoumnsExclusionItems[$mod_strings['LBL_DESCRIPTION']] = 1;
        			$item_exc[$count_exc][$mod_strings['LBL_DESCRIPTION']] = stripslashes($line_item->description);
        		}

        		if(!$isLineItemExists ){        		    
        			unset($item_exc[$count_exc]);
        		}
        		$count_exc++;
        	}
        }
        
        $item_exc = array_values($item_exc);        
         
        if (count($item_exc) > 0){
           
            //set line items if there is no line item then column should not be visible
            $item_exc = $this->setColumnsForTable($item_exc,$arShowCoumnsExclusionItems,"exclusionOptions");
            $this->writeCellTable($item_exc, $this->exclusionOptions);
            $this->drawLine();
           
        }
        
            $total = array();
            
            if(isset($layout_options['exclusions_total']) &&  ($layout_options['exclusions_total'] == '1')){
				$total[0]['BLANK'] = ' ';
				$total[0]['TITLE0'] = '';
				$total[0]['VALUE0'] = '';
				$total[0]['TITLE'] = $mod_strings['LBL_TOTAL'].":";
				$total[0]['VALUE']['value'] = format_number_sugarpdf($this->bean->grand_subtotal, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
				$total[0]['VALUE']['options'] = array("align"=>"R");
			}
			
 			$i = 1;

			if(isset($layout_options['exclusions_tax']) &&  ($layout_options['exclusions_tax'] == '1')){
				$total[$i]['BLANK'] = ' ';
				$total[$i]['TITLE0'] = '';
				$total[$i]['VALUE0'] = '';
				$total[$i]['TITLE'] = $mod_strings['LBL_TAX'].":";
				$total[$i]['VALUE']['value'] =  format_number_sugarpdf($this->bean->tax, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
				$total[$i]['VALUE']['options'] = array("align"=>"R");
				$i++;
			}
			
			if(isset($layout_options['exclusions_shipping']) &&  ($layout_options['exclusions_shipping'] == '1')){
				$total[$i]['BLANK'] = ' ';
				$total[$i]['TITLE0'] = '';
				$total[$i]['VALUE0'] = '';
				$total[$i]['TITLE'] = $mod_strings['LBL_SHIPPING'];
				$total[$i]['VALUE']['value'] =  format_number_sugarpdf($this->bean->shipping, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
				$total[$i]['VALUE']['options'] = array("align"=>"R");
				$i++;
			}
			
			if(isset($layout_options['exclusions_grand_total']) &&  ($layout_options['exclusions_grand_total'] == '1')){
				$total[$i]['BLANK'] = ' ';
				$total[$i]['TITLE0'] = '';
				$total[$i]['VALUE0'] ='';
				$total[$i]['TITLE'] = $mod_strings['LBL_LIST_GRAND_TOTAL'].":";
				$total[$i]['VALUE']['value'] =  format_number_sugarpdf($this->bean->total, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
				$total[$i]['VALUE']['options'] = array("align"=>"R");
			}
			
			if(count($total) >0 ){
            
		    if($this->y >= 240){
		        $this->AddPage();
		    }
		     
            $this->writeCellTable($total, $this->grandTotalOptions);

            $this->drawLine();
			}
            
            //Display Exclusions Table
            if(  isset($layout_options['alternates']) && ($layout_options['alternates'] == '1') ){
            if($this->y >= 240){
                $this->AddPage();
            }                
            $this->MultiCell(0,0, "<b>".$mod_strings['LBL_ALTERNATES_INFORMATION']."</b>" ,0,'L',0,1,"","",true,0,true);
            $count_alt = 0;
            $item_alt = array();
            foreach($products as $line_item){
                
                //flag to check if any of the line item should be displayed
                $isLineItemExists = false;
                
            	if(($line_item->product_type=='alternates') ){
            		$item_alt[$count_alt][$mod_strings['LBL_TITLE']] = '';
            		if($line_item->title_show==1){
            		    $isLineItemExists = true;
            		    $arShowAlternateItems[$mod_strings['LBL_TITLE']] = 1;
            			$item_alt[$count_alt][$mod_strings['LBL_TITLE']] = stripslashes($line_item->name);
            		}
            
            		$item_alt[$count_alt][$mod_strings['LBL_DESCRIPTION']] = '';
            		if($line_item->desc_show==1){
            		    $isLineItemExists = true;
            		    $arShowAlternateItems[$mod_strings['LBL_DESCRIPTION']] = 1;
            			$item_alt[$count_alt][$mod_strings['LBL_DESCRIPTION']] = stripslashes($line_item->description);
            		}
            
            		$item_alt[$count_alt][$mod_strings['LBL_QUANTITY']]['value'] = '';
            		if($line_item->qty_show==1){
            		    $isLineItemExists = true;
            		    $arShowAlternateItems[$mod_strings['LBL_QUANTITY']] = 1;
            		    
            		    //Mohit Kumar Gupta
            		    //unit measure changes for quantity field start
            		    //date 24-oct-2013
            		    $quantityUnit = format_number_sugarpdf($line_item->quantity, 0, 0);
            		    $quantityUnit .= isset($line_item->unit_measure)? " ".$unitMeasureDom[$line_item->unit_measure]:"";
            			$item_alt[$count_alt][$mod_strings['LBL_QUANTITY']]['value'] = $quantityUnit;
            			//unit measure changes for quantity field end
            		}
            		$item_alt[$count_alt][$mod_strings['LBL_QUANTITY']]['options'] = array("align"=>"C");
            
            		$item_alt[$count_alt][$mod_strings['LBL_PRICE']]['value'] = '';
            		if($line_item->price_show==1){
            		    $isLineItemExists = true;
            		    $arShowAlternateItems[$mod_strings['LBL_PRICE']] = 1;
            			$item_alt[$count_alt][$mod_strings['LBL_PRICE']]['value'] = format_number_sugarpdf($line_item->unit_price, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
            		}
            		$item_alt[$count_alt][$mod_strings['LBL_PRICE']]['options'] = array("align"=>"R");
            
            		$item_alt[$count_alt][$mod_strings['LBL_TOTAL']]['value'] = '';
            		if($line_item->total_show==1){
            		    $isLineItemExists = true;
            		    $arShowAlternateItems[$mod_strings['LBL_TOTAL']] = 1;
            			$item_alt[$count_alt][$mod_strings['LBL_TOTAL']]['value'] = format_number_sugarpdf($line_item->total, $locale->getPrecision(), $locale->getPrecision(), $format_number_array);
            		}
            		$item_alt[$count_alt][$mod_strings['LBL_TOTAL']]['options'] = array("align"=>"R");
            		
            		if(!$isLineItemExists){
            			unset($item_alt[$count_alt]);
            		}
            		$count_alt++;
            	}
            }
            }
            
            $item_alt = array_values($item_alt);
            //set line items if there is no line item then column should not be visible
            $item_alt = $this->setColumnsForTable($item_alt,$arShowAlternateItems,"alternateOptions");
         
            if (count($item_alt) > 0)
            	$this->writeCellTable($item_alt, $this->alternateOptions);
            	
           
           if(!empty($this->bean->description) && isset($layout_options['description_panel']) 
				&& ($layout_options['description_panel'] == '1') && isset($layout_options['description_placement'])
					&& ($layout_options['description_placement'] == 'bottom') ){
               $this->Ln();
               $this->Ln();
			   $this->writeHTML(html_entity_decode($this->bean->description), true, 0, true, 0);
		   }
            
      
    }

    /**
     * This method build the name of the PDF file to output.
     */
    function buildFileName(){
        global $mod_strings;
        $fileName = html_entity_decode($this->bean->shipping_account_name, ENT_QUOTES, 'UTF-8');//bug #8584

        if (!empty($this->bean->quote_num)) {
            $fileName .= "_{$this->bean->quote_num}";
        }
        
        /**
         * proposal verisoning
         * Hirak - 07.02.2013
         */
        //$fileName = $mod_strings['LBL_PROPOSAL'].'_' . $fileName . '.pdf';
        $fileName = $this->bean->name .' '.$this->bean->proposal_version.'.pdf';
        //if proposal delivery method is manual no versioning
        if( $this->bean->proposal_delivery_method == 'M' ){
        	$fileName = $this->bean->name.'.pdf';
        }
        
        if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
            //$fileName = $locale->translateCharset($fileName, $locale->getExportCharset());
            $fileName = urlencode($fileName);
        }
        $this->fileName = $fileName;
    }
    /**
     * added for swaping the key correspond to 
     * the missing col.
     * @param array $items
     * @param arrya $arShowColmns
     * @param property $itemOpt
     * @return multitype:array  
     */
    function setColumnsForTable($items,$arShowColmns,$itemOpt){
        
        foreach($items as $iKey => $arValues){
            $arNewItem = array();
            foreach($arValues as $stCol => $arVal){                
                
                if(!in_array($stCol,array_keys($arShowColmns))){
                    $stKeyVal = str_repeat(" ",count(array_keys($arNewItem)));
                    $arNewItem[$stKeyVal] = array('value'=>"&nbsp;",
                            'options' => array('width'=> $this->itemOptions['width'][$stCol])     );
                    $this->{$itemOpt}['width'][$stKeyVal]=$this->{$itemOpt}['width'][$stCol];
                    
                }else{
                    $arNewItem[$stCol] = $arVal;
                }
                
                $items[$iKey] = $arNewItem;
            }
        }
        
        return $items;
    }
}

