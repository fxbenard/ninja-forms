<?php
add_action('init', 'ninja_forms_register_field_desc');

function ninja_forms_register_field_desc(){
	$args = array(
		'name' => 'Text',
		'sidebar' => 'layout_fields',
		'edit_function' => '',
		'edit_options' => array(
			array(
				'type' => 'rte',
				'name' => 'default_value',
				'label' => __('Default Value', 'ninja-forms'),
				'width' => 'wide',
				'class' => 'widefat',
			),
			array(
				'type' => 'select',
				'name' => 'desc_el',
				'label' => __('Text Element', 'ninja-forms'),
				'width' => 'thin',
				'class' => '',
				'options' => array(
					array('name' => 'p', 'value' => 'p'),
					array('name' => 'div', 'value' => 'div'),
					array('name' => 'span', 'value' => 'span'),
					array('name' => 'h1', 'value' => 'h1'),
					array('name' => 'h2', 'value' => 'h2'),
					array('name' => 'h3', 'value' => 'h3'),
					array('name' => 'h4', 'value' => 'h4'),
					array('name' => 'h5', 'value' => 'h5'),
					array('name' => 'h6', 'value' => 'h6'),
				),
			),
		),
		'display_function' => 'ninja_forms_field_desc_display',
		'group' => 'layout_elements',
		'display_label' => false,
		'display_wrap' => false,
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_conditional' => true,
		'process_field' => false,
	);

	ninja_forms_register_field('_desc', $args);
}

function ninja_forms_field_desc_display( $field_id, $data ){

	if(isset($data['desc_el'])){
		$desc_el = $data['desc_el'];
	}else{
		$desc_el = "p";
	}

	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	$default_value = wpautop( $default_value );

	if( isset( $data['display_style'] ) ){
		$display_style = $data['display_style'];
	}else{
		$display_style = '';
	}

	$field_class = ninja_forms_get_field_class($field_id);
	?>
	<<?php echo $desc_el;?> class="<?php echo $field_class;?>" id="ninja_forms_field_<?php echo $field_id;?>_div_wrap" style="<?php echo $display_style;?>" rel="<?php echo $field_id;?>"><?php echo $default_value;?></<?php echo $desc_el;?>>
	<?php
}