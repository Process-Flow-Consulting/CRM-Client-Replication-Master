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
          1 => array(
             'customCode' => '{if $bean->aclAccess("delete")}<input title="Delete" accesskey="" class="button" onclick="deleteOpportunity(\'{$fields.id.value}\',\'Client\');" name="Delete" value="Delete" id="delete_button_old" type="submit">{/if}',
          ),
          2 => 
          array (
            'customCode' => '<input type="button" class="button" id="unlink" name="unlink" value="{$MOD.LBL_UNLINK}" onclick="{if $sub_opp_count <= 1}alert(SUGAR.language.get(\'Opportunities\', \'NTC_UNLINK_SINGLE_SUB_OPP\')){else}window.location.href=\'index.php?module=Opportunities&action=unlinkop&linked_id={$smarty.request.record}&from_url=sub_opp\'{/if}">',
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
      'useTabs' => false,
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
            'customCode' => '{$ACCOUNT_NAME}',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'amount',
            'label' => '{$MOD.LBL_AMOUNT} ({$CURRENCY})',
          ),
          1 => 
          array (
            'name' => 'contact_name',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'date_closed',
          ),
          1 => 
          array (
            'name' => 'bid_due_timezone',
            'label' => 'LBL_BID_DUE_TIMEZONE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'sales_stage',
          ),
          1 => 'client_bid_status',
        ),
        4 => 
        array (
          0 => 'opportunity_name',
          1 => array (
            'name' => 'opportunity_classification',
            'customCode' => '{$fields.opportunity_classification.value}',
          ),
        ),
      	5 =>
      	array (
   		  0 => 'lead_source',
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
          0 => 'team_name',
          1 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
          ),
        ),
      ),
    ),
  ),
);
appendFieldsOnViews($viewdefs['Opportunities']['DetailView'],$detailView=array(),$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Opportunities','DetailView','client_opportunity');
?>
