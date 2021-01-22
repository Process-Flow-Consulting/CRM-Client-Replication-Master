<?php
require_once 'include/MVC/View/views/view.detail.php';
require_once 'custom/include/common_functions.php';

class oss_LeadClientDetailViewDetail extends ViewDetail{

    function oss_LeadClientDetailViewDetail(){
        parent::ViewDetail();
    }

    function  display() {
    	
    	//$account_proview_link = $this->setAccountProviewLink($this->bean);
    	$account_proview_link = proview_url(array('url'=>$this->bean->account_proview_url));
    	
    	$account_name = '<a href="index.php?module=Accounts&action=DetailView&record='.$this->bean->account_id.'">'.$this->bean->account_name.'</a>';
    	
    	$this->ss->assign('ACCOUNT_NAME',$account_proview_link.'&nbsp;'.$account_name);
        parent::display();
    }
    
//     public function setAccountProviewLink(&$focus){
    
//     	if($focus->account_proview_url != '')
//     	{
//     		$focus->account_proview_url = $focus->account_proview_url;
//     		if (preg_match('/^[^:\/]*:\/\/.*/', $focus->account_proview_url)) {
//     			$focus->account_proview_url= $focus->account_proview_url;
//     		} else {
//     			$focus->account_proview_url = 'http://' . $focus->account_proview_url;
//     		}
    
//     		$focus->account_proview_url = '<a href="javascript:void(0)" onclick="window.open(\''.$focus->account_proview_url.'	\',\'\',\'width=600,height=500\')" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//     	}
//     	else{
//     		$focus->account_proview_url = '';
//     		//$focus->account_proview_url = '<a href="javascript:void(0)" /><img src="custom/themes/default/images/proview_icon.gif" border="0"/></a>';
//     	}
    
//     	return $focus->account_proview_url;
//     }
}

?>
