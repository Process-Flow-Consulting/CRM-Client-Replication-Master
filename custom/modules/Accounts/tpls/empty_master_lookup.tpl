<style type="text/css">
{literal}
.search td span.required, 
.search td span.error{
	color: #ff0000;
}
.yui-ac-content{
   width: auto;
 }
{/literal}
</style>

<div class="moduleTitle">
    <h2>{$REPORT_NAME}</h2>
    <span class="utils"></span>
    <div class="clear"></div>
</div>
<div class="clear"></div>

<div id="listViewBody">
<form action="index.php?{$smarty.server.QUERY_STRING}" method="get" class="search_form" id="search_form" name="search_form" onSubmit="return validate_search_form('search_form');">

    <input type="hidden" name="module" value="Accounts"/>
    <input type="hidden" name="action" value="master_lookup"/>
    <input type="hidden" name="to_pdf" value="false"/>

    <div class="edit view search advanced" id="oss_reports_calls_basic_searchSearchForm">
        <table cellspacing="0" cellpadding="0" border="0" width="100%">
            <tbody>
                <tr>
                	<td width="50%">
                		<div style="background-color:#D3D3D3;" id="div1" {$DIV1}>
                		<table cellspacing="0" cellpadding="0" border="0" width="100%">
                		<tr>
		                	<td width="1%">
		                		<input type="radio" name="search_option" id="search_option[1]" value="1" {$OPTION1} onClick="changeDiv('1');">
		                	</td>
		                    <td width="15%" nowrap="nowrap" scope="row" align="right">
		                       Company Name:<span class="required">*</span>
		                    </td>
		                    <td width="34%" nowrap="nowrap" align="left">
		                       <input type="text" name="company" id="company" value="{$COMPANY}">
		                    </td>
		                </tr>
		                <tr>
		                	<td width="1%"></td>
		                    <td width="15%" nowrap="nowrap" scope="row" align="right">
		                       Region:<span class="required">*</span>
		                    </td>
		                    <td width="34%" nowrap="nowrap" align="left">
		                       <select name="region" id="region">
		                       {$REGION}
		                       </select>
		                    </td>
						</tr>
						</table>
						</div>
					</td>
					<td width="50%">
					<div style="background-color:#D3D3D3;" id="div2" {$DIV2}>
					<table cellspacing="0" cellpadding="0" border="0" width="70%">
                		<tr>            
			                <td width="1%">
			                	<input type="radio" name="search_option" id="search_option[2]" value="2" {$OPTION2} onClick="changeDiv('2');">
			                </td>
			                <td width="15%" nowrap="nowrap" scope="row" align="right">
			                     Phone / Fax:<span class="required">*</span>
			                </td>
			                <td width="34%" nowrap="nowrap" align="left">
			                    <input type="text" name="phone" id="phone" value="{$PHONE}">
			                </td>
			            </tr>
					</table>
					</div>
					</td>               
	             </tr>
	             <tr height="30">
                 	<td width="50%"></td>
                 	<td width="40%" rowspan="2">
                    	<div style="border:1px solid #A8A7A7; padding:10px; color:#444444; width: 95%">
                    	The "Search Blue Book" feature allows you to search, select and
                    	save companies from The Blue Book Network Database to your
                    	own private Database. Once the new client has been imported you
                    	can link that client to any existing opportunities, meetings, etc.
                    	directly from the client's record.You can search by company name
      					or classification within a state or search the entire Blue Book
      					databases by phone / fax</div>
                    </td>
                 </tr>
                 <tr>
                	<td width="50%">
                		<div style="background-color:#D3D3D3;" id="div3" {$DIV3} >
                		<table cellspacing="0" cellpadding="0" border="0" width="100%">
                		<tr>
		                 	<td width="1%">
		                		<input type="radio" name="search_option" id="search_option[3]" value="3" {$OPTION3} onClick="changeDiv('3');">
		                	</td>
		                 	<td width="17%" nowrap="nowrap" scope="row" align="right">
		                       Classification:<span class="required">*</span>
		                    </td>
		                    <td width="34%" nowrap="nowrap" align="left">
		                    	<div id="classification_AutoComplete" class="yui-skin-sam">
			                       	<input type="text" name="classification" id="classification" value="{$CLASSIFICATION}" class="sqsEnabled yui-ac-input">
			                       	<input type="hidden" name="cat_class" id="cat_class" value="{$CAT_CLASS}">
		                       	</div>                       
		                    </td>
		                 </tr>
		                 <tr>
		                 	<td width="1%"></td>
		                    <td width="15%" nowrap="nowrap" scope="row" align="right">
		                       Region:<span class="required">*</span>
		                    </td>
		                    <td width="34%" nowrap="nowrap" align="left">
		                       <select name="region1" id="region1">
		                       {$REGION1}
		                       </select>
		                    </td>    
		                 </tr>
		                 </table>
		                 </div>
                  	</td>
                    
                 </tr>
                <tr>
                    <td width="50%" nowrap="nowrap" scope="row" class="sumbitButtons" colspan="3">
                        <input type="submit" id="search_form_submit" value="Search" name="button" class="button" accesskey="Q" title="Search [Alt+Q]" tabindex="2">&nbsp;
                        <input type="button" value="Clear" id="search_form_clear" name="clear" class="button" onclick="SUGAR.searchForm.clear_form(this.form); disableAllDiv(); changeDiv(); return false; MassUpdate.company.value='';MassUpdate.state.value='';MassUpdate.classification.value='';" accesskey="C" title="Clear [Alt+C]" tabindex="2">
                    </td>
                    <td width="50%" nowrap="nowrap"  colspan="3">
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    
</form>

</div>

{literal}
<script type="text/javascript">

function disableAllDiv(){
	for (var i=1; i < 4; i++){
		disableDivElement(i);
	}	
}

disableAllDiv();

function disableDivElement(i){

	var el = document.getElementById('div'+i);
	var elInput = el.getElementsByTagName("input");
	var elSelect = el.getElementsByTagName("select");

	for(var k=0;k<elInput.length;k++){
		if(elInput[k].type!='radio'){
			elInput[k].disabled=true;
		}
	}
	for(var k=0;k<elSelect.length;k++){
		elSelect[k].disabled=true;
	 }
}


function enableDisableDivElement(d){

	for (var i=1; i < 4; i++){
		disableDivElement(i);
	}

	var el = document.getElementById('div'+d);
	var elInput = el.getElementsByTagName("input");
	var elSelect = el.getElementsByTagName("select");
	var elDiv = el.getElementsByTagName("div");

	for(var k=0;k<elInput.length;k++){
		elInput[k].disabled=false;
	}
	for(var k=0;k<elSelect.length;k++){
		elSelect[k].disabled=false;
	}
	for(var k=0;k<elDiv.length;k++){
		elDiv[k].disabled=false;
	}
}

var search_option = '{$smarty.request.search_option}';

if(search_option!="" || search_option!='{$smarty.request.search_option}'){
	changeDiv();
}

function changeDiv(divID){
	
	var searchOption = document.forms['search_form'].elements['search_option'];	
	var searchOptionLength = searchOption.length;
	var searchOptionValue = '';
	
	for(var i = 0; i < searchOptionLength; i++) {
		if(searchOption[i].checked) {
			var searchOptionValue = searchOption[i].value;
		}
	}
	
	document.getElementById('div1').style.backgroundColor = "#D3D3D3";
	document.getElementById('div2').style.backgroundColor = "#D3D3D3";
	document.getElementById('div3').style.backgroundColor = "#D3D3D3";

	if(searchOptionValue!=""){
		document.getElementById('div'+searchOptionValue).style.backgroundColor = "";
		enableDisableDivElement(searchOptionValue);
	}
}

function validate_search_form(myForm){

	var searchOption = document.forms['search_form'].elements['search_option'];	
	var searchOptionLength = searchOption.length;
		
	for(var i = 0; i < searchOptionLength; i++) {
		if(searchOption[i].checked) {
			var searchOptionValue = searchOption[i].value;
		}
	}
	if(searchOptionValue == undefined){
		alert("Please select one search option.");
		return false;
	}
	if(searchOptionValue==1){
		addForm('search_form');
		addToValidate('search_form', 'company', 'varchar', true,'Company' );
		addToValidate('search_form', 'region', 'varchar', true,'Region' );
	}else if(searchOptionValue == 3){
		addForm('search_form');
		addToValidate('search_form', 'classification', 'varchar', true,'Classification' );
		addToValidate('search_form', 'region1', 'varchar', true,'Region' );
	}else{
		addForm('search_form');
		addToValidate('search_form', 'phone', 'phone', true,'Phone / Fax' );
	}
	return check_form('search_form');
}


YAHOO.util.Event.onDOMReady(function(){
    
    var container = document.createElement('div');
    container.innerHTML = '';  
    container.id = 'classificationContainer';

    YAHOO.util.Dom.insertAfter(container ,YAHOO.util.Dom.get('classification'));

    YAHOO.example.classification = function() {
    
var oConfigs = {
        prehighlightClassName: 'yui-ac-prehighlight',
        queryDelay: 0,
        minQueryLength: 0,
        animVert: .01,
        useIFrame: true
    }
    
    // instantiate remote data source
    var oDS = new YAHOO.util.XHRDataSource('index.php?'); 
    oDS.responseType = YAHOO.util.XHRDataSource.TYPE_HTMLTABLE; 
    oDS.responseSchema = { 
       fields: ['name']            
    };
    
    oDS.maxCacheEntries = 10;         

    var oAC = new YAHOO.widget.AutoComplete('classification', 'classificationContainer', oDS, oConfigs);
    
    oAC.useShadow = true;
    
    oAC.generateRequest = function(sQuery) { 
        return 'action=autocomplete&module=Leads&to_pdf=true&classification='+sQuery;
    }; 
        
    return {
        oDS: oDS,
        oAC: oAC,
    };
   }();
 });
</script>
{/literal}
