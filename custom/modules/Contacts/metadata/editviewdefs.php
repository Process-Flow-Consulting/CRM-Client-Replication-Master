<?php
$viewdefs ['Contacts'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'hidden' => 
        array (
          0 => '<input type="hidden" name="opportunity_id" value="{$smarty.request.opportunity_id}">',
          1 => '<input type="hidden" name="case_id" value="{$smarty.request.case_id}">',
          2 => '<input type="hidden" name="bug_id" value="{$smarty.request.bug_id}">',
          3 => '<input type="hidden" name="email_id" value="{$smarty.request.email_id}">',
          4 => '<input type="hidden" name="inbound_email_id" value="{$smarty.request.inbound_email_id}">',
        ),
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
	  'includes' =>
		array (
		  0 =>
		  array(
			'file' => 'custom/modules/Contacts/contacts.js'
		  ),
		),
      'useTabs' => false,
      'tabDefs' => 
      array (
        'LBL_CONTACT_INFORMATION' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
        'LBL_PANEL_ADVANCED' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
        'LBL_EDITVIEW_PANEL1' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
      'syncDetailEditViews' => false,
    ),
    'panels' => 
    array (
      'lbl_contact_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'first_name',
            'customCode' => '{html_options name="salutation" id="salutation" options=$fields.salutation.options selected=$fields.salutation.value}&nbsp;<input name="first_name"  id="first_name" size="25" maxlength="25" type="text" value="{$fields.first_name.value}">',
          ),
          1 => 
          array (
            'name' => 'photo',
            'studio' => 
            array (
              'listview' => true,
            ),
            'label' => 'LBL_PHOTO',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'last_name',
          ),
          1 => 
          array (
            'name' => 'phone_work',
            'comment' => 'Work phone number of the contact',
            'label' => 'LBL_OFFICE_PHONE',
			'customCode' => '<input type="text" name="phone_work" id="phone_work" value="{$fields.phone_work.value}"  style="width:auto;"  /><input type="text" name="phone_work_ext" id="phone_work_ext" size="4" maxlength="4" value="{$fields.phone_work_ext.value}"  style="width:auto;"  />'
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'title',
            'comment' => 'The title of the contact',
            'label' => 'LBL_TITLE',
          ),
          1 => 
          array (
            'name' => 'phone_mobile',
            'comment' => 'Mobile phone number of the contact',
            'label' => 'LBL_MOBILE_PHONE',
          ),
        ),
        3 => 
        array (
          0 => 'department',
          1 => 
          array (
            'name' => 'phone_fax',
            'comment' => 'Contact fax number',
            'label' => 'LBL_FAX_PHONE',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'account_name',
            'displayParams' => 
            array (
           // 'key' => 'billing',
           // 'copy' => 'primary',
            'billingKey' => 'primary',
            'field_to_name_array'=>array("id"=>"account_id",
"name"=>"account_name",
"billing_address_street"=>"primary_address_street",
"billing_address_city"=>"primary_address_city",
"billing_address_state"=>"primary_address_state",
"billing_address_postalcode"=>"primary_address_postalcode",
"billing_address_country"=>"primary_address_country",
'phone_office' => 'phone_work',
'phone_fax' =>'phone_fax',
'phone_office_ext' => 'phone_work_ext', 
),
            'additionalFields' => 
            array (
              'phone_office' => 'phone_work',
              'phone_fax' =>'phone_fax',
              'phone_office_ext' => 'phone_work_ext',              
            ),
          ),
          ),
          1 => 
          array (
            'name' => 'role',
            'label' => 'LBL_ROLE',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'default_contact',
            'label' => 'Default Contact',
          ),
          1 => 
          array (
            'name' => 'international',
            'label' => 'International',
			'customCode' => '{if $fields.international.value}
						{assign var="inter_checked" value="CHECKED"}
      	  				{else}
      	  				{assign var="inter_checked" value=""}
						{/if}
					<input id="international_hidden" name="international" type="hidden" value="0">&nbsp;
					<input id="international" name="international" type="checkbox" value="1" {$inter_checked} onchange="endisState()">',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'primary_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'primary',
              'rows' => 2,
              'cols' => 30,
              'maxlength' => 150,
            ),
          ),
          1 => 
          array (
            'name' => 'alt_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'alt',
              'copy' => 'primary',
              'rows' => 2,
              'cols' => 30,
              'maxlength' => 150,
            ),
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'email1',
            'studio' => 'false',
            'label' => 'LBL_EMAIL_ADDRESS',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
      ),
      'LBL_PANEL_ADVANCED' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'report_to_name',
            'label' => 'LBL_REPORTS_TO',
          ),
          1 => 
          array (
            'name' => 'sync_contact',
            'comment' => 'Synch to outlook?  (Meta-Data only)',
            'studio' => 'true',
            'label' => 'LBL_SYNC_CONTACT',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'lead_source',
            'comment' => 'How did the contact come about',
            'label' => 'LBL_LEAD_SOURCE',
          ),
          1 => 
          array (
            'name' => 'do_not_call',
            'comment' => 'An indicator of whether contact can be called',
            'label' => 'LBL_DO_NOT_CALL',
          ),
        ),
        2 => 
        array (
          0 => 'campaign_name',
          1 => '',
        ),
      ),
      'lbl_editview_panel1' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
          1 => '',
        ),
      ),
    ),
  ),
);
;
appendFieldsOnViews($viewdefs['Contacts']['EditView'],$detailView=array(),$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Contacts','EditView');
?>
