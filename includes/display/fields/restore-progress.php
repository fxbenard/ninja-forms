<?php
/*
 * Used to restore the progress of a user.
 * If the global processing variable $ninja_forms_processing is set, filter the default_value for each field.
 *
 */
function ninja_forms_register_filter_restore_progress(){
	add_filter( 'ninja_forms_field', 'ninja_forms_filter_restore_progress', 10, 2 );
}

add_action( 'init', 'ninja_forms_register_filter_restore_progress' );

function ninja_forms_filter_restore_progress( $data, $field_id ){
	global $ninja_forms_processing, $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	if ( isset( $ninja_forms_fields[$field_type]['process_field'] ) ) {
		$process_field = $ninja_forms_fields[$field_type]['process_field'];
	} else {
		$process_field = false;
	}

	if ( is_object( $ninja_forms_processing ) ) {
		$clear_form = $ninja_forms_processing->get_form_setting( 'clear_complete' );
		$process_complete = $ninja_forms_processing->get_form_setting( 'processing_complete' );
		if ( $process_complete != 1 OR ( $process_complete == 1 AND $clear_form != 1 ) ) {
			if ( $ninja_forms_processing->get_field_value( $field_id ) !== false ) {
				$data['default_value'] = esc_html( $ninja_forms_processing->get_field_value( $field_id ) );
			}
		}
	}
	return $data;
}