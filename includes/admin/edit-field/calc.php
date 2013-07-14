<?php

/*
 *
 * Function used to output calcluation options on each field editing section on the back-end.
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_edit_field_calc( $field_id ) {
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	if ( $ninja_forms_fields[$field_type]['process_field'] AND $field_type != '_calc' ) {
		if ( isset ( $field_row['data']['calc'] ) AND $field_row['data']['calc'] != '' ) {
			$calc = $field_row['data']['calc'];
		} else {
			$calc = array();
		}

		?>
		<div id="ninja-forms-calculations">
			<div class="label">
				Calculations - <a href="#" name="" id="ninja_forms_field_<?php echo $field_id;?>_add_calc" class="ninja-forms-field-add-calc" rel="<?php echo $field_id;?>"><?php _e( 'Add Calculation', 'ninja-forms' );?></a>
				<span class="spinner" style="float:left;"></span>
			</div>
			
			<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[calc]" value="">
			<div id="ninja_forms_field_<?php echo $field_id;?>_calc" class="" name="">
				<?php
				$x = 0;
				foreach ( $calc as $c ) {
					ninja_forms_output_field_calc_row( $field_id, $c, $x );
				 	$x++;
				}
				?>				
			</div>
		</div>
		<?php
	}
}

add_action( 'ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_calc', 11 );

/*
 *
 * Function to output specific calculation options for a given field
 *
 * @param int $field_id - ID of the field being edited.
 * @param array $c - Array containing the data.
 * @param int $x - Index for this row of the calc array.
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_output_field_calc_row( $field_id, $c = array(), $x = 0 ){
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	$form_id = $field_row['form_id'];

	if ( isset ( $c['calc'] ) ) {
		$calc = $c['calc'];
	} else {
		$calc = '';
	}

	if ( isset ( $c['operator'] ) ) {
		$operator = $c['operator'];
	} else {
		$operator = '';
	}

	if ( isset ( $c['value'] ) ) {
		$value = $c['value'];
	} else {
		$value = '';
	}

	if ( isset ( $c['when'] ) ) {
		$when = $c['when'];
	} else {
		$when = '';
	}
	?>
	<div id="ninja_forms_field_<?php echo $field_id;?>_calc_row_<?php echo $x;?>" class="ninja-forms-calc-row" rel="<?php echo $x;?>">
		<a href="#" id="ninja_forms_field_<?php echo $field_id;?>_remove_calc" name="<?php echo $x;?>" rel="<?php echo $field_id;?>" class="ninja-forms-field-remove-calc">X</a>

		<select name="ninja_forms_field_<?php echo $field_id;?>[calc][<?php echo $x;?>][calc]" class="ninja-forms-calc-select">
			<option value=""><?php _e( '- Select a Calculation', 'ninja-forms' );?></option>
			<?php
			// Loop through our fields and output all of our calculation fields.
			$fields = ninja_forms_get_fields_by_form_id( $form_id );
			foreach ( $fields as $field ) {
				if ( $field['type'] == '_calc' ) {
					if ( isset ( $field['data']['calc_name'] ) ) {
						$calc_name = $field['data']['calc_name'];
					} else {
						$calc_name = 'calc_name';
					}
					?>
					<option value="<?php echo $field['id'];?>" <?php selected( $calc, $field['id'] );?>><?php echo $calc_name;?></option>
					<?php
				}
			}
			?>
		</select>

		<select name="ninja_forms_field_<?php echo $field_id;?>[calc][<?php echo $x;?>][operator]">
			<option value="add" <?php selected( $c['operator'], 'add' );?>>+</option>
			<option value="subtract" <?php selected( $c['operator'], 'subtract' );?>>-</option>
			<option value="multiply" <?php selected( $c['operator'], 'multiply' );?>>*</option>
			<option value="divide" <?php selected( $c['operator'], 'divide' );?>>/</option>
		</select>
		<?php
		switch ( $field_type ) {
			case '_checkbox':
				?>
				<input type="text" name="ninja_forms_field_<?php echo $field_id;?>[calc][<?php echo $x;?>][value]" value="<?php echo $c['value'];?>">
				if
				<select name="ninja_forms_field_<?php echo $field_id;?>[calc][<?php echo $x;?>][when]">
					<option value="checked" <?php selected( $c['when'], 'checked' );?>><?php _e( 'Checked', 'ninja-forms' );?></option>
					<option value="unchecked" <?php selected( $c['when'], 'unchecked' );?>><?php _e( 'Unchecked', 'ninja-forms' );?></option>
				</select>
				<?php
				break;
			case '_list':
				_e( 'Selected Option\'s Calc Setting', 'ninja-forms' );
				?>
				<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[calc][<?php echo $x;?>][value]" value="">
				<?php
				break;
			default:
				_e( 'This Value', 'ninja-forms' );
				?>
				<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[calc][<?php echo $x;?>][value]" value="">
				<?php
				break;

		}
		?>

	</div>
	<?php
}