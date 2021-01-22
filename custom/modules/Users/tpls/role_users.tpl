<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="edit view">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                <th align="left" scope="row" colspan="4"><h2>
					<img src="{sugar_getimagepath file='projectpipeline_bb_wizard.png'}" border=0 width="320" height="54" />
		{*$MOD.LBL_BBWIZ_TITLE*}</h2></th>
            </tr>
                    <tr>
                        <td align="left" scope="row" colspan="2">
                        <p>{$MOD.LBL_BBWIZ_TITLE_ADDITIONAL_USER|replace:'3':$MAX_USER_ALLOWED}</p>
                        {if $NUM_INSTANCE_USERS and $CRITICAL_ERROR neq 1}
                        <b>Active users : {math equation=x-y x=$MAX_USER_ALLOWED y=$NUM_INSTANCE_USERS}</b><br/>
                        <b>Remaining users : {$NUM_INSTANCE_USERS}</b>
                        
                        {/if}
                        </td>
                    </tr>
                    <tr>
                        <td align="left" scope="row" colspan="2">&nbsp;<div class="errormessage error" id="wizerror"></div></td>
                    </tr>
                    <!-- Admin User -->
                    <tr>
                        <td width="5%" scope="row" nowrap="nowrap">
                            <div class="selectorCont" >
                               <div class="inputCont" >
                               <input class="txtAlignRight" type="text" readonly size="2" name="admins" id="admin" value="{$smarty.request.admins}" />
                               </div>
                               <div class="iconCont">
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'admin');" class="linkUp"> </a>
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'admin');" class="linkDown" > </a>
                               </div>
                            </div>
                        </td>
                        <td width="90%">
                           <slot><i>{$MOD.LBL_ADMIN_INFO}</i></slot>
                           <div id="admin_container">
                           {if $smarty.request.admin|@count gt 0}
                           {include file="custom/modules/Users/tpls/usersmall.tpl" title="admin" }
                           {/if}
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="5%" scope="row" nowrap="nowrap">
                            <div class="selectorCont" >
                               <div class="inputCont" >
                               <input  class="txtAlignRight" type="text" readonly size="2" name="team_managers" id="team_manager" value="{$smarty.request.team_managers}" />
                               </div>
                               <div class="iconCont">
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'team_manager');" class="linkUp"> </a>
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'team_manager');" class="linkDown" > </a>
                               </div>
                            </div>
                        </td>
                        <td width="90%">
                           <slot><i>{$MOD.LBL_TEAM_MANAGER_INFO}</i></slot>
                           <div id="team_manager_container">
                           {if $smarty.request.team_manager|@count gt 0}
                           {include file="custom/modules/Users/tpls/usersmall.tpl" title="team_manager" }
                           {/if}
                           </div>
                        </td>
                    </tr>

                   <tr>
                        <td width="5%" scope="row" nowrap="nowrap">
                            <div class="selectorCont" >
                               <div class="inputCont" >
                               <input class="txtAlignRight" type="text" size="2" readonly name="full_pipelines" id="full_pipeline" value="{$smarty.request.full_pipelines}" />
                               </div>
                               <div class="iconCont">
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'full_pipeline');" class="linkUp"> </a>
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'full_pipeline');" class="linkDown" > </a>
                               </div>
                            </div>
                        </td>
                        <td width="90%">
                           <slot><i>{$MOD.LBL_FULL_PIPELINE_INFO}</i></slot>
                           <div id="full_pipeline_container">
                           {if $smarty.request.full_pipeline|@count gt 0}
                           {include file="custom/modules/Users/tpls/usersmall.tpl" title="full_pipeline" }
                           {/if}
                           </div>
                        </td>
                    </tr>
                   <tr>
                        <td width="5%" scope="row" nowrap="nowrap">
                            <div class="selectorCont" >
                               <div class="inputCont" >
                               <input class="txtAlignRight" type="text" readonly size="2" name="lead_reviewers" id="lead_reviewer" value="{$smarty.request.lead_reviewers}" />
                               </div>
                               <div class="iconCont">
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'lead_reviewer');" class="linkUp"> </a>
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'lead_reviewer');" class="linkDown" > </a>
                               </div>
                            </div>
                        </td>
                        <td width="90%">
                           <slot><i>{$MOD.LBL_LEAD_REVIEWER_INFO}</i></slot>
                           <div id="lead_reviewer_container">
                           {if $smarty.request.lead_reviewer|@count gt 0}
                           {include file="custom/modules/Users/tpls/usersmall.tpl" title="lead_reviewer" }
                           {/if}
                           </div>
                        </td>
                    </tr>
                   <tr>
                        <td width="5%" scope="row" nowrap="nowrap">
                            <div class="selectorCont" >
                               <div class="inputCont" >
                               <input class="txtAlignRight" type="text" readonly size="2" name="opp_reviewers" id="opp_reviewer" value="{$smarty.request.opp_reviewers}" />
                               </div>
                               <div class="iconCont">
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'opp_reviewer');" class="linkUp"> </a>
                               <a href="javascript:void(0)" onclick="javascript:handleCount(this,'opp_reviewer');" class="linkDown" > </a>
                               </div>
                            </div>
                        </td>
                        <td width="90%">
                           <slot><i>{$MOD.LBL_OPPORTUNITY_REVIEWER_INFO}</i></slot>
                           <div id="opp_reviewer_container">
                           {if $smarty.request.opp_reviewer|@count gt 0}
                           {include file="custom/modules/Users/tpls/usersmall.tpl" title="opp_reviewer" }
                           {/if}
                           </div>
                        </td>
                    </tr>
                    
                </table>
                </div>
            </td>
        </tr>
    </table>
    <div class="nav-buttons">
        {if $SKIP_WELCOME}
        <input title="{$MOD.LBL_BACK}"
            onclick="document.location.href='index.php?module=Configurator&action=AdminWizard&page=smtp';" class="button"
            type="button" name="cancel" value="  {$MOD.LBL_WIZARD_BACK_BUTTON}  " />&nbsp;
        {else}
        <input title="{$MOD.LBL_WIZARD_SKIP}"
            class="button" type="button" name="create_user" value="  {$MOD.LBL_WIZARD_SKIP}  "
            onclick="window.location.href='index.php?module=Users&action=bbwizard&skipwizard=1'" />&nbsp;
        {/if}
        {if $CRITICAL_ERROR}{else}
        <input title="{$MOD.LBL_CREATE_USER}"
            class="button primary" type="button" name="next_tab1" value="  {$MOD.LBL_CREATE_USER}  "
            onclick="SugarWizard.changeScreen('roles_assignment',false);" />
        {/if}
    </div>
<style>
{literal}
.dashletPanelMenu.wizard, .dashletPanelMenu.wizard.yui-module.yui-overlay.yui-panel {
	box-shadow: 0 2px 10px #999999;
	-moz-box-shadow: 0 2px 10px #999999;
	-webkit-box-shadow: 0 2px 10px 
	#999999;
	border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	background-color:
	rgba(0,0,0,.2);
	border: 1px solid
	#999;
	text-shadow: 0px 1px
	#fff;
	font-size: 14px;
	clear: both;
}
.loadCont {
    float: left;
    position: fixed;
    width: 50%;
    z-index: 9999999;
}
div#content {
    padding-top: 0px;
    padding-bottom: 10px;
    margin-left: 0px;
    margin-right: 0px;
}

.dashletPanelMenu.wizard table, .dashletPanelMenu.wizard div {

    font-size: 14px;
}
.dashletPanelMenu.wizard, .dashletPanelMenu.wizard.yui-module.yui-overlay.yui-panel {

    text-shadow: 0px 1px 

    #fff;
    font-size: 14px;

}
.dashletPanelMenu .bd .screen {
    background-color: 
    transparent;
    display: none;
    width: 820px;
    margin-top: 30px;
    margin-right: 30px;
    margin-left: 0px;
   
    padding: 0px;
    border: 0px none;
}

.dashletPanelMenu.wizard table, .dashletPanelMenu.wizard div {
   font-size: 14px;

}
.dashletPanelMenu.wizard, .dashletPanelMenu.wizard.yui-module.yui-overlay.yui-panel {
    text-shadow: 0px 1px 
    #fff;
    font-size: 14px;
}


div.screen div.edit.view {

	height: auto;
	width: 850px;
	padding: 20px !important;
	font-weight: 800;
	font-size: 5em;
	// line-height: 1.35em;
	margin-bottom: 40px;
	color: #fff;
}
.edit tr td {
    font-weight: normal;
    vertical-align: top; 
}

.error:empty {
   background: inherit;
}
div.nav-buttons {
    //margin-top: 1em;
    text-align: right;
}
body {

    //font-family: Arial, Verdana, Helvetica, sans-serif;

}
input {
    -webkit-writing-mode: horizontal-tb !important;
    text-rendering: auto;
    color: -internal-light-dark-color(black, white);
    text-transform: none;
     display: inline-block;
    text-align: start;
    -webkit-appearance: textfield;
    background-color: -internal-light-dark-color(white, black);
    -webkit-rtl-ordering: logical;
    cursor: text;
    margin: 0em;
    font: 400 13.3333px Arial;
    padding: 1px 0px;
   
    border-style: inset;
    color: rgba(0,0,0);
    border-color: initial;
    border-image: initial;
}
table.edit.view {

    box-shadow: #ccc 0px 0px 10px;
    -moz-box-shadow: #ccc 0px 0px 10px;
    -webkit-box-shadow: 

    #ccc 0px 0px 10px;
    margin-top: 0;

}

{/literal}
</style>