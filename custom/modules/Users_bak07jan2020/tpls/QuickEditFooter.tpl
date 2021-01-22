{*
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

*}

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="actionsContainer">
    <tr>
        <td>
        <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button primary" onclick="quickEditSave(); return false;" type="submit" name="Users_dcmenu_save_button" id="Users_dcmenu_save_button" value="Save">
        {{foreach from=$form.buttons key=val item=button}}
           {{sugar_button module="$module" id="$button" view="$view"}}
        {{/foreach}}
        </td>
        <td align="right" nowrap>
            <span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span> {$APP.NTC_REQUIRED}
        </td>
    </tr>
</table>
<script type='text/javascript'>
{literal}

function checkUserEmailData (){
	var table_name = $('.emailaddresses').attr('id');
	var returnFlag = true;
	$('#'+table_name+' tr td input[type=text]').each(function(index,element){
	  if(element.value!=''){
	  		returnFlag = checkUserEmail(element.id,element.value); 
	  		if(returnFlag == false){
	  			return false;
	  		}  
	  }
	});
	return returnFlag;
}


function quickEditSave()
{
	if(checkUserEmailData () == false) {
		return false;
	}
    document.form_DCQuickCreate_Users.action.value='Save';
    var userExists = false;
    $.ajax({url:'index.php?module=Users&to_pdf=1&action=validateUsername&uname='+ document.form_DCQuickCreate_Users.user_name.value+'&record='+document.form_DCQuickCreate_Users.record.value,
        	cache:false,
        	success: function(data) { 
    	    							totalRow = JSON.parse(data);
    	    							//totalRow = jQuery.parseJSON(data);    	    

           					 

    	    					if( check_form('form_DCQuickCreate_Users'))
    	    					{
    	    						if(totalRow['total_row']>0){
        	    						userExists = true;
        	    						add_error_style('form_DCQuickCreate_Users',document.form_DCQuickCreate_Users.user_name.name, '{/literal}{$MOD.LBL_ERROR_USER_NAME_EXISTS}{literal}')// SUGAR.language.get('app_strings','LBL_ERROR_USER_NAME_EXISTS'));
        	    						return false;
               						}
    	        						if(quickEditconfirmReassignRecords())
    	        						{
    	        							DCMenu.save(document.form_DCQuickCreate_Users.id, 'Users_subpanel_save_button');
    	        						}
    	    					}
    	    
    	  }   	  
        	});
}

function quickEditconfirmReassignRecords() {
        var status = document.getElementsByName('status');
        if(status[0] && status[0].value == 'Inactive')
        {
            var r = confirm(SUGAR.language.get('Users','LBL_REASS_CONFIRM_REASSIGN'));
            if(r == true)
            {
                document.form_DCQuickCreate_Users.return_action.value = 'reassignUserRecords';
                document.form_DCQuickCreate_Users.return_module.value = 'Users';
                document.form_DCQuickCreate_Users.submit();
                return false;
            }
        }
        return true;
}
{/literal}
</script>