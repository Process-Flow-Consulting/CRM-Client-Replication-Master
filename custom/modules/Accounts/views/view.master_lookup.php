<?php
// required master database related file
ini_set('display_errors','0');
global $sugar_config;
//include_once $sugar_config['master_config_path'] ;// '/vol/certificate/master_config.php';
include_once 'custom/include/common_functions.php';

class AccountsViewMaster_lookup extends SugarView {
	/*
	 * construct method
	 */
	function AccountsViewMaster_lookup() {
		parent::SugarView ();
	}
	
	/**
	 * Overrwite SugarView Display method
	 * 
	 * @see SugarView::display()
	 *
	 */
	function display() {
		global $app_list_strings, $db, $current_user, $app_strings, $timedate, $sugar_config, $beanList;
		
		
		/*echo '<pre>';
		print_r($_REQUEST);
		echo '</pre>';*/
		
		echo "<style>
			.yui-ac-content{
				width: auto;
			}
		</style>";
		
		/**
		 * **** Check Search Fields ******
		 */
		$params = '';
		
		//search option initialization
		if (isset ( $_REQUEST ['search_option'] ) && ! empty ( $_REQUEST ['search_option'] )) {
			$search_option = $_REQUEST ['search_option'];
		} else {
			$search_option = '';
		}
		
		// if company search text is present
		if (isset ( $_REQUEST ['company'] ) && ! empty ( $_REQUEST ['company'] ) && $search_option == 1) {
			$company = trim ( htmlspecialchars_decode($_REQUEST ['company'] ));
			$params .= "&param1=".urlencode($company);
		} else {
			$company = "";
		}
		
		// if company region text is present
		if (isset ( $_REQUEST ['region'] ) && ! empty ( $_REQUEST ['region'] ) && $search_option == 1) {
			$region = trim ( $_REQUEST ['region'] );
			$params .= "&param2=".$region;
		} else {
			$region = "";
		}
		
		//if phone/fax no is present
		if (isset ( $_REQUEST ['phone'] ) && ! empty ( $_REQUEST ['phone'] ) && $search_option == 2) {
			$phone = trim ( $_REQUEST ['phone'] );
			$params .= "&param1=".clean_ph_no($phone);
		} else {
			$phone = "";
		}
		
		// if classification is choosen
		if (isset ( $_REQUEST ['classification'] ) && ! empty ( $_REQUEST ['classification'] ) && $search_option == 3) {
			$classification = trim ( htmlspecialchars_decode($_REQUEST ['classification'] ));
			
			//get the clssification category no
			$classification_query= " SELECT category_no FROM oss_classification WHERE name = '".$classification."' AND deleted = 0";
			$classification_result = $db->query($classification_query);
			$classification_row = $db->fetchByAssoc($classification_result);
			
			$params .= "&param1=".$classification_row['category_no'];
		} else {
			$classification = "";
		}
		
		//if region is selected with classification serach 
		if (isset ( $_REQUEST ['region1'] ) && ! empty ( $_REQUEST ['region1'] ) && $search_option == 3) {
			$region1 = trim ( $_REQUEST ['region1'] );
			$params .= "&param2=".$region1;
		} else {
			$region1 = "";
		}
		
		
		// pagination strings array
		$navStrings = array ('next' => $app_strings ['LNK_LIST_NEXT'], 'previous' => $app_strings ['LNK_LIST_PREVIOUS'], 'end' => $app_strings ['LNK_LIST_END'], 'start' => $app_strings ['LNK_LIST_START'], 'of' => $app_strings ['LBL_LIST_OF'] );
		
		// query to retrieve company details based on search criteria
		if($search_option==1){
			$clean_url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetsearchresults_json.p?searchtype=name".$params;
		}
		if($search_option==2){
			$clean_url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetsearchresults_json.p?searchtype=phone".$params;
		}
		if($search_option==3){
			$clean_url = "http://www.thebluebook.com/wsnsa.dll/WService=wsbbhub/bb_hub/blgetsearchresults_json.p?searchtype=class".$params;
		}
		
		
		if(isset($_REQUEST['page']) && !empty($_REQUEST['page'])){
			$url = $clean_url.'&page='.$_REQUEST['page'];
		}else{
			$url = $clean_url.'&page=1';
		}		
		
		
		/**
		 * **** GLOBAL Assign Smarty Variables ****
		 */
		//change sorting type of region drop drown to ascending
		//@modified by Mohit Kumar Gupta
		//@date 17-Dec-2013 
		$regionDomArray = $app_list_strings [' region_dom'];
		asort($regionDomArray);
		$this->ss->assign ( 'COMPANY', htmlspecialchars($company,ENT_COMPAT,'utf-8',0));
		$this->ss->assign ( 'PHONE', $phone );
		$this->ss->assign ( 'REGION', get_select_options_with_id ( $regionDomArray, $region ) );
		$this->ss->assign ( 'CLASSIFICATION', $classification );
		$this->ss->assign ( 'REGION1', get_select_options_with_id ( $regionDomArray, $region1 ) );
		$this->ss->assign ( 'REPORT_NAME', 'Search Blue Book' );
		
		//checked search option
		if ($search_option == 1) {
			$this->ss->assign ( 'OPTION1', 'checked="checked"' );
		} else if ($search_option == 2) {
			$this->ss->assign ( 'OPTION2', 'checked="checked"' );
		} else if ($search_option == 3) {
			$this->ss->assign ( 'OPTION3', 'checked="checked"' );
		}
		
		// search only if keyword is placed to search
		if ( (empty ( $region ) && empty ( $company ) && empty ( $classification ) && empty ( $region1 ) && empty ( $phone )) || empty($search_option) ) {
			
			$this->ss->display ( 'custom/modules/Accounts/tpls/empty_master_lookup.tpl' );
		
		} else {
			
			//get json encoded data from the API
			$data = $this->getData($url);
			
			//from file to test
			//$file = "/home/hirakdata/hirak/Downloads/search_json.txt";
			//$data = file_get_contents($file);	
			
			//decode json data
			$data = json_decode($data);
			
			//echo '<pre>'; print_r($data); echo '</pre>'; die;
			
			$html = '';
			$i = 0;
			$fax_exists = false;
			
			foreach( $data->response->SearchResult as $key => $lv )
			{
				//echo '<pre>'; print_r($lv); echo '</pre>'; die;
				
				//county name from mcounty id
				$county_name = '';				
				if(isset($lv->county_id) && !empty($lv->county_id)){
					$county_name = $this->getCounty($lv->county_id, $lv->state);
					
					if(!empty($county_name) && isset($county_name)){
						$county_name = ucwords(strtolower($county_name));
					}
				}
				
				$state_name = $app_list_strings['state_dom'][$lv->state];
				
				$phone_number = formatPhoneNumber($lv->phone);
				
				$fax_number = formatPhoneNumber($lv->fax);
				
				$classification_name = '';
				if(isset($lv->class) && !empty($lv->class)){
					$obClass = new oss_Classification();
					$obClass->retrieve_by_string_fields(array('category_no' => $lv->class));
					
					if(!empty($obClass->name) && isset($obClass->name)){
						$classification_name = $obClass->name;
					}
				}
						
				$bidders_proview = array();
				$bidders_proview['url'] = $lv->proview;
			
				$existing_client_id = $this->checkExistingAccount($lv->client_bb_id);
				$existing_client_sign = '';
				if (preg_match('/^[^:\/]*:\/\/.*/', $lv->proview)) {
	   				$proview_url = $lv->proview;
	    		} else {
	    			$proview_url = 'http://' . $lv->proview;
	     		}
				$client_name = '<a href="'.$proview_url.'" target="_blank">'.$lv->name.'</a>';
				if(!empty($existing_client_id)){
					$existing_client_sign = '<img src="custom/themes/default/images/tick_mark.jpg" alt="Create">';
					$client_name = '<a href="index.php?module=Accounts&action=DetailView&retun_module=Accounts&record='.$existing_client_id.'" target="_blank">'.$lv->name.'</a>';	
				}				
				$ListRow = ($i % 2) == 1 ? 'evenListRowS1' : 'oddListRowS1';
				$html .= '<tr height=\'20\' class=' . $ListRow . ' >
	                <td class="" valign="top" align="left" scope="row"><input title="'.($lv->name).'" onclick="sListView.check_item(this, document.MassUpdate)" type="checkbox" class="checkbox" name="mass[]" value="' . $lv->client_bb_id . '"></td>
	                <td class="" valign="top" align="left" scope="row">'.$existing_client_sign.'</td>
	                <td class="" valign="top" align="left" scope="row">'. proview_url($bidders_proview).'<b>' . $client_name . '</b></td>
	                <td class="" valign="top" align="left" scope="row">' . $lv->city . '</td>
	                <td class="" valign="top" align="left" scope="row">' . $state_name . '</td>
	                <td class="" valign="top" align="left" scope="row">' . $county_name . '</td>
	                <td class="" valign="top" align="left" scope="row">' . $lv->postalcode . '</td>
	                <td class="" valign="top" align="left" scope="row">' . $phone_number . '</td>';
					
					if(!empty($lv->fax) && ($phone == $lv->fax)){
						
						$fax_exists = true;
						
	             	 	$html .= '<td class="" valign="top" align="left" scope="row">' . $fax_number . '</td>';
					}
	                
	             $html .= '<td class="" valign="top" align="left" scope="row">' . $classification_name . '</td>
	              </tr>';
				$i ++;
			}
			
			if (isset ( $_REQUEST ['uid'] ) && ! empty ( $_REQUEST ['uid'] )) {
				$uid = $_REQUEST ['uid'];
			} else {
				$uid = '';
			}
			
			$page_no = $data->response->page;
			$next_page = $data->response->nextpage;
			
			if($page_no > 0){
				$pageData['urls']['prevPage'] = $clean_url.'&page='.($page_no - 1);
				$pageData['offsets']['prev'] = $page_no - 1;
			}
			
			$pageData['urls']['nextPage'] = $clean_url.'&page='.($page_no + 1);
			$pageData['offsets']['next'] = $page_no + 1;
			
			/**
			 * **** Assign Smarty Variables ****
			 */
			$this->ss->assign ( 'next_page',$next_page);
			$this->ss->assign ( 'page_no',$page_no);
			$this->ss->assign ( 'navStrings', $navStrings );
			$this->ss->assign ( 'prerow', true );
			$this->ss->assign ( 'DATA', $html );
			$this->ss->assign ( 'pageData', $pageData );
			$this->ss->assign ( 'moduleString', 'page' );
			$this->ss->assign ( 'UID', $uid );
			$this->ss->assign ( 'FAX_EXISTS', $fax_exists );
			$this->ss->display ( 'custom/modules/Accounts/tpls/master_lookup.tpl' );
		}
	
	}
	private function getData($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	function getCounty($county_no, $state){
		global $db;
		$sql = "SELECT `name` FROM oss_county WHERE county_number = '".$county_no."' AND county_abbr = '".$state."' AND deleted=0";
		$query = $db->query($sql);
		$result = $db->fetchByAssoc($query);
		return $result['name'];
	}
	function checkExistingAccount($mi_account_id){
		global $db;
		$sql = "SELECT `id` FROM `accounts` WHERE `mi_account_id`='".$mi_account_id."' AND `deleted` = 0 AND `visibility`=1";
		$query = $db->query($sql);
		$result = $db->fetchByAssoc($query);
		if(!empty($result['id'])){		
			return $result['id'];			
		}else{
			return false;
		}
	}

}
?>
