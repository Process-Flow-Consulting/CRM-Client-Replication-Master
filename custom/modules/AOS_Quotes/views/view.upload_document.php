<?php

require_once('include/MVC/View/SugarView.php');
require_once 'include/upload_file.php';
require_once 'custom/include/common_functions.php';

class ViewUpload_document extends SugarView {

    function ViewUpload_document() {
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        parent::SugarView();
    }

    function preDisplay() {
        $themeObject = SugarThemeRegistry::current();
        echo $themeObject->getCSS();
        parent::preDisplay();
    }

    function display() {
        if (isset($_REQUEST['button']) && $_REQUEST['button'] == 'Save') {
            global $current_user;              

            if (!empty($_FILES['filename_file'])) {
                $upload_file = new UploadFile('filename_file');                
 			//	echo '<pre>';print_r($_FILES);die;
                if(!isSupportedDocument($_FILES['filename_file']['type']) && trim($_FILES['filename_file']['name']) != ''){
                	
                	SugarApplication::redirect('index.php?module=Quotes&action=upload_document&error=docType');
                }
                
                if (isset($_FILES['filename_file']) && $upload_file->confirm_upload()) {
					                	
                    $doc = new Document();
                    $doc->id = create_guid();
                    $doc->new_with_id = true;
                    //$revision = new DocumentRevision();
                    $doc->filename = $upload_file->get_stored_file_name();
                    $doc->file_mime_type = $upload_file->mime_type;
                    $doc->file_ext = $upload_file->file_ext;
                    $doc->document_name = $doc->filename;
                    $doc->assigned_user_id = $current_user->id;                                        
                    $upload_file->final_move($doc->id);                    
                    $doc->save();                    
                    
                }

             $js_attach = "<script type='text/javascript'>
                    var parent = window.opener;
                    parent.document.getElementById('attach_documentId').value = '" . $doc->id . "';
                    parent.document.getElementById('attach_documentName').value = '" . $doc->filename . "';
                    parent.document.getElementById('attach_div').innerHTML= '<span style=\'padding-top:10px;\'><strong>Attachment: </strong>" . $doc->filename . "</span>';
                  </script>";
             echo $js_attach;
                
            }

            $stDocNames = json_encode($_REQUEST["documentName"]);
            $stDocIds  = json_encode($_REQUEST["documentId"]);
            $js_doc = <<<EQQ

<script type='text/javascript'>
                    var parent = window.opener;
                    var docNames =JSON.parse('$stDocNames');
                    var docIds =JSON.parse('$stDocIds');
                    
                      parent.document.getElementById('documentId').value= docIds.join("|");
                    parent.document.getElementById('doc_div').innerHTML= '<span style=\'padding-top:10px;\'><strong>Document Name: </strong> <ul><li> '+docNames.join('</li><li>')+'</li></ul></span>';
                  </script>
EQQ;

            if (isset($_REQUEST['documentName']) && $_REQUEST['documentName'] != '') {
                echo $js_doc;
            }
echo "<script type='text/javascript'>window.close();</script>";
        }
        
        $this->ss->display('custom/modules/Quotes/tpls/upload_document.tpl');
    }
    
    

}

?>
