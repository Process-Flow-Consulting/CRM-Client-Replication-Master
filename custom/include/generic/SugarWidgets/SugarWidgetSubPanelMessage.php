<?php

class SugarWidgetSubPanelMessage extends SugarWidget{
	
	function display($layout_def){		
		global $mod_strings;
		return " ".str_replace(' * ','<img align="absmiddle" src="custom/themes/default/images/green_money.gif"  />', $mod_strings['LBL_CONVERTED_BIDDERS_MSG']);
	}
} 
?>