<?php
add_action('init', 'ninja_forms_register_tab_form_settings');

function ninja_forms_register_tab_form_settings(){
	$all_forms_link = esc_url(remove_query_arg(array('form_id', 'tab')));
	$args = array(
		'name' => 'Form Settings',
		'page' => 'ninja-forms',
		'display_function' => 'ninja_forms_display_form_settings',
		'save_function' => 'ninja_forms_save_form_settings',
		//'title' => '<h2>Forms <a href="'.$all_forms_link.'" class="add-new-h2">'.__('View All Forms', 'ninja-forms').'</a></h2>',
	);
	ninja_forms_register_tab('form_settings', $args);

}

function ninja_forms_display_form_settings($form_id, $data){
	if(isset($data['form_title'])){
		$form_title = $data['form_title'];
		$prompt_text = 'screen-reader-text';
	}else{
		$form_title = '';
		$prompt_text = '';
	}

	if(isset($data['show_title'])){
		$show_title = $data['show_title'];
	}else{
		$show_title = '';
	}
?>
	<div id="titlediv">
		<div id="titlewrap">
			<label class="<?php echo $prompt_text;?>" id="title-prompt-text" for="title">Enter form title here </label>
			<input type="text" name="form_title" size="30" value="<?php echo $form_title;?>" id="title" autocomplete="off">
		</div>
	</div>
<?php
}

add_action('init', 'ninja_forms_register_form_settings_basic_metabox');
function ninja_forms_register_form_settings_basic_metabox(){

	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_data = $form_row['data'];
	}else{
		$form_id = '';
		$form_row = '';
		$form_data = '';
	}

	$pages = get_pages();
	$pages_array = array();
	$append_array = array();
	array_push($pages_array, array('name' => '- None', 'value' => ''));
	//array_push($pages_array, array('name' => '- Custom', 'value' => ''));
	array_push($append_array, array('name' => '- None', 'value' => ''));
	foreach ($pages as $pagg) {
		array_push($pages_array, array('name' => $pagg->post_title, 'value' => get_page_link($pagg->ID)));
		array_push($append_array, array('name' => $pagg->post_title, 'value' => $pagg->ID));
	}

	if( isset( $form_data['ajax'] ) ){
		$ajax = $form_data['ajax'];
	}else{
		$ajax = 0;
	}

	if( isset( $form_data['landing_page'] ) AND $form_data['landing_page'] != '' ){
		$clear_complete_style = 'display:none;';
		$hide_complete_style = 'display:none;';
		$success_msg_style = 'display:none;';
		$landing_page_style = '';		
	}else{
		$clear_complete_style = '';
		$hide_complete_style = '';
		$landing_page_style = '';
		$success_msg_style = '';
	}

	if( $ajax == 1 ){
		$landing_page_style = 'display:none;';
		$clear_complete_style = '';
		$hide_complete_style = '';
		$success_msg_style = '';
	}

	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'slug' => 'basic_settings',
		'title' => __('Basic Settings', 'ninja-forms'),
		//'display_function' => 'ninja_forms_form_settings_basic_metabox',
		'settings' => array(
			array(
				'name' => 'show_title',
				'type' => 'checkbox',
				'label' => __('Display Form Title', 'ninja-forms'),
			),
			array(
				'name' => 'save_subs',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __('Save form submissions?', 'ninja-forms'),
				'display_function' => '',
				'help' => __('', 'ninja-forms'),
				'default_value' => 1,
			),

			array(
				'name' => 'logged_in',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __( 'Require Logged-in?', 'ninja-forms' ),
				'display_function' => '',
				'help' => __('', 'ninja-forms'),
			),
			array(
				'name' => 'append_page',
				'type' => 'select',
				'desc' => '',
				'label' => __('Append to a page', 'ninja-forms'),
				'display_function' => '',
				'help' => __('', 'ninja-forms'),
				'options' => $append_array,
			),			
			array(
				'name' => 'ajax',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __('Submit via ajax?', 'ninja-forms'),
				'display_function' => '',
				'help' => __('', 'ninja-forms'),
			),			
			array(
				'name' => 'landing_page',
				'type' => 'select',
				'desc' => '',
				'label' => __('Success Page', 'ninja-forms'),
				'display_function' => '',
				'help' => __('', 'ninja-forms'),
				'options' => $pages_array,
				'style' => $landing_page_style,
				'tr_class' => 'ajax-hide'
			),
			array(
				'name' => 'clear_complete',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __('Clear successfully completed form?', 'ninja-forms'),
				'display_function' => '',
				'help' => __('If this box is checked, Ninja Forms will clear the form after it has been successfully submitted.', 'ninja-forms'),
				'default_value' => 1,
				'style' => $clear_complete_style,
				'tr_class' => 'no-ajax-hide landing-page-hide',
			),
			array(
				'name' => 'hide_complete',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __('Hide successfully completed form?', 'ninja-forms'),
				'display_function' => '',
				'help' => __('If this box is checked, Ninja Forms will hide the form after it has been successfully submitted.', 'ninja-forms'),
				'default_value' => 1,
				'style' => $hide_complete_style,
				'tr_class' => 'no-ajax-hide landing-page-hide',
			),
			array(
				'name' => 'success_msg',
				'type' => 'rte',
				'label' => __('Success Message', 'ninja-forms'),
				'desc' => __('If you want to include field data entered by the user, for instance a name, you can use the following shortcode: [ninja_forms_field id=23] where 23 is the ID of the field you want to insert. This will tell Ninja Forms to replace the bracketed text with whatever input the user placed in that field. You can find the field ID when you expand the field for editing.', 'ninja-forms'),
				'style' => $success_msg_style,
				'tr_class' => 'no-ajax-hide landing-page-hide',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

add_action('init', 'ninja_forms_register_form_settings_basic_email_metabox');
function ninja_forms_register_form_settings_basic_email_metabox(){
	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'slug' => 'email_settings',
		'title' => __('Email Settings', 'ninja-forms'),
		'display_function' => '',
		'state' => 'closed',
		'settings' => array(
			//array(
				//'name' => 'send_email',
				//'type' => 'checkbox',
				//'label' => __('Send email to user?', 'ninja-forms'),
				//'desc' => __('Requires the use of an email field.', 'ninja-forms'),
			//),
			array(
				'name' => 'email_from',
				'type' => 'text',
				'label' => __('Email From Address', 'ninja-forms'),
				'desc' => htmlspecialchars(__('Steve Jones <steve@myurl.com>', 'ninja-forms')),
			),
			array(
				'name' => 'email_type',
				'type' => 'select',
				'label' => __('Email Type', 'ninja-forms'),
				'options' => array(
					array('name' => 'HTML', 'value' => 'html'),
					array('name' => 'Plain Text', 'value' => 'plain'),
				),
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}


add_action('init', 'ninja_forms_register_form_settings_user_email_metabox');
function ninja_forms_register_form_settings_user_email_metabox(){
	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'slug' => 'user_email',
		'title' => __('User Email', 'ninja-forms'),
		'display_function' => '',
		'state' => 'closed',
		'settings' => array(
			array(
				'name' => 'user_subject',
				'type' => 'text',
				'label' => __('Subject for the user email', 'ninja-forms'),
			),
			array(
				'name' => 'user_email_msg',
				'type' => 'rte',
				'label' => __('Email message sent to the user', 'ninja-forms'),
				'desc' => __('If you want to include field data entered by the user, for instance a name, you can use the following shortcode: [ninja_forms_field id=23] where 23 is the ID of the field you want to insert. This will tell Ninja Forms to replace the bracketed text with whatever input the user placed in that field. You can find the field ID when you expand the field for editing.', 'ninja-forms'),
			),
			array(
				'name' => 'user_email_fields',
				'type' => 'checkbox',
				'label' => __('Include a list of fields?', 'ninja-forms'),
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

add_action( 'init', 'ninja_forms_register_form_settings_admin_email_metabox' );
function ninja_forms_register_form_settings_admin_email_metabox(){
	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'slug' => 'admin_email',
		'title' => __( 'Administrator Email', 'ninja-forms' ),
		'display_function' => '',
		'state' => 'closed',
		'settings' => array(
			array(
				'name' => 'admin_mailto',
				'type' => '',
				'label' => __( 'Administrator Email Addresses', 'ninja-forms' ),
				'display_function' => 'ninja_forms_admin_email',
			),
			array(
				'name' => 'admin_subject',
				'type' => 'text',
				'label' => __( 'Admin Subject', 'ninja-forms' ),
			),
			array(
				'name' => 'admin_email_msg',
				'type' => 'rte',
				'label' => __( 'Admin Email Message', 'ninja-forms' ),
				'desc' => __('If you want to include field data entered by the user, for instance a name, you can use the following shortcode: [ninja_forms_field id=23] where 23 is the ID of the field you want to insert. This will tell Ninja Forms to replace the bracketed text with whatever input the user placed in that field. You can find the field ID when you expand the field for editing.', 'ninja-forms'),
			),
			array(
				'name' => 'admin_email_fields',
				'type' => 'checkbox',
				'label' => __( 'Include a list of fields?', 'ninja-forms' ),
				'default_value' => 1,
			),
			array(
				'name' => 'admin_attach_csv',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __('Attach CSV of submission?', 'ninja-forms'),
				'display_function' => '',
				'help' => __('', 'ninja-forms'),
				'default_value' => 0,
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

function ninja_forms_admin_email($form_id, $data){
	if(isset($data['admin_mailto'])){
		$admin_mailto = $data['admin_mailto'];
	}else{
		$admin_mailto = '';
	}

	?>
	<label for="">
		<p>
			<?php _e('Administrator Email Addresses', 'ninja-forms');?> &nbsp;&nbsp;<a href="#" id="ninja_forms_add_mailto_<?php echo $form_id;?>" name="" class="ninja-forms-add-mailto">Add New</a>
			<a href="#" class="tooltip">
			    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
			    <span>
			        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
			        <?php _e( 'Please enter all the addresses this form should be sent to.', 'ninja-forms' );?>
			    </span>
			</a>
		</p>
	</label>
	<div id="ninja_forms_mailto">
		<input type="hidden" name="admin_mailto" value="">
		<?php
		if(is_array($admin_mailto) AND !empty($admin_mailto)){
			$x = 0;
			foreach($admin_mailto as $v){
				?>
				<span id="ninja_forms_mailto_<?php echo $x;?>_span">
					<a href="#" id="" class="ninja-forms-remove-mailto">X</a> <input type="text" name="admin_mailto[]" id="" value="<?php echo $v;?>" class="ninja-forms-mailto-address">
				</span>
				<?php
				$x++;
			}
		}
		?>
	</div>
	<br />
	<?php
}

function ninja_forms_save_form_settings($form_id, $data){
	global $wpdb, $ninja_forms_admin_update_message;
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];

	foreach( $data as $key => $val ){
		$form_data[$key] = $val;
	}

	$data_array = array('data' => serialize($form_data));
	if($form_id != 'new'){
		$wpdb->update( NINJA_FORMS_TABLE_NAME, $data_array, array( 'id' => $form_id ));
	}else{
		$wpdb->insert( NINJA_FORMS_TABLE_NAME, $data_array );
		$redirect = add_query_arg( array('form_id' => $wpdb->insert_id, 'update_message' => __( 'Form Settings Saved', 'ninja-forms' ) ) );
		do_action( 'ninja_forms_save_new_form_settings', $wpdb->insert_id, $data );
		header( "Location: ".$redirect );
	}
	$update_msg = __( 'Form Settings Saved', 'ninja-forms' );
	return $update_msg;
}