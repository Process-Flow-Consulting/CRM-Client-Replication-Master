<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

//include ("modules/AOS_Manufacturers/index.php");
class AOS_ManufacturersViewEdit extends ViewEdit
{
    public function __construct()
    {
        parent::__construct();
        $this->useForSubpanel = true;
        $this->useModuleQuickCreateTemplate = true;
    }
	public function predisplay(){
		parent::predisplay();
		function get_manufacturers($add_blank=false,$status='Active')
		{
			global $db;
			$query = "SELECT id, name FROM aos_manufacturers where deleted=0 ";
			if ($status=='Active') {
				$query .= " and status='Active' ";
			}
			elseif ($status=='Inactive') {
				$query .= " and status='Inactive' ";
			}
			elseif ($status=='All') {
			}
			$query .= " order by list_order asc";
			$result = $db->query($query, false);
			
			$GLOBALS['log']->debug("get_manufacturers: result is ".print_r($result,true));

			$list = array();
			if ($add_blank) {
				$list['']='';
			}
			//if($this->db->getRowCount($result) > 0){
				// We have some data.
				while (($row = $db->fetchByAssoc($result)) != null) {
						
				//while ($row = $this->db->fetchByAssoc($result)) {
					$list[$row['id']] = $row['name'];
					$GLOBALS['log']->debug("row id is:".$row['id']);
					$GLOBALS['log']->debug("row name is:".$row['name']);
				}
			//}
		
			return $list;
		}
		$focus = new AOS_Manufacturers();
		$val = get_manufacturers(false,'All');
		$val1 = count($val)+1;
      //  print_r($val1);exit;
	  if(empty($this->bean->id)){
	   $this->bean->list_order = $val1;
	  }
	}
	public function display(){
		parent::display();
		echo"<script>
		$('#status').after('&nbsp;&nbsp;&nbsp;<em>Set status to Inactive to remove this manufacturer from the Manufacturer dropdown lists</em>');
		$('#list_order').parent().parent().removeAttr('class');
		$('[data-label=\"LBL_LIST_ORDER\"]').attr('class','col-xs-8 col-sm-2 label');
		$('#list_order').after('&nbsp;&nbsp;&nbsp;<em>Set the order this manufacturer will appear in the Manufacturer dropdown lists</em>');
		</script>";
	}
}
