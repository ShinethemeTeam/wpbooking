<?php
    /**
     * @since 1.0.0
     **/

    $old_data = ( isset( $data[ 'custom_data' ] ) ) ? esc_html( $data[ 'custom_data' ] ) : get_post_meta( $post_id, esc_html( $data[ 'id' ] ), true );
    if ( empty( $data[ 'std' ] ) and empty( $old_data ) ) {
        $old_data = $data[ 'std' ];
    }


    $class      = ' wpbooking-form-group ';
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class .= ' wpbooking-condition  ';
        $data_class .= ' data-condition=' . $data[ 'condition' ] . ' ';
    }

    $class .= ' width-' . $data[ 'width' ];
    $field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

    $name = isset( $data[ 'custom_name' ] ) ? esc_html( $data[ 'custom_name' ] ) : esc_html( $data[ 'id' ] );


    if ( is_array( $data[ 'value' ] ) && !empty( $data[ 'value' ] ) ) {

        foreach ( $data[ 'value' ] as $key => $value ) {
            $checked = '';
            if ( !empty( $data[ 'std' ] ) && ( esc_html( $key ) == esc_html( $data[ 'std' ] ) ) ) {
                $checked = ' checked ';
            }
            if ( $old_data && !empty( $old_data ) ) {
                if ( esc_html( $key ) == esc_html( $old_data ) ) {
                    $checked = ' checked ';
                } else {
                    $checked = '';
                }
            }
            $field .= '<div class="' . esc_html( $data[ 'class' ] ) . ' ' . esc_attr( $checked ) . '"><label><input type="radio" name="' . $name . '"  value="' . esc_html( $key ) . '" ' . esc_attr( $checked ) . '> <span>' . esc_html( $value ) . '</span></label></div>';
        }
    }

    $field .= '</div></div>';

?>

<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data[ 'id' ] ); ?>"><?php echo esc_html( $data[ 'label' ] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <?php echo do_shortcode( $field ); ?>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
    </div>
</div>