jQuery(document).ready(function(jQuery) {
	// Initiate our response function list variable.
	window['ninja_forms_response_function_list'] = {};

	// Initiate our beforeSubmit function list variable.
	window['ninja_forms_before_submit_function_list'] = {};

	// Prevent the enter key from submitting the form.
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

	/* 
	 * Password Field JS
	 */

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

	/*
	 * Calculation Field JS
	 */

	// Listen to the input elements with our calculation class for focus.
	jQuery(".ninja-forms-field-calc-listen").live("focus", function(e){
		jQuery(this).data( "oldValue", jQuery(this).val() );			
	});

	// Listen to the input elements with our calculation class for focus.
	jQuery(".ninja-forms-field-calc-listen").live("mousedown", function(e){
		jQuery(this).data( "oldValue", jQuery(this).val() );			
	});	

	// Listen to the input elements with our calculation class for focus.
	jQuery(".ninja-forms-field-calc-listen").live("keydown", function(e){
		if( this.type == 'select-multiple' ) {
			jQuery(this).data( "oldValue", jQuery(this).val() );
		}
	});

	jQuery(".ninja-forms-field-list-options-span-calc-listen").live("focus", function(e){
		var field_id = jQuery(this).attr("rel");
		jQuery(this).data("oldValue", jQuery("input[name='ninja_forms_field_" + field_id +"']:checked").val());
	});

	jQuery(".ninja-forms-field-list-options-span-calc-listen").live("mousedown", function(e){
		var field_id = jQuery(this).attr("rel");
		jQuery(this).data("oldValue", jQuery("input[name='ninja_forms_field_" + field_id +"']:checked").val());
	});
	

	// Listen to the input elements with our calculation class for a change in their value/state.
	jQuery(".ninja-forms-field-calc-listen").live("change", function(e){
		var form_id = ninja_forms_get_form_id( this );
		var field_id = jQuery(this).attr("rel");
		var settings = window['ninja_forms_form_' + form_id + '_settings'];
		var calc_settings = settings.calc[field_id];
		for (var i = calc_settings.length - 1; i >= 0; i--) {
			var calc = calc_settings[i].calc;
			var op = calc_settings[i].operator;
			if(typeof calc_settings[i].when !== 'undefined' ){
				var when = calc_settings[i].when;
			}else{
				var when = '';
			}

			if ( typeof calc_settings[i]['value'] === 'object' ) {
				var new_value = calc_settings[i]['value'][this.value];
				var prev_value = calc_settings[i]['value'][jQuery(this).data('oldValue')];
			} else {
				var new_value = this.value;
				var prev_value = jQuery(this).data('oldValue');
			}

			// Check the type, if we're working with a checkbox, check the state for our "When" statement.
			if(this.type == 'checkbox'){
				if( when != '' ) { // We are working with a regular checkbox element.
					if(when == 'checked'){
						new_value = calc_settings[i].value;
						if(!this.checked){
							op = ninja_forms_find_opposite_op(op);
						}
					}else if(when == 'unchecked'){
						new_value = calc_settings[i].value;
						if(this.checked){
							op = ninja_forms_find_opposite_op(op);
						}
					}else{
						new_value = 0;
					}
				} else { // We are working with a list field set to checkbox output.
					var span = jQuery(this).parent().parent().parent().parent();
					prev_value = jQuery(span).data('oldValue'); 
					new_value = this.value;
					if( !this.checked ){
						op = ninja_forms_find_opposite_op(op);
					}
				}
			}else if( this.type == 'radio' ) {
				var span = jQuery(this).parent().parent().parent().parent();
				prev_value = calc_settings[i]['value'][jQuery(span).data('oldValue')];
			} else if ( this.type == 'select-multiple' ) {
				var tmp = 0;
				if( prev_value ){
					for (var i = prev_value.length - 1; i >= 0; i--) {
						tmp = tmp + parseFloat( prev_value[i] );
					};
					prev_value = tmp;
				}else{
					prev_value = 0;
				}
								
				var new_value = jQuery(this).val();
				tmp = 0;
				if( new_value ) {
					for (var i = new_value.length - 1; i >= 0; i--) {
						tmp = tmp + parseFloat( new_value[i] );
					}
					new_value = tmp;
				}else{
					new_value = 0;
				}
			}

			if( prev_value == ''){
				prev_value = 0;
			}
			if(!isNaN(prev_value)){
				prev_value = parseFloat(prev_value);
			}else{
				prev_value = 0;
			}

			if(new_value == ''){
				new_value = 0;
			}
			if(!isNaN(new_value)){
				new_value = parseFloat(new_value);
			}else{
				new_value = 0;
			}
			
			if(jQuery("#ninja_forms_field_" + calc).attr("type") == 'text' ){
				var current_value = jQuery("#ninja_forms_field_" + calc).val();
			}else{
				var current_value = jQuery("#ninja_forms_field_" + calc).html();
			}

			if ( current_value == '' ) {
				current_value = 0;
			}

			if(!isNaN(current_value)){
				current_value = parseFloat(current_value);
			}			

			if(prev_value != 0 && !isNaN(prev_value)){
				var new_op = ninja_forms_find_opposite_op(op);
				var tmp = new ninja_forms_var_operator(new_op);
				var tmp_value = tmp.evaluate(current_value,prev_value);
				current_value = tmp_value;
			}
			
			if(new_value != 0){
				tmp = new ninja_forms_var_operator(op);
				tmp_value = tmp.evaluate(current_value,new_value);
			}else{
				tmp_value = current_value;
			}
			//tmp_value = Math.ceil(tmp_value * 100)/100;
			if(jQuery("#ninja_forms_field_" + calc).attr("type") == 'text' ){
				jQuery("#ninja_forms_field_" + calc).val(tmp_value);
			}else{
				jQuery("#ninja_forms_field_" + calc).html(tmp_value);
			}
			jQuery("#ninja_forms_field_" + calc).trigger('change');
		};
	});

	// Listen to the field referenced in our calcluation fields' advanced equations.
	jQuery(".ninja-forms-field-calc-adv-listen").live("change", function(e){
		var form_id = ninja_forms_get_form_id( this );
		var field_id = jQuery(this).attr("rel");
		var settings = window['ninja_forms_form_' + form_id + '_settings'];
		var calc_adv = settings.calc_adv;
		for ( calc_id in calc_adv ) {

			var tmp_eq = calc_adv[calc_id].eq;
			
			for (var i = calc_adv[calc_id].fields.length - 1; i >= 0; i--) {
			
				var f_id = calc_adv[calc_id].fields[i];

				// There are two possibilities for the calculation field output: input or span. If this calc is a span, then the value will be in the HTML. If it's n input, it'll be in the value.
				if ( jQuery("#ninja_forms_field_" + f_id).get(0).tagName == 'SPAN' ) {
					var f_value = jQuery("#ninja_forms_field_" + f_id).html();
				} else{
					var f_value = jQuery("#ninja_forms_field_" + f_id).val();
				}

				if ( jQuery("#ninja_forms_field_" + f_id + "_type").val() == 'list' ) {
					if ( jQuery("#ninja_forms_field_" + f_id + "_list_type").val() == 'multi' ) {
						tmp = 0;
						if( f_value ) {
							for (var i = f_value.length - 1; i >= 0; i--) {
								tmp = tmp + parseFloat( f_value[i] );
							}
							f_value = tmp;
						}else{
							f_value = 0;
						}
					} else if ( jQuery("#ninja_forms_field_" + f_id + "_list_type").val() == 'radio' ) {
						f_value = jQuery("[name='ninja_forms_field_" + f_id + "']:checked").val();
					} else if ( jQuery("#ninja_forms_field_" + f_id + "_list_type").val() == 'checkbox' ) {
						tmp = 0;
						jQuery(".ninja_forms_field_" + f_id + ":checked").each(function(){
							tmp = tmp + parseFloat(this.value);
						});
						f_value = tmp;
					}
				}

				if ( f_value == '' || typeof f_value == 'undefined' || !f_value ) {
					f_value = 0;
				}
				var find = 'field_' + f_id;
				var re = new RegExp(find, 'g');
				tmp_eq = tmp_eq.replace(re, f_value);
				
			}

			var new_value = eval(tmp_eq);

			if ( isNaN(new_value) || !isFinite(new_value) ){
				new_value = 0;
			}

			if( jQuery("#ninja_forms_field_" + calc_id).attr("type") == 'text' ){
				jQuery("#ninja_forms_field_" + calc_id).val(new_value);
			}else{
				jQuery("#ninja_forms_field_" + calc_id).html(new_value);
			}
			jQuery("#ninja_forms_field_" + calc_id).trigger('change');
			
		}
		
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

function ninja_forms_find_opposite_op(op) {
	switch(op){
		case "add":
            return "subtract";
        case "subtract":
            return "add";
        case "multiply":
            return "divide";
        case "divide":
            return "multiply";
	}

}

function ninja_forms_var_operator(op) {
    this.operation = op;

    this.evaluate = function evaluate(param1, param2) {
    	switch(this.operation) {
            case "add":
                return param1 + param2;
            case "subtract":
                return param1 - param2;
            case "multiply":
                return param1 * param2;
            case "divide":
                return param1 / param2;
        }
    }
}