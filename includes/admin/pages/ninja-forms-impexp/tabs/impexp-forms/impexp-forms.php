<?php
add_action('init', 'ninja_forms_register_tab_impexp_forms');

function ninja_forms_register_tab_impexp_forms(){
	$args = array(
		'name' => __( 'Forms', 'ninja-forms' ),
		'page' => 'ninja-forms-impexp',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_impexp_forms',
		'show_save' => false,
	);
	ninja_forms_register_tab('impexp_forms', $args);

}

add_action('init', 'ninja_forms_register_imp_forms_metabox');
function ninja_forms_register_imp_forms_metabox(){
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_forms',
		'slug' => 'imp_form',
		'title' => __( 'Import a form', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'userfile',
				'type' => 'file',
				'label' => __( 'Select a file', 'ninja-forms' ),
				'desc' => '',
				'max_file_size' => 5000000,
				'help_text' => '',
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __( 'Import Form', 'ninja-forms' ),
				'class' => 'button-secondary',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

add_action('admin_init', 'ninja_forms_register_exp_forms_metabox');
function ninja_forms_register_exp_forms_metabox(){
	$form_results = ninja_forms_get_all_forms();
	$form_select = array();
	if(is_array($form_results) AND !empty($form_results)){
		foreach($form_results as $form){
			if( isset( $form['data'] ) ){
				$data = $form['data'];
				$form_title = $data['form_title'];
				array_push($form_select, array('name' => $form_title, 'value' => $form['id']));
			}
		}
	}
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_forms',
		'slug' => 'exp_form',
		'title' => __('Export a form', 'ninja-forms'),
		'settings' => array(
			array(
				'name' => 'form_id',
				'type' => 'select',
				'label' => __('Select a form', 'ninja-forms'),
				'desc' => '',
				'options' => $form_select,
				'help_text' => '',
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __('Export Form', 'ninja-forms'),
				'class' => 'button-secondary',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

function ninja_forms_export_form( $form_id ){
	if($form_id != ''){
		$form_row = ninja_forms_get_form_by_id($form_id);
		$field_results = ninja_forms_get_fields_by_form_id($form_id);
		$data = $form_row['data'];
		$form_title = $data['form_title'];
		$form_row['id'] = NULL;
		if(is_array($form_row) AND !empty($form_row)){
			if(is_array($field_results) AND !empty($field_results)){
				$x = 0;
				foreach($field_results as $field){
					$form_row['field'][$x] = $field;
					$x++;
				}
			}
		}
		if(isset($plugin_settings['date_format'])){
			$date_format = $plugin_settings['date_format'];
		}else{
			$date_format = 'm/d/Y';
		}

		//$today = date($date_format);
		$current_time = current_time('timestamp');
		$today = date($date_format, $current_time);
		$form_row = serialize($form_row);

		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=".$form_title."-".$today.".nff");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $form_row;
		die();
	}
}

function ninja_forms_save_impexp_forms($data){
	global $wpdb, $ninja_forms_admin_update_message;
	$plugin_settings = get_option("ninja_forms_settings");
	$form_id = $_REQUEST['form_id'];
	$update_msg = '';
	if( $_REQUEST['submit'] == __('Export Form', 'ninja-forms') OR ( isset( $_REQUEST['export_form'] ) AND $_REQUEST['export_form'] == 1 ) ){
		if($form_id != ''){
			ninja_forms_export_form( $form_id );
		}else{
			$ninja_forms_admin_update_message = __( 'Please select a form.', 'ninja-forms' );
		}
	}elseif($_REQUEST['submit'] == __('Import Form', 'ninja-forms')){
		if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK AND is_uploaded_file($_FILES['userfile']['tmp_name'])){
			$file = file_get_contents($_FILES['userfile']['tmp_name']);
			$form = unserialize( trim( $file ) );
			$form_fields = $form['field'];

			unset($form['field']);
			$form = apply_filters( 'ninja_forms_before_import_form', $form );
			$form['data'] = serialize( $form['data'] );
			$wpdb->insert(NINJA_FORMS_TABLE_NAME, $form);
			$form_id = $wpdb->insert_id;
			$form['id'] = $form_id;
			if(is_array($form_fields)){
				for ($x=0; $x < count( $form_fields ); $x++) {
					$form_fields[$x]['form_id'] = $form_id;
					$form_fields[$x]['data'] = serialize( $form_fields[$x]['data'] );
					$old_field_id = $form_fields[$x]['id'];
					$form_fields[$x]['id'] = NULL;
					$wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $form_fields[$x] );
					$form_fields[$x]['id'] = $wpdb->insert_id;
					$form_fields[$x]['old_id'] = $old_field_id;
					$form_fields[$x]['data'] = unserialize( $form_fields[$x]['data'] );
				}
			}
			$form['data'] = unserialize( $form['data'] );
			$form['field'] = $form_fields;
			do_action( 'ninja_forms_after_import_form', $form );
			$update_msg = __( 'Form Imported Successfully.', 'ninja-forms' );
		}else{
			//echo $_FILES['userfile']['error'];
			$update_msg = __( 'Please select a valid exported form file.', 'ninja-forms' );
		}
	}
	return $update_msg;
}

function ninja_forms_import_form( $data ){
	global $wpdb;
	$form = unserialize( $data );
	$form_fields = $form['field'];

	unset($form['field']);
	$form = apply_filters( 'ninja_forms_before_import_form', $form );
	$form['data'] = serialize( $form['data'] );
	$wpdb->insert(NINJA_FORMS_TABLE_NAME, $form);
	$form_id = $wpdb->insert_id;
	$form['id'] = $form_id;
	if(is_array($form_fields)){
		for ($x=0; $x < count( $form_fields ); $x++) {
			$form_fields[$x]['form_id'] = $form_id;
			$form_fields[$x]['data'] = serialize( $form_fields[$x]['data'] );
			$old_field_id = $form_fields[$x]['id'];
			$form_fields[$x]['id'] = NULL;
			$wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $form_fields[$x] );
			$form_fields[$x]['id'] = $wpdb->insert_id;
			$form_fields[$x]['old_id'] = $old_field_id;
			$form_fields[$x]['data'] = unserialize( $form_fields[$x]['data'] );
		}
	}
	$form['data'] = unserialize( $form['data'] );
	$form['field'] = $form_fields;
	do_action( 'ninja_forms_after_import_form', $form );
	return $form['id'];
}