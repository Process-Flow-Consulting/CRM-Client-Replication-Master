<?php 
class ViewImportxml extends SugarView {
	function ViewImportxml(){
		parent::SugarView();
	}
	
	function display(){
		global $mod_strings, $app_strings, $current_user, $sugar_config, $current_language,$app_list_strings;
		$this->ss->assign("HEADER", $app_strings['LBL_IMPORT']." ". $mod_strings['LBL_MODULE_NAME']);
		
		$this->ss->assign("JAVASCRIPT", $this->_getJS());
		
		$this->ss->display('custom/modules/Leads/tpls/xml_upload.tpl');
	}
	
	/**
	 * Returns JS used in this view
	 */
	private function _getJS()
	{
		global $mod_strings;
	
		return <<<EOJAVASCRIPT
<script type="text/javascript">
<!--
document.getElementById('gonext').onclick = function(){
    clear_all_errors();
    var isError = false;
    // be sure we specify a file to upload
    if (document.getElementById('importstep2').userfile.value == "") {
        add_error_style(document.getElementById('importstep2').name,'userfile',"{$mod_strings['ERR_MISSING_REQUIRED_FIELDS']} {$mod_strings['ERR_SELECT_FILE']}");
        isError = true;
    }else{
		document.getElementById('importstep2').action.value = 'saveimportdata';
		document.getElementById('upload_content').style.display = 'none';
		document.getElementById('ajax_content').style.display = 'block';
		document.getElementById('mod_title').innerHTML = '{$mod_strings['LBL_UPLOADING_XML_FILE']}';
	}
    return !isError;
}
-->
</script>
	
EOJAVASCRIPT;
	}
	
}


?>