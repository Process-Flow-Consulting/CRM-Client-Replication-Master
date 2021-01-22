$( document ).ready(function() {

	if($('#international').prop('checked')) {
		$('#county_name').prop('type','text');
		$('#state_free').prop('type','text');
		$('#county_div').hide();
		$('#state_div').hide();
		$('#state_id').prop('disabled',true);
		$('#county').prop('disabled',true);
	}
});


function endisStateCounty()
{
	var formName = $('input#international').closest('form').attr('name');
	if($('#international').prop('checked')) {
		$('#county_name').prop('type','text');
		$('#state_free').prop('type','text');
		$('#county_div').hide();
		$('#state_div').hide();
		$('#state_id').prop('disabled',true);
		$('#county').prop('disabled',true);
		if(formName =='EditView' || formName =='form_DCQuickCreate_Accounts'){
			removeFromValidate(formName, 'phone_office');
			removeFromValidate(formName, 'phone_fax');
		}
	}
	else{
		$('#county_name').prop('type','hidden');
		$('#state_free').prop('type','hidden');
		$('#county_div').show();
		$('#state_div').show();
		$("#state_id").removeAttr('disabled');
		$('#county').removeAttr('disabled');
		if(formName =='EditView' || formName =='form_DCQuickCreate_Accounts'){
			addToValidate(formName, 'phone_office', 'phone', false,'Phone' );
			addToValidate(formName, 'phone_fax', 'phone', false,'Fax' );
		}		
	}
}