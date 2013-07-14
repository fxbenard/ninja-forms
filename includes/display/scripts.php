<?php
add_action( 'ninja_forms_display_js', 'ninja_forms_display_js', 10, 2 );
function ninja_forms_display_js($form_id, $local_vars = ''){
	global $post, $ninja_forms_display_localize_js;

	// Get all of our form fields to see if we need to include the datepicker and/or jqueryUI
	$datepicker = 0;
	$qtip = 0;
	$mask = 0;
	$currency = 0;
	$rating = 0;
	$calc = array();
	$calc_adv = array();
	$fields = ninja_forms_get_fields_by_form_id( $form_id );
	if( is_array( $fields ) AND !empty( $fields ) ){
		foreach( $fields as $field ){
			$field_id = $field['id'];
			$field_type = $field['type'];
			if( isset( $field['data']['datepicker'] ) AND $field['data']['datepicker'] == 1 ){
				$datepicker = 1;
			}

			if( isset( $field['data']['show_help'] ) AND $field['data']['show_help'] == 1 ){
				$qtip = 1;
			}

			if( isset( $field['data']['mask'] ) AND $field['data']['mask'] != '' ){
				$mask = 1;
			}

			if( isset( $field['data']['mask'] ) AND $field['data']['mask'] == 'currency' ){
				$currency = 1;
			}

			if( $field_type == '_rating' ){
				$rating = 1;
			}

			if ( isset ( $field['data']['calc'] ) AND !empty ( $field['data']['calc'] ) ) {
				if ( $field_type == '_list' ) {
					// Get a list of options and their 'calc' setting.
					if ( isset ( $field['data']['list']['options'] ) ) {
						$list_options = $field['data']['list']['options'];
						foreach ( $list_options as $option ) {
							for ($x=0; $x < count( $field['data']['calc'] ); $x++) { 
								$field['data']['calc'][$x]['value'][$option['value']] = $option['calc'];
							}
						}
					}
				}
				$calc[$field_id] = $field['data']['calc'];
			}

			if ( $field_type == '_calc' AND $field['data']['use_calc_adv'] == 1 AND $field['data']['calc_adv'] != '' ) {
				$calc_adv[$field_id]['eq'] = $field['data']['calc_adv'];
				foreach ( $fields as $f ) {
					$f_id = $f['id'];
					if (preg_match("/\bfield_".$f_id."\b/i", $field['data']['calc_adv'] ) ) {
						$calc_adv[$field_id]['fields'][] = $f_id;
					}
				}
			}
		}
	}


	if( $datepicker == 1 ){
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}

	if( $qtip == 1 ){
		wp_enqueue_script( 'jquery-qtip',
			NINJA_FORMS_URL .'/js/min/jquery.qtip.min.js',
			array( 'jquery', 'jquery-ui-position' ) );
	}

	if( $mask == 1 ){
		wp_enqueue_script( 'jquery-maskedinput',
			NINJA_FORMS_URL .'/js/min/jquery.maskedinput.min.js',
			array( 'jquery' ) );
	}

	if( $currency == 1 ){
		wp_enqueue_script('jquery-autonumeric',
			NINJA_FORMS_URL .'/js/min/autoNumeric.min.js',
			array( 'jquery' ) );
	}

	if( $rating == 1 ){
		wp_enqueue_script('jquery-rating',
			NINJA_FORMS_URL .'/js/min/jquery.rating.min.js',
			array( 'jquery' ) );
	}

	$form_row = ninja_forms_get_form_by_id($form_id);
	if( isset( $form_row['data']['ajax'] ) ){
		$ajax = $form_row['data']['ajax'];
	}else{
		$ajax = 0;
	}

	if( isset( $form_row['data']['hide_complete'] ) ){
		$hide_complete = $form_row['data']['hide_complete'];
	}else{
		$hide_complete = 0;
	}

	if( isset( $form_row['data']['clear_complete'] ) ){
		$clear_complete = $form_row['data']['clear_complete'];
	}else{
		$clear_complete = 0;
	}

	$ninja_forms_js_form_settings['ajax'] = $ajax;
	$ninja_forms_js_form_settings['hide_complete'] = $hide_complete;
	$ninja_forms_js_form_settings['clear_complete'] = $clear_complete;
	$ninja_forms_js_form_settings['calc'] = '';
	$ninja_forms_js_form_settings['calc_adv'] = '';

	if ( !empty ( $calc ) ) {
		$ninja_forms_js_form_settings['calc'] = $calc;
	}

	if ( !empty ( $calc_adv ) ) {
		$ninja_forms_js_form_settings['calc_adv'] = $calc_adv;
	}

	$plugin_settings = get_option("ninja_forms_settings");
	if(isset($plugin_settings['date_format'])){
		$date_format = $plugin_settings['date_format'];
	}else{
		$date_format = 'm/d/Y';
	}

	$date_format = ninja_forms_date_to_datepicker($date_format);
	$currency_symbol = $plugin_settings['currency_symbol'];

	$password_mismatch = esc_html(stripslashes($plugin_settings['password_mismatch']));
	$msg_format = $plugin_settings['msg_format'];

	wp_enqueue_script( 'ninja-forms-display',
		NINJA_FORMS_URL .'/js/dev/ninja-forms-display.js',
		array( 'jquery', 'jquery-form' ) );

	if( !isset( $ninja_forms_display_localize_js ) OR !$ninja_forms_display_localize_js ){
		wp_localize_script( 'ninja-forms-display', 'ninja_forms_settings', array('ajax_msg_format' => $msg_format, 'password_mismatch' => $password_mismatch, 'plugin_url' => NINJA_FORMS_URL, 'date_format' => $date_format, 'currency_symbol' => $currency_symbol ) );
		$ninja_forms_display_localize_js = true;
	}

	wp_localize_script( 'ninja-forms-display', 'ninja_forms_form_'.$form_id.'_settings', $ninja_forms_js_form_settings );

	wp_localize_script( 'ninja-forms-display', 'ninja_forms_password_strength', array(
		'empty' => __('Strength indicator'),
		'short' => __('Very weak'),
		'bad' => __('Weak'),
		/* translators: password strength */
		'good' => _x('Medium', 'password strength'),
		'strong' => __('Strong'),
		'mismatch' => __('Mismatch')
		) );

}

add_action( 'ninja_forms_display_css', 'ninja_forms_display_css', 10, 2 );
function ninja_forms_display_css(){
	wp_enqueue_style( 'ninja-forms-display', NINJA_FORMS_URL .'/css/ninja-forms-display.css' );
	wp_enqueue_style( 'jquery-qtip', NINJA_FORMS_URL .'/css/qtip.css' );
	wp_enqueue_style( 'jquery-rating', NINJA_FORMS_URL .'/css/jquery.rating.css' );
}