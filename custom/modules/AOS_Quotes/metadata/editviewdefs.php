<?php
$module_name = 'AOS_Quotes';
$_object_name = 'aos_quotes';
$viewdefs [$module_name] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
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
      'form' => 
      array (
        'enctype' => 'multipart/form-data',
        'buttons' => 
        array (
          0 => 
            array (
			'customCode' => '<input type="button" id="SAVE" value="Save" name="button" onclick="if(check_form_custom(\'EditView\')){literal}{ save_updated(true); }{/literal}" class="button primary save_button" accesskey="S" title="Save [Alt+S]">',
            ),
          1 => 'CANCEL',
        ),
        'hidden' => 
        array (
          0 => '<input type="hidden" name="skip_delivery_date" id="skip_delivery_date" value="">',
          1 => '<input type="hidden" name="skip_delivery_method" id="skip_delivery_method" value="">',
          2 => '<input type="hidden" name="skip_line_items" id="skip_line_items" value="">',
          3 => '<input type="hidden" name="document_uploaded" id="document_uploaded" value="{$document_uploaded}">',
          4 => '<input type="hidden" name="hnd_verify_email_sent" id="hnd_verify_email_sent" value="{$fields.verify_email_sent.value}">',
          5 => '<input type="hidden" name="is_form_updated" id="is_form_updated" value="0">',
          6 => '<input type="hidden" name="pre_form_string" id="pre_form_string" value="" >',
        ),
        'footerTpl' => 'custom/modules/AOS_Quotes/tpls/EditViewFooter.tpl',
      ),
	  'includes' => 
      array (
        0 => 
        array (
          'file' => 'custom/include/javascript/jquery-ui-1.8.2.custom.min.js',
        ),
      ),
      'useTabs' => false,
      'tabDefs' => 
      array (
        'LBL_QUOTE_INFORMATION' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
        'LBL_EDITVIEW_PANEL1' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
        'LBL_EDITVIEW_PANEL2' => 
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
          0 => 'name',
          1 => 'opportunity',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'number',
            'type' => 'readonly',
            'displayParams' => 
            array (
              'required' => false,
            ),
          ),
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
          0 => 
          array (
            'name' => 'billing_account',
            'displayParams' => 
            array (
              'call_back_function' => 'setReturnClient',
            ),
          ),
          1 => 
          array (
            'name' => 'proposal_amount',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'billing_contact',
            'displayParams' => 
            array (
              'initial_filter' => '&account_id_advanced="+this.form.{$fields.billing_account.id_name}.value+"&account_name_advanced="+this.form.{$fields.billing_account.name}.value+"',
              'call_back_function' => 'setReturnClientContact',
            ),
          ),
          1 => 
          array (
            'name' => 'billing_address_street',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'contact_email',
            'label' => 'LBL_CONTACT_EMAIL',
            'customCode' => '<input type="text" name="contact_email" id="contact_email" size="30" maxlength="255" value="{$fields.contact_email.value}" title=""> <img alt="" border="0" class="helpEmail" id="filterHelp" src="custom/themes/SuiteP/images/help-dashlet.png" style="cursor:pointer" title="{$MOD.LBL_HELPTEXT_MULTIPLE_EMAIL}"/>',
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
      ),
      'lbl_editview_panel1' => 
      array (
        0 => 
        array (
          0 => 'assigned_user_name',
          1 => '',
        ),
      ),
      'lbl_editview_panel2' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'date_time_delivery',
            'label' => 'LBL_DATE_TIME_DELIVERY',
            'displayParams' => 
            array (
              'displayCancelDeliveryButton' => true,
            ),
          ),
          1 => 
          array (
            'name' => 'proposal_delivery_method',
            'label' => 'LBL_DELIVERY_METHOD',
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
          array (
            'name' => 'proposal_verified',
            'label' => 'LBL_PROPOSAL_VERIFIED_DV',
          ),
        ),
      ),
    ),
  ),
);
;
?>
