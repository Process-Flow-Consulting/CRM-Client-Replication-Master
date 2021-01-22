<?php 
//ini_set('display_errors',1);
require_once('include/MVC/View/SugarView.php');
require_once('modules/Import/sources/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/ImportCacheFiles.php');
require_once('modules/Import/ImportDuplicateCheck.php');
require_once('include/upload_file.php');
require_once ('custom/include/common_functions.php');


class ViewSaveimportdata extends SugarView {
	function ViewSaveimportdata(){
		parent::SugarView();
	}
	
	function display(){
		global $mod_strings, $app_strings, $current_user, $sugar_config, $current_language,$app_list_strings;
		global $timedate;
		global $db;
		
		$_REQUEST['import_module'] = 'Leads';
		
		// Clear out this user's last import
		$seedUsersLastImport = new UsersLastImport();
		$seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
		ImportCacheFiles::clearCacheFiles();
		
		
		/**************File Upload Limit Check ***********************/
		require_once ('custom/modules/Users/filters/instancePackage.class.php');
		require_once ('custom/include/common_functions.php');
		
		
		global $app_list_strings, $db, $current_user, $timedate, $sugar_config, $app_strings;
		 
		$admin=new Administration();
		$admin_settings = $admin->retrieveSettings('instance', true);
		$geo_filter = $admin->settings ['instance_geo_filter'];
		 
		$obPackage = new instancePackage ();
		$pkgDetails = $obPackage->getPacakgeDetails();
		 
		$upload_field = 'userfile';
		
		$current_upload_directory_size = getDirectorySize('upload/');
		$current_file_size = $_FILES[$upload_field]['size'];
		
		/* if( ($current_upload_directory_size + $current_file_size) > $pkgDetails['upload_limit'] ){
			$GLOBALS['log']->fatal($app_strings['LBL_NOT_ENOUGH_SPACE']);
			SugarApplication::appendErrorMessage($app_strings['LBL_NOT_ENOUGH_SPACE']);
			SugarApplication::redirect('index.php?module=Leads&action=importxml&return_module=' . $_REQUEST['import_module'] . '&return_action=index');
			die();
		} */
		/**************File Upload Limit Check ***********************/
		
		
		//Upload XML File
		$uploadFile = new UploadFile('userfile');
		//echo '<pre>'; print_r($_FILES); echo '</pre>'; die;
		if ( isset($_FILES['userfile']) && $uploadFile->confirm_upload() ) {
			$file_name = create_guid();
			$uploadFile->final_move($file_name);
			$uploadFileName = $uploadFile->get_upload_path($file_name);
		} else {
			sugar_die("ERROR: uploaded file was too big: max filesize: {$sugar_config['upload_maxsize']}");
			return;
		}
		
		if (file_exists($uploadFileName)) {
		
			$now_db_date = $timedate->nowDbDate();
			
			## FOR DODGE ###
			if ($_REQUEST['xml_source'] == 'dodge') {
				
				$status_filename = ImportCacheFiles::getStatusFileName();
				
				$cmd = "/usr/local/zend/bin/php -f cmdscripts/PullDodge.php $uploadFileName $status_filename $current_user->id > /dev/null 2>&1 & echo $!;";
				$process_path = 'upload/process/';
				$lock_file = $process_path. $current_user->id. '_dodge_process_lock';
					
				//Check lock file is exists
				if(file_exists($lock_file)){
					//check pid
					$pid = file_get_contents($lock_file);
				
					if(posix_kill($pid,0)){
						//echo "Process already running";
						//echo 'running';
					}else{
						//Run the command and write pid in a file.
						$this->runCommand($cmd,$lock_file);
						//echo 'start';
					}
				} else {
					// Run the command and write pid in a file.
					$this->runCommand ( $cmd, $lock_file );
					//echo 'start1';
				}
				
echo <<<EOQ
            <div class="dashletPanelMenu">
			        <div class="hd">
				        <div class="tl"></div>
				        <div class="hd-center"></div>
				        <div class="tr"></div>
					</div>
				    <div class="bd">
				        <div class="ml"></div>
				        <div class="bd-center">
		
				        	<div class="screen">
		                            <span id="mod_title">{$mod_strings['LBL_UPLOADING_XML_FILE']}</span>
								    <span id="mod_title"></span><br><div id = "upload_content"></div>
									<div id="ajax_content"><center><img src="custom/modules/Leads/images/ajaxloader.gif" class="ajax-loader"></center></div><br><br><br>
							</div>
					    </div>
			            <div class="mr"></div>
			        </div>
			        <div class="ft">
			            <div class="bl"></div>
			            <div class="ft-center"></div>
			            <div class="br"></div>
			        </div>
            </div>
EOQ;
echo <<<EOQ
		<script type = "text/javascript">
		var interval_id = '';
		function getProcessStatus(){
					var error_pattern = new RegExp("^error_");
					$.ajax({
					type: 'POST',
					url : 'cmdscripts/dodge_process_status.php',
					data: "&current_user=$current_user->id",
					beforeSend:function (){
					}
					,success:function (data){
						if(trim(data) == 'finished'){
							clearInterval(interval_id);
							window.location.href = "index.php?module=Leads&action=finishimport";
							return false;
						}else if(error_pattern.test(trim(data))){
							clearInterval(interval_id);
							document.getElementById('ajax_content').innerHTML = '<center>'+data.replace("error_","")+'<br>Please Go Back and Try again Resolving the Error.</center>';
							return false;
						}
					}
					 ,error:function(data){
						clearInterval(interval_id);
                        window.location.href = "index.php?module=Leads&action=index";
						return false;
					}
					,cache: false
					,async:true
					});
		}
		$(document).ready(function() {
			 interval_id=setInterval( function() { getProcessStatus(); }, 5000 );
		});
			
	    </script>
EOQ;
			
			}				
			
			// FOR REED ###
			else if ($_REQUEST ['xml_source'] == 'reed') {
				
				$status_filename = ImportCacheFiles::getStatusFileName();
				
				$cmd = "/usr/local/zend/bin/php -f cmdscripts/PullReed.php $uploadFileName $status_filename $current_user->id > /dev/null 2>&1 & echo $!;";
				$process_path = 'upload/process/';
				$lock_file = $process_path. $current_user->id. '_reed_process_lock';
					
				//Check lock file is exists
				if(file_exists($lock_file)){
					//check pid
					$pid = file_get_contents($lock_file);
				
					if(posix_kill($pid,0)){
						//echo "Process already running";
						//echo 'running';
					}else{
						//Run the command and write pid in a file.
						$this->runCommand($cmd,$lock_file);
						//echo 'start';
					}
				} else {
					// Run the command and write pid in a file.
					$this->runCommand ( $cmd, $lock_file );
					//echo 'start1';
				}

echo <<<EOQ
            <div class="dashletPanelMenu">
			        <div class="hd">
				        <div class="tl"></div>
				        <div class="hd-center"></div>
				        <div class="tr"></div>
					</div>
				    <div class="bd">
				        <div class="ml"></div>
				        <div class="bd-center">
				
				        	<div class="screen">
		                            <span id="mod_title">{$mod_strings['LBL_UPLOADING_XML_FILE']}</span>
								    <span id="mod_title"></span><br><div id = "upload_content"></div>
									<div id="ajax_content"><center><img src="custom/modules/Leads/images/ajaxloader.gif" class="ajax-loader"></center></div><br><br><br>
							</div>
					    </div>
			            <div class="mr"></div>
			        </div>
			        <div class="ft">
			            <div class="bl"></div>
			            <div class="ft-center"></div>
			            <div class="br"></div>
			        </div>
            </div>
EOQ;
echo <<<EOQ
		<script type = "text/javascript">
		var interval_id = '';
		function getProcessStatus(){
					var error_pattern = new RegExp("^error_");

					$.ajax({
					type: 'POST',
					url : 'cmdscripts/reed_process_status.php',
					data: "&current_user=$current_user->id",
					beforeSend:function (){
					}
					,success:function (data){
						if(trim(data) == 'finished'){
							clearInterval(interval_id);
							window.location.href = "index.php?module=Leads&action=finishimport";
							return false;
						}else if(error_pattern.test(trim(data))){
							clearInterval(interval_id);
							document.getElementById('ajax_content').innerHTML = '<center>'+data.replace("error_","")+'<br>Please Go Back and Try again Resolving the Error.</center>';
							return false;
						}
					}
					 ,error:function(data){
						clearInterval(interval_id);
                        window.location.href = "index.php?module=Leads&action=index";
						return false;
					}
					,cache: false
					,async:true
					});
		}
		$(document).ready(function() {
			 interval_id=setInterval( function() { getProcessStatus(); }, 5000 );
		});
		
	    </script>
EOQ;
		
			}				
			
			// FOR ONVIA ###
			else if ($_REQUEST ['xml_source'] == 'onvia') {			
				
				$status_filename = ImportCacheFiles::getStatusFileName();
				
				$cmd = "/usr/local/zend/bin/php -f cmdscripts/PullOnvia.php $uploadFileName $status_filename $current_user->id > /dev/null 2>&1 & echo $!;";
				
				$process_path = 'upload/process/';
				$lock_file = $process_path.'onvia_process_lock';
					
				//Check lock file is exists
				if(file_exists($lock_file)){
					//check pid
					$pid = file_get_contents($lock_file);

					if(posix_kill($pid,0)){
						//echo "Process already running";
						//echo 'running';
					}else{
						//Run the command and write pid in a file.
						$this->runCommand($cmd,$lock_file);
						//echo 'start';
					}
				} else {
					// Run the command and write pid in a file.
					$this->runCommand ( $cmd, $lock_file );
					//echo 'start1';
				}
				
echo <<<EOQ
            <div class="dashletPanelMenu">
			        <div class="hd">
				        <div class="tl"></div>
				        <div class="hd-center"></div>
				        <div class="tr"></div>
					</div>
				    <div class="bd">
				        <div class="ml"></div>
				        <div class="bd-center">
			
				        	<div class="screen">
		                            <span id="mod_title">{$mod_strings['LBL_UPLOADING_XML_FILE']}</span>
								    <span id="mod_title"></span><br><div id = "upload_content"></div>
									<div id="ajax_content"><center><img src="custom/modules/Leads/images/ajaxloader.gif" class="ajax-loader"></center></div><br><br><br>
							</div>
					    </div>
			            <div class="mr"></div>
			        </div>
			        <div class="ft">
			            <div class="bl"></div>
			            <div class="ft-center"></div>
			            <div class="br"></div>
			        </div>
            </div>
EOQ;
				
echo <<<EOQ
		<script type = "text/javascript">	
		var interval_id = '';		
		function getProcessStatus(){
					var error_pattern = new RegExp("^error_");		
					$.ajax({
					type: 'POST',
					url : 'cmdscripts/onvia_process_status.php',
					data: '',
					beforeSend:function (){						
					}			 
					,success:function (data){							
						if(trim(data) == 'finished'){
							clearInterval(interval_id);
							window.location.href = "index.php?module=Leads&action=finishimport";
							return false;
						}else if(error_pattern.test(trim(data))){
							clearInterval(interval_id);
							document.getElementById('ajax_content').innerHTML = '<center>'+data.replace("error_","")+'<br>Please Go Back and Try again Resolving the Error.</center>';
							return false;
						}
					}
					 ,error:function(data){
						clearInterval(interval_id);
                        window.location.href = "index.php?module=Leads&action=index";
						return false;
					}
					,cache: false
					,async:true
					});	
		}
		$(document).ready(function() {
			 interval_id=setInterval( function() { getProcessStatus(); }, 5000 );	
		});	
					
	    </script>
EOQ;
			}
		}
		
	}
	
	function runCommand($cmd,$lock_file){
		$pid =  exec ( $cmd, $output );
		$fp=fopen($lock_file,"w");
		fwrite($fp,$pid);
		fclose($fp);
	}
	
}
?>
