<?php
/*
 * Function to register a new field for calculations
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_register_field_calc(){
	$args = array(
		'name' => 'Calculation',
		'sidebar' => 'template_fields',
		'edit_function' => 'ninja_forms_field_calc_edit',
		'display_function' => 'ninja_forms_field_calc_display',
		'group' => 'standard_fields',
		'edit_conditional' => true,
		'edit_req' => false,
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_custom_class' => false,
		'edit_help' => false,
		'process_field' => false,
		//'pre_process' => 'ninja_forms_field_calc_pre_process',
	);

	ninja_forms_register_field( '_calc', $args );
}

add_action( 'init', 'ninja_forms_register_field_calc' );

/*
 *
 * Function that filters the field LI label on the edit field back-end.
 *
 * @since 2.2.28
 * @returns $li_label
 */

function ninja_forms_calc_edit_label_filter( $li_label, $field_id ) {
	$field_row = ninja_forms_get_field_by_id( $field_id );
	if ( $field_row['type'] == '_calc' ) {
		if ( isset ( $field_row['data']['calc_name'] ) ) {
			$li_label = $field_row['data']['calc_name'];
		} else {
			$li_label = __( 'calc_name', 'ninja-forms' );
		}

	}
	return $li_label;
}

add_filter( 'ninja_forms_edit_field_li_label', 'ninja_forms_calc_edit_label_filter', 10, 2 );


/*
 * Function that outputs the edit options for our calculation field
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_field_calc_edit( $field_id, $data ){

	if ( isset ( $data['calc_name'] ) ) {
		$calc_name = $data['calc_name'];
	} else {
		$calc_name = 'calc_name';
	}

	if ( isset ( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	} else {
		$default_value = '';
	}

	if ( isset ( $data['calc_payment'] ) ) {
		$calc_payment = $data['calc_payment'];
	} else {
		$calc_payment = '';
	}

	ninja_forms_edit_field_el_output($field_id, 'text', __( 'Calculation name', 'ninja-forms' ), 'calc_name', $calc_name, 'wide', '', 'widefat ninja-forms-calc-name', __( 'This is the programmatic name of your field. Examples are: my_calc, price_total, user-total.', 'ninja-forms' ));
	ninja_forms_edit_field_el_output($field_id, 'text', __( 'Default Value', 'ninja-forms' ), 'default_value', $default_value, 'wide', '', 'widefat' );
	ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Payment Calculation', 'ninja-forms' ), 'calc_payment', $calc_payment, 'wide', '', '' );
	
	echo '<hr>';
	// Output calculation display type
	$options = array(
		array( 'name' => __( '- None', 'ninja-forms' ), 'value' => 'hidden' ),
		array( 'name' => __( 'Textbox', 'ninja-forms' ), 'value' => 'text'),
		array( 'name' => __( 'HTML', 'ninja-forms' ), 'value' => 'html'),
	);
	if ( isset ( $data['calc_display_type'] ) ) {
		$calc_display_type = $data['calc_display_type'];
	} else {
		$calc_display_type = 'hidden';
	}
	
	ninja_forms_edit_field_el_output($field_id, 'select', __( 'Output calculation as', 'ninja-forms' ), 'calc_display_type', $calc_display_type, 'wide', $options, 'widefat ninja-forms-calc-display');
	
	// If the calc_display_type is set to text, then we have several options to output.
	// Set the output to hidden for these options if the calc_display_type is not set to text.
	if ( $calc_display_type != 'text' ) {
		$class = 'hidden';
	} else {
		$class = '';
	}
	echo '<div id="ninja_forms_field_'.$field_id.'_clac_text_display" class="'.$class.'">';
	// Output a label input textbox.
	if ( isset ( $data['label'] ) ) {
		$label = stripslashes( $data['label'] );
	} else {
		$label = '';
	}
	ninja_forms_edit_field_el_output($field_id, 'text', __( 'Label', 'ninja-forms' ), 'label', $label, 'wide', '', 'widefat');

	// Output a label position select box.
	if ( isset ( $data['label_pos'] ) ) {
		$label_pos = $data['label_pos'];
	} else {
		$label_pos = '';
	}
	$options = array(
		array('name' => __( 'Left of Element', 'ninja-forms' ), 'value' => 'left'),
		array('name' => __( 'Above Element', 'ninja-forms' ), 'value' => 'above'),
		array('name' => __( 'Below Element', 'ninja-forms' ), 'value' => 'below'),
		array('name' => __( 'Right of Element', 'ninja-forms' ), 'value' => 'right'),
	);
	ninja_forms_edit_field_el_output($field_id, 'select', __( 'Label Position', 'ninja-forms' ), 'label_pos', $label_pos, 'wide', $options, 'widefat');
	
	// Output a disabled option checkbox.
	if( isset ( $data['calc_display_text_disabled'] ) ) {
		$calc_display_text_disabled = $data['calc_display_text_disabled'];
	} else {
		$calc_display_text_disabled = 1;
	}
	ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Disable input?', 'ninja-forms' ), 'calc_display_text_disabled', $calc_display_text_disabled, 'thin', '', '');
	echo '</div>';

	// Set the output to hidden for the HTML RTE if the calc_display_type is not set to HTML.
	if ( $calc_display_type != 'html' ) {
		$class = 'hidden';
	} else {
		$class = '';
	}
	// Output our RTE. This is the only extra setting needed if the calc_display_type is set to HTML.
	if ( isset ( $data['calc_display_html'] ) ) {
		$calc_display_html = $data['calc_display_html'];
	} else {
		$calc_display_html = '[ninja_forms_calc]';
	}
	echo '<div id="ninja_forms_field_'.$field_id.'_clac_html_display" class="'.$class.'">';
	ninja_forms_edit_field_el_output($field_id, 'rte', '', 'calc_display_html', $calc_display_html, '', '', '', __( 'Use the following shortcode to insert the final calculation: [ninja_forms_calc]', 'ninja-forms' ) );
	echo '</div>';

	// If any option besides "none" is selected, then show our custom class and help options.
	if ( $calc_display_type == 'hidden' ) {
		$class = 'hidden';
	} else {
		$class = '';
	}

	if ( isset ( $data['class'] ) ) {
		$custom_class = $data['class'];
	} else {
		$custom_class = '';
	}

	if ( isset ( $data['show_help'] ) ) {
		$show_help = $data['show_help'];
	} else {
		$show_help = 0;
	}

	if ( isset ( $data['help_text'] ) ) {
		$help_text = $data['help_text'];
	} else {
		$help_text = '';
	}

	if( $show_help == 1 ){
		$display_span = '';
	}else{
		$display_span = 'display:none;';
	}

	echo '<div id="ninja_forms_field_'.$field_id.'_clac_extra_display" class="'.$class.'">';
	// Output our custom class textbox.
	ninja_forms_edit_field_el_output($field_id, 'text', __( 'Custom CSS Class', 'ninja-forms' ), 'class', $custom_class, 'thin', '', '');

	// Output our help text options.
	$help_desc = sprintf(__('If "help text" is enabled, there will be a question mark %s placed next to the input field. Hovering over this question mark will show the help text.', 'ninja-forms'), '<img src="'.NINJA_FORMS_URL.'/images/question-ico.gif">');
	ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Show Help Text', 'ninja-forms' ), 'show_help', $show_help, 'wide', '', 'ninja-forms-show-help');
	?>
	<span id="ninja_forms_field_<?php echo $field_id;?>_help_span" style="<?php echo $display_span;?>">
		<?php
		ninja_forms_edit_field_el_output($field_id, 'textarea', __( 'Help Text', 'ninja-forms' ), 'help_text', $help_text, 'wide', '', 'widefat', $help_desc);
		?>
	</span>
	<?php
	echo '</div>';
	echo '<div class="description description-wide"><hr></div>';
	
	// Advanced equation output.
	if ( isset ( $data['use_calc_adv'] ) ) {
		$use_calc_adv = $data['use_calc_adv'];
	} else {
		$use_calc_adv = 0;
	}

	if ( isset ( $data['calc_adv'] ) ) {
		$calc_adv = $data['calc_adv'];
	} else {
		$calc_adv = '';
	}

	if ( $use_calc_adv == 0 ) {
		$class = 'hidden';
	} else {
		$class = '';
	}

	$desc = '<p>'.__( 'You can enter calculation equations here using field_x where x is the ID of the field you want to use. For example, <strong>field_53 + field_28 + field_65</strong>.', 'ninja-forms' ).'</p>';
	$desc .= '<p>'.__( 'Complex equations can be created by adding parentheses: <strong>( field_45 * field_2 ) / 2</strong>.', 'ninja-forms' ).'</p>';
	$desc .= '<p>'.__( 'Please use these operators: + - * /. This is an advanced feature. Watch out for things like division by 0.', 'ninja-forms' ).'</p>';

	ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Use Advanced Calculations', 'ninja-forms' ), 'use_calc_adv', $use_calc_adv, 'wide', '', 'ninja-forms-use-calc-adv');
	
	?>
	<span id="ninja_forms_field_<?php echo $field_id;?>_calc_adv_span" class="<?php echo $class;?>">
		<?php
		ninja_forms_edit_field_el_output($field_id, 'textarea', '', 'calc_adv', $calc_adv, 'wide', '', 'widefat', $desc);
		?>
	</span>
	<?php
}

/*
 * Function that outputs the display for our calculation field
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_field_calc_display( $field_id, $data ){
	
	if ( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	} else {
		$default_value = 0;
	}

	if ( $default_value == '' ) {
		$default_value = 0;
	}

	if ( isset ( $data['calc_display_text_disabled'] ) AND $data['calc_display_text_disabled'] == 1 ) {
		$disabled = "disabled";
	} else {
		$disabled = '';
	}

	if ( isset ( $data['calc_display_type'] ) ) {
		$calc_display_type = $data['calc_display_type'];
	} else {
		$calc_display_type = 'text';
	}

	if ( isset ( $data['calc_display_html'] ) ) {
		$calc_display_html = $data['calc_display_html'];
	} else {
		$calc_display_html = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id );
	?>
	<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $default_value;?>" class="<?php echo $field_class;?>">
	<?php

	switch ( $calc_display_type ) {
		case 'text':
			?>
			<input type="text" id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $default_value;?>" <?php echo $disabled;?> class="<?php echo $field_class;?>">
			<?php		
			break;
		case 'html':
			$calc_display_html = str_replace( '[ninja_forms_calc]', '<span id="ninja_forms_field_'.$field_id.'" class="'.$field_class.'">'.$default_value.'</span>', $calc_display_html );
			echo $calc_display_html;
			break;
	}
}

function ninja_forms_test(){
	global $ninja_forms_processing;
	$ninja_forms_processing->get_payment();
}

add_action( 'ninja_forms_pre_process', 'ninja_forms_test' );