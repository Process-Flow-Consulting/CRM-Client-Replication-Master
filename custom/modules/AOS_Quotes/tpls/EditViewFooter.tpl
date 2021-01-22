{*
/*********************************************************************************
* The contents of this file are subject to the SugarCRM Master
Subscription * Agreement ("License") which can be viewed at *
http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
* By installing or using this file, You have unconditionally agreed to
the * terms and conditions of the License, and You may not use this file
except in * compliance with the License. Under the terms of the license,
You shall not, * among other things: 1) sublicense, resell, rent, lease,
redistribute, assign * or otherwise transfer Your rights to the
Software, and 2) use the Software * for timesharing or service bureau
purposes such as hosting the Software for * commercial gain and/or for
the benefit of a third party. Use of the Software * may be subject to
applicable fees and any use of the Software without first * paying
applicable fees is strictly prohibited. You do not have the right to *
remove SugarCRM copyrights from the source code or user interface. * *
All copies of the Covered Code must include on each user interface
screen: * (i) the "Powered by SugarCRM" logo and * (ii) the SugarCRM
copyright notice * in the same form as they appear in the distribution.
See full license for * requirements. * * Your Warranty, Limitations of
liability and Indemnity are expressly stated * in the License. Please
refer to the License for the specific language * governing these rights
and limitations under the License. Portions created * by SugarCRM are
Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/

*} {literal}
<style>
.layOptContainer{
width: 11%;

font-weight: normal;
position: relative;
left: 79px;
top: -36px;
}
</style>
<script type='text/javascript'>
     YAHOO.util.Event.onDOMReady(function(){
        var recordId = {/literal} '{$smarty.request.record}'; {literal}
        if(recordId ==''){
        var oppId = document.getElementById('opportunity_id').value;
        var parent_id = {/literal} '{$smarty.request.parent_id}'; {literal}
        var parent_type = {/literal} '{$smarty.request.parent_type}'; {literal}
        var parent_name = {/literal} '{$smarty.request.parent_name}'; {literal}
            if(oppId !=''){
                fillValueFromOpportunity(oppId);
            }else if(parent_type == 'Opportunities' && parent_id !='')
            {
            	document.getElementById('opportunity_id').value = parent_id;
            	document.getElementById('opportunity').value = parent_name;
            	fillValueFromOpportunity(parent_id);
            }
        }
      
    
    //Open Select Proposal Popup
     document.getElementById('select_proposal').onclick = function(){
		var sales_tax_flag = document.getElementById('sales_tax_flag').value;
        var popup_request_data = {
                    'call_back_function' : 'set_proposal_returns',
                    'form_name' : 'EditView',
                    'field_to_name_array' : {
                    'id' : 'id',
                    'name' : 'name'
                    }
                };

                open_popup('AOS_Quotes', 600, 400, '&sales_tax_flag_advanced='+sales_tax_flag, true, false, popup_request_data);
        }
    
    //Open Upload Document Popup
    document.getElementById('upload_document').onclick = function(){ 
    	window.open('index.php?module=AOS_Quotes&action=upload_document','upload_document','height=400,width=600');
    }

    });

    function set_proposal_returns(popup_reply_data){
        var name_to_value_array = popup_reply_data.name_to_value_array;
        var id = name_to_value_array['id'];
            getLineItems(id);      
    }

    function getLineItems(proposalId){
            ajaxStatus.showStatus('loading...');
    		var callback = {
                success:function(o){
					var result_arr = JSON.parse(o.responseText);
                    var line_items = result_arr[0];
                    var quote_info = result_arr[1];                    
                    pLen= line_items.length;                                     
                    for(var i=0; i < pLen; i++){

                    	tableName = line_items[i]['type']+'_table';
                        if(line_items[i]['type']=='line_items'){
							if(document.getElementById('name_0')){
	                            if(document.getElementById('name_0').value == 'This is Sample Text.' && trim(document.getElementById('product_template_id_0').value) == ''){
	                            		deleteLineItemRow(0,tableName);	
	                            }
							}								
                         }

                        if(line_items[i]['type']=='inclusions'){
							if(document.getElementById('name_1')){
	                            if(document.getElementById('name_1').value == 'This is Sample Text.' && trim(document.getElementById('product_template_id_1').value) == ''){
	                            		deleteLineItemRow(1,tableName);	
	                            }
							}								
                         }

                        if(line_items[i]['type']=='exclusions'){
							if(document.getElementById('name_2')){
	                            if(document.getElementById('name_2').value == 'This is Sample Text.' && trim(document.getElementById('product_template_id_2').value) == ''){
	                            		deleteLineItemRow(2,tableName);	
	                            }
							}								
                         }
                                                                       
                    	addLineItemRow(
	                       	'',
	                       	line_items[i]['type'],
	                       	line_items[i]['qty'],
	                       	line_items[i]['qty_show'],	                       	
	                       	line_items[i]['product_template_id'],
	                       	html_entity_decode(line_items[i]['name']),
	                       	line_items[i]['title_show'],
	                       	line_items[i]['cost_price'],
	                       	line_items[i]['price_show'],
	                       	line_items[i]['list_price'],
	                       	line_items[i]['total'],
	                       	line_items[i]['total_show'],
	                       	line_items[i]['discount_price'],
	                       	line_items[i]['in_hours'],
	                       	line_items[i]['unit_price'],
	                       	'',
	                       	line_items[i]['bb_tax'],
	                       	line_items[i]['bb_tax_per'],
	                       	'',
	                       	tableName,
	                       	'',	                       	
	                       	'',
	                       	'',	                       	
	                       	html_entity_decode(line_items[i]['description']),
	                       	line_items[i]['desc_show'],
	                       	'',
	                       	line_items[i]['discount_amount'],
	                       	line_items[i]['discount_select'],
	                       	'',	                       	
	                       	line_items[i]['shipping'],
	                       	line_items[i]['markup_inper'],
	                       	0,
	                       	line_items[i]['unit_measure'],
	                       	line_items[i]['unit_measure_name'],
							line_items[i]['tax_class']  //Tax Class - Added by Hirak
	                   );
    	                  
                   }
                    var subtotal = parseFloat(document.getElementById('subtotal').value);
                    var subtotal_inc = parseFloat(document.getElementById('subtotal_inc').value);                    

                    subtotal = subtotal + parseFloat(quote_info['subtotal']);
                    subtotal_inc = subtotal_inc + parseFloat(quote_info['subtotal_inclusion']);
                    document.getElementById('subtotal_html').innerHTML = subtotal.toFixed(2); 
                    document.getElementById('subtotal_inc_html').innerHTML = subtotal_inc.toFixed(2);

					var sales_tax_flag = document.getElementById('sales_tax_flag').value;
					if(sales_tax_flag == 'total_item'){
						document.getElementById('taxrate_id').value = result_arr.taxrate_id;
						chengeTaxRate();
					}else{
						calculateSubTotal();
					}
                    
                    tinyMCE.activeEditor.setContent($("<div/>").html(result_arr.desc).text());
                    for(elmName in result_arr.layout_options)
                    { 
                       if($('input[name='+elmName+']').attr('type') == 'radio'){
						$(':radio[value='+result_arr.layout_options[elmName]+']').attr('checked',1)
                       }else if(result_arr.layout_options[elmName] ){                         
                         $('input[name='+elmName+']').attr('checked','checked');                         
                       }else{
                         $('input[name='+elmName+']').removeAttr('checked')
                       }
                    }
                    
                    
                    ajaxStatus.hideStatus();
                }
            }
        var connectionObject = YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=AOS_Quotes&action=get_line_items&to_pdf=true&proposalId='+proposalId, callback);
    }


    document.getElementById('btn_opportunity').onclick = function(){
                getOpportunity();
            }

            function getOpportunity(){
                var popup_request_data = {
                    'call_back_function' : 'set_returns',
                    'form_name' : 'EditView',
                        'field_to_name_array' : {
                        'id' : 'id',
                        'name' : 'name'
                        }
                };

                open_popup('Opportunities', 600, 400, '&from_bean=AOS_Quotes', true, false, popup_request_data);
            } 

            function set_returns(popup_reply_data){
                var name_to_value_array = popup_reply_data.name_to_value_array;
                var id = name_to_value_array['id'];
                var name = name_to_value_array['name'];
                document.getElementById('opportunity').value=name;
                document.getElementById('opportunity_id').value = id;
                fillValueFromOpportunity(id);
            }

           function fillValueFromOpportunity(oppId){
             ajaxStatus.showStatus('Please wait while loading...');
             callback = {
        success:function(o){
                    var result = JSON.parse(o.responseText);
                    document.getElementById('name').value = document.getElementById('opportunity').value; 
                    document.getElementById('billing_account').value = result['client_name'];
                    document.getElementById('billing_account_id').value = result['client_id'];
                    if(result['contact_name'])
                    document.getElementById('billing_contact').value = result['contact_name'];
                    if(result['contact_id'])
                    document.getElementById('billing_contact_id').value = result['contact_id'];
                    if(result['phone'])
                    document.getElementById('contact_phone').value = result['phone'];
                    if(result['fax'])
                    document.getElementById('contact_fax').value = result['fax'];
                    if(result['email'])
                    document.getElementById('contact_email').value = result['email'];
                    if(result['amount']){
                        var opportunity_amount = parseFloat(result['amount']);
                    	document.getElementById('proposal_amount').innerHTML = opportunity_amount.toFixed(2);
                    	document.EditView.proposal_amount.value = opportunity_amount.toFixed(2);
                    	document.getElementById('opportunity_amount').value = opportunity_amount.toFixed(2);
                    }
                    /**
                    * Remove Autofill Schedule Delivery Date and Timezone
                    * By Satish Gupta on 09-04-2012
                    */                   
                    //document.getElementById('delivery_timezone').value = result['time_zone'];
                    //document.getElementById('date_time_delivery_date').value = result['date_closed'];
                    //document.getElementById('date_time_delivery_hours').value = result['date_closed_hours'];
                    //document.getElementById('date_time_delivery_minutes').value = result['date_closed_minutes'];                    
                    //document.getElementById('date_time_delivery').value = result['full_date_closed'];

                    //if(document.getElementById('date_time_delivery_meridiem'))
                    //document.getElementById('date_time_delivery_meridiem').value = result['date_closed_meridien'];
                    /**
                    * address also pre filled by client contacts
                    * By Mohit Kumar Gupta on 08-01-2014
                    */
					if(result['billing_address_street'])
                    document.getElementById('billing_address_street').value = result['billing_address_street'];
                    if(result['billing_address_city'])
                    document.getElementById('billing_address_city').value = result['billing_address_city'];
                    if(result['billing_address_state'])
                    document.getElementById('billing_address_state').value = result['billing_address_state'];
                    if(result['billing_address_postalcode'])
                    document.getElementById('billing_address_postalcode').value = result['billing_address_postalcode'];
                    //use for setting function after sqs call on client contact	
					SUGAR.util.doWhen("typeof(sqs_objects['EditView_billing_contact']) != 'undefined'",setSqsClientContact);	
                    ajaxStatus.hideStatus();

                }
            }
            var connectionObject = YAHOO.util.Connect.asyncRequest ('GET', 'index.php?module=AOS_Quotes&action=opportunity_detail&to_pdf=true&oppId='+oppId, callback);
            }
</script>
{/literal}


<!-- Upload Document -->
{literal}
<style>
#filelist {clear:both; margin-top: 15px;}
#documentlist{ clear:both; margin-top: 15px;}
#uploaderContainer, #selectContainer{ width: 180px; float: left;}
#ieUploaderContainer{ width: 350px; float: left;}
#uploadFilesButtonContainer, #selectFilesButtonContainer, #overallProgress { display: inline-block;}
#overallProgress { float: right;}
.yellowBackground { background: #F2E699;}
</style>
{/literal}
{if ($IS_IE neq 1)}
    {sugar_getscript file="custom/modules/Documents/yui-min.js"}
    {sugar_getscript file="custom/modules/AOS_Quotes/proposalDocuments.js"}
{/if}
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					 <td width="60%">
					 
			{if ($IS_IE neq 1)}
		    <div id="uploaderContainer">
			    <div id="selectFilesButtonContainer">
			    </div>
			    <div id="overallProgress">
			    </div>
			</div>
			<div id="selectContainer">
			    <input type="button" name="select_document" id="select_document" value="Select Document">
			    <textarea name="selected_doc_id" id="selected_doc_id" style="display:none;"></textarea>
			</div>
			<div id="filelist">
			  <table id="filenames">
			    <thead>
			       <tr><th>File name</th><th>File size</th><th>Percent uploaded</th></tr>
			       <tr id="nofiles">
			        <td colspan="3" id="ddmessage">
			            No files have been uploaded.
			        </td>
			       </tr>
			    </thead>
			    <tbody>
			    </tbody>
			  </table>
			</div>
			<div id="documentlist">
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
			</div>
			<input type="hidden" name="resetProposal" value="0" />
			<textarea name="doc_id" id="doc_id" style="display:none;"></textarea>     
			{else}
			<div id="ieUploaderContainer">
			    <input id="filename" name="filename" type="file" title="" size="30" maxlength="255">
                <input type="hidden" name="IS_IE" value="1" />
			</div>
			<div id="selectContainer">
			    <input type="button" name="select_document" id="select_document" value="Select Document">
			    <textarea name="selected_doc_id" id="selected_doc_id" style="display:none;"></textarea>
			</div>
			<div id="documentlist">
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
			</div>
          	{/if}
          	
					 </td> 
					 <td width="40%">
					     
					 </td>
				</tr>
			</table>
		</td>
	</tr>
</table></div>
<input type="hidden" name="is_ie" id="is_ie" value={$IS_IE}>
<input type="hidden" name="proposal_amount" id="proposal_amount" value={$PROPOSAL_AMOUNT}>
<input type="hidden" name="opportunity_amount" id="opportunity_amount" value={$OPPORTUNITY_AMOUNT}>

<!-- Select Previous Proposal -->
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<!--  <tr>
					<th align="left" scope="row" scope="row"><h4>{$MOD.LBL_SELECT_PREVIOUS_PROPOSAL}</h4>
					</th>
				</tr>
				-->
				<tr>
					 <td><input type="button" id="select_proposal"
						name="select_proposal" value="Select Previous Proposal"> <!-- <input
						type="button" id="upload_document" name="upload_document"
						value="Upload"> --> {$ATTACHED_DOCUMENTS} <input type="hidden" id="documentId"
						name="documentId" value=""> <input type="hidden" id="documentName"
						name="documentName" value=""> <input type="hidden"
						id="attach_documentId" name="attach_documentId" value=""> <input
						type="hidden" id="attach_documentName" name="attach_documentName"
						value=""> 
						<div id="upload_container">
							<div id="attach_div"></div>
							<div id="doc_div"></div>
						</div></td> 
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<input type="hidden" value="{$DISABLEPRODUCTCATALOG}" name="disable_product_catalog" id="disable_product_catalog">
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th align="left" scope="row" scope="row"><h4>{$MOD.LBL_LINE_ITEM_INFORMATION}</h4>
					<div class="layOptContainer">
					 <input type="checkbox" name='line_items' value='1' {if $LAYOUT_OPTIONS.line_items neq 0} checked{/if} />					
					 {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT}
					 </div>
					</th>
				</tr>				
				<tr>
					<td>
						<div id="add_table_line_items">
							<table width="100%" border="0">
								<tbody>
									<tr>
										<td>
											<table width="100%" border="0" name="line_items_table"
												id="line_items_table">
												<tbody>
													<tr>
														<td class="dataLabel">&nbsp;</td>
														<td class="dataLabel">{$MOD.LBL_TITLE}</td>
														<td class="dataLabel">{$MOD.LBL_DESCRIPTION}</td>
														<td class="dataLabel" colspan="2" >{$MOD.LBL_QUANTITY}</td>
														<td class="dataLabel">{$MOD.LBL_PRICE}</td>
														<td class="dataLabel">{$MOD.LBL_MARKUP}</td>
														<td class="dataLabel">{$MOD.LBL_UNIT_PRICE}</td>
														<td class="dataLabel">{$MOD.LBL_TOTAL}</td>
													</tr>
													<tr>
														<td colspan="9"><hr></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td><input type="button" name="add_item" id="add_line_items"
											value="Add Item"
											onclick="{if $DISABLEPRODUCTCATALOG eq 1} showProductTemplatePopUp {else} showAddItemDiv {/if}(-1,'line_items',this.id);"></td>
									</tr>
									<tr>
										<td>
											<div id="add_li_div"
												style="width: 500px; border: 1px solid #ABC3D7; background-color: #FFFFFF; display: none; position: absolute; border-radius: 6px 6px 6px 6px; box-shadow: 0px 2px 10px rgb(153, 153, 153); z-index: 2;">
												<table width="100%" border="0" cellspacing="0"
													cellpadding="0">
													<tr>
														<td>{$MOD.LBL_PRODUCTS}</td>
														<td colspan="3" align="right">
														<input type="hidden" name="pre_pc_name" id="pre_pc_name" value="">
														<input type="hidden" name="pre_unit_measure" id="pre_unit_measure" value="">
														<input type="hidden" name="pre_cost_price" id="pre_cost_price" value="0.00">
														<input type="hidden" name="pre_unit_price" id="pre_unit_price" value="0.00">
														<input type="hidden" name="pre_markup" id="pre_markup" value="0.00">
														<input type="hidden" name="pre_markup_inper" id="pre_markup_inper" value="0.00">
														<input type="hidden" name="pre_desc" id="pre_desc" value="">
														<input type="hidden" name="pre_quantity" id="pre_quantity" value="1">														
														<input type="text" name="product_tpl_name" id="product_tpl_name" value="" style="border: 0;"> 
														<input type="hidden" name="product_tpl_id" id="product_tpl_id" value="">
															Choose Existing&nbsp;
															<button value="Select" class="button" id="btn_product" name="btn_product" type="button">
																<img src="themes/default/images/id-ff-select.png?s=7ffb40711ab82f9fe5e580baf43ce4de&amp;c=1&amp;developerMode=1606689791">
															</button></td>
													</tr>
													<tr id="tr_hr1">
														<td colspan="4"><hr></td>
													</tr>
													<tr>
														<td colspan="3"></td>
														<td align="center">{$MOD.LBL_SHOW_ON_PROPOSAL}</td>
													</tr>
													<tr>
														<td>{$MOD.LBL_TITLE}:</td>
														<td colspan="2" align="right"><span
															style="text-align: left;"> <input
																class="sqsEnabled sqsNoAutofill" type="text"
																name="pop_product_name" id="pop_product_name" size="30" {if $DISABLEPRODUCTCATALOG eq 1}disabled=true{/if}>
														</span></td>
														<td align="center"><input type="checkbox" checked="checked"
															name="product_show" id="product_show" value="1">
															</td>
													</tr>
													<tr>
														<td>{$MOD.LBL_DESCRIPTION}:</td>
														<td colspan="2" align="right"><textarea
																name="pop_product_description"
																id="pop_product_description" rows="3" cols="30"></textarea></td>
														<td align="center"><input type="checkbox" checked="checked"
															name="product_desc_show" id="product_desc_show" value="1">
															</td>
													</tr>
													<tr id="tr_qty">
														<td colspan="2"><input type="radio" id="radio_qty"
															checked="checked" name="in_hours" value="qty"
															onclick="document.getElementById('radio_price').checked=true;document.getElementById('in_hours_hnd').value='';">
															{$MOD.LBL_QTY} <input type="radio" id="radio_hours"
															name="in_hours" value="hours"
															onclick="document.getElementById('radio_rate').checked=true;document.getElementById('in_hours_hnd').value=1;">
															{$MOD.LBL_HOURS} <input type="hidden" id="in_hours_hnd"
															name="in_hours_hnd" value=""></td>
														<td align="right">
														<table>
															<tr>
																<td >
																<input type="text" align="right" name="pop_quantity" id="pop_quantity"	maxlength="9" onchange="lineItemCalculate();" value="1" size="10">
																</td>
															<td>
															<select name="pop_unit_measure" id="pop_unit_measure" {$UNIT_OF_MEASURE_DISABLED}>{html_options options=$AR_UNIT_MEASURE }</select></td>
															</tr></table></td>
														<td align="center"><input type="checkbox" checked="checked"
															name="quantity_show" id="quantity_show" value="1">
															</td>
													</tr>
													<tr id="tr_price">
														<td colspan="2" id="tr_price_opt"><span
															id="price_label_span" style="display: none;">{$MOD.LBL_PRICE}</span>
															<span id="price_radio_span"><input type="radio"
																id="radio_price" checked="checked" name="in_rates"
																value="price"
																onclick="document.getElementById('radio_qty').checked=true;document.getElementById('in_hours_hnd').value='';">
																{$MOD.LBL_PRICE} <input type="radio" id="radio_rate"
																name="in_rates" value="rate"
																onclick="document.getElementById('radio_hours').checked=true;document.getElementById('in_hours_hnd').value=1;">
																{$MOD.LBL_RATES}</span></td>
														<td id="tr_pop_price" align="right"><input type="text"
															name="pop_price" maxlength="9" id="pop_price"
															onchange="lineItemCalculate();" value="0.00"></td>
														<td align="center">({$MOD.LBL_NEVER_SHOW})</td>
													</tr>
													<tr id="tr_markup">
														<td>{$MOD.LBL_MARKUP}:</td>
														<td>{$MOD.LBL_IN_PERCENT} <input type="checkbox"
															name="pop_markup_inper" id="pop_markup_inper"
															onclick="lineItemCalculate();" value="1"></td>
														<td align="right"><input type="text" name="pop_markup" maxlength="9" 
															id="pop_markup" onchange="lineItemCalculate();"
															value="0.00"></td>
														<td align="center">({$MOD.LBL_NEVER_SHOW})</td>
													</tr>
													<tr id="tr_unit_price">
														<td>{$MOD.LBL_UNIT_PRICE}</td>
														<td colspan="2" align="right"><input type="text"
															name="pop_unit_price" id="pop_unit_price" size="30"
															value="0.00" style="border: 0;" readOnly></td>
														<td align="center"><input type="checkbox" checked="checked"
															name="pop_price_show" id="pop_price_show" value="1">
															</td>
													</tr>
													<tr id="tr_total">
														<td>{$MOD.LBL_TOTAL}:</td>
														<td colspan="2" align="right"><input type="text"
															name="pop_total" id="pop_total" size="30" value="0.00"
															style="border: 0;" readOnly></td>
														<td align="center"><input type="checkbox" checked="checked"
															name="pop_total_show" id="pop_total_show" value="1">
															</td>
													</tr>
													<tr id="tr_hr2">
														<td colspan="4"><hr></td>
													</tr>
													<tr id="tr_adv_option">
														<td colspan="4" align="left">
														<div style="float: left; height: 22px;vertical-align: middle;">
														<a id="displayText"	style="cursor: pointer;" onclick="toggle();">
															<img src="themes/default/images/show_submenu_shortcuts.gif" alt="show_image" border="0">
														</a></div>	
														<div style="height:22px; vertical-align: middle; float:left; padding-top: 3px;">													
														<a onclick="toggle();" style="cursor: pointer;">Advanced Options</a>
														</div>
														</td>
													</tr>
													<tr id="tr_adv_opt_content" style="display: none;">
														<td colspan="4">
															<table border="0" cellspacing="0" cellpadding="0"
																width="100%">
																<tr>
																	<td align="right">{$MOD.LBL_TAX}:</td>
																	<td align="left">
																		{* Tax Class -- Added By Hirak *}
                                                                        {if $SALES_TAX_FLAG eq 'total_item'}
																		<select name="pop_tax_class"
																		 id="pop_tax_class"  onchange="lineItemCalculate();">
                                                                        {html_options options=$AR_TAX_CLASS selected='Taxable' }</select>
																		{else}	
																		<input type="text" name="pop_tax"
																		id="pop_tax" size="5" onchange="lineItemCalculate();"
																		value="0.00">% <input type="hidden"
																		name="pop_tax_amount" id="pop_tax_amount" value="0.00">
																		{/if}
																		{* Tax Class -- Added By Hirak *}
																	</td>
																	<td align="right">{$MOD.LBL_SHIPPING}</td>
																	<td>$<input type="text" name="pop_shipping" maxlength="9"
																		id="pop_shipping" size="5"
																		onchange="lineItemCalculate();" value="0.00">
																	
																	<td align="right">{$MOD.LBL_DISCOUNT}:</td>
																	<td align="left"><input type="text" name="pop_discount"
																		id="pop_discount" size="5"
																		onchange="lineItemCalculate();" value="0.00"> <input
																		type="hidden" name="pop_discount_price"
																		id="pop_discount_price" value="0.00"></td>
																	<td>{$MOD.LBL_IN_PERCENT}</td>
																	<td><input type="checkbox" name="pop_disc_inper"
																		id="pop_disc_inper" onclick="lineItemCalculate();"
																		value="1"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td colspan="4" align="center"><hr></td>
													</tr>
													<tr>
														<td colspan="4" align="center"><input type="button"
															name="pop_save" id="pop_save" value="Save"> <input
															type="button" name="pop_delete" id="pop_delete"
															value="Delete"> <input type="button" name="pop_cancel"
															id="pop_cancel" value="Cancel"></td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td><hr></td>
									</tr>
								</tbody>
							</table>
							<table width="100%" border="0">
								<tbody>
									<tr>
									    <td width="85%" align="right"  > {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT} &nbsp;<input type="checkbox" name='line_itmes_subtotal' value='1' {if $LAYOUT_OPTIONS.line_itmes_subtotal neq 0} checked{/if} /></td>
										<td width="5%" align="right" style="font-weight: bold;">{$MOD.LBL_SUBTOTAL}:</td>
										<td width="10%"  align="right"><span id="subtotal_html"
											style="text-align: right; font-weight: bold;">{$subtotal}</span>
											<input type="hidden" name="subtotal" id="subtotal"
											value="{$bean->subtotal|number_format:2:'.':''}"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th align="left" scope="row" scope="row"><h4>{$MOD.LBL_INCLUSION_INFORMATION}</h4>
					 <div class="layOptContainer">
					  <input type="checkbox" name='inclusions' value='1' {if $LAYOUT_OPTIONS.inclusions neq 0} checked{/if} />					
					  {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT}
					 </div>
					</th>
				</tr>
				<tr>
					<td>
						<div id="add_table_inclusions">
							<table width="100%" border="0">
								<tbody>
									<tr>
										<td>
											<table width="100%" border="0" name="inclusions_table"
												id="inclusions_table">
												<tbody>
													<tr>
														<td class="dataLabel">&nbsp;</td>
														<td class="dataLabel">{$MOD.LBL_TITLE}</td>
														<td class="dataLabel">{$MOD.LBL_DESCRIPTION}</td>
														<td class="dataLabel">{$MOD.LBL_PRICE}</td>
														<td class="dataLabel">{$MOD.LBL_MARKUP}</td>
														<td class="dataLabel">{$MOD.LBL_TOTAL}</td>
													</tr>
													<td colspan="6"><hr></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td><input type="button" name="add_item_inclusions"
											id="add_item_inclusions" value="Add Item"
											onclick="{if $DISABLEPRODUCTCATALOG eq 1} showProductTemplatePopUp {else} showAddItemDiv {/if}(-1,'inclusions',this.id);"></td>
									</tr>
									<tr>
										<td><hr></td>
									</tr>
								</tbody>
							</table>
							<table width="100%" border="0">
								<tbody>
									<tr> 
										<td width="85%" align="right"  > {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT} &nbsp;
										<input type="checkbox" name='inclusion_subtotal' value='1' {if $LAYOUT_OPTIONS.inclusion_subtotal neq 0} checked{/if} /></td>
										<td width="5%" align="right" style="font-weight: bold;">{$MOD.LBL_SUBTOTAL}:</td>
										<td width="10%" align="right"><span id="subtotal_inc_html"
											style="text-align: right; font-weight: bold;">{$subtotal_inclusion}</span>
											<input type="hidden" name="subtotal_inc" id="subtotal_inc"
											value="{$bean->subtotal_inclusion|number_format:2:'.':''}"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th align="left" scope="row" scope="row"><h4>{$MOD.LBL_EXCLUSION_INFORMATION}</h4>
					<div class="layOptContainer">
					  <input type="checkbox" name='exclusions' value='1' {if $LAYOUT_OPTIONS.exclusions neq 0} checked{/if} />					
					  {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT}
					 </div>
					</th>
				</tr>
				<tr>
					<td>
						<div id="add_table_exclusions">
							<table width="100%" border="0">
								<tbody>
									<tr>
										<td>
											<table width="100%" border="0" name="exclusions_table"
												id="exclusions_table">
												<tbody>
													<tr>
														<td class="dataLabel">&nbsp;</td>
														<td class="dataLabel">{$MOD.LBL_TITLE}</td>
														<td class="dataLabel">{$MOD.LBL_DESCRIPTION}</td>

													</tr>
													<tr>
														<td colspan="3"><hr></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td><input type="button" name="add_item_exclusions"
											id="add_item_exclusions" value="Add Item"
											onclick="{if $DISABLEPRODUCTCATALOG eq 1} showProductTemplatePopUp {else} showAddItemDiv {/if}(-1,'exclusions',this.id);"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0"  >
				<tr>
					<td colspan="4">
						<div id='grand_tally' style='display: inline; float: right;width:100%'>
							<table border="0" cellspacing="0" cellpadding="0"  width="100%">
								<tr>
								    {* Tax Class -- Added By Hirak *}
                                    {if $SALES_TAX_FLAG eq 'total_item'}
    									 <td width = "10%" align="left"> {$MOD.LBL_TAXRATE} </td>
    								     <td width = "20%" align="left" >
    										<select name="taxrate_id" id="taxrate_id" onchange="chengeTaxRate();">
                                             {$AR_TAX_RATES}
											</select>
											<input type="hidden" id="tax_rate" name="tax_rate" value="{$TAX_RATE}"> 
    									 </td>
									{else}
										<td width = "10%" align="left"></td>
										<td width = "20%" align="left"></td>
									{/if}
									{* Tax Class -- Added By Hirak *}
								    <td width="55%" align="right" style="vertical-align: inherit;"> {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT} &nbsp;
										<input type="checkbox" name='exclusions_total' value='1' {if $LAYOUT_OPTIONS.exclusions_total neq 0} checked{/if} />
								    </td>
									<td width="5%"  scope="row" NOWRAP style="text-align: left;">{$MOD.LBL_TOTAL}:</td>
									<td width="10%" scope="row" NOWRAP><div style="text-align: right;"
											id='grand_sub_div'>{$grand_subtotal}</div> <input
										type="hidden" id="grand_sub" name="grand_sub"
										value="{$bean->grand_subtotal|number_format:2:'.':''}"></td>
								</tr>
								<tr>
									<td width="85%" colspan="3" align="right"  style="vertical-align: inherit;"> {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT} &nbsp;
										<input type="checkbox" name='exclusions_tax' value='1' {if $LAYOUT_OPTIONS.exclusions_tax neq 0} checked{/if} />
								     </td>
									{* Tax Class -- Edited By Hirak *}
									<td width="5%"  scope="row" NOWRAP style="text-align: left;">{$MOD.LBL_TAX}{if $SALES_TAX_FLAG eq 'total_item'} <span  id="tax_rate_label">{$TAX_RATE_PER}</span>{/if}:</td>
									{* Tax Class -- Edited By Hirak *}
									<td width="10%" scope="row" NOWRAP><div style="text-align: right;"
											id='grand_tax_div'>{$tax}</div> <input type="hidden"
										id="grand_tax" name="grand_tax" value="{$bean->tax|number_format:2:'.':''}"></td>
								</tr>
								<tr>
									<td width="85%" colspan="3"  align="right"  style="vertical-align: inherit;"> {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT} &nbsp;
										<input type="checkbox" name='exclusions_shipping' value='1' {if $LAYOUT_OPTIONS.exclusions_shipping neq 0} checked{/if} />
								     </td>
									<td width="5%"  scope="row" NOWRAP style="text-align: left;">{$MOD.LBL_SHIPPING}</td>
									<td width="10%" scope="row" NOWRAP><div style="text-align: right;"
											id='grand_ship_div'>{$shipping}</div> <input type="hidden"
										id="grand_ship" name="grand_ship" value="{$bean->shipping|number_format:2:'.':''}"></td>
								</tr>
								<tr>
								     <td width="85%" colspan="3"  align="right" > 
										
								     </td>
									<td scope="row">&nbsp;</td>
									<td scope="row">&nbsp;</td>
								</tr>
								<tr>
								    <td width="85%" colspan="3"  align="right"  style="vertical-align: inherit;"> {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT} &nbsp;
										<input type="checkbox" name='exclusions_grand_total' value='1' {if $LAYOUT_OPTIONS.exclusions_grand_total neq 0} checked="checked" {/if} />
								     </td>
									<td width="5%" scope="row" NOWRAP style="text-align: left;">{$MOD.LBL_LIST_GRAND_TOTAL}:</td>
									<td width="10%" scope="row" NOWRAP>
										<div style="text-align: right;" id='grand_total_div'>{$total}</div>
										<input type="hidden" id="grand_total" name="grand_total"
										value="{$bean->total|number_format:2:'.':''}">
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th align="left" scope="row" scope="row"><h4>{$MOD.LBL_ALTERNATES_INFORMATION}</h4>
					<div class="layOptContainer">
					  <input type="checkbox" name='alternates' value='1' {if $LAYOUT_OPTIONS.alternates neq 0} checked="checked" {/if} />					
					  {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT}
					 </div>
					</th>
				</tr>				
				<tr>
					<td>
						<div id="add_table_alternates">
							<table width="100%" border="0">
								<tbody>
									<tr>
										<td>
											<table width="100%" border="0" name="alternates_table"
												id="alternates_table">
												<tbody>
													<tr>
														<td class="dataLabel">&nbsp;</td>
														<td class="dataLabel">{$MOD.LBL_TITLE}</td>
														<td class="dataLabel">{$MOD.LBL_DESCRIPTION}</td>
														<td class="dataLabel" colspan="2">{$MOD.LBL_QUANTITY}</td>
														<td class="dataLabel">{$MOD.LBL_PRICE}</td>
														<td class="dataLabel">{$MOD.LBL_MARKUP}</td>
														<td class="dataLabel">{$MOD.LBL_UNIT_PRICE}</td>
														<td class="dataLabel">{$MOD.LBL_TOTAL}</td>
													</tr>
													<tr>
														<td colspan="9"><hr></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td><input type="button" name="add_item" id="add_alternates"
											value="Add Item"
											onclick="{if $DISABLEPRODUCTCATALOG eq 1} showProductTemplatePopUp {else} showAddItemDiv {/if}(-1,'alternates',this.id);"></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><hr></td>
									</tr>
								</tbody>
							</table>							
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<div class="panel panel-default">
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	class="edit view">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th align="left" scope="row" colspan="2" scope="row"><h4>{$MOD.LBL_DESCRIPTION_AS_TEXT}</h4>
					<div class="layOptContainer">
					  <input type="checkbox" name='description_panel' value='1' {if $LAYOUT_OPTIONS.description_panel neq 0} checked="checked" {/if} />					
					  {$MOD.LBL_SHOW_ON_PROPOSAL_LAYOUT_OPT}
					 </div>
					</th>
				</tr>
				<tr>
					<td width="15%" valign="top" scope="row">
					{$MOD.LBL_DESCRIPTION_PLACEMENT}<br/>
					{html_radios name='description_placement' options=$LAYOUT_PLCEMENT_RADIO_OPTION selected=$LAYOUT_OPTIONS.description_placement separator='<br/>' }
					</td>
					<td width="85%">
					
					<textarea name='description' tabindex='7' cols="60"
							rows="8">{$fields.description.value}</textarea>
							{$tinyjs}
							</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<script type="text/javascript">
Calendar.setup ({literal} { {/literal}
        inputField : "jscal_field", daFormat : "{$CALENDAR_DATEFORMAT}", ifFormat : "{$CALENDAR_DATEFORMAT}", showsTime : false, button : "jscal_trigger", singleClick : true, step : 1, weekNumbers:false
{literal} } {/literal});

Calendar.setup ({literal} { {/literal}
        inputField : "jscal_field_original_po_date", ifFormat : "{$CALENDAR_DATEFORMAT}", showsTime : false, button : "jscal_trigger_original_po_date", singleClick : true, step : 1, weekNumbers:false
{literal} } 
{/literal});

</script>


<script type="text/javascript"
	src="{sugar_getjspath file='custom/modules/AOS_Quotes/quotes.js'}"></script>
<script type="text/javascript"
	src="{sugar_getjspath file='modules/AOS_Quotes/EditView.js'}"></script>

<script type="text/javascript">
{literal}

	//Tax Class - Edited by Hirak
YAHOO.util.Event.onDOMReady(function()
{
	sqs_objects['EditView_pop_product_name']={
	        "id":"EditView_pop_product_name",
	        "form":"EditView",
	        "method":"query",
	        "modules":["AOS_ProductTemplates"],
	        "group":"or",
	        "field_list":["name","id","cost_price","description","discount_price","markup","markup_inper","quantity","unit_measure","unit_measure_name", "tax_class"],
	        "populate_list":["product_tpl_name","product_tpl_id","pop_price","pop_product_description","pop_unit_price","pop_markup","quantity","unit_measure","unit_measure_name",  "tax_class"],
	        "conditions":[{
	            "name":"name",
	            "op":"like_custom",
	            "end":"%",
	            "value":""
	        }],
	        "order":"name",
	        "limit":"30",
	        "no_match_text":"No Match",
	        "post_onblur_function":"set_after_sqs_proposal"	    
	                
	};	
});
function set_after_sqs_proposal(sqs_object, sqs_object_id) {	
	var pop_price = toDecimal(sqs_object['cost_price'], 2);
	var unit_price = toDecimal(sqs_object['discount_price'], 2);	
	var markup = toDecimal(sqs_object['markup'], 2);
	var markup_inper = sqs_object['markup_inper'];
	var unit_measure = sqs_object['unit_measure'];
	
	//Tax Class - Added by Hirak
	if(document.getElementById('sales_tax_flag').value == 'total_item'){
		var tax_class = sqs_object['tax_class'];
		document.getElementById('pop_tax_class').value = tax_class;
	}
	//Tax Class - Added by Hirak
	
	document.getElementById('pop_price').value = pop_price;
	document.getElementById('pop_unit_price').value = unit_price;
	document.getElementById('pop_markup').value = markup;
	document.getElementById('pre_cost_price').value = pop_price;
	document.getElementById('pre_unit_price').value = unit_price;
	document.getElementById('pre_markup').value = markup;
	document.getElementById('pre_desc').value = sqs_object['description'];
	document.getElementById('pop_quantity').value = sqs_object['quantity'];		
	document.getElementById('pop_markup_inper').checked = false;
    if(markup_inper=='1'){
    	document.getElementById('pop_markup_inper').checked =true;
    }	  
    $('#pop_unit_measure option[value='+unit_measure+']').attr('selected',true);
	lineItemCalculate();
	getValueFromProductCatalog(sqs_object['id']);
}	

{/literal}

var precision = "{$PRECISION}";
var default_product_status = "{$DEFAULT_PRODUCT_STATUS}";
var invalidAmount = "{$APP.ERR_INVALID_AMOUNT}";
var selectButtonTitle = "{$APP.LBL_SELECT_BUTTON_TITLE}";
var selectButtonKey = "{$APP.LBL_SELECT_BUTTON_KEY}";
var selectButtonValue = "{$APP.LBL_SELECT_BUTTON_LABEL}";
var deleteButtonName = "{$MOD.LBL_REMOVE_ROW}";
var deleteButtonConfirm = "{$MOD.NTC_REMOVE_PRODUCT_CONFIRMATION}";
var deleteGroupConfirm = "{$MOD.NTC_REMOVE_GROUP_CONFIRMATION}";
var deleteButtonValue = "{$MOD.LBL_REMOVE_ROW}";
var addRowName = "{$MOD.LBL_ADD_ROW}";
var addRowValue = "{$MOD.LBL_ADD_ROW}";
var deleteTableName = "{$MOD.LBL_DELETE_GROUP}";
var deleteTableValue = "{$MOD.LBL_DELETE_GROUP}";
var subtotal_string = "{$MOD.LBL_SUBTOTAL}";
var shipping_string = "{$MOD.LBL_SHIPPING}";
var deal_tot_string = "{$MOD.LBL_DISCOUNT_TOTAL}";
var new_sub_string = "{$MOD.LBL_NEW_SUB}";
var total_string = "{$MOD.LBL_TOTAL}";
var tax_string = "{$MOD.LBL_TAX}";
var list_quantity_string = "{$MOD.LBL_LIST_QUANTITY}"
var list_product_name_string = "{$MOD.LBL_LIST_PRODUCT_NAME}"
var list_mf_part_num_string = "{$MOD.LBL_LIST_MANUFACTURER_PART_NUM}"
var list_taxclass_string = "{$MOD.LBL_LIST_TAXCLASS}"
var list_cost_string = "{$MOD.LBL_LIST_COST_PRICE}"
var list_list_string = "{$MOD.LBL_LIST_LIST_PRICE}"
var list_discount_string = "{$MOD.LBL_LIST_DISCOUNT_PRICE}"
var list_deal_tot = "{$MOD.LBL_LIST_DEAL_TOT}"
var check_data = "{$MOD.LBL_CHECK_DATA}"
var addCommentName = "{$MOD.LBL_ADD_COMMENT}";
var addCommentValue = "{$MOD.LBL_ADD_COMMENT}";
var deleteCommentName = "{$MOD.LBL_REMOVE_COMMENT}";
var deleteCommentValue = "{$MOD.LBL_REMOVE_COMMENT}";
var deleteCommentConfirm = "{$MOD.NTC_REMOVE_COMMENT_CONFIRMATION}";
var lbl_delivery_date_wmsg = "{$MOD.LBL_DELIVERY_DATE_WMSG}";
var lbl_delivery_method_wmsg = "{$MOD.LBL_DELIVERY_METHOD_WMSG}";
var lbl_skip_line_items_cmsg = "{$MOD.LBL_SKIP_LINE_ITEM_WMSG}";
var lbl_about_delivery_method_cmsg = "{$MOD.LBL_ABOUT_DELIVERY_METHOD_CMSG}";
var verify_proposal = "{$verify_proposal}";
var reverify_message = "{$MOD.LBL_REVERIFY_MSG}";
{$ADD_ROWS}
</script>

<script type="text/javascript" language="Javascript">
{literal}

YAHOO.util.Event.onAvailable('show_verify_message',function(Y){
	if(verify_proposal!=1){
		document.getElementById('show_verify_message').style.display='';
	}
	
});

YAHOO.util.Event.onDOMReady(function()
{
    sqs_objects['EditView_billing_account']['post_onblur_function'] = 'setReturnClient';
});


var handleYes = function() {            
    document.getElementById('skip_delivery_date').value = 'true';           
    this.hide();
    save_updated();
};

var handleYes1 = function() {            
    document.getElementById('skip_delivery_method').value = 'true'; 
    this.hide();
    save_updated();
};
   
var handleYes2 = function() {            
    document.getElementById('skip_line_items').value = 'true'; 
    this.hide();
    save_updated();
};

var handleYes3 = function() {
	this.hide();
	return false;
};

/*var handleYes4 = function(){
	this.hide();
	document.EditView.submit();	
};*/

var handleYes5 = function(){
	this.hide();
	var date_time = document.getElementById('date_time_delivery').value;
    var date_time_arr = date_time.split(' ');
    var date = date_time_arr[0];
    var time = date_time_arr[1];
	mySimpleDialog = getSimpleDialog(); 
    mySimpleDialog.setBody('Your proposal will be sent to '+ document.getElementById('billing_account').value +' at '+ time +' on '+ date +'.');
    var myButtons = [
    { text: "OK", handler: handleYes6 }			    
    ];
    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
    mySimpleDialog.render(document.body);    
    mySimpleDialog.show();
};

var handleYes6 = function(){
	this.hide();
	var is_ie = document.EditView.is_ie.value;
	//upload documents first then submit form
	if(is_ie != '1'){
		bbUploader.startUpload();
	}else{
	    document.EditView.submit();
	}
};

var handleNo1 = function(){
	this.hide();
	return false;
};
    
var handleNo = function() {    
    check_form('EditView');	    
    this.hide();
};

var handleYesReVerify = function(){
	var is_ie = document.EditView.is_ie.value;
	document.getElementById('is_form_updated').value = '1';
	this.hide();
	//upload documents first then submit form
	if(is_ie != '1'){
		bbUploader.startUpload();
	}else{
	    document.EditView.submit();
	}
};
    
/*
function check_form_custom(){	
	removeFromValidate('EditView', 'contact_email', 'email', true,'Contact Email' );
	removeFromValidate('EditView', 'contact_fax', 'varchar', true,'Contact Fax');

	if(document.getElementById('delivery_method_email').checked==true){		
		addToValidate('EditView', 'contact_email', 'email', true,'Contact Email');		
	}

	if(document.getElementById('delivery_method_fax').checked==true){
		addToValidate('EditView', 'contact_fax', 'varchar', true,'Contact Fax');
	}
	
	if(document.getElementById('delivery_method_both').checked==true){		
		addToValidate('EditView', 'contact_email', 'email', true,'Contact Email');
		addToValidate('EditView', 'contact_fax', 'varchar', true,'Contact Fax');		
	}	
	
	removeFromValidate('EditView', 'delivery_timezone', 'enum', true,'Delivery Time Zone' );
	if(document.getElementById('date_time_delivery').value != ''){	
		addToValidate('EditView', 'delivery_timezone', 'enum', true,'Delivery Time Zone' );
	}
	
	return check_form('EditView');	
}
*/
function save_updated(){

    var is_ie = document.EditView.is_ie.value;
	document.EditView.action.value = 'Save';

	var proposal_verified_val='';
	//YUI().use('node',function(Y){
		//proposal_verified_val = Y.one('[name=proposal_verified]:checked').get('value');
		proposal_verified_val = $('input:radio[name=proposal_verified]:checked').val();
		//alert(proposal_verified_val);return false;	
	//});	

	var proposal_delivery_method_val = '';
	proposal_delivery_method_val = $('input:radio[name=proposal_delivery_method]:checked').val();
	
	var filledLineItem = false;
		for(var i=0; i<count; i++){
			if(document.getElementById('name_'+i)){
				//if(document.getElementById('name_'+i).value != 'This is Sample Text.'){
				if(trim(document.getElementById('product_template_id_'+i).value) != ''){
					filledLineItem = true;	
				}				
			}
		}		

	////hirak - date : 11-10-2012
	if(proposal_delivery_method_val == 'E' || proposal_delivery_method_val == 'F' || proposal_delivery_method_val == 'EF'){		
		document.getElementById('skip_delivery_method').value = '';
	}

	if(document.getElementById('date_time_delivery').value != ''){		
		document.getElementById('skip_delivery_date').value = '';
	}

	if(filledLineItem==true){		
		document.getElementById('skip_line_items').value = '';
	}

	
	
		/******commented due to problem on reset verification
		if(proposal_verified_val == '1' 
				&& document.getElementById('hnd_verify_email_sent').value == '1' 
					&& proposal_delivery_method_val != 'M'){*/
		if(document.getElementById('hnd_verify_email_sent').value == '1' 
					&& proposal_delivery_method_val != 'M'){
			
			var pre_str = document.getElementById('pre_form_string').value;
			var updated_str = getFormString();		
			if(pre_str != updated_str){												
				mySimpleDialog = getSimpleDialog(); 
			    mySimpleDialog.setBody(reverify_message);
			    var myButtons = [
			    { text: "OK", handler: handleYesReVerify }		    
			    ];
			    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
			    mySimpleDialog.render(document.body);    
			    mySimpleDialog.show();
			    return false;		   	
			}
		}

		if( proposal_verified_val == '1' 
				&& document.getElementById('hnd_verify_email_sent').value == '0'
					&& proposal_delivery_method_val != 'M'){
			
			mySimpleDialog = getSimpleDialog(); 
		    mySimpleDialog.setBody('Proposal Verified can not be set to Yes until verify proposal email has been sent.');
		    var myButtons = [
		    { text: "OK", handler: handleYes3 }		    
		    ];
		    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
		    
		}else if (proposal_verified_val == '2' && proposal_delivery_method_val != 'M'){

			var date = new Date();
			date.setTime(date.getTime()+(1*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
			document.cookie = escape('proposal')+"="+escape('unverified')+expires+"; path=/";
		    //upload documents first then submit form
			if(is_ie != '1'){
				bbUploader.startUpload();
			}else{
			    document.EditView.submit();
			}
			/*
			//now show this message after save on detail view
			mySimpleDialog = getSimpleDialog(); 
		    mySimpleDialog.setBody('Your changes have been saved, however the proposal cannot be sent until it has been verified. Please click the "Return to Client Opportunity" button above. Locate the red X next to the proposal you have just saved and click the X to send yourself a proof (when the proof has been sent the X button will change to a "pending" icon). Once you are satisfied with the proof you may verify the proposal for delivery by clicking on the “pending” icon (when the proposal has been successfully scheduled then the “pending” icon will change into a check mark) and your proposal is ready to be sent.');
		    var myButtons = [
		    { text: "OK", handler: handleYes4 }		    
		    ];
		    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
		    mySimpleDialog.render(document.body);    
		    mySimpleDialog.show();
		    */
		    
		}else{

			if(document.getElementById('date_time_delivery').value == '' 
					&& document.getElementById('skip_delivery_date').value == ''
						&& proposal_delivery_method_val != 'M'){
							
				mySimpleDialog = getSimpleDialog(); 
			    mySimpleDialog.setBody(lbl_delivery_date_wmsg);
			    var myButtons = [
			    { text: "Continue", handler: handleYes },
			    { text:"Cancel", handler: handleNo, isDefault:true}
			    ];
			    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
			    mySimpleDialog.render(document.body);    
			    mySimpleDialog.show();
			    
			//hirak - date : 11-10-2012
			}else if(proposal_delivery_method_val != 'E' 
					&& proposal_delivery_method_val != 'F'
						&& proposal_delivery_method_val != 'EF' 
							&& proposal_delivery_method_val == ''
								&& proposal_delivery_method_val != 'M'){	
						
				mySimpleDialog = getSimpleDialog();
			    mySimpleDialog.setBody(lbl_delivery_method_wmsg);
			    var myButtons = [
			    { text: "Continue", handler: handleYes1 },
			    { text:"Cancel", handler: handleNo, isDefault:true}
			    ];
			    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
			    mySimpleDialog.render(document.body);    
			    mySimpleDialog.show();  	
			    	
			}else if(filledLineItem==false && document.getElementById('skip_line_items').value == '' 
				&& document.getElementById('documentId').value == '' 
					&& document.getElementById('attach_documentId').value == '' 
						&& document.getElementById('document_uploaded').value ==''
							&& proposal_delivery_method_val != 'M'){	
				
				mySimpleDialog = getSimpleDialog();
			    mySimpleDialog.setBody(lbl_skip_line_items_cmsg);
			    var myButtons = [
			    { text: "Continue", handler: handleYes2 },
			    { text:"Cancel", handler: handleNo, isDefault:true}
			    ];
			    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
			    mySimpleDialog.render(document.body);    
			    mySimpleDialog.show();
			    			    
			}else if(document.getElementById('skip_delivery_date').value == '' 
					&& document.getElementById('skip_delivery_method').value == ''
						&& (document.getElementById('skip_line_items').value == '' 
						|| document.getElementById('document_uploaded').value == 'true')
							&& proposal_delivery_method_val != 'M'){	
				
			    mySimpleDialog = getSimpleDialog(); 
			    mySimpleDialog.setBody(lbl_about_delivery_method_cmsg);
			    var myButtons = [
			    { text: "OK", handler: handleYes5 },
		    	{ text:"Cancel", handler: handleNo1, isDefault:true}		    
			    ];
			    mySimpleDialog.cfg.queueProperty("buttons", myButtons);  
			    mySimpleDialog.render(document.body);    
			    mySimpleDialog.show();
			    return false;
			    	   
			}else{
				//upload documents first then submit form
				if(is_ie != '1'){
					bbUploader.startUpload();
				}else{
				    document.EditView.submit();
				}
			}

		}
		
	}

	document.getElementById('select_document').onclick = function(){	
	    var popup_request_data = {
	    	    'call_back_function' : 'set_document_returns',
	    	    'form_name' : 'EditView',
	    	    'field_to_name_array' : {
	    	        'id' : 'id',
	    	        'document_name' : 'document_name',
	    	     },
	    	  };
		open_popup('Documents', 600, 400, '&proposal_docs=1', true, false, popup_request_data, 'MultiSelect', true);
	}
	
	function set_document_returns(popup_reply_data){
		var form_name = popup_reply_data.form_name;
		var name_to_value_array = popup_reply_data.name_to_value_array;
		var select_entire_list = typeof( popup_reply_data.select_entire_list ) == 'undefined' ? 0 : popup_reply_data.select_entire_list;
		var document_array =  new Array();
		var selected_document_id =  document.EditView.selected_doc_id.value;
		
	  	//construct the multi select list
		var selection_list = popup_reply_data.selection_list;
		if (typeof selection_list != 'undefined') {
			for (var the_key in selection_list)
			{
				document_array.push(selection_list[the_key])
			}
		}else if( typeof name_to_value_array != 'undefined'){
			document_array.push(name_to_value_array.id);
		} 
		var document_string = document_array.join(',');
	    var document_mapping = popup_reply_data.mappings;

	    if( (selected_document_id != '') &&  typeof (selected_document_id) != 'undefined') {
	    	document_string = document_string + ',' + selected_document_id;
	    }

	    if( typeof document_string != 'undefined' && document_string != '' ){
	        updateSelectedDocuments(document_string, select_entire_list);
	    }
	}

	function updateSelectedDocuments(document_string, select_entire_list, deleted_document_id){

		if( typeof document_string == 'undefined'){
			document_string = document.EditView.selected_doc_id.value;
		}
		
		select_entire_list = typeof( select_entire_list ) == 'undefined' ? 0 : select_entire_list;
		deleted_document_id = typeof( deleted_document_id ) == 'undefined' ? '' : deleted_document_id;

	    $.ajax({
	        'type' : 'POST',
	        'url' : 'index.php?module=AOS_Quotes&action=selectedDocument&to_pdf=true',
	        'data' : { document_string : document_string,  select_entire_list: select_entire_list, deleted_document_id : deleted_document_id},
	        'cache' : false, 
	    }).done ( function (msg) {
		    var responseData = eval('('+msg+')');
	        $("#documentlist").html(responseData.document_html);
	        document.EditView.selected_doc_id.value = responseData.document_string;
	        document.EditView.is_form_updated.value = '1';
	    });
	    
	}

	function removeSelectedDocuments(deleted_document_id){
		if(confirm('Do you want to remove this document ?')){
			var document_string = document.EditView.selected_doc_id.value;
			var select_entire_list = 0;
			updateSelectedDocuments(document_string, select_entire_list, deleted_document_id);
		}
		return false;
	}	
	
	//use for fill default client contact and set client id to sqs of client contact
	function setReturnClient(popup_reply_data){
		//if callback function called from popup then name_to_value_array have object value
		//else if callback function called from SQS it is have undefined value
		if(typeof(popup_reply_data.name_to_value_array) != 'undefined'){
			set_return(popup_reply_data);
		} 
		//use for setting function after sqs call on client contact	
		SUGAR.util.doWhen("typeof(sqs_objects['EditView_billing_contact']) != 'undefined'",setSqsClientContact);
		getAccountsDefualtContact($('#billing_account_id').val());
	}
	
	//function to fill default client contact		        
	function getAccountsDefualtContact(a_id){
		ajaxStatus.showStatus('Loading ...');
		jQuery.ajax({
	        type: "POST",
	        url: "index.php?module=Accounts&action=default_contacts&to_pdf=true",
	        data: {account_id: a_id},
	       	cache: false,
	       	async:true,
	        complete: function (resp,data) {
	       		aResp = JSON.parse(resp.responseText);
	      		$('#billing_contact_id').val(aResp.contact_id)
	      		$('#billing_contact').val(aResp.contact_name);
	      		setReturnClientContact('');
	      		ajaxStatus.hideStatus();                    	
	        }
        });
	}
		
	//use for fill client contacts details to contact details and address field
	function setReturnClientContact(popup_reply_data){
		//if callback function called from popup then name_to_value_array have object value
		//else if callback function called from SQS it is have undefined value
		var clientContactId = '';
		if(typeof(popup_reply_data.name_to_value_array) != 'undefined'){
			set_return(popup_reply_data);
			clientContactId = popup_reply_data.name_to_value_array.billing_contact_id;
		} else {
			clientContactId = document.getElementById('billing_contact_id').value;
		}
		if(clientContactId !='') {
			jQuery.ajax({
		        type: "POST",
		        url: "index.php?module=AOS_Quotes&action=client_contact_detail&to_pdf=true",
		        data: {clientContactId: clientContactId},
		       	cache: false,
		       	async:false,
		        success: function (obj) {
		       		var result = JSON.parse(obj);
		        	if(result.phone)
                    	document.getElementById('contact_phone').value = result.phone;
                    if(result.fax)
                    	document.getElementById('contact_fax').value = result.fax;
                    if(result.email)
                    	document.getElementById('contact_email').value = result.email;
					if(result.billing_address_street)
                    	document.getElementById('billing_address_street').value = result.billing_address_street;
                    if(result.billing_address_city)
                    	document.getElementById('billing_address_city').value = result.billing_address_city;
                    if(billing_address_state)
                    	document.getElementById('billing_address_state').value = result.billing_address_state;
                    if(billing_address_postalcode)
                    	document.getElementById('billing_address_postalcode').value = result.billing_address_postalcode;                    	
		        }
        	});
		}
	}
	
	//use for set sqs for client contact
	function setSqsClientContact(){
		sqs_objects['EditView_billing_contact']["post_onblur_function"]= "setReturnClientContact";
		sqs_objects['EditView_billing_contact']["account_contact_id"]= document.getElementById('billing_account_id').value;
		sqs_objects['EditView_billing_contact']["method"]= "get_default_contact_array";
	}
		
{/literal}
</script>

{$SAVED_SEARCH_SELECTS}
{{include file='include/EditView/footer.tpl'}}
