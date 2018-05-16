<?php
    $data_value = wpbooking_get_option( $data[ 'id' ], $data[ 'std' ] );
    $name       = 'wpbooking_' . $data[ 'id' ];

    if ( !empty( $data[ 'element_list_item' ] ) ) {
        $name = $data[ 'custom_name' ];
    }
    if ( !empty( $data[ 'element_list_item' ] ) ) {
        $data_value = $data[ 'custom_value' ];
    }
    $class      = $name;
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class      .= ' wpbooking-condition wpbooking-form-group ';
        $data_class .= ' data-condition=wpbooking_' . $data[ 'condition' ] . ' ';
    }
    $data_value = stripslashes( $data_value );
?>
<tr class="<?php echo esc_html( $class ) ?>" <?php echo esc_attr( $data_class ) ?>>
    <th scope="row">
        <label for="<?php echo esc_html( $name ) ?>"><?php echo esc_html( $data[ 'label' ] ) ?>:</label>
    </th>
    <td>
        <textarea id="<?php echo esc_attr( $name ) ?>" name="<?php echo esc_html( $name ) ?>"
                  class="form-control  min-width-500"><?php echo esc_textarea( $data_value ) ?></textarea>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
    </td>

</tr>