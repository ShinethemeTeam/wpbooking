<?php
    /**
     * @since 1.0.0
     **/
    wp_enqueue_script( 'wpbooking-base64' );
    $post_id = $post_id;

    $is_show_map = (int)get_post_meta( $post_id, 'is_show_map', true );
    $map_zoom    = (int)get_post_meta( $post_id, 'map_zoom', true );
    if ( !$map_zoom ) {
        $map_zoom = 10;
    }
    $locations = get_post_meta( $post_id, 'pickup_location', true );

    $class      = ' wpbooking-form-group ';
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class      .= ' wpbooking-condition  ';
        $data_class .= ' data-condition=' . $data[ 'condition' ] . ' ';
    }
    $field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

    $field .= '<div class="wpbooking-pickup-location-wrapper"><div class="gmap-container"><div id="' . esc_html( $data[ 'id' ] ) . '" class="gmap-content"></div>
    <input type="text" name="gmap-search" value="" placeholder="' . esc_html__( 'Enter a address...', 'wp-booking-management-system' ) . '" class="gmap-search">
    </div>
    <input class="pickup-location-input" type="hidden" name="' . esc_attr( $data[ 'id' ] ) . '" value="' . esc_attr( $locations ) . '">
    <input class="map-zoom" type="hidden" name="map_zoom" value="' . esc_attr( $map_zoom ) . '">
    ';
    $field .= ' </div ></div ></div > ';
?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data[ 'id' ] ); ?>"><?php echo esc_html( $data[ 'label' ] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="content-gmap">
            <br>
            <?php echo do_shortcode( $field ); ?>
            <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
        </div>
    </div>
</div>