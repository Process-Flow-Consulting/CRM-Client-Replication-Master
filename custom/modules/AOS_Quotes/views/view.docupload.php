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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


require_once('include/upload_file.php');
require_once 'custom/include/common_functions.php';

class AOS_QuotesViewDocupload extends SugarView
{
	var $upload_file;
	
 	public function display(){
 		
 		global $db, $current_user, $timedate;
		
	 	/* if ($_REQUEST['getswf']) {
	 		
		    $fileName ='custom/themes/default/images/flashuploader.swf';
		    header("Content-Type: application/x-shockwave-flash", true);
		    header("Content-Length: {strlen($fileName)}", true);
		    header("Accept-Ranges: bytes", true);
		    header("Connection: keep-alive", true);
		    header("Content-Disposition: inline; filename=$message->file_name");
		    readfile($fileName);
		    die();
		    
		}else{
			 */
			//$GLOBALS['log']->fatal($_REQUEST);
			//$GLOBALS['log']->fatal($_FILES);
			//$GLOBALS['log']->fatal($this->upload_file->mime_type);
			
	 		$this->upload_file = new UploadFile('filename');
	 		if(!$this->upload_file->confirm_upload()){
	 			echo json_encode( array('error' => 'Error in Upload.'));
	 			return;
	 		}
			
	 		$focus = new Document();
	 		$focus->disable_row_level_security = true;
	 		$focus->status_id = 'Active';
	 		$focus->revision = '1';
	 		$focus->category_id = 'Proposal';
	 		$focus->active_date = $timedate->nowDate();
	 		$focus->assigned_user_id = $current_user->id;
	 		
	 		if ( $this->upload_file->mime_type == 'application/octet-stream' )
	 		{
	 			if ( function_exists('finfo_file') )
	 			{
	 				$finfo = new finfo(FILEINFO_MIME);
	 				$type = $finfo->file($_FILES['filename']['tmp_name']);
	 				$php_mime = substr($type, 0, strpos($type, ';'));
	 			}
	 			elseif ( function_exists('mime_content_type') )
	 			{
	 				$php_mime = mime_content_type($_FILES['filename']['tmp_name']);
	 			}
	 			//$GLOBALS['log']->fatal($php_mime);
	 			if ( $php_mime )
	 			{	
	 				$this->upload_file->mime_type = $php_mime;
	 			}
	 		}
	 		
	 		//$GLOBALS['log']->fatal($this->upload_file->mime_type);
	 		if( isset($_FILES['filename']['name']) && !isSupportedDocument($this->upload_file->mime_type))
	 		{
				$GLOBALS['log']->fatal('Document Type Not Supported.');
 				echo json_encode( array('error' => 'Document Type Not Supported.'));
 				return;
	 		}
	 		
	 		require_once ('custom/modules/Users/filters/instancePackage.class.php');
	 		$admin=new Administration();
	 		$admin_settings = $admin->retrieveSettings('instance', true);
	 		$geo_filter = $admin->settings ['instance_geo_filter'];
	 		
	 		$obPackage = new instancePackage ();
	 		$pkgDetails = $obPackage->getPacakgeDetails();
	 		
	 		$current_upload_directory_size = getDirectorySize('upload/');
	 		$current_file_size = $_FILES['filename']['size'];
	 		
	 		/* if( ($current_upload_directory_size + $current_file_size) > $pkgDetails['upload_limit'] ){
	 			$GLOBALS['log']->fatal('Not enough Space in Directory.');
	 			echo json_encode( array('error' => 'Not enough Space in Directory.'));
	 			return;
	 		} */
	 		
	 		
	 		$focus->id = create_guid();
	 		$focus->disable_row_level_security = true;
	 		$focus->new_with_id = true;
	 		$focus->filename = $this->upload_file->get_stored_file_name();
	 		$focus->file_mime_type = $this->upload_file->mime_type;
	 		$focus->file_ext = $this->upload_file->file_ext;
	 		$focus->document_name = $focus->filename;
	 		
	 		$revision = new DocumentRevision();
	 		$revision->disable_row_level_security = true;
	 		$revision->filename = $this->upload_file->get_stored_file_name();
	 		$revision->file_mime_type = $this->upload_file->mime_type;
	 		$revision->file_ext = $this->upload_file->file_ext;
	 		$revision->revision = $focus->revision;
	 		$revision->document_id = $focus->id;
	 		$revision->change_log = translate('DEF_CREATE_LOG','Documents');
	 		$revision->doc_type = $focus->doc_type;
	 		$revision->in_workflow = true;
	 		$revision->not_use_rel_in_req = true;
	 		$revision->new_rel_id = $focus->id;
	 		$revision->new_rel_relname = 'Documents';
	 		$revision->save();
	 		
	 		$focus->document_revision_id = $revision->id;
			$focus->save();
			
			//echo '<pre>'; print_r($focus); echo '</pre>';

	 		$return_id = $revision->id;
	 		$this->upload_file->final_move($revision->id);
	 		
	 		$focus->save_relationship_changes(true);
	 		
	 		//echo 'Finished!';
	 		echo json_encode( array('success' => $focus->id));
	 		return;
		//}
 	}
}
