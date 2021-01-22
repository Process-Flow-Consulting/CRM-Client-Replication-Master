<?php
$module_name = 'AOS_Products';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
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
      'useTabs' => false,
      'tabDefs' => 
      array (
        'DEFAULT' => 
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
      'syncDetailEditViews' => true,
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 'name',
          1 => 'status',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'quote_name',
            'comment' => 'Quote Name',
            'label' => 'Quote Name',
          ),
          1 => 
          array (
            'name' => 'contact',
            'label' => 'LBL_CONTACT',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'account_name',
            'label' => 'Client Name',
			'customCode' => '{$ACCOUNT_NAME}',
          ),
          1 => '',
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'quantity',
            'label' => 'Quantity',
          ),
          1 => 
          array (
            'name' => 'date_purchased',
            'comment' => '',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'serial_number',
            'label' => 'Serial Number',
          ),
          1 => 
          array (
            'name' => 'date_support_starts',
            'label' => 'Support Starts',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'asset_number',
            'label' => 'Asset Number',
          ),
          1 => 
          array (
            'name' => 'date_support_expires',
            'label' => 'Support Expires',
          ),
        ),
      ),
      'lbl_editview_panel1' => 
      array (
        0 => 
        array (
          0 => 'currency_id',
          1 => '',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'cost_price',
            'label' => 'Cost(USD $)',
          ),
          1 => 
          array (
            'name' => 'list_price',
            'customLabel' => 'Mark up&nbsp;&nbsp;&nbsp;In%<input type="checkbox" name="markup_inper" id="markup_inper" value="1" disabled="disabled" {if $fields.markup_inper.value==1}checked="checked"{/if}>',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'book_value',
            'label' => 'Book Value(USD $)',
          ),
          1 => '',
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'discount_price',
            'label' => 'Unit Price(USD $)',
          ),
          1 => 
          array (
            'name' => 'book_value_date',
            'label' => 'Book Value Date',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'discount_amount',
            'label' => 'Discount Rate',
            'customCode' => '{if $fields.discount_select.value}{sugar_number_format var=$fields.discount_amount.value}%{else}{$fields.currency_symbol.value}{sugar_number_format var=$fields.discount_amount.value}{/if}',
          ),
          1 => 
          array (
            'name' => 'discount_select',
            'label' => 'Discount in %',
          ),
        ),
      ),
      'lbl_editview_panel2' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'website',
            'type' => 'link',
            'label' => 'Product URL:',
          ),
          1 => 
          array (
            'name' => 'tax_class',
            'label' => 'Tax Class',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'manufacturer_id',
            'studio' => 'true',
            'comment' => 'Manufacturer of product',
            'label' => 'Manufacturer',
          ),
          1 => 
          array (
            'name' => 'weight',
            'label' => 'Weight',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'mft_part_num',
            'label' => 'Mft Part Number:',
          ),
          1 => 
          array (
            'name' => 'category',
            'studio' => 'visible',
            'label' => 'LBL_CATEGORY',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'vendor_part_num',
            'label' => 'Vendor Part Number',
          ),
          1 => '',
        ),
        4 => 
        array (
          0 => 'description',
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'support_name',
            'label' => 'Support Title:',
          ),
          1 => 
          array (
            'name' => 'support_contact',
            'label' => 'Support Contact',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'support_description',
            'label' => 'Support Desc:',
          ),
          1 => 
          array (
            'name' => 'support_term',
            'label' => 'Support Term:',
          ),
        ),
      ),
    ),
  ),
);
;
?>
