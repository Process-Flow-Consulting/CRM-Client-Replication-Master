$( document ).ready(function() {
	
	var formName = $('input#international').closest('form').attr('name');
	
	if($('#'+formName+' input[id=international]').prop('checked')) {
		$('#primary_address_state_free').prop('type','text');
		$('#alt_address_state_free').prop('type','text');
		$('#primary_address_state_div').hide();
		$('#alt_address_state_div').hide();
		$('#primary_address_state').prop('disabled',true);
		$('#alt_address_state').prop('disabled',true);
	}
	else{
		$('#primary_address_state_free').prop('disabled',true);
		$('#alt_address_state_free').prop('disabled',true);
	}
});


function endisState()
{
	
	var formName = $('input#international').closest('form').attr('name');
	if($('#'+formName+' input[id=international]').prop('checked')) {
		$('#primary_address_state_free').prop('type','text');
		$('#alt_address_state_free').prop('type','text');
		$('#primary_address_state_div').hide();
		$('#alt_address_state_div').hide();
		$('#primary_address_state').prop('disabled',true);
		$('#alt_address_state').prop('disabled',true);	
		$('#primary_address_state_free').removeAttr('disabled');
		$('#alt_address_state_free').removeAttr('disabled');
		if(formName =='EditView' || formName =='form_DCQuickCreate_Contacts' || formName == 'form_SubpanelQuickCreate_Contacts'){
			removeFromValidate(formName, 'phone_work');
			removeFromValidate(formName, 'phone_fax');
			removeFromValidate(formName, 'phone_mobile');
		}
	}
	else{
		$('#primary_address_state_free').prop('type','hidden');
		$('#alt_address_state_free').prop('type','hidden');
		$('#primary_address_state_free').prop('disabled',true);
		$('#alt_address_state_free').prop('disabled',true);	
		$('#primary_address_state_div').show();
		$('#alt_address_state_div').show();
		$('#primary_address_state').removeAttr('disabled');
		$('#alt_address_state').removeAttr('disabled');
		if(formName =='EditView' || formName =='form_DCQuickCreate_Contacts' || formName == 'form_SubpanelQuickCreate_Contacts'){
			addToValidate(formName, 'phone_work', 'phone', false,'Phone' );
			addToValidate(formName, 'phone_fax', 'phone', false,'Phone' );
			addToValidate(formName, 'phone_mobile', 'phone', false,'Phone' );
		}
	}
}
