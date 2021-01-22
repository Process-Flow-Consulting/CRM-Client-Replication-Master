<?php
$module_name = 'AOS_ProductTemplates';
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
      'useTabs' => false,
      'tabDefs' => 
      array (
        'DEFAULT' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
      'syncDetailEditViews' => false,
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 'name',
          1 => 
          array (
            'name' => 'status',
            'studio' => 'visible',
            'label' => 'LBL_STATUS',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'category_name',
            'studio' => 'visible',
            'label' => 'LBL_AOS_PRODUCT_CATEGORIES',
          ),
          1 => '',
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'website',
            'label' => 'LBL_WEBSITE',
          ),
          1 => 
          array (
            'name' => 'date_available',
            'label' => 'LBL_DATE_AVAILABLE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'tax_class',
            'studio' => 'visible',
            'label' => 'LBL_TAX_CLASS',
          ),
          1 => 
          array (
            'name' => 'qty_in_stock',
            'label' => 'LBL_QTY_IN_STOCK',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'manufacturer_id',
            'label' => 'LBL_LIST_MANUFACTURER_ID',
          ),
          1 => 
          array (
            'name' => 'weight',
            'label' => 'LBL_WEIGHT',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'mft_part_num',
            'label' => 'LBL_MFT_PART_NUM',
          ),
          1 => '',
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'vendor_part_num',
            'label' => 'LBL_VENDOR_PART_NUM',
          ),
          1 => 
          array (
            'name' => 'type_id',
            'label' => 'Type',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'currency_id',
            'studio' => 'visible',
            'label' => 'LBL_CURRENCY',
          ),
          1 => 
          array (
            'name' => 'support_name',
            'label' => 'LBL_SUPPORT_NAME',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'cost_price',
            'label' => 'LBL_COST_PRICE',
          ),
          1 => 
          array (
            'name' => 'support_contact',
            'label' => 'LBL_SUPPORT_CONTACT',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'markup',
            'comment' => 'Mark Up of product',
            'customLabel' => '{$MOD.LBL_MARKUP}&nbsp;&nbsp;&nbsp;{$MOD.LBL_IN_PERCENTAGE}<input type="checkbox" name="markup_inper" id="markup_inper" value="1" {if $fields.markup_inper.value==1}checked="checked"{/if}>',
          ),
          1 => 
          array (
            'name' => 'support_description',
            'label' => 'LBL_SUPPORT_DESCRIPTION',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'discount_price',
            'label' => 'LBL_DISCOUNT_PRICE',
          ),
          1 => 
          array (
            'name' => 'support_term',
            'studio' => 'visible',
            'label' => 'LBL_SUPPORT_TERM',
          ),
        ),
        11 => 
        array (
          0 => 
          array (
            'name' => 'quantity',
            'label' => 'LBL_QUANTITY',
          ),
          1 => 
          array (
            'name' => 'unit_measure',
            'customCode' => '<select id="unit_measure" name="unit_measure">{$UNIT_MEASURE_LIST}</select>',
          ),
        ),
        12 => 
        array (
          0 => 
          array (
            'name' => 'total_cost',
            'label' => 'LBL_TOTAL_COST',
          ),
          1 => 
          array (
            'name' => 'pricing_formula',
            'label' => 'LBL_PRICING_FORMULA',
          ),
        ),
        13 => 
        array (
          0 => 'description',
          1 => '',
        ),
      ),
    ),
  ),
);
;
?>
