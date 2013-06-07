jQuery(document).ready(function(jQuery) {
	// Initiate our response function list variable.
	window['ninja_forms_response_function_list'] = {};

	// Initiate our beforeSubmit function list variable.
	window['ninja_forms_before_submit_function_list'] = {};

	jQuery(".ninja-forms-form input").bind("keypress", function(e) {
		if (e.keyCode == 13) {
			var type = jQuery(this).attr("type");
			if( type != "textarea" ){
				return false;
			}
		}
	});

	/* * * Begin Mask JS * * */

	jQuery("div.label-inside input, div.label-inside textarea").focus(function(){
		var label = jQuery("#" + this.id + "_label_hidden").val();
		if( this.value == label ){
			this.value = '';
		}
	});

	jQuery("div.label-inside input, div.label-inside textarea").blur(function(){
		var label = jQuery("#" + this.id + "_label_hidden").val();
		if( this.value == '' ){
			this.value = label;
		}
	});

	if( jQuery.fn.mask ){
		jQuery(".ninja-forms-mask").each(function(){
			var mask = this.title;
			jQuery(this).mask(mask);
		});

		jQuery(".ninja-forms-date").mask('99/99/9999');
	}

	if( jQuery.fn.datepicker ){
		jQuery(".ninja-forms-datepicker").datepicker({
			dateFormat: ninja_forms_settings.date_format,
		});
	}

	if( jQuery.fn.autoNumeric ){
		jQuery(".ninja-forms-currency").autoNumeric({aSign: ninja_forms_settings.currency_symbol});
	}

	/* * * End Mask JS * * */

	/* * * Begin Help Hover JS * * */

	if( jQuery.fn.qtip ){
		jQuery(".ninja-forms-help-text").qtip({
			style: {
				classes: 'qtip-shadow qtip-dark'
			}
		});
	}

	/* * * End Help Hover JS * * */


	/* * * Begin ajaxForms JS * * */

	jQuery(".ninja-forms-form").each(function(){
		var form_id = this.id.replace("ninja_forms_form_", "");
		var settings = window['ninja_forms_form_' + form_id + '_settings'];
		ajax = settings.ajax
		if(ajax == 1){
			var options = {
			beforeSubmit:  ninja_forms_before_submit,
			success:       ninja_forms_response,
			//url: 		   'http://demo.wpninjas.com/ninja-forms/wp-admin/admin-ajax.php'
			dataType: 'json'
			};
			jQuery(this).ajaxForm(options);

			// Add our default response handler if "custom" hasn't been selected.
			ninja_forms_register_response_function( form_id, 'ninja_forms_default_response' );

			// Add our default beforeSubmit handler if "custom" hasn't been selected.
			ninja_forms_register_before_submit_function( form_id, 'ninja_forms_default_before_submit' );
		}
	});

	/* * * End ajaxForm JS * * */

	jQuery('.pass1').val('').keyup( function(){
		var pass1 = this.value;
		var pass2 = this.id.replace( "pass1", "pass2" );
		pass2 = jQuery( "#" + pass2 ).val();
		check_pass_strength( pass1, pass2 );
	});
	jQuery('.pass2').val('').keyup( function(){
		var pass2 = this.value;
		var pass1 = this.id.replace( "pass2", "pass1" );
		pass1 = jQuery( "#" + pass1 ).val();
		check_pass_strength( pass1, pass2 );
	});

}); //End document.ready

function ninja_forms_before_submit(formData, jqForm, options){
	var form_id = formData[1].value;
	var result = true;
	for( var name in window['ninja_forms_before_submit_function_list'][form_id] ){
		var function_name = window['ninja_forms_before_submit_function_list'][form_id][name];
		if( result ){
			result = window[function_name](formData, jqForm, options);
		}
	}
}

function ninja_forms_response(responseText, statusText, xhr, jQueryform){
	//alert(responseText);
	if( ninja_forms_settings.ajax_msg_format == 'inline' ){
		//var response = jQuery.parseJSON( responseText );
		var response = responseText;
		var form_id = response.form_id;
		var result = true;
		for( var name in window['ninja_forms_response_function_list'][form_id] ){
			var function_name = window['ninja_forms_response_function_list'][form_id][name];
			if( result ){
				result = window[function_name](response);
			}
		}
	}
}

function ninja_forms_default_before_submit(formData, jqForm, options){
	var form_id = formData[1].value;

	// Show the ajax spinner and processing message.
	jQuery("#ninja_forms_form_" + form_id + "_process_msg").show();
	jQuery("#ninja_forms_form_" + form_id + "_response_msg").prop("innerHTML", "");
	jQuery("#ninja_forms_form_" + form_id + "_response_msg").removeClass("ninja-forms-error-msg");
	jQuery("#ninja_forms_form_" + form_id + "_response_msg").removeClass("ninja-forms-success-msg");
	jQuery(".ninja-forms-field-error").prop("innerHTML", "");
	jQuery(".ninja-forms-error").removeClass("ninja-forms-error");
	return true;
}

function ninja_forms_default_response(response){
	var form_id = response.form_id;

	jQuery("#ninja_forms_form_" + form_id + "_process_msg").hide();

	ninja_forms_update_error_msgs(response)
	ninja_forms_update_success_msg(response)
	return true;
}

function ninja_forms_register_response_function(form_id, name){
	if( typeof window['ninja_forms_response_function_list'][form_id] == 'undefined' ){
		window['ninja_forms_response_function_list'][form_id] = {};
	}
	window['ninja_forms_response_function_list'][form_id][name] = name;
}

function ninja_forms_register_before_submit_function(form_id, name){
	if( typeof window['ninja_forms_before_submit_function_list'][form_id] == 'undefined' ){
		window['ninja_forms_before_submit_function_list'][form_id] = {};
	}
	window['ninja_forms_before_submit_function_list'][form_id][name] = name;
}

function ninja_forms_update_success_msg(response){
	var innerHTML = '';
	var form_id = response.form_id;
	var success = response.success;
	//alert(success);
	var form_settings = response.form_settings;
	var hide_complete = form_settings.hide_complete;
	var clear_complete = form_settings.clear_complete;

	if(success != false){
		for( var propName in success ){
			innerHTML += '<p>' + success[propName] + '</p>';
		}
		if(innerHTML != ''){
			jQuery("#ninja_forms_form_" + form_id + "_response_msg").removeClass("ninja-forms-error-msg")
			jQuery("#ninja_forms_form_" + form_id + "_response_msg").addClass("ninja-forms-success-msg")
			jQuery("#ninja_forms_form_" + form_id + "_response_msg").prop("innerHTML", innerHTML);
		}
		if(hide_complete == 1 ){
			jQuery("#ninja_forms_form_" + form_id ).hide();
		}
		if(clear_complete == 1 ){
			jQuery("#ninja_forms_form_" + form_id ).resetForm();
		}
	}
}

function ninja_forms_update_error_msgs(response){
	var innerHTML = '';
	var form_id = response.form_id;
	var errors = response.errors;
	var form_id = response.form_id;
	if(errors != false){
		for( var propName in errors ){
			if(errors[propName]['location'] == 'general' ){
	    		innerHTML += '<p>' + errors[propName]['msg'] + '</p>';
	    	}else{
	    		var field_id = errors[propName]['location'];
	    		jQuery("#ninja_forms_field_" + field_id + "_error").show();
	    		jQuery("#ninja_forms_field_" + field_id + "_error").prop("innerHTML", errors[propName]['msg']);
	    		jQuery("#ninja_forms_field_" + field_id + "_div_wrap").addClass("ninja-forms-error");

	    	}
		}
		if(innerHTML != ''){
			jQuery("#ninja_forms_form_" + form_id + "_response_msg").removeClass("ninja-forms-success-msg")
			jQuery("#ninja_forms_form_" + form_id + "_response_msg").addClass("ninja-forms-error-msg")
			jQuery("#ninja_forms_form_" + form_id + "_response_msg").prop("innerHTML", innerHTML);
		}
	}
}

function ninja_forms_html_decode(value) {
	if (value) {
		var decoded = jQuery('<div />').html(value).text();
		decoded = jQuery('<div />').html(decoded).text();
		return decoded;
	} else {
		return '';
	}
}

function ninja_forms_toggle_login_register(form_type, form_id) {

	var el_id = 'ninja_forms_form_' + form_id + '_' + form_type + '_form';
	if(form_type == 'login'){
		var opp_id = 'ninja_forms_form_' + form_id + '_register_form';
	}else{
		var opp_id = 'ninja_forms_form_' + form_id + '_login_form';
	}
	var ele = document.getElementById(el_id);
	var opp_ele = document.getElementById(opp_id);
	if(ele.style.display == "block") {
		ele.style.display = "none";
  	}else{
		ele.style.display = "block";
		opp_ele.style.display = "none";
	}
}

function ninja_forms_get_form_id(element){
	var form_id = jQuery(element).closest('form').prop("id");
	form_id = form_id.replace("ninja_forms_form_", "");
	if(form_id == '' || form_id == 'ninja_forms_admin'){
		form_id = jQuery("#_form_id").val();
	}
	return form_id;
}

function check_pass_strength(pass1, pass2) {

	jQuery('#pass-strength-result').removeClass('short bad good strong');
	if ( ! pass1 ) {
		jQuery('#pass-strength-result').html( ninja_forms_password_strength.empty );
		return;
	}

	strength = passwordStrength(pass1, pass2);

	switch ( strength ) {
		case 2:
			jQuery('#pass-strength-result').addClass('bad').html( ninja_forms_password_strength['bad'] );
			break;
		case 3:
			jQuery('#pass-strength-result').addClass('good').html( ninja_forms_password_strength['good'] );
			break;
		case 4:
			jQuery('#pass-strength-result').addClass('strong').html( ninja_forms_password_strength['strong'] );
			break;
		case 5:
			jQuery('#pass-strength-result').addClass('short').html( ninja_forms_password_strength['mismatch'] );
			break;
		default:
			jQuery('#pass-strength-result').addClass('short').html( ninja_forms_password_strength['short'] );
	}
}

function passwordStrength(password1, password2) {
	var shortPass = 1, badPass = 2, goodPass = 3, strongPass = 4, mismatch = 5, symbolSize = 0, natLog, score;

	// password 1 != password 2
	if ( (password1 != password2) && password2.length > 0)
		return mismatch

	//password < 4
	if ( password1.length < 4 )
		return shortPass

	//password1 == username

	if ( password1.match(/[0-9]/) )
		symbolSize +=10;
	if ( password1.match(/[a-z]/) )
		symbolSize +=26;
	if ( password1.match(/[A-Z]/) )
		symbolSize +=26;
	if ( password1.match(/[^a-zA-Z0-9]/) )
		symbolSize +=31;

	natLog = Math.log( Math.pow(symbolSize, password1.length) );
	score = natLog / Math.LN2;

	if (score < 40 )
		return badPass

	if (score < 56 )
		return goodPass

    return strongPass;
}