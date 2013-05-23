<?php
/**
 * This Ninja Forms Processing Class is used to interact with Ninja Forms as it processes form data.
 * It is based upon the WordPress Error API.
 *
 * Contains the Ninja_Forms_Processing class
 * 
 */

/**
 * Ninja Forms Processing class.
 *
 * Class used to interact with form processing.
 * This class stores all data related to the form submission, including data from the Ninja Form mySQL table.
 * It can also be used to report processing errors and/or processing success messages.
 *
 * Form Data Methods:
 *		get_form_ID() - Used to retrieve the form ID of the form being processed.
 *		get_user_ID() - Used to retrieve the User ID if the user was logged in.
 *		get_action() - Used to retrieve the action currently being performed. ('submit', 'save', 'edit_sub').
 *		set_action('action') - Used to set the action currently being performed. ('submit', 'save', 'edit_sub').
 *
 * Submitted Values Methods:
 *		get_all_fields() - Returns an array of all the user submitted fields in the form of array('field_ID' => 'user value').
 *		get_field_value('field_ID') - Used to access the submitted data by field_ID.
 *		update_field_value('field_ID', 'new_value') - Used to change the value submitted by the user. If the field does not exist, it will be created.
 *		remove_field_value('field_ID') - Used to delete values submitted by the user.
 *		get_field_settings('field_ID') - Used to get all of the back-end data related to the field (type, label, required, show_help, etc.).
 *		update_field_settings('field_ID', $data) - Used to temporarily update the back-end data related to the field. This is NOT permanent and will only affect the current form processing.
 *		
 * Extra Fields Methods (These are fields that begin with an _ and aren't Ninja Forms Fields )
 * 		get_all_extras() - Returns an array of all extra form inputs.
 *		get_extra_value('name') - Used to access the value of an extra field.
 *		update_extra_value('name', 'new_value') - Used to update an extra value.
 *		remove_extra_value('name') - Used to delete the extra value from the processing variable.
 *
 * Form Settings Methods (Please note that the changes made with these methods only affect the current process and DO NOT permanently change these settings):
 *		get_all_form_settings() - Used to get all of the settings of the form currently being processed.
 *		get_form_setting('setting_ID') - Used to retrieve a form setting from the form currently being processed.
 *		update_form_setting('setting_ID', 'new_value') - Used to change the value of a form setting using its unique ID. If the setting does not exist, it will be created.
 *		remove_form_setting('setting_ID') - Used to remove a form setting by its unique ID.
 * 
 * Error Reporting Methods:
 *		get_all_errors() - Used to get an array of all error messages in the format: array('unique_id' => array('error_msg' => 'Error Message', 'display_location' => 'Display Location')).
 *			An empty array is returned if no errors are found.
 *		get_error('unique_id') - Used to get a specific error message by its unique ID.
 *		get_errors_by_location('location') - Used to retrieve an array of error messages with a given display location.
 *		add_error('unique_ID', 'Error Message', 'display_location') - Used to add an error message. The optional 'display_location' tells the display page where to show this error.
 *			Possible examples include a valid field_ID or 'general'. If this value is not included, the latter will be assumed and  will place this error at the beginning of the form.
 *		remove_error('unique_ID') - Used to remove an error message.
 *		remove_all_errors() - Used to remove all currently set error messages.
 *
 * Success Reporting Methods:
 *		get_all_success_msgs() - Used to get an array of all success messages in the format: array('unique_ID' => 'Success Message').
 *		get_success_msg('unique_ID') - Used to get a specific success message.
 *		add_success_msg('unique_ID', 'Success Message') - Used to add a success message.
 *		remove_success_msg('unique_ID') - Used to remove a success message.
 *
 */

class Ninja_Forms_Processing {

	/**
	 *
	 * Stores the data accessed by the other parts of the class.
	 * All response messages will be stored in this value.
	 * 
	 * @var array
	 * @access private
	 */
	var $data = array();

	/**
	 * Constructor - Sets up the form ID.
	 *
	 * If the form_ID parameter is empty then nothing will be done.
	 *
	 */
	function __construct($form_ID = '') {
		if(empty($form_ID)){
			return false;
		}else{
			$this->data['form_ID'] = $form_ID;
			$current_user = wp_get_current_user();
			$user_ID = $current_user->ID;
			if(!$user_ID){
				$user_ID = '';
			}
			$this->data['user_ID'] = $user_ID;
		}
	}

	/**
	 * Add the submitted vars to $this->data['fields'].
	 * Also runs any functions registered to the field's pre_process hook.
	 * 
	 *
	 */
	function setup_submitted_vars() {
		global $ninja_forms_fields;
		$form_ID = $this->data['form_ID'];

		//Get our plugin settings
		$plugin_settings = get_option("ninja_forms_settings");
		$req_field_error = $plugin_settings['req_field_error'];

		if(empty($this->data)){
			return '';
		}else{

			/*
			//Loop through our field list and add any fields that have process_field set to false.
			//Anything that saves/edits/uses $ninja_forms_processing to get field should check the process_field value before doing anything.
			$all_fields = ninja_forms_get_fields_by_form_id( $form_ID );
			if( is_array( $all_fields ) AND !empty( $all_fields ) ){
				foreach( $all_fields as $field ){
					$field_ID = $field['id'];
					$field_type = $field['type'];
					if( !$ninja_forms_fields[$field_type]['process_field'] ){
						if( isset( $field['data']['default_value'] ) ){
							$this->data['fields'][$field_ID] = $field['data']['default_value'];
							$this->data['field_data'][$field_ID] = $field;
						}
					}
				}
			}
			*/

			//Loop through the $_POST'd field values and add them to our global variable.
			foreach($_POST as $key => $val){
				if(substr($key, 0, 1) != '_'){
					$process_field = strpos($key, 'ninja_forms_field_');
					if($process_field !== false){
						$field_ID = str_replace('ninja_forms_field_', '', $key); // Get the id # of each field.
						$field_row = ninja_forms_get_field_by_id($field_ID);
						if(is_array($field_row) AND !empty($field_row)){
							if(isset($field_row['type'])){
								$field_type = $field_row['type'];
							}else{
								$field_type = '';
							}
							if(isset($field_row['data']['req'])){
								$req = $field_row['data']['req'];			
							}else{
								$req = '';
							}

							$val = ninja_forms_stripslashes_deep( $val );
							//$val = ninja_forms_esc_html_deep( $val );
							
							$this->data['fields'][$field_ID] = $val;
							$field_row = ninja_forms_get_field_by_id( $field_ID );
							$this->data['field_data'][$field_ID] = $field_row;
						}
					}
				}else{
					$this->data['extra'][$key] = $val;
				}
			}

			//Grab the form info from the database and store it in our global form variables.
			$form_row = ninja_forms_get_form_by_id($form_ID);
			$form_data = $form_row['data'];

			if(isset($_REQUEST['_sub_id']) AND !empty($_REQUEST['_sub_id'])){
				$form_data['sub_id'] = $_REQUEST['_sub_id'];
			}else{
				$form_data['sub_id'] = '';
			}

			$this->data['action'] = 'submit';

			//Loop through the form data and set the global $ninja_form_data variable.
			if(is_array($form_data) AND !empty($form_data)){
				foreach($form_data as $key => $val){
					if(!is_array($val)){
						$value = stripslashes($val);
						//$value = esc_html($value);
						//$value = htmlspecialchars($value);
					}else{
						$value = $val;
					}
					$this->data['form'][$key] = $value;
				}
				$this->data['form']['admin_attachments'] = array();
				$this->data['form']['user_attachments'] = array();
			}	

		}
	}

	/**
	 * Submitted Values Methods:
	 *
	**/

	/**
	 * Retrieve the form ID of the form currently being processed.
	 *
	 */
	function get_form_ID() {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['form_ID'];			
		}
	}

	/**
	 * Retrieve the User ID of the form currently being processed.
	 *
	 */
	function get_user_ID() {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['user_ID'];			
		}
	}		

	/**
	 * Set the User ID of the form currently being processed.
	 *
	 */
	function set_user_ID( $user_id ) {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['user_ID'] = $user_id;			
		}
	}	

	/**
	 * Retrieve the action currently being performed.
	 *
	 */
	function get_action() {
		if ( empty($this->data['action']) ){
			return false;
		}else{
			return $this->data['action'];			
		}
	}

	/**
	 * Set the action currently being performed.
	 *
	 */
	function set_action( $action ) {
		if ( empty($this->data) ){
			return false;
		}else{
			return $this->data['action'] = $action;			
		}
	}

	/**
	 * Retrieve all the user submitted form data.
	 *
	 */
	function get_all_fields() {
		if ( empty($this->data['fields']) ){
			return false;
		}else{
			return $this->data['fields'];			
		}
	}


	/**
	 * Retrieve user submitted form data by field ID.
	 *
	 */
	function get_field_value($field_ID = '') {
		if(empty($this->data) OR $field_ID == '' OR !isset($this->data['fields'][$field_ID])){
			return false;
		}else{
			return $this->data['fields'][$field_ID];			
		}
	}

	/**
	 * Change the value of a field.
	 *
	 */
	function update_field_value($field_ID = '', $new_value = '') {
		if(empty($this->data) OR $field_ID == ''){
			return false;
		}else{
			$this->data['fields'][$field_ID] = $new_value;
			return true;
		}
	}

	/**
	 * Remove a field and its value from the user submissions.
	 *
	 */
	function remove_field_value($field_ID = '') {
		if(empty($this->data) OR $field_ID == ''){
			return false;
		}else{
			unset($this->data['fields'][$field_ID]);
			return true;
		}
	}

	/**
	 * Retrieve field data by field ID. This data includes all of the information entered in the admin back-end.
	 *
	 */
	function get_field_settings($field_ID = '') {
		if(empty($this->data) OR $field_ID == '' OR !isset($this->data['field_data'][$field_ID])){
			return false;
		}else{
			return $this->data['field_data'][$field_ID];			
		}
	}

	/**
	 * Update field data by field ID. This data includes all of the informatoin entered into the admin back-end. (Please note that the changes made with these methods only affect the current process and DO NOT permanently change these settings):
	 *
	 */
	function update_field_settings($field_ID = '', $new_value = '') {
		if(empty($this->data) OR $field_ID == ''){
			return false;
		}else{
			$this->data['field_data'][$field_ID] = $new_value;
			return true;
		}
	}

	/**
	 * Extra Form Values Methods
	 *
	**/

	/**
	 * Retrieve all the extra submitted form data.
	 *
	 */
	function get_all_extras() {
		if ( empty($this->data['extra']) ){
			return false;
		}else{
			return $this->data['extra'];			
		}
	}


	/**
	 * Retrieve user submitted form data by field ID.
	 *
	 */
	function get_extra_value($name = '') {
		if(empty($this->data) OR $name == '' OR !isset($this->data['extra'][$name])){
			return false;
		}else{
			return $this->data['extra'][$name];			
		}
	}

	/**
	 * Change the value of a field.
	 *
	 */
	function update_extra_value($name = '', $new_value = '') {
		if(empty($this->data) OR $name == ''){
			return false;
		}else{
			$this->data['extra'][$name] = $new_value;
			return true;
		}
	}

	/**
	 * Remove a field and its value from the user submissions.
	 *
	 */
	function remove_extra_value($name = '') {
		if(empty($this->data) OR $name == ''){
			return false;
		}else{
			unset($this->data['extra'][$name]);
			return true;
		}
	}


	/**
	 * Form Settings Methods (Please note that the changes made with these methods only affect the current process and DO NOT permanently change these settings):
	 *
	**/

	/**
	 * Retrieve all the settings for the form currently being processed.
	 *
 	*/
	function get_all_form_settings() {
		if(empty($this->data['form']) OR !isset($this->data['form'])){
			return false;
		}else{
			return $this->data['form'];			
		}
	}

	/**
	 * Retrieve a form setting value by its unique ID.
	 *
 	*/
	function get_form_setting($setting_ID) {
		if(empty($this->data['form']) OR !isset($this->data['form'][$setting_ID])){
			return false;
		}else{
			return $this->data['form'][$setting_ID];			
		}
	}

	/**
	 * Update a form setting value by its unique ID.
	 *
 	*/
	function update_form_setting($setting_ID, $new_value = '') {
		if(empty($this->data['form'])){
			return false;
		}else{
			return $this->data['form'][$setting_ID] = $new_value;			
		}
	}

	/**
	 * Remove a form setting value by its unique ID.
	 *
 	*/
	function remove_form_setting($setting_ID, $new_value = '') {
		if(empty($this->data['form']) OR !isset($this->data['form'][$setting_ID])){
			return false;
		}else{
			unset($this->data['form'][$setting_ID]);
			return true;
		}
	}



	/**
	 * Error Reporting Methods:
	 *
	**/

	/**
	 * Retrieve all error messages.
	 *
	 */
	function get_all_errors() {
		if(empty($this->data['errors']) OR !isset($this->data['errors'])){
			return false;
		}else{
			return $this->data['errors'];			
		}
	}

	/**
	 * Retrieve an error message and location by its unique ID.
	 *
	 */
	function get_error($error_ID = '') {
		if(empty($this->data['errors']) OR !isset($this->data['errors'][$error_ID]) OR $error_ID == ''){
			return false;
		}else{
			return $this->data['errors'][$error_ID];			
		}
	}


	/**
	 * Retrieve an array of error_IDs and messages by display location.
	 *
	 */
	function get_errors_by_location($error_location = '') {
		$tmp_array = array();
		if(empty($this->data['errors']) OR !isset($this->data['errors']) OR $error_location == ''){
			return false;
		}else{
			foreach($this->data['errors'] as $ID => $error){
				if($error['location'] == $error_location){
					$tmp_array[$ID] = $error;
				}
			}
			if(!empty($tmp_array)){
				return $tmp_array;
			}else{
				return false;
			}
		}
	}


	/**
	 * Add an error message.
	 *
	 */
	function add_error($error_ID, $error_msg, $error_location = 'general') {
		$this->data['errors'][$error_ID]['msg'] = $error_msg;
		$this->data['errors'][$error_ID]['location'] = $error_location;
		return true;	
	}

	/**
	 * Remove an error message by its unique ID.
	 *
	 */
	function remove_error($error_ID = '') {
		if(empty($this->data['errors']) OR !isset($this->data['errors']) OR $error_ID == ''){
			return false;
		}else{
			unset($this->data['errors'][$error_ID]);
			return true;			
		}
	}

	/**
	 * Remove all set error messages.
	 *
	 */
	function remove_all_errors() {
		if(empty($this->data['errors']) OR !isset($this->data['errors'])){
			return true;	
		}else{
			$this->data['errors'] = array();
			return true;
		}	
	}

	/**
	 * Success Reporting Methods:
	 *
	**/

	/**
	 * Retrieve all success messages.
	 *
	 */
	function get_all_success_msgs() {
		if(empty($this->data['success']) OR !isset($this->data['success'])){
			return false;
		}else{
			return $this->data['success'];			
		}
	}

	/**
	 * Retrieve a success message by unique ID.
	 *
	 */
	function get_success_msg($success_ID = '') {
		if(empty($this->data['success']) OR !isset($this->data['success']) OR $success_ID == ''){
			return array();
		}else{
			return $this->data['success'][$success_ID];			
		}
	}

	/**
	 * Add a success message.
	 *
	 */
	function add_success_msg($success_ID, $success_msg) {
		$this->data['success'][$success_ID] = $success_msg;
		return true;	
	}

	/**
	 * Remove a success message by its unique ID.
	 *
	 */
	function remove_success_msg($success_ID = '') {
		if(empty($this->data['success']) OR !isset($this->data['success']) OR $success_ID == ''){
			return false;
		}else{
			unset($this->data['success'][$success_ID]);
			return true;			
		}
	}

}