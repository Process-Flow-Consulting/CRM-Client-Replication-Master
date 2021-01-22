<?php
$viewdefs ['Opportunities'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'form' => array (
				'buttons' => array (
					0 => array (
						'customCode' => '<input type="button" id="SAVE" value="Save" name="button" onclick="check_form_custom(\'EditView\');" class="button primary" accesskey="a" title="Save">' 
					),
					1 => 'CANCEL' 
				),
				'hidden' => array (
					0 => '{if $fields.id.value neq ""}<input type="hidden" name="copy_amount" id="copy_amount" value="{$fields.amount.value}" />{/if}
					<input name="online_plan" type="hidden"  id="online_plan" /> ' 
				) 
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
        array (
          'file' => 'custom/modules/Opportunities/opportunities.js',
        ),
      ),
      'javascript' => '{$PROBABILITY_SCRIPT}',
      'useTabs' => true,
      'tabDefs' => 
      array (
        'LBL_OVERVIEW' => 
        array (
          'newTab' => true,
          'panelDefault' => 'expanded',
        ),
        'LBL_PROJECT_INFORMATION' => 
        array (
          'newTab' => false,
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
      'LBL_OVERVIEW' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'name',
          ),
          1 => 
          array (
            'name' => 'date_closed',
          ),
        ),
        1 => 
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
        2 => 
        array (
          0 => 'amount',
          1 => 
          array (
            'name' => 'assigned_user_name',
            'displayParams' => 
            array (
              'initial_filter' => '&lead_reviewer=false',
              'call_back_function' => 'set_return_oppassigneduser',
            ),
          ),
        ),
        3 => 
        array (
          0 => 'is_archived',
          1 => 
          array (
            'name' => 'team_name',
          ),
        ),
      ),
      'LBL_PROJECT_INFORMATION' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'lead_address',
          ),
          1 => 
          array (
            'name' => 'lead_source',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'lead_state',
            'displayParams' => 
            array (
              'javascript' => 'onchange="getCounty(this.value,\'\');"',
            ),
          ),
          1 => 
          array (
            'name' => 'lead_received',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'lead_county',
            'customCode' => '{$lead_county}',
          ),
          1 => 
          array (
            'name' => 'lead_structure',
            'customCode' => '{$lead_structure}',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'lead_city',
          ),
          1 => 
          array (
            'name' => 'lead_type',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'lead_zip_code',
          ),
          1 => 
          array (
            'name' => 'lead_owner',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'lead_project_status',
          ),
          1 => 
          array (
            'name' => 'lead_union_c',
            'customLabel' => '{$MOD.LBL_LABOR_TYPE}',
            'customCode' => '<input type="checkbox" name="lead_union_c" id="lead_union_c" value="1" {if $fields.lead_union_c.value==1}checked="checked"{/if} >&nbsp;{$MOD.LBL_LEAD_UNION_C}&nbsp;&nbsp;<input type="checkbox" name="lead_non_union" id="lead_non_union" value="1" {if $fields.lead_non_union.value==1}checked="checked"{/if}>&nbsp;{$MOD.LBL_LEAD_NON_UNION}&nbsp;&nbsp;<input type="checkbox" name="lead_prevailing_wage" id="lead_prevailing_wage" value="1"  {if $fields.lead_prevailing_wage.value==1}checked="checked"{/if} >&nbsp;{$MOD.LBL_LEAD_PREVAILING_WAGE}',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'lead_start_date',
          ),
          1 => 
          array (
            'name' => 'lead_square_footage',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'lead_end_date',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'lead_contact_no',
          ),
          1 => 
          array (
            'name' => 'lead_stories_below_grade',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'lead_number_of_buildings',
          ),
          1 => 
          array (
            'name' => 'lead_stories_above_grade',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'lead_valuation',
          ),
          1 => 
          array (
            'name' => 'probability',
          ),
        ),
        11 => 
        array (
          0 => 
          array (
            'name' => 'lead_scope_of_work',
          ),
        ),
      ),
    ),
  ),
);
;
appendFieldsOnViews($viewdefs['Opportunities']['EditView'],$detailView=array(),$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Opportunities','EditView','parent_opportunity');
?>
