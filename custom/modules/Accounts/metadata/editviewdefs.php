<?php
$viewdefs ['Accounts'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'SAVE',
		 /*  array('customCode' => '<input type="button" disabled id="SAVE" value="Save" name="button" onclick="check_form_custom();" class="button primary" accesskey="a" title="Save">'),
          1 => 'CANCEL',
        ), */
      	'hidden' => array(
      		0 => '<input type="hidden" name="pre_form_string" id="pre_form_string">',
      		1 => '<input type="hidden" name="form_updated" id="form_updated" value="0">'		
      	),
      ),
	 ),
      'maxColumns' => '2',
      'useTabs' => false,
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
      	1 =>
      	array(
          'file' => 'custom/modules/Accounts/accounts.js'	
        ),
      ),
      'syncDetailEditViews' => false,
    		'javascript' => <<<EQQ
  			<script type="text/javascript">
    		{literal}
    		function getCounty(stateAbbr,selCounty){
    		var callback = {
    		success:function(o){
    		//alert(o.responseText);
    		document.getElementById("county_div").innerHTML = o.responseText;
    		}
    		}
    		var connectionObject = YAHOO.util.Connect.asyncRequest ("GET", "index.php?entryPoint=CountyAjaxCall&state_abbr="+stateAbbr+"&selected_county="+selCounty, callback);
    		
    		}
    		{/literal}
    		</script>
    		
EQQ
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
            'label' => 'LBL_NAME',
            'displayParams' => 
            array (
              'required' => true,
            ),
          ),
          1 => 
          array (
            'name' => 'phone_office',
            'label' => 'LBL_PHONE_OFFICE',
             'customCode' => '<input type="text" name="phone_office" id="phone_office" value="{$fields.phone_office.value}" /><input type="text" name="phone_office_ext" id="phone_office_ext" value="{$fields.phone_office_ext.value}" size="5" maxlength="4" />'
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'phone_fax',
            'label' => 'LBL_FAX',
          ),
          1 => 
          array (
            'name' => 'proview_url',
           // 'type' => 'link',
            'label' => 'LBL_WEBSITE',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'address1',
          ),
          1 => 
          array (
            'name' => 'address2',
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
            'customCode' => '<input type="hidden" name="billing_address_state" id="state_free" value="{$fields.billing_address_state.value}"/>
          					<div id="state_div">
          						<select id="state_id" name="billing_address_state" onchange="getCounty(this.value,\'\');" >
            					{html_options options=$STATE_DOM selected=$fields.billing_address_state.value}
          						</select>
          					</div>',
          ),
        ),
        4 => 
        array (
          0 => '',
          1 => 
          array (
            'name' => 'county',
            'studio' => 'visible',
            'label' => 'LBL_COUNTY',
            'customCode' => '<div id="county_div">
          					<select title="" id="county" name="county_id">
            				{html_options options=$COUNTY_DOM selected=$SAVED_COUNTY}
          					</select>
          					</div>          					
          					<input type="hidden" id="county_name" name="county_name" value="{$fields.county_name.value}" />',
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
          1 => 'industry',
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
       /* 7 => 
        array (
          0 => 
          array (
            'name' => 'year_established',
            'label' => 'LBL_YEAR_ESTABLISHED',
            'type' => 'readonly',
          ),
          1 => 
          array (
            'name' => 'typical_project_size',
            'label' => 'LBL_TYPICAL_PROJECT_SIZE',
            'type' => 'readonly',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'no_of_employees',
            'label' => 'LBL_NO_OF_EMPLOYEES',
            'type' => 'readonly',
          ), 
          1 => 
          array (
            'name' => 'previous_projects',
            'label' => 'LBL_PREVIOUS_PROJECTS',
            'type' => 'readonly',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'custom_field_1',
            'label' => 'LBL_CUSTOM_FIELD_1',
          ),
          1 => 
          array (
            'name' => 'custom_field_2',
            'label' => 'LBL_CUSTOM_FIELD_2',
          ),
        ),*/
      	11 =>
      	array (
          0 =>
      	  array (
      	  ),
      	  1 =>
      	  array (
      		'name' => 'international',
      		'label' => 'LBL_INTERNATIONAL',
      	  	'customCode' => '{if $fields.international.value} 
						{assign var="inter_checked" value="CHECKED"}
      	  				{else}
      	  				{assign var="inter_checked" value=""}
						{/if}
					<input id="international_hidden" name="international" type="hidden" value="0">&nbsp;
					<input id="international" name="international" type="checkbox" value="1" {$inter_checked} onchange="endisStateCounty()">', 
//       	  	'displayParams' =>
//       	  	  array (
//       	  		'javascript' => 'onchange="endisStateCounty();"'
      	  		
//       	      ),
      	  ),
      	),
        12 => 
        array (
          0 => 
          array (
            'name' => 'email1',
            'studio' => 'false',
            'label' => 'LBL_EMAIL',
          ),
        ),
        13 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
        14 =>array(0=>array('name'=>'assigned_user_name'),
                      1 =>array('name' =>'team_name'))
      ),
    ),
  ),
);
;
appendFieldsOnViews($viewdefs['Accounts']['EditView'],$detailView=array(),$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Accounts','EditView');
?>
  		