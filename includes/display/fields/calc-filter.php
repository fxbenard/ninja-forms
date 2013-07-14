<?php

/*
 *
 * Function that filters our fields looking for calculations that need to be made upon page load.
 *
 * @since 2.2.28
 * @returns $data
 */

function ninja_forms_field_calc_filter( $data, $field_id ){
	global $ninja_forms_processing;
	$field_row = ninja_forms_get_field_by_id( $field_id );
	if ( $field_row['type'] == '_calc' ) {

		// Check to see if the advanced calculation field is to be used.
		if ( isset ( $field_row['data']['use_calc_adv'] ) AND $field_row['data']['use_calc_adv'] == 1 ) {
			$use_calc_adv = $field_row['data']['use_calc_adv'];
		} else {
			$use_calc_adv = 0;
		}

		// Check to see if we have an advanced calculation equation.
		if ( isset ( $field_row['data']['calc_adv'] ) AND $field_row['data']['calc_adv'] != '' ) {
			$calc_adv = $field_row['data']['calc_adv'];
		} else {
			$calc_adv = '';
		}

		if ( isset ( $ninja_forms_processing ) ) {
			$data['default_value'] = 0;
			$all_fields = $ninja_forms_processing->get_all_fields();
			$form_id = $ninja_forms_processing->get_form_ID();
			if ( is_array ( $all_fields ) ) {
				foreach ( $all_fields as $f_id => $user_value ) {
					$field = $ninja_forms_processing->get_field_settings( $f_id );
					$field_data = $field['data'];
					remove_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
					$field_data = apply_filters( 'ninja_forms_field', $field_data, $field['id'] );

					// Get our default value.
					if ( isset ( $field_data['default_value'] ) ) {
						$default_value = $field_data['default_value'];
					} else {
						$default_value = 0;
					}

					if ( !$default_value OR $default_value == '' ) {
						$default_value = 0;
					} else if ( is_array ( $default_value ) ) {
						$default_value = array_shift( $default_value );
					}

					// Check to see if we are using the advanced calculation field.
					if ( $use_calc_adv == 1 ) {
						if (preg_match('/\bfield_'.$field['id'].'\b/i', $calc_adv ) ) {
							add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
							$field_data = apply_filters( 'ninja_forms_field', $field_data, $field['id'] );
							// Get our default value.
							if ( isset ( $field_data['default_value'] ) ) {
								$default_value = $field_data['default_value'];
							} else {
								$default_value = 0;
							}

							if ( !$default_value OR $default_value == '' ) {
								$default_value = 0;
							} else if ( is_array ( $default_value ) ) {
								$default_value = array_shift( $default_value );
							}

						}

						// We are using the advanced field, check to see if this field id is in the calculation.
						$calc_adv = preg_replace('/\bfield_'.$field['id'].'\b/', $default_value, $calc_adv );
						$eq = new eqEOS();
						$result = $eq->solveIF($calc_adv);
						$data['default_value'] = $result;
					} else {
						// If we aren't using the advanced field, then check this field for calculations that affect this calc field.
						if ( isset ( $field_data['calc'] ) AND is_array ( $field_data['calc'] ) ) {
							foreach ( $field_data['calc'] as $calc ) {
								if ( $calc['calc'] == $field_id ) {
									$perform_op = true;
									
									// Figure out what our default value should be for performing the calculation.
									if ( isset ( $calc['value'] ) AND $calc['value'] != '' ) {
										$calc_value = $calc['value'];
									} else if ( $field['type'] == '_list' ) {
										if ( isset ( $field_data['list']['options'] ) ) {
											foreach ( $field_data['list']['options'] as $option ) {
												if ( $option['label'] == $default_value OR $option['value'] == $default_value ) {
													$calc_value = $option['calc'];
													break;
												}
											}
										}
									} else {
										$calc_value = $default_value;
									}

									if ( !isset( $calc_value ) OR !is_numeric ( $calc_value ) OR $calc_value == '' ) {
										$calc_value = 0;
									}

									// If this is a checkbox, look at the "when" statement and make sure it matches our default value.
									if ( isset ( $calc['when'] ) AND $calc['when'] != '' ) {
										if ( $default_value != $calc['when'] ) {
											$perform_op = false;
										}
									}

									if ( $perform_op AND $default_value !== 0 ) {
										$data['default_value'] = ninja_forms_calc_evaluate( $calc['operator'], $data['default_value'], $calc_value );
									}
								}
							}
						}
					}
				}
				add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
			}
		} else {
			$form_row = ninja_forms_get_form_by_field_id( $field_id );
			$form_id = $form_row['id'];
			$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
			if ( is_array ( $all_fields ) ) {
				foreach ( $all_fields as $field ) {
					$field_data = $field['data'];
					remove_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
					$field_data = apply_filters( 'ninja_forms_field', $field_data, $field['id'] );

					// Get our default value.
					if ( isset ( $field_data['default_value'] ) ) {
						$default_value = $field_data['default_value'];
					} else {
						$default_value = 0;
					}

					if ( !$default_value OR $default_value == '' ) {
						$default_value = 0;
					} else if ( is_array ( $default_value ) ) {
						$default_value = array_shift( $default_value );
					}

					// Check to see if we are using the advanced calculation field.
					if ( $use_calc_adv == 1 ) {
						if (preg_match('/\bfield_'.$field['id'].'\b/i', $calc_adv ) ) {
							add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
							$field_data = apply_filters( 'ninja_forms_field', $field_data, $field['id'] );
							// Get our default value.
							if ( isset ( $field_data['default_value'] ) ) {
								$default_value = $field_data['default_value'];
							} else {
								$default_value = 0;
							}

							if ( !$default_value OR $default_value == '' ) {
								$default_value = 0;
							} else if ( is_array ( $default_value ) ) {
								$default_value = array_shift( $default_value );
							}

						}

						// We are using the advanced field, check to see if this field id is in the calculation.
						$calc_adv = preg_replace('/\bfield_'.$field['id'].'\b/', $default_value, $calc_adv );
						$eq = new eqEOS();
						$result = $eq->solveIF($calc_adv);
						$data['default_value'] = $result;
					} else {
						// If we aren't using the advanced field, then check this field for calculations that affect this calc field.
						if ( isset ( $field_data['calc'] ) AND is_array ( $field_data['calc'] ) ) {
							foreach ( $field_data['calc'] as $calc ) {
								if ( $calc['calc'] == $field_id ) {
									$perform_op = true;
									
									// Figure out what our default value should be for performing the calculation.
									if ( isset ( $calc['value'] ) AND $calc['value'] != '' ) {
										$calc_value = $calc['value'];
									} else if ( $field['type'] == '_list' ) {
										if ( isset ( $field_data['list']['options'] ) ) {
											foreach ( $field_data['list']['options'] as $option ) {
												if ( $option['label'] == $default_value OR $option['value'] == $default_value ) {
													$calc_value = $option['calc'];
													break;
												}
											}
										}
									} else {
										$calc_value = $default_value;
									}

									if ( !isset( $calc_value ) OR !is_numeric ( $calc_value ) OR $calc_value == '' ) {
										$calc_value = 0;
									}

									// If this is a checkbox, look at the "when" statement and make sure it matches our default value.
									if ( isset ( $calc['when'] ) AND $calc['when'] != '' ) {
										if ( $default_value != $calc['when'] ) {
											$perform_op = false;
										}
									}

									if ( $perform_op AND $default_value !== 0 ) {

										$data['default_value'] = ninja_forms_calc_evaluate( $calc['operator'], $data['default_value'], $calc_value );
									}
								}
							}
						}
					}
				}
				add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
			}
		}
	}
	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );

/*
 *
 * Function that filters the list options span and adds the appropriate listener class if there is a calc needed for the field.
 *
 * @since 2.2.28
 * @returns $class
 */

function ninja_forms_calc_filter_list_options_span( $class, $field_id ){
	$field_row = ninja_forms_get_field_by_id( $field_id );
	if ( isset ( $field_row['data']['calc'] ) AND !empty ( $field_row['data']['calc'] ) ) {
		$class .= ' ninja-forms-field-list-options-span-calc-listen';
	}
	return $class;
}

add_filter( 'ninja_forms_display_list_options_span_class', 'ninja_forms_calc_filter_list_options_span', 10, 2 );

/*
 *
 * Function that takes two variables and our calculation string operator and returns the result.
 *
 * @since 2.2.28
 * @returns int value
 */

function ninja_forms_calc_evaluate($op, $value1, $value2 ){
	switch ( $op ) {
		case 'add':
			return $value1 + $value2;
			break;
		case 'subtract':
			return $value1 - $value2;
			break;
		case 'multiply':
			return $value1 * $value2;
			break;
		case 'divide':
			return $value1 / $value2;
			break;
	}

}