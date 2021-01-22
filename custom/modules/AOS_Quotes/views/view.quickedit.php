<?php
require_once 'include/MVC/View/views/view.quickedit.php';

class AOS_QuotesViewQuickedit extends ViewQuickedit {
	
	protected $defaultButtons = array(array('customCode' => '<input type="button" value="Save" id="Quotes_dcmenu_save_button" name="Quotes_dcmenu_save_button" onclick="saveQuickEdit();" class="button primary" accesskey="a" title="Save">'), 'DCMENUCANCEL', 'DCMENUFULLFORM');
	
	public function display() {		
		$view = (! empty ( $_REQUEST ['target_view'] )) ? $_REQUEST ['target_view'] : 'QuickCreate';
		$module = $_REQUEST ['module'];
		
		// locate the best viewdefs to use: 1. custom/module/quickcreatedefs.php
		// 2. module/quickcreatedefs.php 3. custom/module/editviewdefs.php 4.
		// module/editviewdefs.php
		$base = 'modules/' . $module . '/metadata/';
		$source = 'custom/' . $base . strtolower ( $view ) . 'defs.php';
		if (! file_exists ( $source )) {
			$source = $base . strtolower ( $view ) . 'defs.php';
			if (! file_exists ( $source )) {
				// if our view does not exist default to EditView
				$view = 'EditView';
				$source = 'custom/' . $base . 'editviewdefs.php';
				if (! file_exists ( $source )) {
					$source = $base . 'editviewdefs.php';
				}
			}
		}
		
		// in some cases, the source file will not exist. In these cases lets
		// just navigate to the full form directlhy
		if (! file_exists ( $source )) {
			global $app_strings;
			
			// write out jscript that will get evaluated and redirect the
			// browser window.
			$no_defs_js = '<script>SUGAR.ajaxUI.loadContent("index.php?return_module=' . $this->bean->module_dir . '&module=' . $this->bean->module_dir . '&action=EditView&record=' . $this->bean->id . '")</script>';
			
			// reports is a special case as it does not have an edit view so
			// navigate to wizard view
			if (strtolower ( $module ) == 'reports') {
				$no_defs_js = '<script>SUGAR.ajaxUI.loadContent("index.php?return_module=' . $this->bean->module_dir . '&module=' . $this->bean->module_dir . '&action=ReportsWizard&record=' . $this->bean->id . '")</script>';
			}			// if this is not reports and there are no edit view files then go
			// to detail view
			elseif (! file_exists ( 'custom/' . $base . 'editviewdefs.php' ) && ! file_exists ( $base . 'editviewdefs.php' ) && ! file_exists ( 'custom/modules/' . $module . '/EditView.php' ) && ! file_exists ( 'modules/' . $module . '/EditView.php' )) {
				$no_defs_js = '<script>SUGAR.ajaxUI.loadContent("index.php?return_module=' . $this->bean->module_dir . '&module=' . $this->bean->module_dir . '&action=DetailView&record=' . $this->bean->id . '")</script>';
			}
			
			echo json_encode ( array ('scriptOnly' => $no_defs_js ) );
			
			return;
		
		}
		
		$this->ev = new EditView ();
		$this->ev->view = $view;
		$this->ev->ss = new Sugar_Smarty ();
		
		$this->ev->ss->assign ( 'isDCForm', $this->_isDCForm );
		// $_REQUEST['return_action'] = 'SubPanelViewer';
		$this->ev->setup ( $module, $this->bean, $source );
		$this->ev->showSectionPanelsTitles = false;
		$this->ev->defs ['templateMeta'] ['form'] ['headerTpl'] = $this->headerTpl;
		$this->ev->defs ['templateMeta'] ['form'] ['footerTpl'] = $this->footerTpl;
		$this->ev->defs ['templateMeta'] ['form'] ['buttons'] = $this->defaultButtons;
		$this->ev->defs ['templateMeta'] ['form'] ['button_location'] = 'bottom';
		$this->ev->defs ['templateMeta'] ['form'] ['hidden'] = '<input type="hidden" name="is_ajax_call" value="1" />';
		$this->ev->defs ['templateMeta'] ['form'] ['hidden'] .= '<input type="hidden" id="from_dcmenu" name="from_dcmenu" value="1" />';		
		$this->ev->defs ['templateMeta'] ['form'] ['hidden'] .= '<input type="hidden" name="is_form_updated" id="is_form_updated" value="0"/>';
		$this->ev->defs ['templateMeta'] ['form'] ['hidden'] .= '<input type="hidden" id="pre_form_string" name="pre_form_string" value="" />';
		$this->ev->defs ['templateMeta'] ['form'] ['hideAudit'] = true;
		
		// use module level view if available
		$editFileName = 'modules/' . $module . '/views/view.edit.php';
		if (file_exists ( 'custom/modules/' . $module . '/views/view.edit.php' )) {
			$editFileName = 'custom/modules/' . $module . '/views/view.edit.php';
		}
		
		$defaultProcess = true;
		if (file_exists ( $editFileName )) {
			include ($editFileName);
			$c = $module . 'ViewEdit';
			
			if (class_exists ( $c )) {
				$view = new $c ();
				if ($view->useForSubpanel) {
					$defaultProcess = false;
					
					// Check if we should use the module's QuickCreate.tpl file
					if ($view->useModuleQuickCreateTemplate && file_exists ( 'modules/' . $module . '/tpls/QuickCreate.tpl' )) {
						$this->ev->defs ['templateMeta'] ['form'] ['headerTpl'] = 'modules/' . $module . '/tpls/QuickCreate.tpl';
					}
					
					$view->ev = & $this->ev;
					$view->ss = & $this->ev->ss;
					$class = $GLOBALS ['beanList'] [$module];
					if (! empty ( $GLOBALS ['beanFiles'] [$class] )) {
						require_once ($GLOBALS ['beanFiles'] [$class]);
						$bean = new $class ();
						if (isset ( $_REQUEST ['record'] ) && $_REQUEST ['record'] != false) {
							$bean->retrieve ( $_REQUEST ['record'] );
						}
						$view->bean = $bean;
					}
					$view->ev->formName = 'form_DC' . $view->ev->view . '_' . $module;
					$view->showTitle = false; // Do not show title since this is for
					                          // subpanel
					ob_start ();
					$view->display ();
					$captured = ob_get_clean ();
					echo json_encode ( array ('title' => $this->bean->name, 'url' => 'index.php?module=' . $this->bean->module_dir . '&action=DetailView&record=' . $this->bean->id, 'html' => $captured, 'eval' => true ) );
				}
			}
		}
		
		// if defaultProcess is still true, then the default edit view was not
		// used. Finish processing.
		if ($defaultProcess) {
			$form_name = 'form_DC' . $this->ev->view . '_' . $module;
			$this->ev->formName = $form_name;
			$this->ev->process ( true, $form_name );
			ob_clean ();
			echo json_encode ( array ('title' => $this->bean->name, 'url' => 'index.php?module=' . $this->bean->module_dir . '&action=DetailView&record=' . $this->bean->id, 'html' => $this->ev->display ( false, true ), 'eval' => true ) );
		}
	}
}