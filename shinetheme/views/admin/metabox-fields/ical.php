<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 11/30/2017
     * Time: 8:19 AM
     * Since: 1.0.0
     * Updated: 1.0.0
     */

    $data = wp_parse_args( $data, [
        'placeholder' => ''
    ] );

    $class = $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class .= ' wpbooking-condition wpbooking-form-group ';
        $data_class .= ' data-condition=wpbooking_' . $data[ 'condition' ] . ' ';
    }

    $name = 'wpbooking_' . $data[ 'id' ];


    $old_data = esc_html( $data[ 'std' ] );

    $value = get_post_meta( $post_id, 'ical_url', true );
    if ( !empty( $value ) ) {
        $old_data = $value;
    }

    $ical_des = get_post_meta( $post_id, 'sys_created', true );
?>
<div
    class="form-table wpbooking-settings wpbooking-form-group wpbooking_ical <?php echo esc_attr( $class ); ?>" <?php echo esc_attr( $data_class ) ?>>

    <div class="wpbooking-field-content wpbooking-field-content-ical pl15 pr15">
        <input type="text" id="<?php echo esc_attr( $name ) ?>" class="form-control  min-width-500"
               value="<?php echo esc_html( $value ) ?>" name="<?php echo esc_html( $name ) ?>"
               placeholder="<?php echo esc_html( $data[ 'placeholder' ] ) ?>">
        <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
        <input type="hidden" name="type_ical" value="<?php echo esc_attr( $data[ 'post_type' ] ); ?>">
        <div class="clear block mt10"></div>
        <label><input type="checkbox" class="overwrite" name="overwrite"
                      value="yes"><?php echo esc_html__( 'Overwrite previous sync', 'wp-booking-management-system' ); ?></label>
        <div class="clear block mt10"></div>
        <button
            class="button button-primary button-medium wb-button save"><?php echo esc_html__( 'Synchronized', 'wp-booking-management-system' ) ?> </button>
        <?php if ( $ical_des ): ?>
            <p class="wpbooking-ical-des mt15"><?php echo sprintf( esc_html__( 'Last synchronized on %s', 'wp-booking-management-system' ), date( 'Y-m-d H:i:s', $ical_des ) ); ?></p>
        <?php endif; ?>
        <div class="clear block mt20"></div>
        <div class="form-message block"></div>
    </div>
    <div class="overlay-content hide">
        <span class="spinner is-active"></span>
    </div>
</div>