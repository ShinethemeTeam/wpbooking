<?php
    /**
     * @since 1.0.0
     **/
    $default    = [
        'days'    => 0,
        'hours'   => 0,
        'minutes' => 0
    ];
    $old_data   = ( isset( $data[ 'custom_data' ] ) ) ? $data[ 'custom_data' ] : get_post_meta( $post_id, esc_html( $data[ 'id' ] ), true );
    $old_data   = wp_parse_args( $old_data, $default );
    $class      = ' wpbooking-form-group ';
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class      .= ' wpbooking-condition ';
        $data_class .= ' data-condition=' . $data[ 'condition' ] . ' ';
    }
    $class .= ' width-' . $data[ 'width' ];
    $attr  = false;
    if ( !empty( $data[ 'attr' ] ) and is_array( $data[ 'attr' ] ) ) {
        $attr = implode( ' ', $data[ 'attr' ] );
    }

    $field = '';
    $name  = isset( $data[ 'custom_name' ] ) ? esc_html( $data[ 'custom_name' ] ) : esc_html( $data[ 'id' ] );
    $name  = str_replace( '[]', '', $name );
?>
<div class="wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data[ 'id' ] ); ?>"><?php echo esc_html( $data[ 'label' ] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group field-day-range">
                <div class="mb7">
                    <label><input type="number" value="<?php echo esc_attr($old_data[ 'days' ]) ?>"
                                  name="<?php echo esc_attr($name); ?>[days][]">Days</label>
                    <label><input type="number" value="<?php echo esc_attr($old_data[ 'hours' ]) ?>"
                                  name="<?php echo esc_attr($name); ?>[hours][]">Hours</label>
                    <label><input type="number" value="<?php echo esc_attr($old_data[ 'minutes' ]) ?>"
                                  name="<?php echo esc_attr($name); ?>[minutes][]">Minutes</label>
                </div>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
    </div>
</div>