<?php
require_once('include/ListView/ListViewFacade.php');

class CustomListViewFacade extends ListViewFacade{

	function CustomListViewFacade($focus, $module, $type = 0){
		$this->focus = $focus;
 		$this->module = $module;
 		$this->type = $type;
 		$this->build();
	}
	
	function build(){
		//we will assume that if the ListView.html file exists we will want to use that one
		if(file_exists('modules/'.$this->module.'/ListView.html')){
			$this->type = 1;
			$this->lv = new ListView();
			$this->template = 'modules/'.$this->module.'/ListView.html';
		}else{
			$metadataFile = null;
			$foundViewDefs = false;
			
			if( ($this->module == 'Opportunities') && file_exists('custom/modules/' . $this->module. '/metadata/listviewdefs_sub.php') ){
				$metadataFile = 'custom/modules/' .  $this->module . '/metadata/listviewdefs_sub.php';
				$foundViewDefs = true;		
			}else if(file_exists('custom/modules/' . $this->module. '/metadata/listviewdefs.php')){
				$metadataFile = 'custom/modules/' .  $this->module . '/metadata/listviewdefs.php';
				$foundViewDefs = true;
			}else{
				if(file_exists('custom/modules/'. $this->module.'/metadata/metafiles.php')){
					require_once('custom/modules/'. $this->module.'/metadata/metafiles.php');
					if(!empty($metafiles[ $this->module]['listviewdefs'])){
						$metadataFile = $metafiles[ $this->module]['listviewdefs'];
						$foundViewDefs = true;
					}
				}elseif(file_exists('modules/'. $this->module.'/metadata/metafiles.php')){
					require_once('modules/'. $this->module.'/metadata/metafiles.php');
					if(!empty($metafiles[ $this->module]['listviewdefs'])){
						$metadataFile = $metafiles[ $this->module]['listviewdefs'];
						$foundViewDefs = true;
					}
				}
			}
			if(!$foundViewDefs && file_exists('modules/'. $this->module.'/metadata/listviewdefs.php')){
				$metadataFile = 'modules/'. $this->module.'/metadata/listviewdefs.php';
			}
			
			require_once($metadataFile);
	
			if($this->focus->bean_implements('ACL'))
				ACLField::listFilter($listViewDefs[ $this->module], $this->module, $GLOBALS['current_user']->id ,true);
	
			$this->lv = new ListViewSmarty();
			$displayColumns = array();
			if(!empty($_REQUEST['displayColumns'])) {
				foreach(explode('|', $_REQUEST['displayColumns']) as $num => $col) {
					if(!empty($listViewDefs[$this->module][$col]))
						$displayColumns[$col] = $listViewDefs[$this->module][$col];
				}
			}
			else {
				foreach($listViewDefs[$this->module] as $col => $params) {
					if(!empty($params['default']) && $params['default'])
						$displayColumns[$col] = $params;
				}
			}

			$this->lv->displayColumns = $displayColumns;
			$this->type = 2;
			$this->template = 'include/ListView/ListViewGeneric.tpl';
		}
	}
}