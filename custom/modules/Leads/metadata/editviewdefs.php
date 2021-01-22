<?php
// created: 2012-02-29 16:36:47
$viewdefs['Leads']['EditView'] = array (
  'templateMeta' => 
  array (
    'form' => 
    array (
      'hidden' => 
      array (
        0 => '<input type="hidden" name="prospect_id" value="{if isset($smarty.request.prospect_id)}{$smarty.request.prospect_id}{else}{$bean->prospect_id}{/if}">',
        1 => '<input type="hidden" name="account_id" value="{if isset($smarty.request.account_id)}{$smarty.request.account_id}{else}{$bean->account_id}{/if}">',
        2 => '<input type="hidden" name="contact_id" value="{if isset($smarty.request.contact_id)}{$smarty.request.contact_id}{else}{$bean->contact_id}{/if}">',
        3 => '<input type="hidden" name="opportunity_id" value="{if isset($smarty.request.opportunity_id)}{$smarty.request.opportunity_id}{else}{$bean->opportunity_id}{/if}">',
      	4 => '<input type="hidden" name="dup_checked" value="false">',	
      ),
      'buttons' => 
      array (
        0 => 'SAVE',
        1 => 'CANCEL',
      ),
    ),
    'maxColumns' => '2',
    'useTabs' => false,
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
    'javascript' => '<script type="text/javascript" language="Javascript">
		{literal}
			var count = YAHOO.util.Selector.query(\'input[id^=project_url_l]\').length +1 ;

	function add_file_field_dd(){ 

		var container = document.getElementById(\'project_url_div\');					//code for label of url
		
			var label_a = document.createElement(\'label\');
			label_a.id = "label_desc"+count;
			label_a.innerHTML="Description: ";
			container.appendChild(label_a);
		
			var file_field_l = document.createElement(\'input\');
			file_field_l.type = \'text\';
			file_field_l.name=\'project_url_l[]\';                        
			file_field_l.setAttribute("id",\'project_url_l\'+count);
			container.appendChild(file_field_l);

			var label_b = document.createElement(\'label\');
			label_b.id= "label_url"+count;
			label_b.innerHTML=" URL: ";
			container.appendChild(label_b);
		
		
			var file_field = document.createElement(\'input\');
			file_field.type = \'text\';
			file_field.value = "http://";
			file_field.name=\'project_url[]\';
			file_field.setAttribute("id",\'project_url\'+count);
			container.appendChild(file_field);
	
			var file_field_b = document.createElement(\'img\');
			file_field_b.name=\'remove\';
			file_field_b.setAttribute("id","Remove"+count);
			file_field_b.src = \'index.php?entryPoint=getImage&themeName=Sugar&imageName=id-ff-remove.png\';
			file_field_b.style.position="absolute";
			file_field_b.setAttribute("onclick",\'remove_file_field(\'+count+\')\');
			container.appendChild(file_field_b);
		
			var br_field = document.createElement(\'br\');
			br_field.setAttribute("id",\'bmain\'+count);
			container.appendChild(br_field);
			
			count++;
}


function remove_file_field(deleteID){
		
		var element_desc = document.getElementById(\'label_desc\'+deleteID);			
		element_desc.parentNode.removeChild(element_desc);
		
		var element_l = document.getElementById(\'project_url_l\'+deleteID);			//code for label of url
		element_l.parentNode.removeChild(element_l);
		
		var element_url = document.getElementById(\'label_url\'+deleteID);			
		element_url.parentNode.removeChild(element_url);
		
		var element = document.getElementById(\'project_url\'+deleteID);
		element.parentNode.removeChild(element);
		
		var remove = document.getElementById(\'Remove\'+deleteID);
		remove.parentNode.removeChild(remove);
		
		var breakId = document.getElementById(\'bmain\'+deleteID);
		breakId.parentNode.removeChild(breakId);
		
		count--;
		
}

{/literal}
</script>',
    'syncDetailEditViews' => false,
  ),
  'panels' => 
  array (
    'LBL_CONTACT_INFORMATION' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'project_lead_id',
          'label' => 'LBL_PROJECT_LEAD_ID',
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
          'name' => 'project_title',
          'label' => 'LBL_PROJECT_TITLE',
        ),
        1 => 
        array (
          'name' => 'received',
          'label' => 'LBL_RECEIVED',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'address',
          'label' => 'LBL_ADDRESS',
        ),
        1 => 'status',
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'state',
          'comment' => 'STATE OF LEAD',
          'label' => 'LBL_STATE',
          'displayParams' => 
          array (
            'javascript' => 'onchange="getCounty(this.value,\'\');"',
          ),
        ),
        1 => 
        array (
          'name' => 'structure',
          'comment' => 'STRUCTURE OF LEAD',
          'label' => 'LBL_STRUCTURE',
          'customCode' => '{$structure}',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'county',
          'comment' => 'COUNTY LIST',
          'label' => 'LBL_COUNTY',
          'customCode' => '{$county}',
        ),
        1 => 
        array (
          'name' => 'type',
          'comment' => 'TYPE OF LEAD',
          'label' => 'LBL_TYPE',
        ),
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'city',
          'label' => 'LBL_CITY',
        ),
        1 => 
        array (
          'name' => 'owner',
          'comment' => 'OWNER OF LEAD',
          'label' => 'LBL_OWNER',
        ),
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'zip_code',
          'label' => 'LBL_ZIP_CODE',
        ),
        1 => 
        array (
          'name' => 'project_status',
          'comment' => 'STATUS OF PROJECT',
          'label' => 'LBL_PROJECT_STATUS',
        ),
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'pre_bid_meeting',
          'label' => 'LBL_PRE_BID_MEETING',
        ),
        1 => 
        array (
          'name' => 'start_date',
          'label' => 'LBL_START_DATE',
        ),
      ),
      8 => 
      array (
        0 => 
        array (
          'name' => 'asap',
          'label' => 'LBL_ASAP',
        ),
        1 => 
        array (
          'name' => 'end_date',
          'label' => 'LBL_END_DATE',
        ),
      ),
      9 => 
      array (
        0 => 
        array (
          'name' => 'bids_due',
          'label' => 'LBL_BIDS_DUE',
          'displayParams' =>
          array (
          		'required' => true,
          ),
        ),
        1 => 
        array (
          'name' => 'contact_no',
          'label' => 'LBL_CONTACT_NO',
        ),
      ),
      10 => 
      array (
        0 => 
        array (
          'name' => 'bid_due_timezone',
          'label' => 'LBL_BID_DUE_TIMEZONE',
          'displayParams' => 
          array (
            'required' => true,
          ),
        ),
        1 => 
        array (
          'name' => 'valuation',
          'label' => 'LBL_VALUATION',
        ),
      ),
      /*11 => 
      array (
        0 => 
        array (
          'name' => 'test',
          'label' => 'LBL_TEST',
          'customCode' => '<input type=\'button\' value=\'Add URL\' onclick=\'add_file_field_dd();\' ><br />',
        ),
      ),
      12 => 
      array ( 
        0 => 
        array (
          'name' => 'project_url',
          'label' => 'LBL_PROJECT_URL',
          'customCode' => ' <div id=\'project_url_div\'>{$dom}</div>',
        ),
      ),*/
    ),
    'lbl_editview_panel2' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'union_c',
          'customLabel' => '{$MOD.LBL_LABOR_TYPE}',
          'customCode' => '<input type="checkbox" name="union_c" id="union_c" value="1" {if $fields.union_c.value==1}checked="checked"{/if} >&nbsp;{$MOD.LBL_UNION}&nbsp;&nbsp;<input type="checkbox" name="non_union" id="non_union" value="1" {if $fields.non_union.value==1}checked="checked"{/if}>&nbsp;{$MOD.LBL_NON_UNION}&nbsp;&nbsp;<input type="checkbox" name="prevailing_wage" id="prevailing_wage" value="1"  {if $fields.prevailing_wage.value==1}checked="checked"{/if} >&nbsp;{$MOD.LBL_PREVAILING_WAGE}',
        ),
        1 => '',
      ),
    ),
    'lbl_editview_panel1' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'number_of_buildings',
          'label' => 'LBL_NUMBER_OF_BUILDINGS',
        ),
        1 => 
        array (
          'name' => 'square_footage',
          'label' => 'LBL_SQUARE_FOOTAGE',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'stories_above_grade',
          'label' => 'LBL_STORIES_ABOVE_GRADE',
        ),
        1 => 
        array (
          'name' => 'stories_below_grade',
          'label' => 'LBL_STORIES_BELOW_GRADE',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'scope_of_work',
          'label' => 'LBL_SCOPE_OF_WORK',
        ),
      ),
    ),
    'LBL_PANEL_ASSIGNMENT' => 
    array (
      0 => 
      array (
        0 => 'description',
      ),
    ),
  ),
);
appendFieldsOnViews($viewdefs['Leads']['EditView'],$detailView=array(),$searchDefs=array(),$listViewDefs=array(),$searchFields=array(),'Leads','EditView');
?>
