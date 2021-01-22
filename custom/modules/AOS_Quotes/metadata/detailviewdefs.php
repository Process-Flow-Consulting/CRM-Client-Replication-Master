<?php
// created: 2012-03-09 14:47:59
$viewdefs['AOS_Quotes']['DetailView'] = array (
  'templateMeta' => 
  array (
    'form' => 
    array (
      'closeFormBeforeCustomButtons' => true,
      'links' => 
      array (
        0 => '{$MOD.PDF_FORMAT} <select name="sugarpdf" id="sugarpdf">{$LAYOUT_OPTIONS}</select></form>',
      ),
      'buttons' => 
      array (
        0 => 'EDIT',
        1 => 'DUPLICATE',
        2 => 'DELETE',
        3 => 
        array (
         // 'customCode' => '<form action="index.php" method="POST" name="Quote2Opp" id="form"><input type="hidden" name="module" value="AOS_Quotes"><input type="hidden" name="record" value="{$fields.id.value}"><input type="hidden" name="user_id" value="{$current_user->id}"><input type="hidden" name="team_id" value="{$fields.team_id.value}"><input type="hidden" name="user_name" value="{$current_user->user_name}"><input type="hidden" name="action" value="QuoteToOpportunity"><input type="hidden" name="opportunity_subject" value="{$fields.name.value}"><input type="hidden" name="opportunity_name" value="{$fields.name.value}"><input type="hidden" name="opportunity_id" value="{$fields.billing_account_id.value}"><input type="hidden" name="amount" value="{$fields.total.value}"><input type="hidden" name="valid_until" value="{$fields.date_quote_expected_closed.value}"><input type="hidden" name="currency_id" value="{$fields.currency_id.value}"><input title="{$APP.LBL_QUOTE_TO_OPPORTUNITY_TITLE}" accessKey="{$APP.LBL_QUOTE_TO_OPPORTUNITY_KEY}" class="button" type="submit" name="opp_to_quote_button" value="{$APP.LBL_QUOTE_TO_OPPORTUNITY_LABEL}"></form>',
		  'customCode' =>  '<form action="index.php" method="GET" name="copyProposal" id="form"><input type="hidden" name="module" value="AOS_Quotes"><input type="hidden" name="record" value="{$fields.id.value}"><input type="hidden" name="action" value="copyProposal"><input title="{$MOD.LBL_COPY_PROPOSAL_TITLE}" accessKey="{$MOD.LBL_COPY_PROPOSAL_KEY}" class="button" type="submit" name="copy_proposal" value="{$MOD.LBL_COPY_PROPOSAL_TITLE}"></form>',
        ),
		4 => 
        array(
			'customCode' => '{$pdfButtons}',
        ),
		5 => 
        array(
			'customCode' => '{$pdfViewButton}',
        ),
        6 => 
        array (
          //'customCode' => '<form action="index.php" method="{$PDFMETHOD}" name="ViewPDF" id="form"><input type="hidden" name="module" value="AOS_Quotes"><input type="hidden" name="record" value="{$fields.id.value}"><input type="hidden" name="action" value="sugarpdf"><input type="hidden" name="email_action"><input title="{$APP.LBL_EMAIL_PDF_BUTTON_TITLE}" accessKey="{$APP.LBL_EMAIL_PDF_BUTTON_KEY}" class="button" type="submit" name="button" value="{$APP.LBL_EMAIL_PDF_BUTTON_LABEL}" onclick="this.form.email_action.value=\'EmailLayout\';"> <input title="{$APP.LBL_VIEW_PDF_BUTTON_TITLE}" accessKey="{$APP.LBL_VIEW_PDF_BUTTON_KEY}" class="button" type="submit" name="button" value="{$APP.LBL_VIEW_PDF_BUTTON_LABEL}">',
          'customCode' => '<input type="button" name="btn_proposal_verified" id="btn_proposal_verified" value="Verify Proposal" class="button" onclick="verifyEmail();">',
        ),     	
      	
      ),
      'footerTpl' => 'custom/modules/AOS_Quotes/tpls/DetailViewFooter.tpl',
    ),
    'maxColumns' => '2',
    'widths' => 
    array (
      0 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
      1 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
    ),
    'useTabs' => true,
	'tabDefs' => 
      array (
        'LBL_QUOTE_INFORMATION' => 
        array (
          'newTab' => true,
          'panelDefault' => 'expanded',
        ),
        'LBL_PANEL_ASSIGNMENT' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
        'LBL_DETAILVIEW_PANEL1' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
  ),
  'panels' => 
  array (
    'lbl_quote_information' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'name',
          'label' => 'LBL_QUOTE_NAME',
        ),
        1 => 
        array (
          'name' => 'opportunity',
        ),
      ),
      1 => 
      array (
        0 => 'number',
        1 => 
        array (
          'name' => 'date_time_sent',
          'label' => 'LBL_DATE_TIME_SENT',
        ),
      ),
      2 => 
      array (
        0 => 'purchase_order_num',
        1 => 
        array (
          'name' => 'date_time_received',
          'label' => 'LBL_DATE_TIME_RECEIVED',
        ),
      ),
      3 => 
      array (
        0 => 'stage',
        1 => 
        array (
          'name' => 'date_time_opened',
          'label' => 'LBL_DATE_TIME_OPENED',
        ),
      ),
      4 => 
      array (
        0 => array(
        	'name' => 'billing_account',
			'label' => 'LBL_BILLING_ACCOUNT',
        	'customCode' => '{$ACCOUNT_NAME}',
        ),
        1 => array(
            'name' => 'proposal_amount',
        ),
      ),
      5 => 
      array (
        0 => 'billing_contact',
        1 => array (
          'name' => 'billing_address_street',
        ),
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'contact_email',
          'label' => 'LBL_CONTACT_EMAIL',
        ),
        1 => 
        array (
          'name' => 'billing_address_city',
          'label' => 'LBL_BILLING_ADDRESS_CITY',
        ),
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'contact_fax',
          'label' => 'LBL_CONTACT_FAX',
        ),
        1 => 
        array (
          'name' => 'billing_address_state',
          'label' => 'LBL_BILLING_ADDRESS_STATE',
        ),
      ),
      8 => 
      array (
        0 => 
        array (
          'name' => 'contact_phone',
          'label' => 'LBL_CONTACT_PHONE',
        ),
        1 => 
        array (
          'name' => 'billing_address_postalcode',
          'label' => 'LBL_BILLING_ADDRESS_POSTAL_CODE',
        ),
      ),
      9 => 
      array (
        0 =>array('name' => 'description',
					'label' => 'LBL_DESCRIPTION_AS_TEXT',
					/* 'customCode' => '{$fields.description.value|escape:\'html_entity_decode\'|strip_tags|escape:\'html\'|url2html|nl2br}', */
					
				),
      ),
    ),
    'LBL_PANEL_ASSIGNMENT' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_ASSIGNED_TO',
        ),
        1 => 
        array (
          'name' => 'date_modified',
          'label' => 'LBL_DATE_MODIFIED',
          'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'date_entered',
          'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
        ),
      ),
    ),
    'lbl_detailview_panel1' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'date_time_delivery',
          'label' => 'LBL_DATE_TIME_DELIVERY',
        ),
        1 => 
        array (
          //'name' => 'delivery_method_email',
          'name' => 'proposal_delivery_method',
          'label' => 'LBL_DELIVERY_METHOD',
          //'customCode' => '<input type="checkbox" disabled name="delivery_method_email" id="delivery_method_email" value="1" {if $fields.delivery_method_email.value==1}checked="checked"{/if} > &nbsp;{$MOD.LBL_DELIVERY_METHOD_EMAIL} &nbsp;<input type="checkbox" disabled name="delivery_method_fax" id="delivery_method_fax" value="1" {if $fields.delivery_method_fax.value==1}checked="checked"{/if} > &nbsp;{$MOD.LBL_DELIVERY_METHOD_FAX} &nbsp;<input type="checkbox" disabled name="delivery_method_both" id="delivery_method_both" value="1" {if $fields.delivery_method_both.value==1}checked="checked"{/if} > &nbsp;{$MOD.LBL_DELIVERY_METHOD_BOTH}',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'delivery_timezone',
          'label' => 'LBL_DELIVERY_TIMEZONE',
        ),
        1 => 
      	array(
      		'name' => 'proposal_verified',
      		'label' => 'LBL_PROPOSAL_VERIFIED_DV',
      		'customCode' => '<label><input disabled {$proposal_yes_chk} type="radio" title="" id="proposal_verified" value="1" name="proposal_verified">Yes</label><label>
<input disabled id="proposal_verified" type="radio" title="" {$proposal_no_chk} value="2" name="proposal_verified">
No</label>'    			
      	),
      ),
    ),
  ),
);
