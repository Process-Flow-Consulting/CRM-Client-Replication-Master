<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author satish
 */

class AOS_QuotesController extends SugarController {

    function AOS_QuotesController(){
        parent::SugarController();
    }

    function action_opportunity_detail(){
        $this->view = 'opportunity_detail';
    }

    function action_get_line_items(){
        $this->view = 'get_line_items';
    }
    
    function action_upload_document(){
    	$this->view = 'upload_document';
    }
    
    function action_listview(){
    	require_once 'custom/modules/AOS_Quotes/CustomQuote.php';
    	$this->bean = new CustomAOS_Quotes();
    	$this->view = 'list';
    }
    
    function action_send_verify_email(){
    	$this->view = 'send_verify_email';
    }
    
    function action_get_product_catalog(){
    	$this->view = 'get_product_catalog';	
    }
    function action_date_diff(){
    	$this->view = 'datediff';
    }
    
    function action_cancelProposal(){

    	$this->view = 'cancel_proposal';
    }
    
    function action_save_product_catalog(){
    	$this->view = 'save_product_catalog';
    }
    
    /**
     * @to define action check related project
     */
    function action_checkRelatedProject(){
    	$this->view = 'checkrelatedproject';
    }
    function action_copyProposal(){
        $this->view = 'copyproposal';
    }
    function action_docupload(){
        $this->view = 'docupload';
    }
    function action_selectedDocument(){
        $this->view = 'selecteddocument';
    }
    function action_client_contact_detail(){
    	$this->view = 'client_contact_detail';
    }
    
    function action_taxrate(){
        $this->view = 'taxrate';
    }
}
?>
