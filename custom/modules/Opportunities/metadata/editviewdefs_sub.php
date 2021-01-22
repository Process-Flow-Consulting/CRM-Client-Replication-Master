<?php
$viewdefs ['Opportunities'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'hidden' => 
        array (
          0 => '<input type="hidden" name="save_action" id="save_action" value="">',
          1 => '<input type="hidden" name="project_lead_id" id="project_lead_id" value="{$PROJECT_LEAD_ID}">',
        ),
        'buttons' => 
        array (
          0 => 
          array (
            'customCode' => '<input type="button" id="SAVE_HEADER" value="Save" name="button" onclick="document.EditView.save_action.value=\'save\';check_form_custom(\'EditView\');" class="button primary" accesskey="a" title="Save">',
          ),
          1 => 
          array (
            'customCode' => '<input type="button" id="save_and_create" value="Save and Create Another Client Opportunity" name="save_and_create" onclick="document.EditView.save_action.value=\'save_and_create\';check_form_custom(\'EditView\');" class="button" accesskey="c" title="Save and Create Another Client Opportunity">',
          ),
          2 => 
          array (
            'customCode' => '<input type="button" id="cancel" value="Return to Project Opportunity" name="cancel" onclick="return_to_project();" class="button" accesskey="r" title="Return to Project Opportunity">',
          ),
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
      'javascript' => '{$PROBABILITY_SCRIPT}
            <script src="custom/modules/Opportunities/opportunities.js" ></script>
                        
            ',
            
      'useTabs' => false,
      'useModuleQuickCreateTemplate' => true,
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'name',
          ),
          1 => 
          array (
            'name' => 'account_name',
             'displayParams' =>array(
             		'size' => 29,
             		'call_back_function' =>'set_return_oppaccounts'
             ),
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'currency_id',
            'label' => 'LBL_CURRENCY',
          ),
          1 => array (
            'name' => 'contact_name',
             'displayParams' =>array(
             	'size' => 29,
             ),
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'amount',
          ),
          1 => 
          array (
            'name' => 'date_closed',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'sales_stage',
          ),
          1 => 
          array (
            'name' => 'bid_due_timezone',
            'label' => 'LBL_BID_DUE_TIMEZONE',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'client_bid_status',
          ),
          1 => 
          array (
            'name' => 'opportunity_name',
            'displayParams' => 
            array (
              'required' => true,
              'size' => 29,
            ),
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'opportunity_classification',
          ),
          1 => 
          array (
            'name' => 'lead_source',
          ),
        ),
      ),
      'LBL_PANEL_ASSIGNMENT' => 
      array (
        0 => 
        array (
          0 => array( 
          	'name' =>'assigned_user_name',
            'displayParams' => array(
            	'initial_filter'=>'&lead_reviewer=false',
            	'call_back_function' =>'set_return_oppassigneduser',
            )
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
appendFieldsOnViews($viewdefs['Opportunities']['EditView'],$detailView=array(),$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Opportunities','EditView','client_opportunity');
?>
