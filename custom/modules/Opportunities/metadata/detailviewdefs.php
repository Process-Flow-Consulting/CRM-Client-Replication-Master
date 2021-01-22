<?php
$viewdefs ['Opportunities'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 
          array (
            'customCode' => '{if $bean->aclAccess("delete")}<input title="Delete" accesskey="" class="button" onclick="deleteOpportunity(\'{$fields.id.value}\',\'Project\');" name="Delete" value="Delete" id="delete_button_old" type="submit">{/if}',
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
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'custom/modules/Opportunities/OpportunitiesDetailView.js',
        ),
      ),
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
          0 => 
          array (
            'name' => 'amount',
            'label' => '{$MOD.LBL_AMOUNT} ({$CURRENCY})',
          ),
          1 => 
          array (
            'name' => 'team_name',
          ),
        ),
        3 => 
        array (
          0 => 'assigned_user_name',
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'date_modified',
            'label' => 'LBL_DATE_MODIFIED',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
          ),
          1 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
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
            'customCode' => '{$fields.lead_county.value}',
          ),
          1 => 
          array (
            'name' => 'lead_structure',
            'customCode' => '{$fields.lead_structure.value}',
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
            'customCode' => '<input type="checkbox" name="lead_union_c" id="lead_union_c" value="1" {if $fields.lead_union_c.value==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_LEAD_UNION_C}&nbsp;&nbsp;<input type="checkbox" name="lead_non_union" id="lead_non_union" value="1" {if $fields.lead_non_union.value==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_LEAD_NON_UNION}&nbsp;&nbsp;<input type="checkbox" name="lead_prevailing_wage" id="lead_prevailing_wage" value="1"  {if $fields.lead_prevailing_wage.value==1}checked="checked"{/if} disabled>&nbsp;{$MOD.LBL_LEAD_PREVAILING_WAGE}',
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
appendFieldsOnViews($editView=array(),$viewdefs['Opportunities']['DetailView'],$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Opportunities','DetailView','parent_opportunity');
?>
