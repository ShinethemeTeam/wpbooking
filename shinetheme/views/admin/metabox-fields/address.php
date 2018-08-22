<?php
    $old_data   = ( isset( $data[ 'custom_data' ] ) ) ? esc_html( $data[ 'custom_data' ] ) : get_post_meta( $post_id, esc_html( $data[ 'id' ] ), true );
    $class      = ' wpbooking-form-group ';
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class      .= ' wpbooking-condition ';
        $data_class .= ' data-condition=' . $data[ 'condition' ] . ' ';
    }
    if ( !empty( $data[ 'container_class' ] ) ) $class .= ' ' . $data[ 'container_class' ];
    $class .= ' width-' . $data[ 'width' ];
    $name  = isset( $data[ 'custom_name' ] ) ? esc_html( $data[ 'custom_name' ] ) : esc_html( $data[ 'id' ] );
?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data[ 'id' ] ); ?>"><?php echo esc_html( $data[ 'label' ] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <div class="wpbooking-row">
                    <div class="wpbooking-col-sm-12">
                        <?php wp_dropdown_categories( [
                            'show_option_all' => esc_html__( 'Please Select', 'wp-booking-management-system' ),
                            'taxonomy'        => 'wpbooking_location',
                            'class'           => 'widefat form-control',
                            'name'            => 'location_id',
                            'orderby'         => 'name',
                            'order'           => 'ASC',
                            'selected'        => get_post_meta( $post_id, 'location_id', true ),
                            'hide_empty'      => false,
                            'hierarchical'    => 1
                        ] ) ?>
                        <p class="help-block"><?php echo esc_html__( 'Place, location, spot, site, locality', 'wp-booking-management-system' ) ?></p>
                    </div>
                    <div class="wpbooking-col-sm-12">
                        <input type="text" name="zip_code"
                               placeholder="<?php echo esc_html__( 'Zip/Postcode', 'wp-booking-management-system' ) ?>"
                               value="<?php echo esc_attr( get_post_meta( $post_id, 'zip_code', true ) ) ?>"
                               class="widefat form-control">
                        <p class="help-block"><?php echo esc_html__( 'Zip/ postcode', 'wp-booking-management-system' ) ?></p>
                    </div>
                    <div class="wpbooking-col-sm-12">
                        <input type="text" name="address" placeholder="<?php echo esc_html__( 'Address', 'wp-booking-management-system' ) ?>"
                               value="<?php echo esc_attr( get_post_meta( $post_id, 'address', true ) ) ?>"
                               class="widefat form-control">
                        <p class="help-block"><?php echo esc_html__( 'The address of neighborhood, organization and clusters', 'wp-booking-management-system' ) ?></p>
                    </div>
                    <?php if ( empty( $data[ 'exclude' ] ) or !in_array( 'apt_unit', $data[ 'exclude' ] ) ) { ?>
                        <div class="wpbooking-col-sm-12">
                            <input type="text" name="apt_unit"
                                   placeholder="<?php echo esc_html__( 'Apt/Unit #', 'wp-booking-management-system' ) ?>"
                                   value="<?php echo esc_attr( get_post_meta( $post_id, 'apt_unit', true ) ) ?>"
                                   class="widefat form-control">
                            <p class="help-block"><?php echo esc_html__( 'The number of house, floor, building', 'wp-booking-management-system' ) ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
    </div>
</div>
