<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
* Agreement ("License") which can be viewed at
* http://www.sugarcrm.com/crm/master-subscription-agreement
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
require_once('include/MVC/View/SugarView.php');
require_once 'custom/include/common_functions.php';

class AOS_QuotesViewSelecteddocument extends SugarView {

    function AOS_QuotesViewSelecteddocument() {
        parent::SugarView();
    }

    function display() {
        
        global $db;
        
        if( isset( $_REQUEST['deleted_document_id'] ) && !empty($_REQUEST['deleted_document_id']) 
            && isset($_REQUEST['document_string']) && !empty($_REQUEST['document_string']) ){
            $document_string = str_replace( ",".$_REQUEST['deleted_document_id'], "", $_REQUEST['document_string']);
            $document_string = str_replace( $_REQUEST['deleted_document_id'], "", $document_string );
        }else if( isset($_REQUEST['document_string']) && !empty($_REQUEST['document_string']) ) {
            $document_string = $_REQUEST['document_string'];
        }else{
            $document_string = '';
        }
        
        if( isset($_REQUEST['select_entire_list']) ){
            $select_entire_list = $_REQUEST['select_entire_list'];
        }else{
            $select_entire_list = 0;
        }
        
        $documentIds = array();
        $sqlDocument = " SELECT id, document_name FROM documents WHERE deleted = 0 ";
        if( $select_entire_list == '0' && !empty($document_string) ){
            $documentIds = str_replace(',',"','",$document_string);
            $sqlDocument .= " AND id IN ('".$documentIds."') ";
        } else if( $select_entire_list == '1'){
            $sqlDocument .= " ";
        } else if ( empty($document_string)  && ($select_entire_list == '0') ){
            $document_string = '';
            $html = $this->noneSelected();
            echo json_encode(array( 'document_html' => $html, 'document_string' => $document_string ));
            die;
        }

        //echo $sqlDocument;
        
        $resultDocument =  $db->query($sqlDocument);
        
        $html = '<table id="documentnames">
	    <thead>
	       <tr><th>File name</th><th>File</th><th>Remove</th></tr>
        </thead>
	    <tbody>';
        
        $documentIds = array();
        
        $themeObject = SugarThemeRegistry::current();
        
        while ( $rowDocuemnt = $db->fetchByAssoc($resultDocument) ){
            
            $documentIds[] = $rowDocuemnt['id'];
            
            $html .= '<tr>
                        <td>'.$rowDocuemnt['document_name'].'</th>
                        <td><a href="index.php?entryPoint=download&id='.$rowDocuemnt['id'].'&type=Documents" target="_blank">'.$rowDocuemnt['document_name'].'</a></td>
                        <td><a href="javascript:void(0);" onClick="removeSelectedDocuments(\''.$rowDocuemnt['id'].'\')"><img src='.$themeObject->getImageURL('delete_inline.png').' border="0"></a></td>
                    </tr>';
            
        }
        
        $html .= ' </tbody>
	    </table>';
        
        $document_string = implode(',', $documentIds);
        
        echo json_encode(array( 'document_html' => $html, 'document_string' => $document_string ));
        die;
        
    }
    
    function noneSelected(){
        return <<<EOQ
        <table id="documentnames">
	    <thead>
	       <tr><th>File name</th><th>File</th><th>Remove</th></tr>
	       <tr id="nofiles">
	        <td colspan="3" id="ddmessage">
	            No files have been selected.
	        </td>
	       </tr>
	    </thead>
	    <tbody>
	    </tbody>
	  </table>
EOQ;
    }
    
}
?>
