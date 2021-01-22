<?php

class LeadsViewStructuredom extends SugarView{
	
	public function LeadsViewStructuredom(){
		parent::SugarView();
	}
	
	public function preDisplay()
	{
		parent::preDisplay();
	}
	
	public function display()
	{
		
		global $app_list_strings;
		
		$selected_structure = $_REQUEST['selected'];
		
		$structure = '';
       // $structure .= '<select id="structure" name="structure">';
        $structure .= '<optgroup style="background:#ececec" label="Residential Building"></optgroup>';
        foreach ($app_list_strings['structure_residential'] as $key => $value) {
            $selected = '';
            if ($selected_structure == $value) {
                $selected = 'selected';
            }
            $structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $structure .= '<optgroup style="background:#ececec" label="Non-Residential Building"></optgroup>';
        foreach ($app_list_strings['structure_non_residential'] as $key => $value) {
            $selected = '';
            if ($selected_structure == $value) {
                $selected = 'selected';
            }
            $structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $structure .= '<optgroup style="background:#ececec" label="Non-Building Construction"></optgroup>';
        foreach ($app_list_strings['structure_non_building'] as $key => $value) {
            $selected = '';
            if ($selected_structure == $value) {
                $selected = 'selected';
            }
            $structure .= '<option value="' . $key . '" label="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
      //  $structure .= '</select>';
		
		echo $structure;
		
	}
} 