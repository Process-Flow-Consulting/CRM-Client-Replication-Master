<?php

class SaveDocument {

    function SaveDocument(&$focus) {

    	
    	//echo '<pre>';print_r($_REQUEST);die;
        if (isset($_REQUEST['documentId'])) {
            $relate_value = array('document_id' => $_REQUEST['documentId'],'quote_id' => $focus->id);
            //$data_value = array('quote_id' => $focus->id);
            //$focus->set_relationship('documents_quotes', $relate_value, true, false, $data_value);
            $focus->set_relationship('documents_quotes', $relate_value);
        }

        if (isset($_REQUEST['attach_documentId'])) {
            $relate_value = array('document_id' => $_REQUEST['attach_documentId'],'quote_id' => $focus->id);
            //$data_value = array('quote_id' => $focus->id);
            //$focus->set_relationship('documents_quotes', $relate_value, true, true, $data_value);
            $focus->set_relationship('documents_quotes', $relate_value);
        }     
        
        
    }

}

?>
