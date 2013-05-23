<?php
function ninja_forms_register_field($slug, $args = array()){
	global $ninja_forms_fields;
	if(!isset($args['edit_function'])){
		$args['edit_function'] = '';
	}
	if(!isset($args['edit_options'])){
		$args['edit_options'] = '';
	}
	if(!isset($args['name'])){
		$args['name'] = $slug;
	}
	if(!isset($args['group'])){
		$args['group'] = '';
	}
	if(!isset($args['edit_label'])){
		$args['edit_label'] = true;
	}
	if(!isset($args['edit_label_pos'])){
		$args['edit_label_pos'] = true;
	}
	if(!isset($args['edit_req'])){
		$args['edit_req'] = true;
	}
	if(!isset($args['edit_custom_class'])){
		$args['edit_custom_class'] = true;
	}
	if(!isset($args['edit_help'])){
		$args['edit_help'] = true;
	}
	if(!isset($args['edit_meta'])){
		$args['edit_meta'] = true;
	}
	if(!isset($args['save_function'])){
		$args['save_function'] = '';
	}	
	if(!isset($args['display_function'])){
		$args['display_function'] = '';
	}
	if(!isset($args['display_label'])){
		$args['display_label'] = true;
	}	
	if(!isset($args['display_wrap'])){
		$args['display_wrap'] = true;
	}
	if(!isset($args['edit_conditional'])){
		$args['edit_conditional'] = false;
	}
	if(!isset($args['pre_process'])){
		$args['pre_process'] = '';
	}	
	if(!isset($args['process'])){
		$args['process'] = '';
	}
	if(!isset($args['post_process'])){
		$args['post_process'] = '';
	}	
	if(!isset($args['edit_sub_pre_process'])){
		$args['edit_sub_pre_process'] = '';
	}	
	if(!isset($args['edit_sub_process'])){
		$args['edit_sub_process'] = '';
	}
	if(!isset($args['edit_sub_post_process'])){
		$args['edit_sub_post_process'] = '';
	}
	if(!isset($args['interact'])){
		$args['interact'] = true;
	}
	if(!isset($args['conditional'])){
		$args['conditional'] = '';
	}
	if(!isset($args['nesting'])){
		$args['nesting'] = false;
	}
	if(!isset($args['sub_edit'])){
		$args['sub_edit'] = 'text';
	}
	if(!isset($args['process_field'])){
		$args['process_field'] = true;
	}
	if(!isset($args['req_validation'])){
		$args['req_validation'] = '';
	}	
	if(!isset($args['sub_edit_function'])){
		$args['sub_edit_function'] = '';
	}
	if(!isset($args['limit'])){
		$args['limit'] = '';
	}
	if(!isset($args['req'])){
		$args['req'] = false;
	}
	if(!isset($args['save_sub'])){
		$args['save_sub'] = true;
	}	
	if(!isset($args['use_li'])){
		$args['use_li'] = true;
	}
	if(is_array($args)){
		foreach($args as $key => $val){
			$ninja_forms_fields[$slug][$key] = $val;
		}
	}
}

function ninja_forms_register_field_type_group( $slug, $args ){
	global $ninja_forms_field_type_groups;

	foreach( $args as $key => $val ){
		$ninja_forms_field_type_groups[$slug][$key] = $val;
	}
}

function ninja_forms_register_tab($slug, $args){
	global $ninja_forms_tabs;
	
	if(!isset($args['name'])){
		$args['name'] = '';
	}
	if(!isset($args['page'])){
		$args['page'] = '';
	}else{
		$page = $args['page'];
	}
	if(!isset($args['display_function'])){
		$args['display_function'] = '';
	}
	if(!isset($args['save_function'])){
		$args['save_function'] = '';
	}
	if(!isset($args['show_save'])){
		$args['show_save'] = true;
	}
	if(!isset($args['active_class'])){
		$args['active_class'] = '';
	}
	if(!isset($args['inactive_class'])){
		$args['inactive_class'] = '';
	}
	if(!isset($args['add_form_id'])){
		$args['add_form_id'] = true;
	}
	if(!isset($args['show_on_no_form_id'])){
		$args['show_on_no_form_id'] = true;
	}	
	if(!isset($args['show_tab_links'])){
		$args['show_tab_links'] = true;
	}	
	if(!isset($args['show_this_tab_link'])){
		$args['show_this_tab_link'] = true;
	}	
	if(!isset($args['disable_no_form_id'])){
		$args['disable_no_form_id'] = false;
	}
	if(!is_array($ninja_forms_tabs)){
		$ninja_forms_tabs = array();
	}
	foreach($args as $key => $val){
		$ninja_forms_tabs[$page][$slug][$key] = $val;
	}
}

function ninja_forms_register_sidebar($slug, $args){
	global $ninja_forms_sidebars;

	$page = $args['page'];
	$tab = $args['tab'];

	if(!isset($args['name'])){
		$args['name'] = '';
	}

	if(!isset($args['display_function'])){
		$args['display_function'] = '';
	}
	
	if(!isset($args['save_function'])){
		$args['save_function'] = '';
	}
	
	if(!isset($args['order'])){
		$args['order'] = '';
	}

	if( !isset( $args['settings'] ) ){
		$args['settings'] = '';
	}

	if(!is_array($ninja_forms_sidebars)){
		$ninja_forms_sidebars = array();
	}

	foreach($args as $key => $val){
		$ninja_forms_sidebars[$page][$tab][$slug][$key] = $val;
	}
	
}

function ninja_forms_register_sidebar_option($slug, $args){
	global $ninja_forms_sidebars;

	$page = $args['page'];
	$tab = $args['tab'];
	$sidebar = $args['sidebar'];

	if( !isset( $args['desc'] ) ){
		$args['desc'] = '';
	}

	if( !isset( $args['help'] ) ){
		$args['help'] = '';
	}

	if( !isset( $args['display_function'] ) ){
		$args['display_function'] = '';
	}

	if( !isset( $args['name'] ) ){
		$args['name'] = '';
	}

	foreach($args as $key => $val){
		$ninja_forms_sidebars[$page][$tab][$sidebar]['settings'][$slug][$key] = $val;
	}
}

function ninja_forms_register_sidebar_options( $args ){
	global $ninja_forms_sidebars;

	$page = $args['page'];
	$tab = $args['tab'];
	$sidebar = $args['sidebar'];

	foreach( $args['settings'] as $setting ){

		if( !isset( $setting['desc'] ) ){
			$setting['desc'] = '';
		}

		if( !isset( $setting['help'] ) ){
			$setting['help'] = '';
		}

		if( !isset( $setting['display_function'] ) ){
			$setting['display_function'] = '';
		}

		if( !isset( $setting['name'] ) ){
			$setting['name'] = '';
		}

		$slug = $setting['name'];

		foreach($setting as $key => $val){
			$ninja_forms_sidebars[$page][$tab][$sidebar]['settings'][$slug][$key] = $val;
		}
	}

}

function ninja_forms_field_edit($slug){
	global $ninja_forms_fields;
	$function_name = $ninja_forms_fields[$slug]['edit_function'];
	$arguments = func_get_args();
    array_shift($arguments); // We need to remove the first arg ($function_name)
    call_user_func_array($function_name, $arguments);
}

//Screen option registration function
function ninja_forms_register_screen_option($id, $args){
	global $ninja_forms_screen_options;
	if(isset($args['display_function'])){
		$display_function = $args['display_function'];
	}else{
		$display_function = '';
	}
	if(isset($args['save_function'])){
		$save_function = $args['save_function'];
	}else{
		$save_function = '';
	}
	if(isset($args['page'])){
		$page = $args['page'];
	}else{
		$page = '';
	}
	if(isset($args['tab'])){
		$tab = $args['tab'];
	}else{
		$tab = '';
	}
	if(isset($args['order'])){
		$order = $args['order'];
	}else{
		$order = '';
	}
	
	if($page == '' AND $tab == ''){
		$ninja_forms_screen_options['_universal_'][$id]['display_function'] = $display_function;	
		$ninja_forms_screen_options['_universal_'][$id]['save_function'] = $save_function;	
	}elseif($page != '' AND $tab == ''){
		$ninja_forms_screen_options[$page]['_universal_'][$id]['display_function'] = $display_function;	
		$ninja_forms_screen_options[$page]['_universal_'][$id]['save_function'] = $save_function;	
	}elseif($page != '' AND $tab != ''){
		$ninja_forms_screen_options[$page][$tab][$id]['display_function'] = $display_function;	
		$ninja_forms_screen_options[$page][$tab][$id]['save_function'] = $save_function;	
	}
}

//Help tab registration function
function ninja_forms_register_help_screen_tab($id, $args){
	global $ninja_forms_help_screen_tabs;

	if(isset($args['title'])){
		$title = $args['title'];
	}else{
		$title = '';
	}
	if(isset($args['display_function'])){
		$display_function = $args['display_function'];
	}else{
		$display_function = '';
	}
	if(isset($args['page'])){
		$page = $args['page'];
	}else{
		$page = '';
	}
	if(isset($args['tab'])){
		$tab = $args['tab'];
	}else{
		$tab = '';
	}
	if(isset($args['order'])){
		$order = $args['order'];
	}else{
		$order = '';
	}
	
	if($page == '' AND $tab == ''){
		$ninja_forms_help_screen_tabs['_universal_'][$id]['title'] = $title;
		$ninja_forms_help_screen_tabs['_universal_'][$id]['content'] = $display_function;
	}elseif($page != '' AND $tab == ''){
		$ninja_forms_help_screen_tabs[$page]['_universal_'][$id]['title'] = $title;
		$ninja_forms_help_screen_tabs[$page]['_universal_'][$id]['content'] = $display_function;
	}elseif($page != '' AND $tab != ''){
		$ninja_forms_help_screen_tabs[$page][$tab][$id]['title'] = $title;
		$ninja_forms_help_screen_tabs[$page][$tab][$id]['content'] = $display_function;
	}
}

//Tab - Metaboxes Registration function
function ninja_forms_register_tab_metabox($args = array()){
	global $ninja_forms_tabs_metaboxes;

	$page = $args['page'];
	$tab = $args['tab'];
	$slug = $args['slug'];
	
	if(!isset($args['state'])){
		$args['state'] = '';
	}

	if( !isset( $args['display_container'] ) ){
		$args['display_container'] = true;
	}

	if( !isset( $args['save_function'] ) ){
		$save_function = '';
	}

	foreach($args as $key => $val){
		$ninja_forms_tabs_metaboxes[$page][$tab][$slug][$key] = $val;
	}
}

//Register Tab Metabox Options
function ninja_forms_register_tab_metabox_options( $args = array() ){
	global $ninja_forms_tabs_metaboxes;
	
	$page = $args['page'];
	$tab = $args['tab'];
	$slug = $args['slug'];
	$new_settings = $args['settings'];

	if( isset( $ninja_forms_tabs_metaboxes[$page][$tab][$slug]['settings'] ) ){
		$settings = $ninja_forms_tabs_metaboxes[$page][$tab][$slug]['settings'];
	}else{
		$settings = array();
	}

	if( is_array( $new_settings ) AND !empty( $new_settings ) ){
		foreach( $new_settings as $s ){
			if( is_array( $settings ) ){
				array_push( $settings, $s );
			}
		}
	}

	$ninja_forms_tabs_metaboxes[$page][$tab][$slug]['settings'] = $settings;
}