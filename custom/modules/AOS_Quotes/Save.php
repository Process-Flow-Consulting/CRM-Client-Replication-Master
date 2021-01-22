<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
	die ( 'Not A Valid Entry Point' );
/**
 * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License. Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party. Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited. You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution. See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License. Please refer to the License for the specific language
 * governing these rights and limitations under the License. Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 * ******************************************************************************
 */

/**
 * *******************************************************************************
 *
 * Description: TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ******************************************************************************
 */

require_once('include/upload_file.php');
require_once 'custom/include/common_functions.php';
require_once ('custom/modules/Users/filters/instancePackage.class.php');

//echo "<pre>"; print_r($_FILES); echo "</pre>";

/**
 * proposal verisoning
 * Hirak - 07.02.2013
 */
global $db, $current_user, $sugar_config, $timedate;
if (($_REQUEST ['is_form_updated'] == '1') && ($_REQUEST['proposal_delivery_method'] != 'M' ) ) {
	
	/**
	 * ###Modifiy the logic of proposal versioning###
	 * Proposal Versioning will be start if proposal is modified, scheduled or
	 * delivered and delivery date has passed.
	 * Modified By Satish Gupta on 20th Feb 2013
	 */
	
	$oldproposal = new Quote ();
	$oldproposal->retrieve ( $_REQUEST ['record'] );
	
	$old_delivery_date = $timedate->to_db($oldproposal->date_time_delivery);
	$old_delivery_date = date('Y-m-d H:i:s',strtotime($old_delivery_date)-3600);
	$now_gmt_date = $timedate->get_gmt_db_datetime();
	
	// echo '<pre>'; print_r($focus->fetched_row); echo '</pre>'; die;
	
	require_once 'custom/include/common_functions.php';
	
		
	//Creating the db master db connection and include the required files.
	/*
	require_once $sugar_config['master_config_path'] ;// '/vol/certificate/master_config.php';
	require_once 'custom/include/master_db/mysql.class.php';	
	require_once 'custom/modules/Quotes/schedule_quotes/class.easylinkmessage.php';
	require_once 'include/SugarDateTime.php';
	
	$obEasyLink = new easyLinkMessage($sugar_config['EASY_LINK_USER_NAME'], $sugar_config ['EASY_LINK_USER_PASS'] );	
	$cdb = $obEasyLink->__getCentralDB();
	
	//Get the status of proposal
	$sqlGetProposalStatus = "SELECT * FROM oss_proposalqueue 
					WHERE deleted = 0 AND instance_folder_name = '".$arAdminData->settings ['instance_account_name']."'
					AND ((proposal_schedule_status = 'scheduled' AND process_stat = '2')
        			OR (proposal_schedule_status = 'delivered' AND process_stat = '4'))
        			AND UTC_TIMESTAMP() >= date_sub(date_schedule,INTERVAL 60 MINUTE)";
	*/
	
	
	if($old_delivery_date < $now_gmt_date){
	//if ($oldproposal->proposal_sent_count > 0) {
		
		require_once ('include/Sugarpdf/SugarpdfFactory.php');
		
		$stFileName = $oldproposal->name . ' ' . $oldproposal->proposal_version . '.pdf';
		
		$note = new Note ();
		$note->disable_row_level_security = true;
		$note->id = create_guid ();
		
		$object_map = array ();
		$pdf = SugarpdfFactory::loadSugarpdf ( 'Standard', 'Quotes', $oldproposal, $object_map );
		$pdf->process ();
		$stTmpPdf = $pdf->Output ( "{$sugar_config['upload_dir']}{$note->id}", 'F' );
		
		if (file_exists ( "{$sugar_config['upload_dir']}{$note->id}" )) {
			
			$note->new_with_id = true; // duplicating the note with files
			$note->parent_id = $oldproposal->id;
			$note->parent_type = $oldproposal->module_dir;
			$note->name = $stFileName;
			$note->filename = $stFileName;
			$note->file_mime_type = Email::email2GetMime ( "{$sugar_config['upload_dir']}{$note->id}" );
			$note->team_id = $oldproposal->team_id;
			$note->team_set_id = $oldproposal->team_set_id;
			$note->assigned_user_id = $oldproposal->assigned_user_id;
			$note->save ();
			
			$_REQUEST ['note_id'] = $note->id;
		}
		
		$stUpdateSentData = 'UPDATE quotes SET proposal_sent_count = proposal_sent_count+1  WHERE  id = "' .$oldproposal->id  . '"';
		$db->query ( $stUpdateSentData );
	}
	
	
	
}

$request_date_time = $_REQUEST ['date_time_delivery'];

require_once ('include/formbase.php');
require_once ('modules/AOS_Quotes/config.php');
require_once ('include/SugarFields/SugarFieldHandler.php');

/*
 * use custom bean to overwrite notification body
 */
require_once ('custom/modules/AOS_Quotes/CustomQuote.php');
$focus = new CustomAOS_Quotes ();

$focus = populateFromPost ( '', $focus );

if (! $focus->ACLAccess ( 'Save' )) {
	ACLController::displayNoAccess ( true );
	sugar_cleanup ( true );
}

// we have to commit the teams here in order to obtain the team_set_id for use
// with products and product bundles.
/* if (empty ( $focus->teams )) {
	$focus->load_relationship ( 'teams' );
} */
/* $focus->teams->save ();
// bug: 35297 - set the teams to have not been saved, so workflow can update if
// necessary
$focus->teams->setSaved ( false ); */

if (! empty ( $_POST ['assigned_user_id'] ) && ($focus->assigned_user_id != $_POST ['assigned_user_id']) && ($_POST ['assigned_user_id'] != $current_user->id)) {
	$check_notify = TRUE;
} else {
	$check_notify = FALSE;
}

if (empty ( $focus->id )) {
	// bug 14323, add this to create products firsts, and create the quotes at
	// last, so the workflow can manipulate the products.
	$focus->id = create_guid ();
	$focus->new_with_id = true;
}

// remove the relate id element if this is a duplicate
if (isset ( $_REQUEST ['duplicateSave'] ) && isset ( $_REQUEST ['relate_id'] )) {
	// this is a 'create duplicate' quote, keeping the relate_id in focus will
	// assign the quote product bundles
	// to the original quote, not the new duplicate one, so we will unset the
	// element
	unset ( $_REQUEST ['relate_id'] );
}

global $beanFiles;
require_once ($beanFiles ['AOS_Products']);
$GLOBALS ['log']->debug ( "Saving associated products" );

// Fix bug 25509
$focus->process_save_dates = true;

// Mark delete of deleted products if product id is not empty 
// Modifed by Mohit kumar Gupta 31-01-2014
if (isset ( $_POST ['deleted_prod_id'] )) {
	$deleted_items = $_POST ['deleted_prod_id'];
	foreach ( $deleted_items as $key => $value ) {
	    if (!empty($value)) {
	       $product = new AOS_Products ();
	       $GLOBALS ['log']->debug ( "deleting product id " . $value );
	       $product->mark_deleted ( $value );
	       unset ( $product );
	    }		
	}
}

// Save Products
if (isset ( $_REQUEST ['product_type'] )) {
	$productType = $_REQUEST ['product_type'];
	
	//check the access of product catalog for add or modify
	//@modified By Mohit Kumar Gupta
	//@date 18-04-2014
	$disableProductCatalog = 0;
	if (!getProductCatalogUpdateAccess()) {
	    $disableProductCatalog = 1;
	}
	$i=0;
	foreach ( $productType as $key => $value ) {
	    //add one more condition if product_type is not empty
	    //modify the condition for sample text, check it is linked to any product catalog or not
	    //Mohit Kumar Gupta 31-01-2014 03-07-2014
		if (!empty($_REQUEST ['product_template_id'] [$key]) && trim($value)!='') {
			$product = new AOS_Products ();
			if (! empty ( $_REQUEST ['product_id'] [$key] )) {
				$product->retrieve ( $_REQUEST ['product_id'] [$key] );
			}
			$product->quote_id = $focus->id;
			$product->team_id = $focus->team_id;
			$product->team_set_id = $focus->team_set_id;
			$product->account_id = $focus->billing_account_id;
			$product->contact_id = $focus->billing_contact_id;
			if ($i == 0) {
               $product->indexs = 0;
            } else {
               $product->indexs = $i;
            }
			$product->product_type = $_REQUEST ['product_type'] [$key];
			
			// Fetch Product Type
			$pt = loadBean ( 'AOS_ProductTypes' );
			$pc_ptype = $_REQUEST ['product_type'] [$key];
			if ($_REQUEST ['product_type'] [$key] == 'alternates') {
				$pc_ptype = 'line_items';
			}
			$pt->retrieve_by_string_fields ( array (
					'name' => str_replace ( '_', ' ', $pc_ptype ) 
			) );
			
			$product->type_id = $pt->id;
			
			if (isset($_REQUEST ['product_template_id'] [$key]) && trim($_REQUEST ['product_template_id'] [$key])!='') {
				$product->product_template_id = $_REQUEST ['product_template_id'] [$key];
			}			
			
			// Prodcut Template Id
			//create or update product template if it is have permission to do
			//@modified By Mohit Kumar Gupta
			//@date 18-04-2014
			if (isset ( $_REQUEST ['product_template_id'] [$key] ) && $disableProductCatalog == '0') {				
				// Save Line Items, Inclusions and exclusions as product
				// catalogue.
				
				$create_new_pc = false;
				if (! empty ( $_REQUEST ['product_template_id'] [$key] )) {
					$pc = loadBean ( 'AOS_ProductTemplates' );
					$pc->retrieve ( $_REQUEST ['product_template_id'] [$key] );
					if (trim ( $_REQUEST ['product_name'] [$key] ) != trim ( $pc->name )) {
						$create_new_pc = true;
					}
					
					// Update product template if user wants to update the
					// value.
					if ($_REQUEST ['pc_modify'] [$key] == 1) {
						$pc = loadBean ( 'AOS_ProductTemplates' );
						$pc->retrieve ( $_REQUEST ['product_template_id'] [$key] );
						$pc->cost_price = $_REQUEST ['cost_price'] [$key];
						$pc->discount_price = $_REQUEST ['unit_price'] [$key];
						$pc->markup = $_REQUEST ['mark_up'] [$key];
						$pc->description = $_REQUEST ['product_description'] [$key];
						$pc->markup_inper = $_REQUEST ['markup_inper'] [$key];
						$pc->quantity = $_REQUEST ['quantity'] [$key];
						$pc->unit_measure = $_REQUEST ['unit_measure'] [$key];
						$pc->save ();
					}
				}
				
				// Create New Product template if template not already cretaed
				// or that name has been modified.
				if (empty ( $_REQUEST ['product_template_id'] [$key] ) || $create_new_pc == true) {
					
					$pc = loadBean ( 'AOS_ProductTemplates' );
					$pc->name = trim ( $_REQUEST ['product_name'] [$key] );
					
					// Fetch Product Type
					/*
					 * $pt = loadBean ( 'ProductTypes' ); $pc_ptype = $_REQUEST
					 * ['product_type'] [$key]; if($_REQUEST ['product_type']
					 * [$key]=='alternates'){ $pc_ptype = 'line_items'; }
					 * $pt->retrieve_by_string_fields ( array ('name' =>
					 * str_replace ( '_', ' ', $pc_ptype ) ) );
					 */
					
					$pc->type_id = $pt->id;
					$pc->cost_price = $_REQUEST ['cost_price'] [$key];
					$pc->discount_price = $_REQUEST ['unit_price'] [$key];
					$pc->markup = $_REQUEST ['mark_up'] [$key];
					$pc->description = $_REQUEST ['product_description'] [$key];
					$pc->markup_inper = $_REQUEST ['markup_inper'] [$key];
					$pc->quantity = $_REQUEST ['quantity'] [$key];
					$pc->unit_measure = $_REQUEST ['unit_measure'] [$key];
					$pc->save ();
					$product->product_template_id = $pc->id;
				}
			}
			
			// Flag if apply hours instead of price
			if (isset ( $_REQUEST ['in_hours_li'] [$key] )) {
				$product->in_hours = $_REQUEST ['in_hours_li'] [$key];
			}
			
			// Tax Amount
			if (isset ( $_REQUEST ['bb_tax'] [$key] )) {
				$product->bb_tax = $_REQUEST ['bb_tax'] [$key];
			}
			
			// Tax Percentage
			if (isset ( $_REQUEST ['bb_tax_per'] [$key] )) {
				$product->bb_tax_per = $_REQUEST ['bb_tax_per'] [$key];
			}
			
			// Shipping Value
			if (isset ( $_REQUEST ['bb_shipping'] [$key] )) {
				$product->bb_shipping = $_REQUEST ['bb_shipping'] [$key];
			}
			
			// Discount Price
			if (isset ( $_REQUEST ['discount_price'] [$key] )) {
				$product->discount_price = $_REQUEST ['discount_price'] [$key];
			}
			
			// Discount Amount
			if (isset ( $_REQUEST ['discount_amount'] [$key] )) {
				$product->discount_amount = $_REQUEST ['discount_amount'] [$key];
			}
			
			// Discount Select
			if (isset ( $_REQUEST ['discount_select'] [$key] )) {
				$product->discount_select = $_REQUEST ['discount_select'] [$key];
			}
			
			// Markup InPercentage
			if (isset ( $_REQUEST ['markup_inper'] [$key] )) {
				$product->markup_inper = $_REQUEST ['markup_inper'] [$key];
			}
			
			// Product Name
			if (isset ( $_REQUEST ['product_name'] [$key] )) {
				$product->name = $_REQUEST ['product_name'] [$key];
			}
			
			// Show Product Name Flag
			if (isset ( $_REQUEST ['title_show'] [$key] )) {
				$product->title_show = $_REQUEST ['title_show'] [$key];
			}
			
			// Product Description
			if (isset ( $_REQUEST ['product_description'] [$key] )) {
				$product->description = $_REQUEST ['product_description'] [$key];
			}
			
			// Show Product Description Flag
			if (isset ( $_REQUEST ['desc_show'] [$key] )) {
				$product->desc_show = $_REQUEST ['desc_show'] [$key];
			}
			
			// Quantity
			if (isset ( $_REQUEST ['quantity'] [$key] )) {
				$product->quantity = $_REQUEST ['quantity'] [$key];
				$product->unit_measure = $_REQUEST ['unit_measure'] [$key];
			}
			
			// Show Quantity Flag
			if (isset ( $_REQUEST ['qty_show'] [$key] )) {
				$product->qty_show = $_REQUEST ['qty_show'] [$key];
			}
			
			// Cost Price
			if (isset ( $_REQUEST ['cost_price'] [$key] )) {
				$product->cost_price = $_REQUEST ['cost_price'] [$key];
			}
			
			// Show Price Flag
			if (isset ( $_REQUEST ['price_show'] [$key] )) {
				$product->price_show = $_REQUEST ['price_show'] [$key];
			}
			
			// Mark up
			if (isset ( $_REQUEST ['mark_up'] [$key] )) {
				$product->list_price = $_REQUEST ['mark_up'] [$key];
			}
			
			// Product Total
			if (isset ( $_REQUEST ['group_total'] [$key] )) {
				$product->total = $_REQUEST ['group_total'] [$key];
			}
			
			// Show Product Total Flag
			if (isset ( $_REQUEST ['total_show'] [$key] )) {
				$product->total_show = $_REQUEST ['total_show'] [$key];
			}
			
			// Unit Price
			if (isset ( $_REQUEST ['unit_price'] [$key] )) {
				$product->unit_price = $_REQUEST ['unit_price'] [$key];
			}
			
			// Tax Class -- Added By Hirak
			if (isset ( $_REQUEST ['tax_class'] [$key] )) {
			    $product->tax_class = $_REQUEST ['tax_class'] [$key];
			}
			
			//save product only if it is having any related product template
			//@modified By Mohit Kumar Gupta
			//@date 18-04-2014
			if (isset($product->product_template_id) && $product->product_template_id !='') {
				$product->save ();
			}			
		}
		$i++;
	}
}

if ($_REQUEST ['subtotal'] != '') {
	$focus->subtotal_amount = $_REQUEST ['subtotal'];
}
if ($_REQUEST ['subtotal_inc'] != '') {
	$focus->subtotal_inclusion = $_REQUEST ['subtotal_inc'];
}
if ($_REQUEST ['grand_ship'] != '') {
	$focus->shipping = $_REQUEST ['grand_ship'];
}
if ($_REQUEST ['grand_tax'] != '') {
	$focus->tax = $_REQUEST ['grand_tax'];
}
if ($_REQUEST ['grand_total'] != '') {
	$focus->total = $_REQUEST ['grand_total'];
}
if ($_REQUEST ['grand_sub'] != '') {
	$focus->grand_subtotal = $_REQUEST ['grand_sub'];
}

require_once 'custom/include/OssTimeDate.php';
$oss_timedate = new OssTimeDate ();

if (! isset ( $_REQUEST ['from_dcmenu'] )) {
	
	// date time proposal scheduled delivery
	$due_date = $oss_timedate->convertDateForDB ( $request_date_time, $_REQUEST ['delivery_timezone'] );
	$focus->date_time_delivery = $due_date;
	
	// hirak - date : 11-10-2012
	// isset ( $_REQUEST ['delivery_method_email'] ) ?
	// $focus->delivery_method_email = 1 : $focus->delivery_method_email = 0;
	// isset ( $_REQUEST ['delivery_method_fax'] ) ? $focus->delivery_method_fax
	// = 1 : $focus->delivery_method_fax = 0;
	// isset ( $_REQUEST ['delivery_method_both'] ) ?
	// $focus->delivery_method_both = 1 : $focus->delivery_method_both = 0;
	
	$_REQUEST ['skip_delivery_date'] != '' ? $focus->skip_delivery_date = 1 : $focus->skip_delivery_date = 0;
	$_REQUEST ['skip_delivery_method'] != '' ? $focus->skip_delivery_method = 1 : $focus->skip_delivery_method = 0;
	$_REQUEST ['skip_line_items'] != '' ? $focus->skip_line_items = 1 : $focus->skip_line_items = 0;
}

if (isset ( $_REQUEST ['delivery_timezone'] ) && ! empty ( $_REQUEST ['delivery_timezone'] )) {
	
	// date time proposal sent
	if (isset ( $_REQUEST ['date_time_sent'] ) && ! empty ( $_REQUEST ['date_time_sent'] )) {
		$focus->date_time_sent = $oss_timedate->convertDateForDB ( $_REQUEST ['date_time_sent'], $_REQUEST ['delivery_timezone'] );
	}
	
	// date time proposal received
	if (isset ( $_REQUEST ['date_time_received'] ) && ! empty ( $_REQUEST ['date_time_received'] )) {
		$focus->date_time_received = $oss_timedate->convertDateForDB ( $_REQUEST ['date_time_received'], $_REQUEST ['delivery_timezone'] );
	}
	
	// date time proposal opened
	if (isset ( $_REQUEST ['date_time_opened'] ) && ! empty ( $_REQUEST ['date_time_opened'] )) {
		$focus->date_time_opened = $oss_timedate->convertDateForDB ( $_REQUEST ['date_time_opened'], $_REQUEST ['delivery_timezone'] );
	}
}

if (isset ( $GLOBALS ['check_notify'] )) {
	$check_notify = $GLOBALS ['check_notify'];
} else {
	$check_notify = FALSE;
}
$focus->save ( $check_notify );

$return_id = $focus->id;

/*********ADD DOCUMENT RELATIONSHIP*************/
$focus->load_relationship('documents');

if(isset($_REQUEST['doc_id']) && !empty($_REQUEST['doc_id']) && !isset($_REQUEST['IS_IE'])){
    $docIds = explode(',',$_REQUEST['doc_id']);
    foreach ($docIds as $docId){
        $focus->documents->add($docId);
    }
}


if(isset($_REQUEST['selected_doc_id']) && !empty($_REQUEST['selected_doc_id']) ){
    $docIds = explode(',',$_REQUEST['selected_doc_id']);
    foreach ($docIds as $docId){
        $focus->documents->add($docId);
    }
}


if(!empty($_FILES['filename']['size'])){
	
	$admin=new Administration();
	$admin_settings = $admin->retrieveSettings('instance', true);
	$geo_filter = $admin->settings ['instance_geo_filter'];

	$obPackage = new instancePackage ();
	$pkgDetails = $obPackage->getPacakgeDetails();
	
	$current_upload_directory_size = getDirectorySize('upload/');
	$current_file_size = $_FILES['filename']['size'];

	if( ($current_upload_directory_size + $current_file_size) > $pkgDetails['upload_limit'] ){
		$GLOBALS['log']->fatal('Not enough Space in Directory.');
		SugarApplication::appendErrorMessage('Not enough Space in Directory.');
	}else{
		$this->upload_file = new UploadFile('filename');
		if(!$this->upload_file->confirm_upload()){
			$GLOBALS['log']->fatal('Error in Upload.');
			SugarApplication::appendErrorMessage('Error in Upload.');
		}else{
			$document = new Document();
			$document->disable_row_level_security = true;
			$document->status_id = 'Active';
			$document->revision = '1';
			$document->category_id = 'Proposal';
			$document->active_date = $timedate->nowDate();
			$document->assigned_user_id = $current_user->id;

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
				if ( $php_mime )
				{	
					$this->upload_file->mime_type = $php_mime;
				}
			}
			if( isset($_FILES['filename']['name']) && !isSupportedDocument($this->upload_file->mime_type)){
				$GLOBALS['log']->fatal('Document Type Not Supported.');
				SugarApplication::appendErrorMessage('Document Type Not Supported.');
			}else{
				$document->id = create_guid();
				$document->disable_row_level_security = true;
				$document->new_with_id = true;
				$document->filename = $this->upload_file->get_stored_file_name();
				$document->file_mime_type = $this->upload_file->mime_type;
				$document->file_ext = $this->upload_file->file_ext;
				$document->document_name = $document->filename;

				$revision = new DocumentRevision();
				$revision->disable_row_level_security = true;
				$revision->filename = $this->upload_file->get_stored_file_name();
				$revision->file_mime_type = $this->upload_file->mime_type;
				$revision->file_ext = $this->upload_file->file_ext;
				$revision->revision = $document->revision;
				$revision->document_id = $document->id;
				$revision->change_log = translate('DEF_CREATE_LOG','Documents');
				$revision->doc_type = $document->doc_type;
				$revision->in_workflow = true;
				$revision->not_use_rel_in_req = true;
				$revision->new_rel_id = $document->id;
				$revision->new_rel_relname = 'Documents';
				$revision->save();

				$document->document_revision_id = $revision->id;
				$document->save();
				
				//$return_id = $revision->id;
				$this->upload_file->final_move($revision->id);

				$document->save_relationship_changes(true);
				
				if(!empty($document->id))
				$focus->documents->add($document->id);
			}
		}
	}
}
/*********ADD DOCUMENT RELATIONSHIP*************/

$GLOBALS ['log']->debug ( "Saved record with id of " . $return_id );
$return_module = 'AOS_Quotes';
if (! empty ( $_REQUEST ['return_module'] )) {
	$return_module = $_REQUEST ['return_module'];
}
handleRedirect ( $return_id, $return_module );

?>
