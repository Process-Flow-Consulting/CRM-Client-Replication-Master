<?php
$viewdefs ['AOR_Reports'] = 
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
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 
          array (
            'customCode' => '{if $can_export}<input type="button" class="button" id="download_csv_button_old" value="{$MOD.LBL_EXPORT}">{/if}',
          ),
          4 => 
          array (
            'customCode' => '{if $can_export}<input type="button" class="button" id="download_pdf_button_old" value="{$MOD.LBL_DOWNLOAD_PDF}">{/if}',
          ),
          5 => 
          array (
            'customCode' => '<input type="button" class="button" onClick="openProspectPopup();" value="{$MOD.LBL_ADD_TO_PROSPECT_LIST}">',
          ),
        ),
        'footerTpl' => 'modules/AOR_Reports/tpls/report.tpl',
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
          'file' => 'modules/AOR_Reports/AOR_Report.js',
        ),
      ),
      'tabDefs' => 
      array (
        'DEFAULT' => 
        array (
          'newTab' => false,
          'panelDefault' => 'collapsed',
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
          0 => 'name',
          1 => '',
        ),
        1 => 
        array (
          0 => '',
          1 => 
          array (
            'name' => 'report_url_c',
            'label' => 'LBL_REPORT_URL',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
            'label' => 'LBL_DATE_ENTERED',
          ),
          1 => 
          array (
            'name' => 'date_modified',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
        3 => 
        array (
          0 => 'description',
        ),
      ),
    ),
  ),
);
;
?>
