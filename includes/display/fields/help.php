<?php
/**
 * Outputs the HTML of the help icon if it is set to display.
 *
**/
add_action('init', 'ninja_forms_register_display_field_help');
function ninja_forms_register_display_field_help(){
	add_action( 'ninja_forms_display_field_help', 'ninja_forms_display_field_help' );
}

function ninja_forms_display_field_help( $field_id ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$data = $field_row['data'];

	if( isset( $data['show_help'] ) ){
		$show_help = $data['show_help'];
	}else{
		$show_help = 0;
	}

	if( isset( $data['help_text'] ) ){
		$help_text = $data['help_text'];
	}else{
		$help_text = '';
	}



	if($show_help){
		?>
		<img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="<?php echo $help_text;?>">
	<?php
	}
}