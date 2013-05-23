<?php
/**
 * Used to restore the progress of a user. 
 * If the global processing variable $ninja_forms_processing is set, filter the default_value for each field.
 *
**/
add_action( 'init', 'ninja_forms_register_filter_restore_progress' );
function ninja_forms_register_filter_restore_progress(){
	add_filter( 'ninja_forms_field', 'ninja_forms_filter_restore_progress', 10, 2 );
}

function ninja_forms_filter_restore_progress( $data, $field_id ){
	global $current_user, $ninja_forms_processing, $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	$process_field = $ninja_forms_fields[$field_type]['process_field'];

	get_currentuserinfo();
	$form_row = ninja_forms_get_form_by_field_id( $field_id );
	$form_id = $form_row['id'];
	
	if( isset( $current_user ) ){
		$user_id = $current_user->ID;	
	}else{
		$user_id = '';
	}
		
	if( is_object( $ninja_forms_processing ) AND $process_field ){

		$clear_form = $ninja_forms_processing->get_form_setting( 'clear_complete' );
		$process_complete = $ninja_forms_processing->get_form_setting( 'processing_complete' );
		if( $process_complete != 1 OR ( $process_complete == 1 AND $clear_form != 1 ) ){
			if( $ninja_forms_processing->get_field_value( $field_id ) !== false ){
				$data['default_value'] = $ninja_forms_processing->get_field_value( $field_id );
			}
		}
	}

	return $data;
}