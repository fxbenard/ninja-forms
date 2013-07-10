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
		'edit_options' => array(
			/*
			array(
				'type' => 'text',
				'name' => 'calc_name',
				'label' => __( 'Calculation Name', 'ninja-forms' ),
				'class' => 'widefat ninja-forms-calc-name',
				'desc' => __( 'This is the programmatic name of your field. Examples are: my_calc, price_total, user-total.', 'ninja-forms' ),
				'default' => 'calc_name',
			),			
			array(
				'type' => 'select',
				'name' => 'calc_display_type',
				'label' => __( 'Output calculation as', 'ninja-forms' ),
				'options' => array(
					array( 'name' => __( '- None', 'ninja-forms' ), 'value' => 'hidden' ),
					array( 'name' => __( 'Textbox', 'ninja-forms' ), 'value' => 'text'),
					array( 'name' => __( 'HTML', 'ninja-forms' ), 'value' => 'html'),
				),
				'class' => 'widefat',
			),
			array(
				'type' => 'text',
				'name' => 'label',
				'label' => __( 'Label', 'ninja-forms' ),
				'class' => 'widefat calc-text',
			),
			array(
				'type' => 'select',
				'name' => 'label_pos',
				'label' => __( 'Label Position', 'ninja-forms' ),
				'options' => array(
					array('name' => __( 'Left of Element', 'ninja-forms' ), 'value' => 'left'),
					array('name' => __( 'Above Element', 'ninja-forms' ), 'value' => 'above'),
					array('name' => __( 'Below Element', 'ninja-forms' ), 'value' => 'below'),
					array('name' => __( 'Right of Element', 'ninja-forms' ), 'value' => 'right'),
				),
				'class' => 'widefat calc-text',
			),
			*/
		),
		'display_function' => 'ninja_forms_field_calc_display',
		'group' => 'standard_fields',
		'edit_conditional' => true,
		'edit_req' => false,
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_custom_class' => false,
		'edit_help' => false,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		//'pre_process' => 'ninja_forms_field_text_pre_process',
	);

	ninja_forms_register_field( '_calc', $args );
}

add_action( 'init', 'ninja_forms_register_field_calc' );

/*
 * Function that outpus the edit options for our calculation field
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
	ninja_forms_edit_field_el_output($field_id, 'text', __( 'Calculation name', 'ninja-forms' ), 'calc_name', $calc_name, 'wide', '', 'widefat ninja-forms-calc-name', __( 'This is the programmatic name of your field. Examples are: my_calc, price_total, user-total.', 'ninja-forms' ));

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
	echo '<div id="ninja_forms_field_'.$field_id.'_clac_html_display" class="'.$class.'">';
	ninja_forms_edit_field_el_output($field_id, 'rte', '', 'calc_display_html', '[ninja_forms_calc]', '', '', $class, __( 'Use the following shortcode to insert the final calculation: [ninja_forms_calc]', 'ninja-forms' ) );
	echo '</div>';

	// If any option besides "none" is selected, then show our custom class and help options.
	if ( $calc_display_type == 'hidden' ) {
		$class = 'hidden';
	} else {
		$class = '';
	}

	if ( isset ( $data['calc_use_js'] ) ) {
		$calc_use_js = $data['calc_use_js'];
	} else {
		$calc_use_js = 1;
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
	// Output a checkbox for our "Use JS" option.
	ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Use Javascript calculation', 'ninja-forms' ), 'calc_use_js', $calc_use_js, 'thin', '', '');
	
	// Output our custom class textbox.
	ninja_forms_edit_field_el_output($field_id, 'text', __( 'Custom CSS Class', 'ninja-forms' ), 'class', $custom_class, 'thin', '', '');

	// Output our help text options.
	$help_desc = sprintf(__('If "help text" is enabled, there will be a question mark %s placed next to the input field. Hovering over this question mark will show the help text.', 'ninja-forms'), '<img src="'.NINJA_FORMS_URL.'/images/question-ico.gif">');
	ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Show Help Text', 'ninja-forms' ), 'show_help', $show_help, 'wide', '', 'ninja-forms-show-help');
	?>
	<span id="ninja_forms_field_<?php echo $field_id;?>_help_span" style="<?php echo $display_span;?>">
		<?php
		ninja_forms_edit_field_el_output($field_id, 'textarea', 'Help Text', 'help_text', $help_text, 'wide', '', 'widefat');
		ninja_forms_edit_field_el_output($field_id, 'desc', $help_desc, 'help_desc');
		?>
	</span>
	<?php
	echo '</div>';
}

/*
 * Function that outputs the display for our calculation field
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_field_calc_display( $data, $field_id ){
	if ( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	} else {
		$default_value = '';
	}
	?>
	<input type="text" name="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $default_value;?>">
	<?php
}