function disableReturnSubmission(e) {
   var key = window.event ? window.event.keyCode : e.which;
   return (key != 13);
}
/* bind event handler for swap selected classifications*/
YUI().use('node','event',"selector-css3","io-form", function (Y) {
	Y.on('domready',function(){
		//function to enable/disable 
		Y.all('input[name^=geo_filter_for]').on('click',function(e){
					
			switch(e.target.get('value')){
				case "city":
					Y.all("#city_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
					Y.all("#city_row_filter input").each(function(elm){elm.removeAttribute('disabled')})
					
					Y.all("#zip_row_filter select").setAttribute('disabled','disabled')
					Y.all("#zip_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
					
					Y.all("#state_row_filter select").setAttribute('disabled','disabled')
					Y.all("#state_row_filter input[type=button]").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
					
					Y.all("#county_row_filter select").setAttribute('disabled','disabled')
					Y.all("#county_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
				break;	
				case"state":
					Y.all("#state_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
					Y.all("#state_row_filter input").each(function(elm){elm.removeAttribute('disabled')})
		
					Y.all("#city_row_filter select").setAttribute('disabled','disabled')
					Y.all("#city_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
			
					Y.all("#zip_row_filter select").setAttribute('disabled','disabled')
					Y.all("#zip_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
					
					Y.all("#county_row_filter select").setAttribute('disabled','disabled')
					Y.all("#county_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
					
				break;
				case "county":
					Y.all("#county_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
					Y.all("#county_row_filter input").each(function(elm){elm.removeAttribute('disabled')})
		
					Y.all("#city_row_filter select").setAttribute('disabled','disabled')
					Y.all("#city_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
											
					Y.all("#state_row_filter select").setAttribute('disabled','disabled')
					Y.all("#state_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
					
					Y.all("#zip_row_filter select").setAttribute('disabled','disabled')
					Y.all("#zip_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
						
				break;
				case "zip":
					Y.all("#zip_row_filter select").each(function(elm){elm.removeAttribute('disabled')})
					Y.all("#zip_row_filter input").each(function(elm){elm.removeAttribute('disabled')})
		
					Y.all("#city_row_filter select").setAttribute('disabled','disabled')
					Y.all("#city_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
											
					Y.all("#state_row_filter select").setAttribute('disabled','disabled')
					Y.all("#state_row_filter input[type=button]").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
					
					Y.all("#county_row_filter select").setAttribute('disabled','disabled')
					Y.all("#county_row_filter input").each(function(elm){if(elm.get('type') !='radio')elm.setAttribute('disabled','disabled')})
				break;
			}
		});
		//event handler for counties
		Y.one('#count_swap_lft').on('click',function(e){
			Y.all('#county option').each(function(elm){
				if(elm.get('selected'))
				{
					b = Y.Node.create('<option value="'+elm.get('value')+'" >'+elm.get('innerHTML')+'</option>');
					b.set('selected',false)
					if(YAHOO.util.Selector.query('#county_filters option[value="'+elm.get('value')+'"]').length == 0){
						Y.one('#county_filters').append(b)
					}
					elm.remove();
				}
			})
		});
		Y.one('#count_swap_rgt').on('click',function(e){
			Y.all("#county_filters option").each(function (elm){
				if(elm.get('selected')){
					elm.remove();
				}
			});
			first=(Y.one('#state_county option'))?Y.one('#state_county').get('value'):'';
			//load counties
			getCounty(first,'');
		});	
	});
});

/*fucntion to swap values to multiselects*/
function swapSelected(srcId,destId){
		
	YUI().use('node',"selector-css3", function (Y) {
		if(Y.one('div.validation-message'))
		{
			Y.all('div.validation-message').remove()
			
		}
		srcDom = document.getElementById(srcId);
		dstDom = document.getElementById(destId);
		
		var scNodeType = srcDom.nodeName;
		if(scNodeType == "SELECT"){
			//handle dripdowns
			Y.one("#"+srcId).get("options").each( function(){
				// this = option from the select
				var selected = this.get('selected');
				var value  = this.get('value');
				var text = this.get('text');
				if(selected){
					if(dstDom.nodeName != 'INPUT')
					{
						var found = false;
						//if it already exists then no need to add
						Y.one("#"+destId).get("options").each(function(node){
							if(node.get("value") == srcDom.value){found= true;}
						});
						if(found && destId != 'county' ){
							
								ERR_MSG = 'Already Exists.';
							
							add_error_style('UserWizard',document.getElementById(srcId),ERR_MSG,false );
							return;
						}
						if(value !=''){
							b = Y.Node.create('<option value="'+value+'" >'+text+'</option>');
							b.set("selected",false);
							Y.one("#"+destId).append(b);
						}
						
					}
					this.remove();
				}
			});

		}
		else if (scNodeType == 'INPUT'){
			//handle input type elements
			//check if already exists in the destination mutliselect
			var found = false;
			Y.one("#"+destId).get("options").each(function(node){
				if(node.get("value") == srcDom.value){found= true;}
			});
			if(!found && srcDom.value !=''){
				b = Y.Node.create('<option value="'+srcDom.value+'" >'+srcDom.value+'</option>');
				b.set("selected",false);
				Y.one("#"+destId).append(b);
				srcDom.value='';
			}else{
				if(srcDom.value =='')
				{
					ERR_MSG = 'Please enter a value.';
				}
				else{
					ERR_MSG = 'Already Exists.';
				}
				add_error_style('UserWizard',document.getElementById(srcId),ERR_MSG,false );
			}

		}
		sortSelect(srcDom);
		sortSelect(dstDom);
	});
}

function getCounty(stateAbbr,selCounty){
	try{
		var postParams ='';
		YUI().use('node',"selector-css3",function(Y){
			var varName = (Y.one('#county_filters') != null)?Y.one('#county_filters').get('name'):'';
			var allVals = Y.all('#county_filters option').get('value');
			var varValues = Y.all('#county_filters option').get('value');
			for(i=0;i<varValues.length;i++){
				postParams+='&'+varName+'='+varValues[i];
			}
		});
		var callback = {
			success:function(o){

				document.getElementById("county_div").innerHTML = o.responseText;
			}
		}
		var connectionObject = YAHOO.util.Connect.asyncRequest ("POST", "index.php?entryPoint=CountyAjaxCall&multisel=1&state_abbr="+stateAbbr+"&selected_county="+selCounty, callback,postParams);

	}catch(e){

	}
}

function manageCourties(){

	YUI().use('node',"io-base","selector-css3",function(Y){

		postParm = {};

		first=(Y.one('#state_county')!=null)?Y.one('#state_county').get('value'):'';
		//load counties
		getCounty(first,'');


		var varName = Y.one('#state_apply').get('name');
		var varValues = Y.all('#state_apply option').get('value');
		var postParams= '';
		for(i=0;i<varValues.length;i++){
			postParams+='&'+varName+'='+varValues[i];
		}
		var varName = Y.one('#county_filters').get('name');
		var varValues = Y.all('#county_filters option').get('value');
		for(i=0;i<varValues.length;i++){
			postParams+='&'+varName+'='+varValues[i];
		}

		var uri = 'index.php?module=Users&action=handle_requests&filter_counties=1&to_pdf=1';
		var callback = {
			success: function(o) {
				var response = JSON.parse(o.responseText);

				//clear all the options
				Y.all("#county_filters option").remove();
				//assign these couties
				for(var i in response){

					b = Y.Node.create('<option value="'+i+'" >'+response[i]+'</option>');
					b.set("selected",false);
					Y.one("#county_filters").append(b);

				}
			},
			failure: function(o) {Y.log('Request Failed')},

		}

		YAHOO.util.Connect.asyncRequest('POST', uri, callback,postParams);

	});
}
//call on load

function sortSelect(selElem) {
    var tmpAry = new Array();
    for (var i=0;i<selElem.options.length;i++) {
            tmpAry[i] = new Array();
            tmpAry[i][0] = selElem.options[i].text;
            tmpAry[i][1] = selElem.options[i].value;
    }
    tmpAry.sort();
    while (selElem.options.length > 0) {
        selElem.options[0] = null;
    }
    for (var i=0;i<tmpAry.length;i++) {
            var op = new Option(tmpAry[i][0], tmpAry[i][1]);
            selElem.options[i] = op;
    }
    return;
}

//check custom validation
function checkCustomVAlidation() {	
	addToValidate("UserWizard", "name", "text", true,"Name" );
	selected_value = jQuery("input[name=geo_filter_for]:checked").val();
	if(selected_value == 'city') {
		$("#city_name option").attr("selected", "selected");
		addToValidate('UserWizard', 'city_name[]', 'mutienum', true, 'City');
		removeFromValidate("UserWizard", "state_apply[]");
		removeFromValidate("UserWizard", "county_filters[]");
		removeFromValidate("UserWizard", "zip_filters[]");
	} else if(selected_value == 'state') {
		$("#state_apply option").attr("selected", "selected");
		addToValidate('UserWizard', 'state_apply[]', 'mutienum', true, 'State');
		removeFromValidate("UserWizard", "city_name[]");
		removeFromValidate("UserWizard", "county_filters[]");
		removeFromValidate("UserWizard", "zip_filters[]");
	} else if(selected_value == 'county') {
		$("#county_filters option").attr("selected", "selected");
		addToValidate('UserWizard', 'county_filters[]', 'mutienum', true, 'County');
		removeFromValidate("UserWizard", "city_name[]");
		removeFromValidate("UserWizard", "state_apply[]");
		removeFromValidate("UserWizard", "zip_filters[]");
	} else if(selected_value == 'zip') {
		$("#zip_filters option").attr("selected", "selected");
		addToValidate('UserWizard', 'zip_filters[]', 'mutienum', true, 'Zip Code');
		removeFromValidate("UserWizard", "city_name[]");
		removeFromValidate("UserWizard", "county_filters[]");
		removeFromValidate("UserWizard", "state_apply[]");
	}
	if(check_form('UserWizard')){
		checkZoneName();
		if(trim(document.getElementById("zone_msg").innerHTML).length > 0) {
			return false;
		} else {
			$("#UserWizard").submit();
		}
	} 
	return false;
}

/**
 * check zone name exists or not ajax request
 * @author Mohit Kumar Gupta
 * @date 12 Oct 2013
*/
function checkZoneName() {
	var zoneName = document.getElementById("name").value;
	$.ajax({
        type: "POST",
        url: "index.php?module=oss_Zone&action=check_zone_name&to_pdf=true",
        data: {zoneName: zoneName,recordId: recordId},
        dataType: "json",
       	cache: false,
       	async:false,
        success: function (json) {		        	
            var response = json.data;
            if(trim(response) != ""){    
            	document.getElementById("zone_msg").innerHTML = "Zone Name Already Exists.";
    			document.getElementById("zone_msg").style.display = "inline";
    			document.getElementById("zone_msg").classList.add("required");
            	return false;
            } else {
            	document.getElementById("zone_msg").style.display = "none";
            	document.getElementById("zone_msg").classList.remove("required");
            	document.getElementById("zone_msg").innerHTML = "";
            	return true;
            }	             
        },
        
    });	
}