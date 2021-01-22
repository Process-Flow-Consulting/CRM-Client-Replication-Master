<?php
$viewdefs ['Accounts'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          'SEND_CONFIRM_OPT_IN_EMAIL' => 
          array (
            'customCode' => '<input type="submit" class="button hidden" disabled="disabled" title="{$APP.LBL_SEND_CONFIRM_OPT_IN_EMAIL}" onclick="this.form.return_module.value=\'Accounts\'; this.form.return_action.value=\'Accounts\'; this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'sendConfirmOptInEmail\'; this.form.module.value=\'Accounts\'; this.form.module_tab.value=\'Accounts\';" name="send_confirm_opt_in_email" value="{$APP.LBL_SEND_CONFIRM_OPT_IN_EMAIL}"/>',
            'sugar_html' => 
            array (
              'type' => 'submit',
              'value' => '{$APP.LBL_SEND_CONFIRM_OPT_IN_EMAIL}',
              'htmlOptions' => 
              array (
                'class' => 'button hidden',
                'id' => 'send_confirm_opt_in_email',
                'title' => '{$APP.LBL_SEND_CONFIRM_OPT_IN_EMAIL}',
                'onclick' => 'this.form.return_module.value=\'Accounts\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'sendConfirmOptInEmail\'; this.form.module.value=\'Accounts\'; this.form.module_tab.value=\'Accounts\';',
                'name' => 'send_confirm_opt_in_email',
                'disabled' => true,
              ),
            ),
          ),
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
          4 => 
          array (
            'customCode' => '{if $bean->aclAccess("delete")}<input type="submit" class="button" name="search_and_merge"  id="search_and_merge" value="{$MOD.LBL_SEARCH_MERGE_CLIENT_ACTION_MENU}"  onclick="$(this.form).attr(\'action\',\'index.php?module=Accounts&action=clientmerge\');this.form.record.value=\'{$id}\';this.form.return_module.value=\'Accounts\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$id}\'; this.form.action.value=\'clientmerge\'; this.form.module.value=\'Accounts\';" >{/if}',
          ),
        ),
      ),
      'maxColumns' => '2',
      'useTabs' => true,
      'widths' => 
      array (
        0 => 
        array (
          'label' => '15',
          'field' => '25',
        ),
        1 => 
        array (
          'label' => '15',
          'field' => '25',
        ),
      ),
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'modules/Accounts/Account.js',
        ),
      ),
      'syncDetailEditViews' => true,
      'tabDefs' => 
      array (
        'LBL_ACCOUNT_INFORMATION' => 
        array (
          'newTab' => true,
          'panelDefault' => 'expanded',
        ),
        'LBL_CUSTOM_INFORMATION' => 
        array (
          'newTab' => true,
          'panelDefault' => 'expanded',
        ),
      ),
    ),
    'panels' => 
    array (
      'lbl_account_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'comment' => 'Name of the Company',
            'label' => 'LBL_NAME',
            'displayParams' => 
            array (
              'enableConnectors' => true,
              'module' => 'Accounts',
              'connectors' => 
              array (
                0 => 'ext_rest_linkedin',
                1 => 'ext_rest_twitter',
              ),
            ),
          ),
          1 => 
          array (
            'name' => 'phone_office',
            'comment' => 'The office phone number',
            'label' => 'LBL_PHONE_OFFICE',
            'customCode' => '{sugar_phone_number_format value=$fields.phone_office.value} {if $fields.phone_office_ext.value neq ""} <i>(Ext.)</i> {$fields.phone_office_ext.value}{/if}',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'phone_fax',
            'comment' => 'The fax phone number of this company',
            'label' => 'LBL_FAX',
          ),
          1 => 
          array (
            'name' => 'proview_url',
            'label' => 'LBL_WEBSITE',
            'customCode' => '{sugar_proview_url url=$fields.proview_url.value website=true} <!-- {if $fields.proview_url.value neq ""} Proview : <a href="javascript:void(0)" onclick="window.open(\'{$fields.proview_url.value|to_url}	\',\'\',\'width=600,height=500\')"  /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>{/if}-->',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'address1',
            'label' => 'LBL_ADDRESS1',
          ),
          1 => 
          array (
            'name' => 'address2',
            'label' => 'LBL_ADDRESS2',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_city',
            'comment' => 'The city used for billing address',
            'label' => 'LBL_BILLING_ADDRESS_CITY',
          ),
          1 => 
          array (
            'name' => 'billing_address_state',
            'comment' => 'The state used for billing address',
            'label' => 'LBL_BILLING_ADDRESS_STATE',
            'customCode' => '{$fields.billing_address_state.value}',
          ),
        ),
        4 => 
        array (
          0 => '',
          1 => 
          array (
            'name' => 'county_id',
            'label' => 'LBL_COUNTY',
            'customCode' => '{$fields.county_name.value}',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_postalcode',
            'comment' => 'The postal code used for billing address',
            'label' => 'LBL_BILLING_ADDRESS_POSTALCODE',
          ),
          1 => 
          array (
            'name' => 'industry',
            'comment' => 'The company belongs in this industry',
            'label' => 'LBL_INDUSTRY',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'default_message',
            'label' => 'LBL_DEFAULT_MESSAGE',
          ),
          1 => 
          array (
            'name' => 'delivery_method',
            'label' => 'LBL_DELIVERY_METHOD',
          ),
        ),
        7 => 
        array (
          0 => '',
          1 => 
          array (
            'name' => 'international',
            'label' => 'LBL_INTERNATIONAL',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'email1',
            'studio' => 'false',
            'label' => 'LBL_EMAIL',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'comment' => 'Full text of the note',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'assigned_user_name',
          ),
          1 => 
          array (
            'name' => 'team_name',
          ),
        ),
      ),
    ),
  ),
);
;
appendFieldsOnViews($editView=array(),$viewdefs['Accounts']['DetailView'],$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Accounts','DetailView');

?>
