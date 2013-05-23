<?php

add_action( 'init', 'ninja_forms_register_common_field_type_groups', 8 );
function ninja_forms_register_common_field_type_groups(){
	$args = array(
		'name' => 'Standard Fields',
	);
	ninja_forms_register_field_type_group( 'standard_fields', $args );

	$args = array(
		'name' => 'Layout Elements',
	);
	ninja_forms_register_field_type_group( 'layout_elements', $args );
}