<?php
add_action('init', 'ninja_forms_register_edit_field_label');
function ninja_forms_register_edit_field_label(){
	add_action('ninja_forms_edit_field_before_registered', 'ninja_forms_edit_field_label', 10);
}

function ninja_forms_edit_field_label($field_id){
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_label = $reg_field['edit_label'];
	if($edit_label){
		if(isset($field_data['label'])){
			$label = stripslashes($field_data['label']);
		}else{
			$label = '';
		}

		ninja_forms_edit_field_el_output($field_id, 'text', 'Label', 'label', $label, 'wide', '', 'widefat ninja-forms-field-label');
	}
}

add_action('init', 'ninja_forms_register_edit_field_label_pos');
function ninja_forms_register_edit_field_label_pos(){
	add_action('ninja_forms_edit_field_before_registered', 'ninja_forms_edit_field_label_pos', 10);
}

function ninja_forms_edit_field_label_pos($field_id){
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_label_pos = $reg_field['edit_label_pos'];

	if($edit_label_pos){
		if(isset($field_data['label_pos'])){
			$label_pos = $field_data['label_pos'];
		}else{
			$label_pos = '';
		}
		$options = array(
			array('name' => 'Left of Element', 'value' => 'left'),
			array('name' => 'Above Element', 'value' => 'above'),
			array('name' => 'Below Element', 'value' => 'below'),
			array('name' => 'Right of Element', 'value' => 'right'),
			array('name' => 'Inside Element', 'value' => 'inside'),
		);
		ninja_forms_edit_field_el_output($field_id, 'select', 'Label Position', 'label_pos', $label_pos, 'wide', $options, 'widefat');
	}

}