<?php
require_once 'include/MVC/View/views/view.list.php';
require_once 'custom/include/common_functions.php';

class oss_LeadClientDetailViewList extends ViewList{
	
	function oss_LeadClientDetailViewList(){
		parent::ViewList();
	}
	
	function display(){
		require_once 'custom/modules/oss_LeadClientDetail/customLeadClientDetail.php';
		$this->bean = new customLeadClientDetail();
		
		parent::display();
		
		echo '<script type ="text/javascript">
				$("a.no_proview").each(function(indexVal,elm){$(elm).tipTip({maxWidth: \'auto\',edgeOffset: 10,content: \'No proview available.\',defaultPosition: \'bottom\'})})
		</script>';
		
	}
}