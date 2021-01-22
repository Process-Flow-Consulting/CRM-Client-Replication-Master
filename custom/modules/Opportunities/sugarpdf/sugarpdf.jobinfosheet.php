<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
ini_set('xdebug.max_nesting_level', 700);
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
require_once('include/Sugarpdf/Sugarpdf.php');
/**
 * Sugar PDF Etxtended Class for Job Information Sheet PDF 
 * @author ritikadavial
 * @date 1/10/2014
 *
 */
class OpportunitiesSugarpdfJobInfoSheet extends Sugarpdf 
{	
    /**
     * function to process the display of single PDF and save multiple PDF's using Zip file
     * @author ritika davial
     * @date 1/10/2014
     */
    public function process() 
    {
    	//fetch record posted
    	$record = isset($_REQUEST['records'])?$_REQUEST['records']:'';
    	$records = explode(',', $record);
    	// if only one record selected
    	if (count($records) == 1 && trim($records[0]) != '') {
    		$this->preDisplay();
    		$this->display($records[0]);
    		$this->fileName = 'JobInfoSheet.pdf';
    		$this->Output($this->fileName,'I');	
    	}
    	//if more than on record selected
    	else if(count($records) > 1) {
    		
    		$zip = new ZipArchive();
    		$file = 'JobSheetPDF.zip';
    		$resultFile = $zip->open($file, ZipArchive::CREATE);
    		//if zip file created and open
    		if($resultFile === true) {
    			//create and save pdfs in zip file
    			foreach ($records as $idNum => $id) {
    				$pdf = new OpportunitiesSugarpdfJobInfoSheet();	 
    				$pdf->preDisplay();
    				$pdf->display($id);
    				$fileName[$idNum] = 'JobSheetInfo'.$idNum.'.pdf';
    			    $pdf->Output($fileName[$idNum],'F');
    				$zip->addFile($fileName[$idNum]);
    			} 
    			//close zip file
    			$zip->close();
    			//show zip file to download
    			header('Content-type: application/zip');
    			header('Content-Disposition: attachment; filename=JobSheetPDF.zip');
    			readfile('JobSheetPDF.zip');
    		}
    		//if zipfile not created then show error
    		else {
    			sugar_die("Zip File Cannot be created !!");
    		}
    	}
    	// if no record selected
    	else {
    		sugar_die("No Record Selected !!");
    	}
    }
    /**
     * function for Custom header for the job sheet PDF
     * @author ritika davial
     * @date 1/10/2014
    */
    public function Header()
    {   
    	// Get Instance Logo saved from admin
    	$obAdmin = BeanFactory::getBean('Administration');
    	$obAdmin->disable_row_level_security = true;
    	
    	$arAdminData = $obAdmin->retrieveSettings('sugarpdf', true);
    	$arSyncConfig = $arAdminData->settings['sugarpdf_pdf_small_header_logo'];
        
    	//if settings found then set header data according to settings saved
        if(isset($arSyncConfig)) {
    		$this->setHeaderData($arSyncConfig,'PDF_HEADER_LOGO_WIDTH','','');
        }
        //if settings not found then set default header data 
        else {
        	$this->setHeaderData('_blank.png','PDF_HEADER_LOGO_WIDTH','','');
        }
        //set header margins
        $this->setHeaderMargin(10);
    	$ormargins = $this->getOriginalMargins();
        $headerfont = $this->getHeaderFont();
        $headerdata = $this->getHeaderData();
        
        //set header title
        $title = 'JOB INFORMATION SHEET';  
        
        //set header image
        if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {    
        	
        	if($headerdata['logo'] == '_blank.png') {
        		$logo = K_BLANK_IMAGE;       		
        	}
        	else {
        		$logo = K_PATH_CUSTOM_IMAGES.$headerdata['logo'];
        	}
        	$imsize = @getimagesize($logo);
        	if ($imsize === FALSE) {
        		// encode spaces on filename
        		$logo = str_replace(' ', '%20', $logo);
        		$imsize = @getimagesize($logo);
        		if ($imsize === FALSE) {
        			$logo = K_PATH_IMAGES.$headerdata['logo'];
        		}
        	}
        	if ($imsize) {
        		$this->Image($logo, $this->GetX(), $this->getHeaderMargin(),58,14);
        		$imgy = $this->getImageRBY();
        	}
        }
        $this->SetFont($headerfont[0], 'B', 12);
        $this->SetY((2.835/$this->getScaleFactor()+$imgy-7));
        $this->Cell(0, 0, $title, 0, 1, 'R', 0, '', 0);
        $this->ln();
    }
    /**
     * function for blank footer
     * @author ritikadavial
     * @date 7/10/2014
     */
    public function Footer() {}
   
    /**
     * display Main content of PDF
     * @author ritikadavial
     * @date 1/10/2014
    */
    function display($id)
    {  
    	global $app_list_strings,$mod_strings;
       
		$obOppBean = BeanFactory::getBean('Opportunities');
		$obParentOppBean = BeanFactory::getBean('Opportunities');
		//fetch all Fields related to current Opportunity
		$obOppAllFields = $obOppBean->retrieve($id);
		
		//fetch assigned user name
		$obUserBean = BeanFactory::getBean('Users');
		$obUserAllFields = $obUserBean->retrieve($obOppAllFields->assigned_user_id);
		
		//fetch current client name
		$obAccountBean = BeanFactory::getBean('Accounts');
		$obOppBean->load_relationship('accounts');
		$retrievedAccountFields = $obOppBean->accounts->get();
        $clientId = $retrievedAccountFields[0];
        $obAccAllFields = $obAccountBean->retrieve($clientId);
         
        //fetch primary contact name for opportunity
        $obContactBean = BeanFactory::getBean('Contacts');
        $obContacAllFields = $obContactBean->retrieve($obOppAllFields->contact_id);
        
        //fetch Parent Opportunity Detail for currnt client opportunity
        $obParentOppAllFields = $obParentOppBean->retrieve($obOppAllFields->parent_opportunity_id);
        
    	//Start PDF 
        $this->AddPage();
        
        $this->SetLineStyle(array('width' => 2 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->Rect(14.8,27,186.3,233-27,'D',array('L' => 1,'R' => 1,'T' => 1),'', '');
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->SetFillColor(213, 212, 214);
        $this->SetLineStyle(array('width' => 0.85 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        
        //heading row
        $this->Cell(0, 5,$mod_strings['LBL_JOB_SHEET_PDF_OFFICE_USE'] , '', 1, 'C', 1, '', 0, false, 'T', 'C');
        $headingNames = $mod_strings['LBL_JOB_SHEET_PDF_SALES']."        ".$mod_strings['LBL_JOB_SHEET_PDF_ACCOUNTING']."         ".$mod_strings['LBL_JOB_SHEET_PDF_CLOSE OUT']."        ".
                        $mod_strings['LBL_JOB_SHEET_PDF_DESIGN']."        ".$mod_strings['LBL_JOB_SHEET_PDF_PURCHASING']."         ".
                        $mod_strings['LBL_JOB_SHEET_PDF_CONSTRUCTION']."        ".$mod_strings['LBL_JOB_SHEET_PDF_ADMIN'];
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $this->Cell(0, 8, $headingNames, '', 1, 'C', 1, '', 0, false, 'T', 'C');
        $this->SetFillColor(255, 255, 255);
        
        //line to print sales person
        $this->SetY($this->Gety()+1);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(27, '', $mod_strings['LBL_JOB_SHEET_PDF_SALES_PERSON'].":", 0, 'L', 1, 0, 136, '', true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->SetY($this->Gety()-1);
        $this->MultiCell(40, '', $obUserAllFields->first_name." ".$obUserAllFields->last_name, 'B', 'L', 1, 0, 161, '', true);
        
        //line to print job name and total price
        $y = $this->SetY($this->Gety()+max(($this->getNumLines($obUserAllFields->first_name." ".$obUserAllFields->last_name,40)*4),5));
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(25, '', $mod_strings['LBL_JOB_SHEET_PDF_JOB_NAME'].": ", 0, 'L', 1, 0, 15, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(95,'',$obOppAllFields->name,'B', 'L', 1, 0, 34, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $y = $this->SetY($this->Gety()+1);
        $this->MultiCell(15, '', $mod_strings['LBL_JOB_SHEET_PDF_JOB']." #: ", 0, 'L', 1, 0, 146, $y, true);
        $this->MultiCell(40, ''," " , 'B', 'L', 1, 0, 161, '', true);
        
        //line to print street address and square feet
        $y = $this->SetY($this->GetY()+max(($this->getNumLines($obOppAllFields->name,95)*4),14));
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(29, '', $mod_strings['LBL_JOB_SHEET_PDF_STREET_ADDRESS'].": ", 0, 'L', 1, 0, 15, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $leadAddress = (isset($obParentOppAllFields->lead_address) && trim($obParentOppAllFields->lead_address)!= '' )?$obParentOppAllFields->lead_address.', ' :'';
        $this->MultiCell(75, '', $leadAddress, 'B', 'L', 1, 0, 40, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $y = $this->SetY($this->Gety()-8);
        $this->MultiCell(20,'', $mod_strings['LBL_JOB_SHEET_PDF_TOTAL_PRICE'].": ", 0, 'L', 1, 0, 139, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(40, '', "$ ".number_format($obOppAllFields->amount,2), 'B', 'L', 1, 0, 161, '', true);
        
        //line to print blank line
        $y = $this->SetY($this->GetY()+max(($this->getNumLines($leadAddress,75)*6),16));
        
        $leadCity = (isset($obParentOppAllFields->lead_city) && trim($obParentOppAllFields->lead_city)!= '' )?$obParentOppAllFields->lead_city.', ' :'';
        
        $this->MultiCell(75,'',$leadCity.$GLOBALS['app_list_strings']['state_dom'][$obParentOppAllFields->lead_state], 'B', 'L', 1, 0,40, $y, true);
        $this->MultiCell(85,'','', '', 'L', 1, 0,116, $y, true);
        
        //line to print Labour and Submittals due
        $y = $this->SetY($this->Gety()+max(($this->getNumLines($leadCity.$GLOBALS['app_list_strings']['state_dom'][$obParentOppAllFields->lead_state],75)*5),15));
        $this->MultiCell(25,'',"", 0, 'L', 1, 0, 15, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(24, '',$mod_strings['LBL_JOB_SHEET_PDF_LABOUR'].": ",0, 'L', 1, 0, 48, $y, true);
        $this->MultiCell(30, '', " ", 'B', 'L', 1, 0,69, $y, true);
        $this->MultiCell(28, '',$mod_strings['LBL_JOB_SHEET_PDF_SUBMITTALS_DUE'].": ", 0, 'L', 1, 0, 133, $y, true);
        $this->MultiCell(40, ''," ", 'B', 'L', 1, 0, 161, $y, true);
   
        //line to print design hours
        $y = $this->SetY($this->Gety()+7);
        $this->MultiCell(25, '',"", 0, 'L', 1, 0, 15, $y, true);
        $this->MultiCell(29,'',$mod_strings['LBL_JOB_SHEET_PDF_DESIGN_HOURS'].": ", '', 'L', 1, 0,46, $y, true);
        $this->MultiCell(30, '', " ", 'B', 'L', 1, 0,69, $y, true);
        $y = $this->SetY($this->Gety()-.5);
        $this->MultiCell(51, '', " ", 0, 'L', 1, 0,150, $y, true);

        //line to print head count/devices and start date
        $y = $this->SetY($this->Gety()+7);
        $this->MultiCell(15, '',"", 0, 'L', 1, 0, 15, $y, true);
        $this->MultiCell(40,'',$mod_strings['LBL_JOB_SHEET_PDF_HEAD_COUNT_DEVICES'].": ", '', 'L', 1, 0, 37, $y, true);
        $this->MultiCell(30, '', " ", 'B', 'L', 1, 0,69, $y, true);
        $this->MultiCell(62, '',$mod_strings['LBL_JOB_SHEET_PDF_START_DATE'].": ", 0, 'L', 1, 0, 110, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(40, '',$obParentOppAllFields->lead_start_date, 'B', 'L', 1, 0, 161, $y, true);
        
        //line to print square foot and end date
        $y = $this->SetY($this->Gety()+6);
        $this->MultiCell(15, '',"", 0, 'L', 1, 0, 15, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(25, '',$mod_strings['LBL_JOB_SHEET_PDF_SQ_FT'].": ", 0, 'L', 1, 0, 58, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(30, '',number_format($obParentOppAllFields->lead_square_footage,2), 'B', 'L', 1, 0,69, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(62, '',$mod_strings['LBL_JOB_SHEET_PDF_END_DATE'].": ", 0, 'L', 1, 0, 110, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(40, '',$obParentOppAllFields->lead_end_date, 'B', 'L', 1, 0, 161, $y, true);
        $y = $this->SetY($this->Gety()+7);
        $this->MultiCell(36, '',"", 'B', 'L', 1, 0, 165,$y, true);
        $this->Ln1();
        $this->drawLine();
        $this->ln(2);

        //line to print Bonds And their options
        $y = $this->SetY($this->GetY()-7);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(20, '','', 0, 'L', 1, 0, 15, $y, true);
        $logo = K_PATH_CUSTOM_IMAGES."checkbox.png";
        $k = 1.5;    
        $bondTypesCol = <<<EOD
        	{$mod_strings['LBL_JOB_SHEET_PDF_P_AND_P_BOND']} :<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_GC_BOND']} :<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_JOINT_CHECK']} :<br>
       	 	{$mod_strings['LBL_JOB_SHEET_PDF_CERTIFIED_PAYROLL']} :<br>
EOD;
        $this-> MultiCell(40, '', $bondTypesCol, 0, 'L', 0, 1, 31, $y, true,0, true, true, 0, true);
        $bondTypesYes = <<<EOD
        	Yes<br />Yes<br />Yes<br />Yes
EOD;
        $y = $this->SetY($this->GetY() -19.5);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this-> MultiCell(15, '', $bondTypesYes, 0, 'L', 0, 1,63, $y, true,0, true, true, 0, true);
         
        //print checkbox image for Yes options
        $y = $this->SetY($this->GetY()-16);
        for ($i = 0;$i < 4;$i++) {
        	$this->image($logo,71,$this->gety()+$k,3,3);
        	$k=$k+3.5;
        }
        $bondTypesNo = <<<EOD
            No<br>No<br>No<br>No
EOD;
        $this-> MultiCell(15, '', $bondTypesNo, 0, 'L', 0, 1,78, $y, true,0, true, true, 0, true);
         
        //print checkbox image for No options
        $y = $this->SetY($this->GetY()-30.5);
        for ($i = 0;$i < 4;$i++) {
        	$this->image($logo,85,$this->gety()+$k, 3,3);
        	$k = $k+3.5;
        }
        
        //line to print job And their options
        $y = $this->SetY($this->GetY()+13);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this-> MultiCell(10, '',$mod_strings['LBL_JOB_SHEET_PDF_JOB'].": ", 0, 'L', 0, 1, 115, $y, true,0, true, true, 0, true);
        $y = $this->SetY($this->GetY()-5);
        $k = 1.5;
        
        //print checkbox image for Job Type Options
        for ($i = 0;$i < 6;$i++) {
        	$this->Image($logo,130,$this->GetY()+$k, 3,3);
        	$k = $k+3.5;
        }
        $jobTypeCheckBoxCol1 = <<<EOD
         	{$mod_strings['LBL_JOB_SHEET_PDF_NEW_BUILDING']}<br>
         	{$mod_strings['LBL_JOB_SHEET_PDF_RETRO_FIT']}<br>
         	{$mod_strings['LBL_JOB_SHEET_PDF_DESIGN_ONLY']}<br>
         	{$mod_strings['LBL_JOB_SHEET_PDF_INSTALL_ONLY']}<br>
            {$obParentOppAllFields->lead_type}<br>	
            <hr width=270px />			
EOD;
         $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
         $this-> MultiCell(37, '', $jobTypeCheckBoxCol1,0, 'L', 0, 1, 135, $y, true,0, true, true, 0, true);
         $y = $this->SetY($this->GetY()-27);
         $k = 1.5;
         //print checkbox image for Job Type Options
         for ($i = 0;$i < 4;$i++) {
             $this->Image($logo,173,$this->GetY()+$k, 3,3);
             $k = $k+3.5;
         }
         $jobTypeCheckBoxCol2 = <<<EOD
             {$mod_strings['LBL_JOB_SHEET_PDF_FIRE_ALARM']}<br>
             {$mod_strings['LBL_JOB_SHEET_PDF_FIRE_ALARM_RETRO']}<br>
             {$mod_strings['LBL_JOB_SHEET_PDF_SERVICE']}<br>
             {$mod_strings['LBL_JOB_SHEET_PDF_INSPECTION']}<br>             	
EOD;
         $this-> MultiCell(23, '', $jobTypeCheckBoxCol2, 0, 'L', 0, 1, 178, $y, true,0, true, true, 0, true);
        
         //line to print general contractor
         $y = $this->SetY($this->GetY());
         $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
         $this->MultiCell(30, '', $mod_strings['LBL_JOB_SHEET_PDF_GENERAL_CONTRACTOR'].": ", 0, 'L', 1, 0, 15, $y, true);
         $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
         $this->MultiCell(63, '', $obAccAllFields->name, 'B', 'L', 1, 0, 45, $y, true);
     
         //line to print billing address 
         $y = $this->SetY($this->GetY()+max(($this->getNumLines( $obAccAllFields->name,63)*4),10));
         $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
         $this->MultiCell(24,'', $mod_strings['LBL_JOB_SHEET_PDF_BILLING_ADDRESS'].": ",0, 'L', 1, 0, 15, $y, true);
         $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
         
         $clientOppAddress = (isset($obAccAllFields->address1) && trim($obAccAllFields->address1)!= '' )?$obAccAllFields->address1.', ' :'';
         $clientOppCity = (isset($obAccAllFields->billing_address_city) && trim($obAccAllFields->billing_address_city)!= '' )?$obAccAllFields->billing_address_city.', ' :'';
         $clientOppState = (isset($obAccAllFields->billing_address_state) && trim($obAccAllFields->billing_address_state)!= '' )?$GLOBALS['app_list_strings']['state_dom'][$obAccAllFields->billing_address_state].', ' :'';
         $clientOppCountry = (isset($obAccAllFields->billing_address_country) && trim($obAccAllFields->billing_address_country)!= '' )?$obAccAllFields->billing_address_country.', ' :'';
         
         $this->MultiCell(63, '',  $clientOppAddress.$obAccAllFields->address2, 'B', 'L', 1, 0,45, $y, true);
         $this->MultiCell(26, '', "", 0, 'L', 1, 0, 15, $y, true);
         $y = $this->SetY($this->GetY()+max(($this->getNumLines( $clientOppAddress.$obAccAllFields->address2,63)*4),10));
         $this->MultiCell(63, '',$clientOppCity.$clientOppState.$clientOppCountry.$obAccAllFields->billing_address_postalcode, 'B', 'L', 1, 0, 45, $y, true);
         $this->MultiCell(91, '', "",0, 'L', 1, 0, 110, $y, true);
         
         //line to print job types And their options
        $y = $this->SetY($this->GetY()-19);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this-> MultiCell(20, '', $mod_strings['LBL_JOB_SHEET_PDF_JOB_TYPE'].": ", 0, 'L', 0, 1, 110, $y, true,0, true, true, 0, true);
        $y = $this->SetY($this->GetY());
        $k = 1.5;
        //print checkbox image for Job Type Options
        for($i = 0;$i < 7;$i++) {
        	$this->Image($logo,130,$this->GetY()+$k, 3,3);
        	$k = $k+3.5;
        }
        $jobTypeCheckBoxCol1 = <<<EOD
        	{$mod_strings['LBL_JOB_SHEET_PDF_CHURCH']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_CORRECTIONAL']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_EDUCATIONAL']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_GOVERNMENT']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_HOTEL']}<br>
            {$obParentOppAllFields->lead_structure}<br>
            <hr width=270px />		
EOD;
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this-> MultiCell(45, '', $jobTypeCheckBoxCol1,0, 'L', 0, 1, 135, $y, true,0, true, true, 0, true);
        $y = $this->SetY($this->GetY()-30);
        $k = 1.5;
        //print checkbox image for Job Type Options
        for($i = 0;$i < 5;$i++) {
        	$this->Image($logo,173,$this->GetY()+$k, 3,3);
        	$k = $k+3.5;
        }   
        $jobTypeCheckBoxCol2 = <<<EOD
        	{$mod_strings['LBL_JOB_SHEET_PDF_MANUFACTURING']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_MEDICAL']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_MERCANTILE']}<br>
        	{$mod_strings['LBL_JOB_SHEET_PDF_RESIDENTIAL']}<br>
            {$mod_strings['LBL_JOB_SHEET_PDF_RESTAURANT']}<br>  	
EOD;
        $this-> MultiCell(23, '', $jobTypeCheckBoxCol2, 0, 'L', 0, 1, 178, $y, true,0, true, true, 0, true);
        $y = $this->SetY($this->GetY()+max(($this->getNumLines($clientOppCity.$clientOppState.$clientOppCountry.$obAccAllFields->billing_address_postalcode,63)*4),0));
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this-> MultiCell(33, '', $mod_strings['LBL_JOB_SHEET_PDF_PROJECT_MANAGER'].": ", 0, 'L', 1, 0, 15, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(63, '',$obContacAllFields->first_name." ".$obContacAllFields->last_name, 'B', 'L', 1, 0,45, $y, true);
        
        //line to print project manager's phone and Fax
        $y = $this->SetY($this->GetY()+max(($this->getNumLines($obContacAllFields->first_name." ".$obContacAllFields->last_name,63)*4),7));
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(25, '', $mod_strings['LBL_JOB_SHEET_PDF_PHONE'].": ", '', 'L', 1, 0, 45, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(35, '', $obAccAllFields->phone_office, 'B', 'L', 1, 0,73, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(20, '',$mod_strings['LBL_JOB_SHEET_PDF_FAX'].": ",0, '', 1, 0, 115, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(73, '', $obAccAllFields->phone_fax, 'B', 'L', 1, 0, 128, $y, true);
        
        //line to print project manager's mobile and Email
        $y = $this->SetY($this->GetY()+6);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(25, '', $mod_strings['LBL_JOB_SHEET_PDF_MOBILE'].": ", 0, 'L', 1, 0, 45, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(35, '', $obContacAllFields->phone_work, 'B', 'L', 1, 0,73, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->MultiCell(20, '',$mod_strings['LBL_JOB_SHEET_PDF_EMAIL'].": ", '', '', 1, 0, 115, $y, true);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->MultiCell(73, '', $obAccAllFields->email1, 'B', 'L', 1, 0, 128, $y, true);
  
       //line to print job supervisor 
        $y = $this->SetY($this->GetY()+max(($this->getNumLines($obAccAllFields->email1,73)*4),12));
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this-> MultiCell(25,'', $mod_strings['LBL_JOB_SHEET_PDF_JOB_SUPERVISOR'].": ", 0, 'L', 1, 0, 15, $y, true);
        $y = $this->SetY($this->GetY());
        $this->MultiCell(63, '', "", 'B', 'L', 0, 1, 45, $y, true);
        $this->MultiCell(20, '',$mod_strings['LBL_JOB_SHEET_PDF_EMAIL'].": ", '', '', 1, 0, 115, $y, true);
        $this->MultiCell(73, '', "", 'B', 'L', 1, 0, 128, $y, true);
        
        //line to print  job supervisor's  phone 
        $y = $this->SetY($this->GetY()+1);
        $this->MultiCell(10, '', '', 0, 'L', 1, 0, 15, $y, true);
        $this->MultiCell(25, '', $mod_strings['LBL_JOB_SHEET_PDF_PHONE'].": ", '', 'L', 1, 0, 45, $y, true);
        $this->MultiCell(35, '','', 'B', 'L', 1, 0,73, $y, true);
        
        //line to print job supervisor's mobile
        $y = $this->SetY($this->GetY()+6.5);
        $this->MultiCell(10, '', '', 0, 'L', 1, 0, 15, $y, true);
        $this->MultiCell(25, '', $mod_strings['LBL_SUPER_JOB_SHEET_PDF_MOBILE'].": ", '', 'L', 1, 0, 45, $y, true);
        $this->MultiCell(35, '', '', 'B', 'L', 1, 0,73, $y, true);
        $this->MultiCell(20, '','', '', '', 1, 0, 115, $y, true);
        $this->MultiCell(73, '', '', 0, 'L', 1, 0, 128, $y, true);
        $y = $this->SetY($this->GetY()+9);
        $this->drawLine();
                
        //line to print Owner's Name and phone
        $y = $this->SetY($this->GetY()-1);
        $this-> MultiCell(30, '5', $mod_strings['LBL_JOB_SHEET_PDF_OWNER_NAME'].": ", 0, 'L', 1, 0, 15, $y, true);
        $this->MultiCell(63, '5', "", 'B', 'L', 1, 0, 45, $y, true);
        $this->MultiCell(18, '5',$mod_strings['LBL_JOB_SHEET_PDF_PHONE']." : ", '', '', 1, 0, 114, $y, true);
        $this->MultiCell(73, '5', "", 'B', 'L', 1, 0, 128, $y, true);
        $y = $this->SetY($this->GetY()+10);
   
        //line to print Notes and handling of page break
        $y = $this->SetY($this->GetY()-5);
        
        if ($this->GetY() >= 225) {
        	$this->SetLineStyle(array('width' => 2 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        	$this->Rect(14.8,27,186.3,$this->GetY()-18,'D',array('L' => 1,'R' => 1,'T' => 1,'B'=>1),'', '');
        	$this->addPage();	
        	$this->ln(5);
            $this-> MultiCell(186.3,23, $mod_strings['LBL_JOB_SHEET_PDF_NOTES'].": ",'BLTR', 'L', 1, 0, 14.8, $y, true);
            $this->SetLineStyle(array('width' => 2 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $this->Rect(14.8,32,186.3,$this->GetY()-9,'D',array('L' => 1,'R' => 1,'T' => 1,'B'=>1),'', '');
        }
        else {    
         	$this->ln(4);
         	$this->drawLine();
        	$this->SetLineStyle(array('width' => 2 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        	$this->Rect(14.8,27,186.3,$this->GetY()-9,'D',array('L' => 1,'R' => 1,'T' => 1,'B'=>1),'', '');
        	$y = $this->SetY($this->GetY()-5);
        	$this-> MultiCell(186.3, 23, $mod_strings['LBL_JOB_SHEET_PDF_NOTES'].": ",'LBR', 'L', 1, 0, 14.8, $y, true);
        }   
    }
    /**
     * method to draw an horizontal line with a specific style.
     * @author ritikadavial
     * @date 1/10/2014
     */
    protected function drawLine()
    {
        $this->SetLineStyle(array('width' => 0.85 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->MultiCell(0, 0, '', 'T', 0, 'C');
    } 
}